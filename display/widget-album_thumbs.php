<?php
/**
 * Displays the widget control form for the album thumbnails widget. 
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.1
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>
 
<div>
<dl class="shashin_widget">
<dt><label for="shashin_album_thumbs_title">Title:</label></dt>
<dd><?php
    $titleData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_album_thumbs_title', $titleData, $options['shashin_album_thumbs_title']);
?></dd>

<dt>&nbsp;</dt>
<dd>If you want to display all your albums, indicate how to order them: options are 'pub_date',
'title', or 'last_updated' (add ' desc' for reverse ordering).
<strong>- OR -</strong> If you want only certain albums, enter their album keys instead, for example '12|22|14|17'</dd>

<dt><label for="shashin_album_thumbs_photo_keys">Albums -<br />Keys or Sort:</label></dt>
<dd><?php
    $keysData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_album_thumbs_keys', $keysData, $options['shashin_album_thumbs_keys']);
?></dd>

<dt><label for="shashin_album_thumbs_max_cols">Max # of Columns:</label></dt>
<dd><?php
    $colsData = array('inputType' => 'text', 'inputSize' => 5);
    echo ToppaWPFunctions::displayInput('shashin_album_thumbs_max_cols', $colsData, $options['shashin_album_thumbs_max_cols']);
?></dd>

<dt><label for="shashin_album_thumbs_location_yn">Show Location:</label></dt>
<dd><?php
    $locationData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_album_thumbs_location_yn', $locationData, $options['shashin_album_thumbs_location_yn']);
?></dd>
<dt><label for="shashin_album_thumbs_pubdate_yn">Show Pub. Date:</label></dt>
<dd><?php
    $pubdateData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_album_thumbs_pubdate_yn', $pubdateData, $options['shashin_album_thumbs_pubdate_yn']);
?></dd>

<dt>&nbsp;</dt>
<dd><input type="submit" name="shashin_album_thumbs_submit" id="shashin_album_thumbs_submit" value="Submit" /></dd>
</dl>
</div>

