<?php
/**
 * Displays the widget control form for the album thumbnails widget.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
?>

<dl class="shashin_widget">
<dt><label for="shashin_widget_album_thumbs_title"><?php _e("Title", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[title]', $title_data, $shashin_options['widget_album_thumbs']['title']); ?></dd>

<dt>&nbsp;</dt>
<dd><?php _e("If you want to display all your albums, indicate how to order them: options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering).", SHASHIN_L10N_NAME); ?>
<?php _e("<strong>- OR -</strong> If you want only certain albums, enter their album keys instead, for example '12|22|14|17'", SHASHIN_L10N_NAME); ?></dd>

<dt><label for="shashin_widget_album_thumbs_album_key"><?php _e("Albums", SHASHIN_L10N_NAME); ?> -<br /><?php _e("Keys or Sort"); ?>:</label></dt>
<dd><?php $keys_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[album_key]', $keys_data, $shashin_options['widget_album_thumbs']['album_key']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_max_cols"><?php _e("Max No. of Columns", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $cols_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[max_cols]', $cols_data, $shashin_options['widget_album_thumbs']['max_cols']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_location_yn"><?php _e("Show Location", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $location_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => __("Yes", SHASHIN_L10N_NAME), 'n' => __("No", SHASHIN_L10N_NAME)));
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[location_yn]', $location_data, $shashin_options['widget_album_thumbs']['location_yn']); ?></dd>
<dt><label for="shashin_widget_album_thumbs_pubdate_yn"><?php _e("Show Pub. Date", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $pubdate_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => __("Yes", SHASHIN_L10N_NAME), 'n' => __("No", SHASHIN_L10N_NAME)));
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[pubdate_yn]', $pubdate_data, $shashin_options['widget_album_thumbs']['pubdate_yn']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_float"><?php _e("CSS Float/Align", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[float]', $float_data, $shashin_options['widget_album_thumbs']['float']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_clear"><?php _e("CSS Clear", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[clear]', $clear_data, $shashin_options['widget_album_thumbs']['clear']); ?></dd>
</dl>
<br clear="all" />
