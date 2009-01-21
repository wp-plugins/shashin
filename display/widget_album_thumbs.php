<?php
/**
 * Displays the widget control form for the album thumbnails widget.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
?>

<dl class="shashin_widget">
<dt><label for="shashin_widget_album_thumbs_title"><?php _e("Title"); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[title]', $title_data, $shashin_options['widget_album_thumbs']['title']); ?></dd>

<dt>&nbsp;</dt>
<dd>If you want to display all your albums, indicate how to order them: options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering).
<strong>- OR -</strong> If you want only certain albums, enter their album keys instead, for example '12|22|14|17'</dd>

<dt><label for="shashin_widget_album_thumbs_album_key"><?php _e("Albums"); ?> -<br /><?php _e("Keys or Sort"); ?>:</label></dt>
<dd><?php $keys_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[album_key]', $keys_data, $shashin_options['widget_album_thumbs']['album_key']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_max_cols"><?php _e("Max # of Columns"); ?>:</label></dt>
<dd><?php $cols_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[max_cols]', $cols_data, $shashin_options['widget_album_thumbs']['max_cols']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_location_yn"><?php _e("Show Location"); ?>:</label></dt>
<dd><?php $location_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO));
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[location_yn]', $location_data, $shashin_options['widget_album_thumbs']['location_yn']); ?></dd>
<dt><label for="shashin_widget_album_thumbs_pubdate_yn"><?php _e("Show Pub. Date"); ?>:</label></dt>
<dd><?php $pubdate_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO));
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[pubdate_yn]', $pubdate_data, $shashin_options['widget_album_thumbs']['pubdate_yn']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_float"><?php _e("CSS Float"); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[float]', $float_data, $shashin_options['widget_album_thumbs']['float']); ?></dd>

<dt><label for="shashin_widget_album_thumbs_clear"><?php _e("CSS Clear"); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_album_thumbs[clear]', $clear_data, $shashin_options['widget_album_thumbs']['clear']); ?></dd>
</dl>
<br clear="all" />
