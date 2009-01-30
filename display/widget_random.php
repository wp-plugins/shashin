<?php
/**
 * Displays the widget control form for the random photos widget.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3.3
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
?>

<dl class="shashin_widget">
<dt><label for="shashin_widget_random_title"><?php _e("Title"); ?>:</label></dt>
<dd><?php $title_data = array('input_type' => 'text', 'input_size' => 25);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[title]', $title_data, $shashin_options['widget_random']['title']); ?></dd>

<dt><label for="shashin_widget_random_album_key"><?php _e("Album"); ?>:</label></dt>
<dd><?php $key_data = array('input_type' => 'select', 'input_subgroup' => $args[0]);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[album_key]', $key_data, $shashin_options['widget_random']['album_key']); ?></dd>

<dt><label for="shashin_widget_random_max_size"><?php _e("Image Size"); ?>:</label></dt>
<dd><?php $size_data = array('input_type' => 'select', 'input_subgroup' => $sizes);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[max_size]', $size_data, $shashin_options['widget_random']['max_size']); ?></dd>

<dt><label for="shashin_widget_random_max_cols"><?php _e("Max Cols"); ?>:</label></dt>
<dd><?php $cols_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[max_cols]', $cols_data, $shashin_options['widget_random']['max_cols']); ?></dd>

<dt><label for="shashin_widget_random_how_many"><?php _e("# of Images"); ?>:</label></dt>
<dd><?php $count_data = array('input_type' => 'text', 'input_size' => 5);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[how_many]', $count_data, $shashin_options['widget_random']['how_many']); ?></dd>

<dt><label for="shashin_widget_random_caption_yn"><?php _e("Show Captions"); ?>:</label></dt>
<dd><?php $caption_data = array('input_type' => 'radio', 'input_subgroup' => array('y' => __("Yes", SHASHIN_L10N_NAME), 'n' => __("No", SHASHIN_L10N_NAME)));
    echo ToppaWPFunctions::displayInput('shashin_widget_random[caption_yn]', $caption_data, $shashin_options['widget_random']['caption_yn']); ?></dd>

<dt><label for="shashin_widget_random_float"><?php _e("CSS Float"); ?>:</label></dt>
<dd><?php $float_data = array('input_type' => 'select', 'input_subgroup' => $float_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[float]', $float_data, $shashin_options['widget_random']['float']); ?></dd>

<dt><label for="shashin_widget_random_clear"><?php _e("CSS Clear"); ?>:</label></dt>
<dd><?php $clear_data = array('input_type' => 'select', 'input_subgroup' => $clear_options);
    echo ToppaWPFunctions::displayInput('shashin_widget_random[clear]', $clear_data, $shashin_options['widget_random']['clear']); ?></dd>
</dl>
<br clear="all" />

