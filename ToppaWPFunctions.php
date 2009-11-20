<?php
/**
 * ToppaWPFunctions class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage Classes
 */

/**
 * A collection of static methods facilitating common tasks for WordPress plugins.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ToppaWPFunctions {
    /**
     * Creates or updates a WordPress table, based on ref_data in a
     * passed-in object. Note that dbDelta is very picky about
     * formatting - see http://codex.wordpress.org/Creating_Tables_with_Plugins
     *
     * @static
     * @access public
     * @param object an object containing the ref_data needed for defining the table.
     * @param string $table The name of the table to create
     * @return mixed passes along the return value of WordPress' dbDelta()
     */
    public function createTable(&$object, $table) {
        $sql_end = "";
        $sql = "CREATE TABLE $table (\n";
        foreach ($object->ref_data as $k=>$v) {
            $sql .= $k . " " . $v['col_params']['type'];

            if (strlen($v['col_params']['length'])) {
                $sql .= "(" . $v['col_params']['length'];

                if (strlen($v['col_params']['precision'])) {
                    $sql .= "," . $v['col_params']['precision'];
                }

                $sql .= ")";
            }

            if ($v['col_params']['not_null']) {
                $sql .= " NOT NULL";
            }

            // dbDelta requires 2 spaces in front of primary key declaration
            if ($v['col_params']['primary_key']) {
                $sql .= "  PRIMARY KEY";
            }

            if (strlen($v['col_params']['other'])) {
                $sql .= " " . $v['col_params']['other'];
            }

            $sql .= ",\n";

            // WP requires unqiue indexes declared at the end, using KEY
            if ($v['col_params']['unique_key']) {
                $sql_end .= "UNIQUE KEY $k ($k),\n";
            }
        }

        $sql = $sql . $sql_end;
        $sql = substr($sql, 0, -2);
        $sql .= "\n)\nDEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        return dbDelta($sql, true);
    }

    /**
     * Creates and executes a SQL select statement based on passed-in
     * parameters.
     *
     * If $conditions is an array, it will escape values. If it's a string, it
     * must contain the WHERE keyword, and the values must be escaped before
     * calling this function.
     *
     * @static
     * @access public
     * @param string $table the name of the table to query
     * @param string|array $keywords the fields to return
     * @param string|array $conditions (optional) array of key-values pairs, or a string containing its own WHERE clause
     * @param string $other (optional) any additional conditions for the query (GROUP BY, etc.)
     * @param string $type (optional) the WP type of select query to run (default: get_row)
     * @param string $return (optional) how to format the return value (default: ARRAY_A)
     * @return mixed passes along the return value of the $wpdb call
     * @throws Exception invalid arguments or wuery error
     */
    public function sqlSelect($table, $keywords = null, $conditions = null, $other = null, $type = 'get_row', $return = ARRAY_A) {
        global $wpdb;
        $sql = "select ";

        if (is_string($keywords)) {
            $sql .= $keywords;
        }

        elseif (is_array($keywords)) {
            $sql .= implode(", ", $keywords);
        }

        else {
            throw new Exception(__("'keywords' argument must be a string or array", 'shashin'));
        }

        if (is_string($table)) {
            $sql .= " from $table ";
        }

        else {
            throw new Exception(__("'table' argument must be a string", 'shashin'));
        }

        if (is_array($conditions)) {
            $sql .= "where ";
            $sql .= ToppaWPFunctions::sqlPrepare($conditions);
        }

        elseif (is_string($conditions)) {
            $sql .= $conditions;
        }

        if (is_string($other)) {
            $sql .= " " . $other;
        }

        $sql .= ";";
        //var_dump($sql);
        //exit;

        $result = false;

        switch ($type) {
        case "get_results":
            $result = $wpdb->get_results($sql, $return);
            break;
        case "get_col":
            $result = $wpdb->get_col($sql);
            break;
        case "get_var":
            $result = $wpdb->get_var($sql);
            break;
        case "get_row":
            $result = $wpdb->get_row($sql, $return);
            break;
        }

        if ($result === false) {
            throw new Exception(__("Select query error", 'shashin'));
        }

        return $result;
    }

    /**
     * Builds a properly formatted partial SQL clause of key-value pairs, with
     * values escaped.
     *
     * @static
     * @access private
     * @param array $conditions key-value pairs to use in building the query string
     * @param string $glue (optional) glue for concatenating the name-value pairs (default: " and ")
     * @param boolean $values_only (optional) whether to include values only in the string (default: false)
     * @return string a formatted partial SQL clause of key-value pairs
     */
    private function sqlPrepare($conditions, $glue = " and ", $values_only = false) {
        global $wpdb;

        foreach ($conditions as $k=>$v) {
            if (!$values_only) {
                $sql .= "$k = ";
            }

            $sql .= is_numeric($v) ? $v : ("'" . mysql_real_escape_string($v) . "'");
            $sql .= $glue;
        }

        // remove the trailing glue
        return substr($sql, 0, -(strlen($glue)));
    }

    /**
     * Generates and echoes xhtml for form inputs.
     *
     * @static
     * @access public
     * @param string $input_name (required) the name to use for the input field
     * @param array $ref_data (required) contains data about how the input field should be set up
     * @param string $input_value (optional) a value to apply to the input
     * @param string $delimiter (optional) separator between radio buttons and checkboxes
     */
    public function displayInput($input_name, $ref_data, $input_value = null, $delimiter = null) {
        $input_id = str_replace("[", "_", $input_name);
        $input_id = str_replace("]", "", $input_id);

        switch ($ref_data['input_type']) {
        case 'text':
            $field = '<input type="text" name="' . $input_name
                . '" id="' . $input_id
                . '" value="' . htmlspecialchars($input_value)
                . '" size="' . $ref_data['input_size'] . '"';

            if (strlen($ref_data['col_params']['length'])) {
                $field .= ' maxlength="' . $ref_data['col_params']['length'] . '"';
            }

            $field .=  " />\n";
            break;

        case 'radio':
            foreach ($ref_data['input_subgroup'] as $value=>$label) {
                $id = $input_id . "_" . htmlspecialchars($value);
                $field .=  '<input type="radio" name="' . $input_name
                    . '" id="' . $id
                    . '" value="' . htmlspecialchars($value) . '"';

                if ($input_value == $value) {
                    $field .=  ' checked="checked"';
                }

                $field .=  ' /> <label for="' . $id . '">' . $label . "</label>"
                    . $delimiter . "\n";
            }
            break;

        case 'select':
            $field =  '<select name="' . $input_name . '" id="' . $input_id . '">' . "\n";

            foreach ($ref_data['input_subgroup'] as $value=>$label) {
                $field .=  '<option value="' . htmlspecialchars($value) . '"';

                if ($input_value == $value) {
                    $field .=  ' selected="selected"';
                }

                $field .=  '>' . $label . "</option>\n";
            }

            $field .=  "</select>\n";
            break;

        case 'textarea':
            $field =  '<textarea name="' . $input_name . '" id="' . $input_id
                . '" cols="' . $ref_data['input_cols']
                . '" rows="' . $ref_data['input_rows'] . '">'
                . htmlspecialchars($input_value) . '</textarea>';
            break;

        case 'checkbox':
            foreach ($ref_data['input_subgroup'] as $value=>$label) {
                $id = $input_id . "_" . htmlspecialchars($value);
                $field .=  '<input type="checkbox" name="' . $input_name
                    . '" id="' . $id
                    . '" value="' . htmlspecialchars($value) . '"';

                if ($input_value == $value) {
                    $field .=  ' checked="checked"';
                }

                $field .=  ' />';

                if ($label) {
                    $field .= '<label for="' . $id . '">' . $label . "</label>";
                }

                $field .= $delimiter . "\n";
            }
        }

        return $field;
    }

    /**
     * array_walk callback method for htmlentities()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    public function awHtmlentities(&$string, $key) {
        $string = htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * array_walk callback method for trim()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    public function awTrim(&$string, $key) {
        $string = trim($string);
    }
}
?>
