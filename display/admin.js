/**
 * Accepts variables passed in from Shashin, and customizes the display of Highslide.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 */


jQuery(document).ready(function($) {
/*
    $j("#shashin_main_toggle").toggle(
        function() {
$j('#shashin_main_expander').attr('src','http://localhost/wordpress/wp-content/plugins/shashin/display/images/plus.gif');
            $j('#shashin_main').hide('slow');
        }, function () {
            $j('#shashin_main_expander').attr('src','http://localhost/wordpress/wp-content/plugins/shashin/display/images/minus.gif');
            $j('#shashin_main').show('slow');
    });
*/

    $('#shashin_main').shashin_toggle('shashin_main');

    $.fn.shashin_toggle = function(base_name) {
        if ($('#' + base_name + '_section').is(':visible')) {
            $('#' + base_name + '_button').attr('src','http://localhost/wordpress/wp-content/plugins/shashin/display/images/plus.gif');
            $('#' + base_name + '_section').hide('slow');
        }
    }
});


