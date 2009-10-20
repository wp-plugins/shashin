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
    public $ref_data;
    public $data;

    /**
     * The constructor sets $this->ref_data, which is used for creating the
     * shashin_album table, for mapping Picasa RSS feed params to table
     * field names, and for generating form input fields.
     *
     * @access public
     */
    function __construct() {
        // link_url is set from the feed but we give it input params since
        // we initially gather it from user input
        $this->ref_data = array(
            'album_key' => array(
                'col_params' => array('type' => 'int unsigned', 'not_null' => true,
                    'primary_key' => true, 'other' => 'AUTO_INCREMENT'),
                'label' => 'Album Key', 'source' => 'db'),
            'album_id' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'Album ID', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'id'),
            'user' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'User', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'user'),
            'name' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'Name', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'name'),
            'link_url' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'Link URL', 'source' => 'feed',
                'feed_param_1' => 'link',
                'input_type' => 'text', 'input_size' => '40'),
            'title' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'Title', 'source' => 'feed',
                'feed_param_1' => 'title'),
            'description' => array(
                'col_params' => array('type' => 'text'),
                'label' => 'Description', 'source' => 'feed',
                'feed_param_1' => 'description'),
            'location' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Location', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'location'),
            'cover_photo_url' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Cover Photo URL', 'source' => 'feed',
                'feed_param_1' => 'media', 'feed_param_2' => 'thumbnail', 'attrs' => 'url'),
            'last_updated' => array(
                'col_params' => array('type' => 'int unsigned'),
                'label' => 'Last Updated', 'source' => 'user'),
            'photo_count' => array(
                'col_params' => array('type' => 'int unsigned',  'not_null' => true),
                'label' => 'Photo Count', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'numphotos'),
            'pub_date' => array(
                'col_params' => array('type' => 'int unsigned', 'not_null' => true),
                'label' => 'Pub Date', 'source' => 'feed',
                'feed_param_1' => 'pubDate'),
            'geo_pos' => array(
                'col_params' => array('type' => 'varchar', 'length' => '25'),
                'label' => 'Pub Date', 'source' => 'feed',
                'feed_param_1' => 'gml', 'feed_param_2' => 'pos'),
            'include_in_random' => array(
                'col_params' => array('type' => 'char', 'length' => '1', 'other' => "default 'Y'"),
                'label' => 'Include in random photo display', 'source' => 'user',
                'input_type' => 'radio',
                'input_subgroup' => array('Y' => __('Yes', 'shashin'), 'N' => __('No', 'shashin'))),
        );
    }
}
?>
