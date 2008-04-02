<?php
/**
 * Displays the widget control form for the album thumbnail widget. 
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.0.4
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>
 
<div>
<dl class="shashin_widget">
<dt><label for="shashin_album_title">Title:</label></dt>
<dd><?php
    $titleData = array('inputType' => 'text', 'inputSize' => 25);
    echo ToppaWPFunctions::displayInput('shashin_album_title', $titleData, $options['shashin_album_title']);
?></dd>

<dt><label for="shashin_album_album_key">Album:</label></dt>
<dd><?php
    $keyData = array('inputType' => 'select', 'inputSubgroup' => $args[0]);
    echo ToppaWPFunctions::displayInput('shashin_album_album_key', $keyData, $options['shashin_album_album_key']);
?></dd>
<dt><label for="shashin_album_location_yn">Show Location:</label></dt>
<dd><?php
    $locationData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_album_location_yn', $locationData, $options['shashin_album_location_yn']);
?></dd>
<dt><label for="shashin_album_pubdate_yn">Show Pub. Date:</label></dt>
<dd><?php
    $pubdateData = array('inputType' => 'radio', 'inputSubgroup' => array('y' => 'Yes', 'n' => 'No'));
    echo ToppaWPFunctions::displayInput('shashin_album_pubdate_yn', $pubdateData, $options['shashin_album_pubdate_yn']);
?></dd>

<dt>&nbsp;</dt>
<dd><input type="submit" name="shashin_album_submit" id="shashin_album_submit" value="Submit" /></dd>
</dl>
</div>

