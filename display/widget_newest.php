<?php
/**
 * Displays the widget control form for the newest photos widget.
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
<dt><label for="shashin_widget_newest_title"><?php _e("Title", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[title]', $title_data, $shashin_options['widget_newest']['title']); ?></dd>

<dt><label for="shashin_widget_newest_album_key"><?php _e("Album", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $key_data = array('input_type' => 'select', 'input_subgroup' => $args[0]);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[album_key]', $key_data, $shashin_options['widget_newest']['album_key']); ?></dd>

<dt><label for="shashin_widget_newest_max_size"><?php _e("Image Size", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $size_data = array('input_type' => 'select', 'input_subgroup' => $sizes);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[max_size]', $size_data, $shashin_options['widget_newest']['max_size']); ?></dd>

<dt><label for="shashin_widget_newest_max_cols"><?php _e("Max No. of Columns", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $cols_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[max_cols]', $cols_data, $shashin_options['widget_newest']['max_cols']); ?></dd>

<dt><label for="shashin_widget_newest_how_many"><?php _e("No. of Images", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $count_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[how_many]', $count_data, $shashin_options['widget_newest']['how_many']); ?></dd>

<dt><label for="shashin_widget_newest_caption_yn"><?php _e("Show Captions", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $caption_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => __("Yes", SHASHIN_L10N_NAME), 'n' => __("No", SHASHIN_L10N_NAME)));
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[caption_yn]', $caption_data, $shashin_options['widget_newest']['caption_yn']); ?></dd>

<dt><label for="shashin_widget_newest_float"><?php _e("CSS Float/Align", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[float]', $float_data, $shashin_options['widget_newest']['float']); ?></dd>

<dt><label for="shashin_widget_newest_clear"><?php _e("CSS Clear", SHASHIN_L10N_NAME); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_newest[clear]', $clear_data, $shashin_options['widget_newest']['clear']); ?></dd>
</dl>
<br clear="all" />
