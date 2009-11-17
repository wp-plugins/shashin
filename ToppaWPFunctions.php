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
    function createTable(&$object, $table) {
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
     * Generates and echoes xhtml for form inputs.
     *
     * @static
     * @access public
     * @param string $input_name (required) the name to use for the input field
     * @param array $ref_data (required) contains data about how the input field should be set up
     * @param string $input_value (optional) a value to apply to the input
     * @param string $delimiter (optional) separator between radio buttons and checkboxes
     */
    function displayInput($input_name, $ref_data, $input_value = null, $delimiter = null) {
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
    function htmlentities(&$string, $key) {
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
    function trim(&$string, $key) {
        $string = trim($string);
    }
}
?>
