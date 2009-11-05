/**
 * Accepts variables passed in from Shashin, and customizes the display of Highslide.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 */

var $j = jQuery.noConflict();

$j(document).ready(function() {
    $j('a#shashin_main_toggle').click(function() {
        $j('#shashin_main').toggle(400);
        return false;
    });
});


