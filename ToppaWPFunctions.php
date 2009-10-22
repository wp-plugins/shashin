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
}
?>
