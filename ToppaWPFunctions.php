<?php
/**
 * ToppaWPFunctions class file.
 *
 * @author Michael Toppa
 * @version 0.6
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007 Michael Toppa
 * 
 * This file is part of Shashin.
 *
 * Shashin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shashin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
        $sql = "CREATE TABLE $tableName\n(";
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
            
            $sql .= ",\n";
        }

        $sql = substr("$sql", 0, -2); 
        $sql .= ");";

        require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
        $return = dbDelta($sql);
    }

    /**
     * Creates and executes a SQL select statement based on passed-in parameters.
     *
     * @static
     * @access public
     * @param string $table the table to query
     * @param string|array $fields the fields to return
     * @param string $conditions a string containing any desired WHERE, ORDER BY, etc clauses
     * @param string $type the type of select query to run (supports get_results, get_col, get_var, and get_row)
     * @param string $return how to format the return value (defaults to ARRAY_A)
     * @return mixed passes along the return value of the $wpdb call
     */   
    function select($table, $fields = '*', $conditions, $type = null, $return = ARRAY_A) {
        global $wpdb;
        $sql = "SELECT ";
        
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
    function insert($table, $assoc = null, $fields = null, $values = null) {
        global $wpdb;
        $sql = "INSERT INTO $table (";

        if ($assoc) {
            $fields = array_keys($assoc);
            $values = array_values($assoc);
        }
        
        $sql .= implode(",", $fields);
        $sql .= ") VALUES ('";
        $sql .= implode("','", $values);
        $sql .= "');";
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
                . '" value="' . htmlspecialchars($inputValue)
                . '" size="' . $refData['inputSize'] . '"';
            
            if (strlen($refData['colParams']['length'])) {
                echo ' maxlength="' . $refData['colParams']['length'] . '"';    
            }
            
            echo ' />';
        }
        
        elseif ($refData['inputType'] == 'radio') {
            foreach ($refData['inputSubgroup'] as $value=>$label) {
                echo '<input type="radio" name="' . $inputName
                    . '" value="' . htmlspecialchars($value) . '"';
                
                if ($inputValue == $value) {
                    echo ' checked="checked"';
                }
                
                echo ' /> ' . $label . ' ';
            }
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
     * Reads an RSS feed. Uses the included rss_functions-mod.php, which has
     * been modified to properly parse Picasa feeds. Sets output to UTF-8.
     *
     * @static
     * @access public
     * @param string $feedURL the feed to parse
     * @param boolean $cache whether or not to cache the feed
     * @return array the content of the feed
     */   
    function readFeed($feedURL, $cache = true) {
        require_once(SHASHIN_DIR . '/rss-functions-mod.php');
        #require_once(ABSPATH . WPINC . '/rss-functions.php');
        
        if ($cache === false) {
            define('MAGPIE_CACHE_ON', null);
        }
        
        $feedContent = @fetch_rss_mod($feedURL);
        return $feedContent;
    }

    /**
     * The function is intended to be a fairly generic feed parser, but so far
     * I've only used it with Picasa.
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
        $doLoop = false;
        $break = false;
        
        foreach ($feedContent->items as $item) {
            // if there's a matchfield, that means we're parsing the user's feed
            // for all albums, and we want to return just the matching album 
            if (strlen($matchField) && isset($refData[$matchField]['feedParam2'])
              && $item[$refData[$matchField]['feedParam1']][$refData[$matchField]['feedParam2']] == $matchValue) {
                $doLoop = true;
                $break = true;
            }
                
            elseif (strlen($matchField) && $item[$refData[$matchField]['feedParam1']] == $matchValue) {
                $doLoop = true;
                $break = true;
            }

            // if there's no album id passed in, then we're parsing a photo feed
            // (i.e. the album is already known, based on the feed URL) and we
            // want to get all the photos.
            elseif ($albumName == null) {
                $doLoop = true; 
            }

            if ($doLoop === false) {
                return false;
            }
            
            $parsed = array();
            foreach ($refData as $refK=>$refV) {
                if ($refV['source'] == 'feed') {
                    if (isset($refV['feedParam2'])) {
                        $parsed[$refK] = addslashes($item[$refV['feedParam1']][$refV['feedParam2']]);
                    }
                    
                    else {
                        $parsed[$refK] = addslashes($item[$refV['feedParam1']]);
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