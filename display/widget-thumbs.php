<?php
/**
 * Displays the widget control form for the thumbnails widget. 
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.2
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>

 
<div>
<dl class="shashin_widget">
<dt><label for="shashin_thumbs_title">Title:</label></dt>
<dd><?php
    $titleData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_thumbs_title', $titleData, $options['shashin_thumbs_title']);
?></dd>

<dt><label for="shashin_thumbs_photo_keys">Photo Keys:</label></dt>
<dd><?php
    $keysData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_thumbs_photo_keys', $keysData, $options['shashin_thumbs_photo_keys']);
?><br /><em>example: 12|22|14|17</em></dd>

<dt><label for="shashin_thumbs_max_size">Image Size:</label></dt>
<dd><?php
    $sizeData = array('inputType' => 'text', 'inputSize' => 5);
    echo ToppaWPFunctions::displayInput('shashin_thumbs_max_size', $sizeData, $options['shashin_thumbs_max_size']);
?></dd>


<dt><label for="shashin_thumbs_max_cols">Max Cols:</label></dt>
<dd><?php
    $colsData = array('inputType' => 'text', 'inputSize' => 5);
    echo ToppaWPFunctions::displayInput('shashin_thumbs_max_cols', $colsData, $options['shashin_thumbs_max_cols']);
?></dd>

<dt><label for="shashin_thumbs_caption_yn">Show Captions:</label></dt>
<dd><?php
    $captionData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_thumbs_caption_yn', $captionData, $options['shashin_thumbs_caption_yn']);
?></dd>

<dt>&nbsp;</dt>
<dd><input type="submit" name="shashin_thumbs_submit" id="shashin_thumbs_submit" value="Submit" /></dd>
</dl>
</div>

