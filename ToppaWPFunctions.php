<?php
/**
 * ToppaWPFunctions class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
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
     * passed-in object. Note that dbDelta is very picky about formatting - see
     * http://codex.wordpress.org/Creating_Tables_with_Plugins
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
     */
    function sqlSelect($table, $keywords = null, $conditions = null, $other = null, $type = 'get_row', $return = ARRAY_A) {
        global $wpdb;
        $sql = "select ";

        if (is_string($keywords)) {
            $sql .= $keywords;
        }

        elseif (is_array($keywords)) {
            $sql .= implode(", ", $keywords);
        }

        else {
            return false;
        }

        if (is_string($table)) {
            $sql .= " from $table ";
        }

        else {
            return false;
        }

        if (is_array($conditions)) {
            $sql .= "where ";
            $sql .= ToppaWPFunctions::_sqlPrepare($conditions);
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

        return $result;
    }

    /**
     * Creates and executes a SQL update statement based on passed-in parameters.
     *
     * It will escape values in the $fields array. If $conditions is an array,
     * it will escape values. If it's a string, it must contain the WHERE
     * keyword, and the values must be escaped before calling this function.
     *
     * @static
     * @access public
     * @param string $table the name of the table to query
     * @param array $fields name-value pairs of the fields to update
     * @param string|array $conditions (optional) array of key-values pairs, or a string containing its own WHERE clause
     * @return mixed passes along the return value of the $wpdb call
     */
    function sqlUpdate($table, $fields, $conditions = null) {
        global $wpdb;

        if (is_string($table)) {
            $sql = "update $table set ";
        }

        else {
            return false;
        }

        if (is_array($fields)) {
            $sql .= ToppaWPFunctions::_sqlPrepare($fields, ",");
            $sql .= " ";
        }

        else {
            return false;
        }

        if (is_array($conditions)) {
            $sql .= "where ";
            $sql .= ToppaWPFunctions::_sqlPrepare($conditions);
        }

        elseif (is_string($conditions)) {
            $sql .= $conditions;
        }

        $sql .=";";
        return $wpdb->query($sql);
    }

    /**
     * Creates and executes a SQL insert statement based on passed-in parameters.
     *
     * If you pass $fields and $values, they are treated as the name-value pairs
     * to update, and are related positionally. If you pass $fields only, it
     * assumes its actually a hash of key-value pairs. All values are escaped.
     * Note the update option currently works only if $fields contains key-
     * value pairs.
     *
     * @static
     * @access public
     * @param string $table the name of the table to query
     * @param array $fields column names or a hash of key-value pairs
     * @param array $values (optional) the values to insert if $keys is only a list of columns
     * @param string $update (optional) run an update instead if the insert would cause a duplicate key on a unique index (default: false)
     * @return mixed passes along the return value of the $wpdb call
     */
    function sqlInsert($table, $fields, $values = null, $update = false) {
        global $wpdb;

        if (is_string($table)) {
            $sql = "insert into $table (";
        }

        else {
            return false;
        }

        if (is_array($fields) && !$values) {
            $values = array_values($fields);
            $keys = array_keys($fields);
        }

        if (!is_array($values)) {
            return false;
        }

        $sql .= implode(",", $keys);
        $sql .= ") values (";
        $sql .= ToppaWPFunctions::_sqlPrepare($values, ",", true);
        $sql .= ")";

        if ($update) {
            $sql .= " on duplicate key update ";
            $sql .= ToppaWPFunctions::_sqlPrepare($fields, ",");
        }

        $sql .= ";";
        return $wpdb->query($sql);
    }

    /**
     * Creates and executes a SQL delete statement based on passed-in parameters.
     *
     * If $conditions is an array, it will escape values. If it's a string, it
     * must contain the WHERE keyword, and the values must be escaped before
     * calling this function.
     *
     * @static
     * @access public
     * @param string $table the name of the table to query
     * @param string|array $conditions array of key-values pairs, or a string containing its own WHERE clause
     * @return mixed passes along the return value of the $wpdb call
     */
    function sqlDelete($table, $conditions) {
        global $wpdb;

        if (is_string($table)) {
            $sql = "delete from $table ";
        }

        else {
            return false;
        }

        if (is_array($conditions)) {
            $sql .= "where ";
            $sql .= ToppaWPFunctions::_sqlPrepare($conditions);
        }

        elseif (is_string($conditions)) {
            $sql .= $conditions;
        }

        else {
            return false;
        }

        $sql .=";";
        return $wpdb->query($sql);
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
    function _sqlPrepare($conditions, $glue = " and ", $values_only = false) {
        global $wpdb;

        foreach ($conditions as $k=>$v) {
            if (!$values_only) {
                $sql .= "$k = ";
            }

            $sql .= ToppaWPFunctions::_sqlEscape($v);
            $sql .= $glue;
        }

        // remove the trailing glue
        return substr($sql, 0, -(strlen($glue)));
    }

    /**
     * Escapes and quotes values for safe inserting/updating to the database
     *
     * @static
     * @access private
     * @param string|float|integer $value a value to escape
     * @return string|float|integer returns $value, escaped and quoted as needed
     */
    function _sqlEscape($value) {
        global $wpdb;
        return (is_numeric($value) ? $value : ("'" . $wpdb->escape($value) . "'"));
    }

    /**
     * Generates and echoes xhtml for form inputs.
     *
     * Checkbox groups are currently hardcoded to a 3 column display.
     *
     * @static
     * @access public
     * @param string $input_name (required) the name to use for the input field
     * @param array $ref_data (required) contains data about how the input field should be set up
     * @param string $input_value (optional) a value to apply to the input
     */
    function displayInput($input_name, $ref_data, $input_value = null) {
        $input_id = str_replace("[", "_", $input_name);
        $input_id = str_replace("]", "", $input_id);

        switch ($ref_data['input_type']) {
        case 'text':
            echo '<input type="text" name="' . $input_name
                . '" id="' . $input_id
                . '" value="' . htmlspecialchars($input_value)
                . '" size="' . $ref_data['input_size'] . '"';

            if (strlen($ref_data['col_params']['length'])) {
                echo ' maxlength="' . $ref_data['col_params']['length'] . '"';
            }

            echo " />\n";
            break;

        case 'radio':
            foreach ($ref_data['input_subgroup'] as $value=>$label) {
                echo '<input type="radio" name="' . $input_name
                    . '" id="' . $input_id . "_" . htmlspecialchars($value)
                    . '" value="' . htmlspecialchars($value) . '"';

                if ($input_value == $value) {
                    echo ' checked="checked"';
                }

                echo ' /> ' . $label . "\n";
            }
            break;

        case 'select':
            echo '<select name="' . $input_name . '" id="' . $input_id . '">' . "\n";

            foreach ($ref_data['input_subgroup'] as $value=>$label) {
                echo '<option value="' . htmlspecialchars($value) . '"';

                if ($input_value == $value) {
                    echo ' selected="selected"';
                }

                echo '>' . $label . "</option>\n";
            }

            echo "</select>\n";
            break;

        case 'textarea':
            echo '<textarea name="' . $input_name . '" id="' . $input_id
                . '" cols="' . $ref_data['input_cols']
                . '" rows="' . $ref_data['input_rows'] . '">'
                . htmlspecialchars($input_value) . '</textarea>';
            break;

        case 'checkbox':
            echo '<input type="checkbox" name="' . $input_name
                    . '" id = "' . $input_id
                    . '" value="' . htmlspecialchars($value) . '"';

            if ($input_value == $value) {
                echo ' checked="checked"';
            }

            echo ' /> ' . $label . "\n";
            break;
        }
    }

    /**
     * Reads an RSS feed. Uses the included ToppaXMLParser.
     *
     * @static
     * @access public
     * @param string $feed_url the feed to parse
     * @param string $username needed only if authenticating
     * @param string $password needed only if authenticating
     * @param string $source needed only if authenticating
     * @param string $service needed only if authenticating
     * @param string $server needed only if authenticating
     * @return array the content of the feed
     */
    function readFeed($feed_url, $username = null, $password = null, $source = null, $service = null, $server = null) {
        $parser = new ToppaXMLParser();
        $authCode = null;

        if ($username && $password) {
            $authCode = ToppaWPFunctions::googleAuthenticate($username, $password, $source, $service, $server);

            if ($authCode === false) {
                return false;
            }
        }

        $parser->fetch($feed_url, $authCode);
        return $parser->parse();
    }

    /**
     * The function is for parsing the Picasa RSS feed. It assumes an
     * array of items, with keys that may have a : dividing the names
     * of related items, and containing an array with a 'data' value
     * and a possible 'attrs' array
     *
     * @static
     * @access public
     * @param array $feed_content should be supplied by the readFeed function
     * @param array $ref_data maps your local data structure to the feed's structure; it can handle arrays nested two levels below $item
     * @param string $match_field allows you to limit the results of the parse to a specific item
     * @param string $match_value the value to look for in $match_field
     * @param string $key allows you to use an item from the feed as the key for the returned array
     * @return boolean|array the parsed contents of the feed, or false on failure
     */
    function parseFeed($feed_content, $ref_data, $match_field = null, $match_value = null, $key = null) {
        $all_parsed = array();
        $break = false;

        # make sure there's something to parse
        if (!$feed_content) {
            return false;
        }

        foreach ($feed_content as $item) {
            // if there's a match_field, that means we're parsing the user's feed
            // for all albums, and we want to return just the matching album
            if (strlen($match_field) && isset($ref_data[$match_field]['feed_param_2'])
              && $item[$ref_data[$match_field]['feed_param_1'] . ":" . $ref_data[$match_field]['feed_param_2']]['data'] == $match_value) {
                $break = true;
            }

            elseif (strlen($match_field) && $item[$ref_data[$match_field]['feed_param_1']]['data'] == $match_value) {
                $break = true;
            }

            if ($key && $ref_data[$key]['feed_param_2']) {
                $key_val = $item[$ref_data[$key]['feed_param_1'] . ":" . $ref_data[$key]['feed_param_2']]['data'];
            }

            elseif ($key) {
                $key_val = $item[$ref_data[$key]['feed_param_1']]['data'];
            }

            $parsed = array();
            foreach ($ref_data as $ref_k=>$ref_v) {
                if ($ref_v['source'] == 'feed') {
                    if (isset($ref_v['feed_param_2'])) {
                        // if attrs is set, then we're looking for a particular value in the attrs array
                        // otherwise assume we're getting a string from 'data'
                        if (isset($ref_v['attrs'])) {
                            $parsed[$ref_k] = $item[$ref_v['feed_param_1'] . ":" . $ref_v['feed_param_2']]['attrs'][$ref_v['attrs']];
                        }

                        else {
                            $parsed[$ref_k] = $item[$ref_v['feed_param_1'] . ":" . $ref_v['feed_param_2']]['data'];
                        }
                    }

                    else {
                        if (isset($ref_v['attrs'])) {
                            $parsed[$ref_k] = $item[$ref_v['feed_param_1']]['attrs'][$ref_v['attrs']];
                        }

                        else {
                            $parsed[$ref_k] = $item[$ref_v['feed_param_1']]['data'];
                        }
                    }
                }
            }

            if ($break === true) {
                return $parsed;
            }

            if ($key_val) {
                $all_parsed[$key_val] = $parsed;

            }

            else {
                $all_parsed[] = $parsed;
            }
        }

        return $all_parsed;
    }

    /**
     * Get an authentication token for a Google service (defaults to
     * Picasa). Puts the token in session variable and will re-use it as
     * needed, instead of fetching a new token for every call.
     *
     * @static
     * @access public
     * @param string $username Google email account
     * @param string $password Password for Google email account
     * @param string $source name of the calling application
     * @param string $service name of the Google service to call
     * @param string $server the Google server to use (defaults to google.com)
     * @return boolean|string An authentication token, or false on failure
     */
    function googleAuthenticate($username, $password, $source, $service, $server = 'https://www.google.com') {
        $session_token = $source . '_' . $service . '_auth_token';

        if ($_SESSION[$session_token]) {
            return $_SESSION[$session_token];
        }

        // get an authorization token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server . "/accounts/ClientLogin");
        $post_fields = "accountType=" . urlencode('HOSTED_OR_GOOGLE')
            . "&Email=" . urlencode($username)
            . "&Passwd=" . urlencode($password)
            . "&source=" . urlencode($source)
            . "&service=" . urlencode($service);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true); // for debugging the request
        //var_dump(curl_getinfo($ch,CURLINFO_HEADER_OUT)); //for debugging the request
        $response = curl_exec($ch);
        curl_close($ch);

        if (strpos($response, '200 OK') === false) {
            return false;
        }

        // find the auth code
        preg_match("/(Auth=)([\w|-]+)/", $response, $matches);

        if (!$matches[2]) {
            return false;
        }

        $_SESSION[$session_token] = $matches[2];
        return $matches[2];
    }
}

?>
