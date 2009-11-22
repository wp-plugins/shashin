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
    public $ref_data = array(
        'album_key' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true, 'primary_key' => true,
            'other' => 'AUTO_INCREMENT')),
        'album_id' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'feed_params' =>
            array('picasa_1' => 'gphoto', 'picasa_2' => 'id',
            'flickr_1' => 'id', 'twitpic_1' => 'link')),
        'user' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'feed_params' =>
            array('picasa_1' => 'gphoto', 'picasa_2' => 'user')),
        'name' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'feed_params' =>
            array('picasa_1' => 'gphoto', 'picasa_2' => 'name')),
        'link_url' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'feed_params' =>
            array('picasa_1' => 'link', 'flickr_1' => 'id')),
        'title' => array('col_params' => array('type' => 'varchar',
            'length' => '255', 'not_null' => true), 'feed_params' =>
            array('picasa_1' => 'title', 'flickr_1' => 'title',
            'twitpic_1' => 'title')),
        'description' => array('col_params' => array('type' => 'text'),
            'feed_params' => array('picasa_1' => 'description',
            'flickr_1' => 'subtitle', 'twitpic_1' => 'description')),
        'location' => array('col_params' => array('type' => 'varchar',
            'length' => '255'), 'feed_params' => array('picasa_1' =>
            'media', 'picasa_2' => 'thumbnail',
            'picasa_attrs' => 'url', )),
        'cover_photo_url' => array('col_params' => array('type' => 'varchar',
            'length' => '255'), 'feed_params' => array(
            'picasa_1' => 'media', 'picasa_2' => 'thumbnail',
            'picasa_attrs' => 'url', 'flickr_1' => 'icon')),
        'last_updated' => array('col_params' => array(
            'type' => 'int unsigned')),
        'photo_count' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'feed_params' => array(
            'picasa_1' => 'gphoto', 'picasa_2' => 'numphotos')),
        'pub_date' => array('col_params' => array('type' => 'int unsigned',
            'not_null' => true), 'feed_params' => array(
            'picasa_1' => 'pubDate', 'flickr_1' => 'updated')),
        'geo_pos' => array('col_params' => array('type' => 'varchar',
            'length' => '25'), 'feed_params' => array(
            'picasa_1' => 'gml', 'picasa_2' => 'pos')),
        'include_in_random' => array('col_params' => array('type' => 'char',
            'length' => '1', 'other' => "default 'Y'"),
            'input_type' => 'radio',
            'input_subgroup' => array('Y' => 'Yes', 'N' => 'No')),
        );

    public $data;
    public $shashin;

    public function __construct(&$shashin) {
        $this->shashin = &$shashin;
    }

    /**
     * Inserts or updates a Picasa album in Shashin.
     *
     * Reads the user's Picasa feed of all albums and retieves data for the
     * specified album. Does not insert/update album photos. On success, calls
     * getAlbum() to (re)populate the album object.
     *
     * @access public
     * @param string $user_name The Picasa user_name of the album's owner
     * @param array $album_identifier A key-value pair (e.g. 'album_id' => 37)
     * @param array $local_data (optional) A hash of local album data (data not from the Picasa feed)
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ToppaWPFunctions::sqlUpdate()
     * @uses ToppaWPFunctions::sqlInsert()
     * @uses ShashinAlbum::getAlbum()
     * @return array 0: true on success, false on failure; 1: message; 2: true if SQL error
     */
    function setAlbum($rss_url, $local_data = null) {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        if (is_string($user_name)) {
            // read the feed for the user
            $feed_url = $shashin_options['picasa_server'] . SHASHIN_USER_RSS;
            $feed_url = str_replace("USERNAME", $user_name, $feed_url);
            $feed_content = ToppaWPFunctions::readFeed($feed_url);
        }

        else {
            return array(false, __("ShashinAlbum::setAlbum - First argument to setAlbum is not a valid username.", SHASHIN_L10N_NAME));
        }

        if (is_array($album_identifier)) {
            $key = key($album_identifier);
            $value = current($album_identifier);
            $album_data = ToppaWPFunctions::parseFeed($feed_content, $this->ref_data, $key, $value);

            if (!$album_data) {
                return array(false, __("ShashinAlbum::setAlbum - Failed to parse album feed.", SHASHIN_L10N_NAME));
            }

            $exists = $this->getAlbum($album_identifier);

            if ($exists[0] === false) {
                return $exists;
            }

            $album_data[$key] = $value;
        }

        else {
            return array(false, __("ShashinAlbum::setAlbum - Second argument to setAlbum is not a valid album identifier.", SHASHIN_L10N_NAME));
        }

        // update local data
        if (is_array($local_data)) {
            foreach ($local_data as $k=>$v) {
                $album_data[$k] = $v;
            }
        }

        // time-related updates
        $album_data['pub_date'] = strtotime($album_data['pub_date']);
        $album_data['last_updated'] = time();

        // if the album exists, update it
        if ($exists[0] === true) {
            $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_ALBUM_TABLE, $album_data, $album_identifier);
        }

        // if the album does not exist, insert it
        else {
            $sql_result = ToppaWPFunctions::sqlInsert(SHASHIN_ALBUM_TABLE, $album_data);
        }

        if ($sql_result === false) {
            return array(false, __("ShashinAlbum::setAlbum - Failed to insert/update database record for album. SQL Error:", SHASHIN_L10N_NAME), true);
        }

        // update the object data
        $this->data = $album_data;

        return array(true, __("Album metadata synchronized", SHASHIN_L10N_NAME));
    }

}
?>
