<?php
/**
 * ShashinAlbum class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.4
 * @package Shashin
 * @subpackage Classes
 */

/**
 * Instantiate this class and use its methods to manipulate Picasa albums in
 * Shashin. Also has a static population method, getAlbums() and a static
 * method for generating markup, getAlbumMarkup().
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinAlbum {
    public $ref_data = = array(
        'album_key' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true, 'primary_key' => true,
            'other' => 'AUTO_INCREMENT'), 'label' => 'Album Key'),
        'album_id' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'Album ID'),
        'user' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'User'),
        'name' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'Name'),
        'link_url' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'Link URL'),
        'title' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'label' => 'Title'),
        'description' => array('col_params' => array('type' => 'text'),
            'label' => 'Description'),
        'location' => array('col_params' => array('type' => 'varchar',
            'length' => '255'), 'label' => 'Location'),
        'cover_photo_url' => array('col_params' => array('type' => 'varchar',
            'length' => '255'), 'label' => 'Cover Photo URL'),
        'last_updated' => array('col_params' => array('type' => 'int unsigned'),
            'label' => 'Last Updated'),
        'photo_count' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'label' => 'Photo Count'),
        'pub_date' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'label' => 'Pub Date', 'source' => 'feed'),
        'geo_pos' => array('col_params' => array('type' => 'varchar',
            'length' => '25'), 'label' => 'Pub Date'),
        'include_in_random' => array('col_params' => array('type' => 'char',
            'length' => '1', 'other' => "default 'Y'"),
            'label' => 'Include in random photo display',
            'input_type' => 'radio',
            'input_subgroup' => array('Y' => __('Yes', 'shashin'), 'N' => __('No', 'shashin'))),
        );

    public $data;

    /**
     * The constructor sets $this->ref_data, which is used for creating the
     * shashin_album table, for mapping Picasa RSS feed params to table
     * field names, and for generating form input fields.
     *
     * @access public
     */
    function __construct() {
    }
}
?>
