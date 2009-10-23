<?php
/**
 * ShashinPhoto class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage Classes
 */

/**
 * For manipulating and displaying Picasa photos in Shashin.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinPhoto {
    public $ref_data = array(
        'photo_key' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true, 'primary_key' => true,
            'other' => 'AUTO_INCREMENT'), 'label' => 'Photo Key'),
        'photo_id' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true, 'unique_key' => true),
            'label' => 'Photo ID'),
        'album_id' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'Album ID'),
        'title' => array(
            'col_params' => array('type' => 'varchar', 'length' => '255'),
            'label' => 'Title'),
        'description' => array('col_params' => array('type' => 'text'),
            'label' => 'Description'),
        'link_url' => array('col_params' => array('type' => 'text',
            'not_null' => true), 'label' => 'Link URL'),
        'content_url' => array('col_params' => array('type' => 'text',
            'not_null' => true), 'label' => 'Content URL'),
        'width' => array('col_params' => array('type' => 'smallint unsigned',
            'not_null' => true), 'label' => 'Width'),
        'height' => array('col_params' => array('type' => 'smallint unsigned',
            'not_null' => true), 'label' => 'Height'),
        'taken_timestamp' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'label' => 'Date Taken'),
        'uploaded_timestamp' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'label' => 'Date Uploaded'),
        'tags' => array('col_params' => array('type' => 'text'),
            'label' => 'Tags'),
        'include_in_random' => array('col_params' => array('type' => 'char',
            'length' => '1', 'other' => "default 'Y'"),
            'label' => 'Include in random photo display',
            'input_type' => 'radio',
            'input_subgroup' => array('Y' => 'Yes', 'N' => 'No')),
        'deleted' => array('col_params' => array('type' => 'char',
            'length' => '1', 'other' => "default 'N'"), 'label' => 'Deleted'),
        'enclosure_url' => array('col_params' => array('type' => 'text',
            'not_null' => true), 'label' => 'Enclosure URL'),
        'enclosure_type' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true),
            'label' => 'Enclosure Type'),
        'picasa_order' => array('col_params' => array('type' => 'int unsigned'),
            'label' => 'Assigned Order'),
        'fstop' => array('col_params' => array('type' => 'varchar',
            'length' => '10'), 'label' => 'F-Stop'),
        'make' => array('col_params' => array('type' => 'varchar',
            'length' => '20'), 'label' => 'Make'),
        'model' => array('col_params' => array('type' => 'varchar',
            'length' => '20'), 'label' => 'Model'),
        'exposure' => array('col_params' => array('type' => 'varchar',
            'length' => '10'), 'label' => 'Model'),
        'focal_length' => array('col_params' => array('type' => 'varchar',
            'length' => '10'), 'label' => 'Focal Length'),
        'iso' => array('col_params' => array('type' => 'varchar',
            'length' => '10'), 'label' => 'ISO'),
        );

    public $data;
    public $shashin;

    function __construct(&$shashin) {
        $this->shashin = &$shashin;
    }
}

?>
