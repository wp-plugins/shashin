<?php
/**
 * Displays the widget control form for the thumbnails widget.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3.1
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>

<dl class="shashin_widget">
<dt><label for="shashin_widget_thumbs_title"><?php _e("Title"); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[title]', $title_data,  $shashin_options['widget_thumbs']['title']); ?></dd>

<dt><label for="shashin_widget_thumbs_photo_key"><?php _e("Photo Keys"); ?>:</label></dt>
<dd><?php $keys_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[photo_key]', $keys_data, $shashin_options['widget_thumbs']['photo_key']); ?>
    <br /><em>example: 12|22|14|17</em></dd>

<dt><label for="shashin_widget_thumbs_max_size"><?php _e("Image Size"); ?>:</label></dt>
<dd><?php $size_data = array('input_type' => 'select', 'input_subgroup' => $sizes);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[max_size]', $size_data, $shashin_options['widget_thumbs']['max_size']); ?></dd>

<dt><label for="shashin_widget_thumbs_max_cols"><?php _e("Max Cols"); ?>:</label></dt>
<dd><?php $cols_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[max_cols]', $cols_data, $shashin_options['widget_thumbs']['max_cols']); ?></dd>

<dt><label for="shashin_widget_thumbs_caption_yn"><?php _e("Show Captions"); ?>:</label></dt>
<dd><?php $caption_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO));
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[caption_yn]', $caption_data, $shashin_options['widget_thumbs']['caption_yn']); ?></dd>

<dt><label for="shashin_widget_thumbs_float"><?php _e("CSS Float"); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[float]', $float_data, $shashin_options['widget_thumbs']['float']); ?></dd>

<dt><label for="shashin_widget_thumbs_clear"><?php _e("CSS Clear"); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_thumbs[clear]', $clear_data, $shashin_options['widget_thumbs']['clear']); ?></dd>
</dl>
<br clear="all" />
