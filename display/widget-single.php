<?php
/**
 * Displays the widget control form for the single photo widget. 
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.0
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>

 
<div>
<dl class="shashin_widget">
<dt><label for="shashin_random_title">Title:</label></dt>
<dd><?php
    $titleData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_single_title', $titleData, $options['shashin_single_title']);
?></dd>

<dt><label for="shashin_random_album_key">Photo Key:</label></dt>
<dd<?php
    $keyData = array('inputType' => 'text', 'inputSize' => 5);
    echo ToppaWPFunctions::displayInput('shashin_single_photo_key', $keyData, $options['shashin_single_photo_key']);
?></dd>
<dt><label for="shashin_random_max_size">Image Size:</label></dt>
<dd><?php
    $sizes = array();
    foreach (eval(SHASHIN_IMAGE_SIZES) as $imageSize) {
        if (in_array($imageSize, eval(SHASHIN_CROP_SIZES))) {
            $sizes[$imageSize] = "$imageSize (cropped square)";
        }
        
        else {
            $sizes[$imageSize] = $imageSize;
        }
    }
    
    $sizeData = array('inputType' => 'select', 'inputSubgroup' => $sizes);
    echo ToppaWPFunctions::displayInput('shashin_single_max_size', $sizeData, $options['shashin_single_max_size']);
?></dd>

<dt><label for="shashin_random_caption_yn">Show Captions:</label></dt>
<dd><?php
    $captionData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_single_caption_yn', $captionData, $options['shashin_single_caption_yn']);
?></dd>

<dt>&nbsp;</dt>
<dd><input type="submit" name="shashin_single_submit" id="shashin_single_submit" value="Submit" /></dd>
</dl>
</div>

