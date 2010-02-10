<?php
/**
 * Displays the widget control form for the single photo widget.
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
<dt><label for="shashin_widget_single_title"><?php _e("Title", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_single[title]', $title_data, $shashin_options['widget_single']['title']); ?></dd>

<dt><label for="shashin_widget_single_photo_key"><?php _e("Photo Key", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $key_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_single[photo_key]', $key_data, $shashin_options['widget_single']['photo_key']); ?></dd>

<dt><label for="shashin_widget_single_max_size"><?php _e("Image Size", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $size_data = array('input_type' => 'select', 'input_subgroup' => $sizes);
    echo ToppaWPFunctions::displayInput('shashin_widget_single[max_size]', $size_data, $shashin_options['widget_single']['max_size']); ?></dd>

<dt><label for="shashin_widget_single_caption_yn"><?php _e("Show Captions", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $caption_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => __("Yes", SHASHIN_L10N_NAME), 'n' => __("No", SHASHIN_L10N_NAME)));
    echo ToppaWPFunctions::displayInput('shashin_widget_single[caption_yn]', $caption_data, $shashin_options['widget_single']['caption_yn']); ?></dd>

<dt><label for="shashin_widget_single_float"><?php _e("CSS Float/Align", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_single[float]', $float_data, $shashin_options['widget_single']['float']); ?></dd>

<dt><label for="shashin_widget_single_clear"><?php _e("CSS Clear", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_single[clear]', $clear_data, $shashin_options['widget_single']['clear']); ?></dd>
</dl>
<br clear="all" />

