<?php
/**
 * Display informational or error message in the Shashin admin panels.
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
 *
 * @author Michael Toppa
 * @version 1.0.3
 * @package Shashin
 * @subpackage AdminPanels
 *
 */
 
echo '<div id="message" class="updated fade"><p>' . $message .'</p></div>';
unset($message);

if ($dbError == true) {
    global $wpdb;
    $wpdb->print_error();
    $dbError = false;
}

?>
