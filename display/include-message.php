<?php
/**
 * Display informational or error message in the Shashin admin panels.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage AdminPanels
 *
 */

echo '<div id="message" class="updated fade"><p>' . $message .'</p></div>';
unset($message);

if ($db_error == true) {
    global $wpdb;
    $wpdb->print_error();
    $db_error = false;
}

?>
