<?php
/**
 * ToppaWPFunctions class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.0.2
 * @package Shashin
 * @subpackage Classes
 */

/**
 * A collection of static methods covering common tasks in WordPress plugins.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ToppaWPFunctions {
    /**
     * Creates a WordPress table, based on refData in a passed-in object.
     *
     * @static
     * @access public
     * @param object an object containing the refData needed for defining the table.
     * @param string $tableName
     * @return mixed passes along the return value of WordPress' dbDelta()
     */
    function createTable(&$object, $tableName) {
        $sql = "CREATE TABLE $tableName (";
        foreach ($object->refData as $k=>$v) {
            $sql .= $k . " " . $v['colParams']['type'];

            if (strlen($v['colParams']['length'])) {
                $sql .= "(" . $v['colParams']['length'];

                if (strlen($v['colParams']['precision'])) {
                    $sql .= "," . $v['colParams']['precision'];
                }

                $sql .= ")";
            }

            if (strlen($v['colParams']['notNull'])) {
                $sql .= " NOT NULL";
            }

            if (strlen($v['colParams']['other'])) {
                $sql .= " " . $v['colParams']['other'];
            }

            $sql .= ", ";
        }

        $sql = substr("$sql", 0, -2);
        $sql .= ") DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";

        require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
        $return = dbDelta($sql, true);
    }

    /**
     * Creates and executes a SQL select statement based on passed-in parameters.
     *
     * @static
     * @access public
     * @param string $table the table to query
     * @param string|array $fields the fields to return
     * @param string $conditions optional string containing any desired WHERE, ORDER BY, etc clauses
     * @param string $type the type of select query to run (supports get_results, get_col, get_var, and get_row)
     * @param string $return how to format the return value (defaults to ARRAY_A)
     * @param string $keywords optional keywords for the select statement (e.g. DISTINCT)
     * @return mixed passes along the return value of the $wpdb call
     */
    function select($table, $fields = '*', $conditions = null, $type = null, $return = ARRAY_A, $keywords = null) {
        global $wpdb;
        $sql = "SELECT $keywords ";

        if (is_array($fields)) {
            $sql .= implode(", ", $fields);
        }

        else {
            $sql .= $fields;
        }

        $sql .= " FROM $table $conditions";

        switch ($type) {
        case "get_results":
            $retVal = $wpdb->get_results($sql, $return);
            break;
        case "get_col":
            $retVal = $wpdb->get_col($sql);
            break;
        case "get_var":
            $retVal = $wpdb->get_var($sql);
            break;
        default:
            $retVal = $wpdb->get_row($sql, $return);
        }

        return $retVal;
    }

    /**
     * Creates and executes a SQL update statement based on passed-in parameters.
     *
     * Incoming values need to have been escaped already (since we can't tell
     * here whether data came from form input or somewhere else). Fields and
     * values can be passed as a single associative array, or two separate
     * arrays.
     *
     * @static
     * @access public
     * @param string $table the table to query
     * @param string $where a SQL where clause (not including the WHERE keyword)
     * @param array $assoc an optional associative array of field and values to update
     * @param array $fields an optional array of field names (required if $assoc is null)
     * @param array $values an optional array of values to set (required if $fields is set)
     * @return mixed passes along the return value of the $wpdb call
     */
    function update($table, $where, $assoc = null, $fields = null, $values = null) {
        global $wpdb;
        $sql = "UPDATE $table SET";

        if ($assoc) {
            foreach ($assoc as $k=>$v) {
                $sql .= " $k = '$v',";
            }
        }

        else {
            for ($i = 0; $i < count($fields); $i++) {
                $sql .= $fields[$i] . " = '" . $values[$i] . "',";
            }
        }

        $sql = substr("$sql", 0, -1);
        $sql .= " WHERE $where";
        return $wpdb->query($sql);
    }

    /**
     * Creates and executes a SQL insert statement based on passed-in parameters.
     *
     * Incoming values need to have been escaped already (since we can't tell
     * here whether data came from form input or somewhere else). Fields and
     * values can be passed as a single associative array, or two separate
     * arrays.
     *
     * @static
     * @access public
     * @param string $table the table to query
     * @param array $assoc an optional associative array of field and values to insert
     * @param array $fields an optional array of field names (required if $assoc is null)
     * @param array $values an optional array of values to insert (required if $fields is set)
     * @return mixed passes along the return value of the $wpdb call
     */
    function insert($table, $assoc = null, $fields = null, $values = null, $update = null) {
        global $wpdb;
        $sql = "INSERT INTO $table (";

        if ($assoc) {
            $fields = array_keys($assoc);
            $values = array_values($assoc);
        }

        $sql .= implode(",", $fields);
        $sql .= ") VALUES ('";
        $sql .= implode("','", $values);
        $sql .= "')";

        // right now works with $assoc only
        if ($update) {
            $sql .= " ON DUPLICATE KEY UPDATE $update = '" . $assoc[$update] . "'";
        }

        $sql .= ";";

        return $wpdb->query($sql);
    }

    /**
     * Creates and executes a SQL delete statement based on passed-in parameters.
     *
     * @static
     * @access public
     * @param string $table the table to query
     * @param string $where a SQL where clause (not including the WHERE keyword)
     * @return mixed passes along the return value of the $wpdb call
     */
    function delete($table, $where) {
        global $wpdb;
        $sql = "DELETE FROM $table WHERE ";
        $sql .= $where;
        return $wpdb->query($sql);
    }

    /**
     * Generates and echoes xhtml for form inputs.
     *
     * Checkbox groups are currently hardcoded to a 3 column display.
     *
     * @static
     * @access public
     * @param string $inputName (required) the name to use for the input field
     * @param array $refData (required) contains data about how the input field should be set up
     * @param string $inputValue (optional) a value to apply to the input
     * @param string $arrayValue (optional) to make an input field a PHP array (pass an empty string for an indexed array)
     */
    function displayInput($inputName, $refData, $inputValue = null, $arrayValue = null) {
        if ($arrayValue !== null) {
            $inputName .= "[$arrayValue]";
        }

        if ($refData['inputType'] == 'text') {
            echo '<input type="text" name="' . $inputName
                . '" id="' . $inputName
                . '" value="' . htmlspecialchars($inputValue)
                . '" size="' . $refData['inputSize'] . '"';

            if (strlen($refData['colParams']['length'])) {
                echo ' maxlength="' . $refData['colParams']['length'] . '"';
            }

            echo " />\n";
        }

        elseif ($refData['inputType'] == 'radio') {
            foreach ($refData['inputSubgroup'] as $value=>$label) {
                echo '<input type="radio" name="' . $inputName
                    . '" id="' . $inputName
                    . '" value="' . htmlspecialchars($value) . '"';

                if ($inputValue == $value) {
                    echo ' checked="checked"';
                }

                echo ' /> ' . $label . "\n";
            }
        }

        elseif ($refData['inputType'] == 'select') {
            echo '<select name="' . $inputName . '" id="' . $inputName . '">' . "\n";

            foreach ($refData['inputSubgroup'] as $value=>$label) {
                echo '<option value="' . htmlspecialchars($value) . '"';

                if ($inputValue == $value) {
                    echo ' selected="selected"';
                }

                echo '>' . $label . "</option>\n";
            }

            echo "</select>\n";
        }

        elseif ($refData['inputType'] == 'textarea') {
            echo '<textarea name="' . $inputName . '" cols="30" rows="5">'
                . htmlspecialchars($inputValue) . '</textarea>';
        }

        elseif ($refData['inputType'] == 'checkbox') {
            $cbCount = 1;
            echo '<table border="0" cellspacing="3" cellpadding="3">' . "\n";
            foreach ($refData['inputSubgroup'] as $value=>$label) {
                $setLastTR = FALSE;
                if ($cbCount % 3 == 1) {
                    echo "<tr>\n";
                }
                echo '<td><input type="checkbox" name="' . $inputName
                    . '[]" value="' . htmlspecialchars($value) . '"';

                if (isset($object->$key) && in_array($value, $inputValue)) {
                    echo ' checked="checked"';
                }

                echo ' /> ' . $label . "</td>\n";

                if ($cbCount % 3 == 0) {
                    echo "</tr>\n";
                    $setLastTR = TRUE;
                }
                $cbCount++;
            }

            if ($setLastTR == FALSE) {
                echo "</tr>\n";
            }

            echo "</table>\n";
        }
    }

    /**
     * Reads an RSS feed. Uses the included ToppaXMLParser.
     *
     * @static
     * @access public
     * @param string $feedURL the feed to parse
     * @param boolean $cache whether or not to cache the feed
     * @return array the content of the feed
     */
    function readFeed($feedURL) {
        $parser = new ToppaXMLParser();
        return $parser->parse($feedURL);
    }

    /**
     * The function is for parsing the Picasa RSS feed. It assumes an
     * array of items, with keys that may have a : dividing the names
     * of related items, and containing an array with a 'data' value
     * and a possible 'attrs' array
     *
     * @static
     * @access public
     * @param array $feedContent should be supplied by the readFeed function
     * @param array $refData maps your local data structure to the feed's structure; it can handle arrays nested two levels below $item
     * @param string $matchField allows you to limit the results of the parse to a specific item
     * @param string $matchValue the value to look for in $matchField
     * @return boolean|array the parsed contents of the feed, or false on failure
     */
    function parseFeed($feedContent, $refData, $matchField = null, $matchValue = null) {
        $allParsed = array();
        $break = false;

        # make sure there's something to parse
        if (empty($feedContent)) {
            return false;
        }

        foreach ($feedContent as $item) {
            // if there's a matchfield, that means we're parsing the user's feed
            // for all albums, and we want to return just the matching album
            if (strlen($matchField) && isset($refData[$matchField]['feedParam2'])
              && $item[$refData[$matchField]['feedParam1'] . ":" . $refData[$matchField]['feedParam2']]['data'] == $matchValue) {
                $break = true;
            }

            elseif (strlen($matchField) && $item[$refData[$matchField]['feedParam1']]['data'] == $matchValue) {
                $break = true;
            }

            $parsed = array();
            foreach ($refData as $refK=>$refV) {
                if ($refV['source'] == 'feed') {
                    if (isset($refV['feedParam2'])) {
                        // if attrs is set, then we're looking for a particular value in the attrs array
                        // otherwise assume we're getting a string from 'data'
                        if (isset($refV['attrs'])) {
                            $parsed[$refK] = addslashes($item[$refV['feedParam1'] . ":" . $refV['feedParam2']]['attrs'][$refV['attrs']]);
                        }

                        else {
                            $parsed[$refK] = addslashes($item[$refV['feedParam1'] . ":" . $refV['feedParam2']]['data']);
                        }
                    }

                    else {
                        if (isset($refV['attrs'])) {
                            $parsed[$refK] = addslashes($item[$refV['feedParam1']]['attrs'][$refV['attrs']]);
                        }

                        else {
                            $parsed[$refK] = addslashes($item[$refV['feedParam1']]['data']);
                        }
                    }
                }
            }

            if ($break === true) {
                return $parsed;
            }

            $allParsed[] = $parsed;
        }

        return $allParsed;
    }
}

?>
