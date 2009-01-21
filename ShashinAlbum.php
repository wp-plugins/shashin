<?php
/**
 * ShashinAlbum class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3
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
    var $ref_data;
    var $data;

    /**
     * The constructor sets $this->ref_data, which is used for creating the
     * shashin_album table, for mapping Picasa RSS feed params to table
     * field names, and for generating form input fields.
     *
     * @access public
     */
    function ShashinAlbum() {
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
                'input_subgroup' => array('Y' => SHASHIN_YES, 'N' => SHASHIN_NO)),
        );
    }

    /**
     * Populates a ShashinAlbum object based on an album indetifier (can be a
     * Picasa ID, album name, or Shashin key) or a passed-in array of album
     * data.
     *
     * @access public
     * @param array $album_identifier (optional) a key-value pair (e.g. 'album_id' => 37)
     * @param array $album_data (optional) a complete array of album data
     * @return array 0: true on success, false on SQL error, null if album not found; 1: message; 2: true if SQL error
     * @uses ToppaWPFunctions::sqlSelect
     */
    function getAlbum($album_identifier = null, $album_data = null) {
        if (is_array($album_data)) {
            $this->data = $album_data;
        }

        elseif (is_array($album_identifier)) {
            $row = ToppaWPFunctions::sqlSelect(SHASHIN_ALBUM_TABLE, '*', $album_identifier);

            if ($row === false) {
                return array(false, __("ShashinAlbum::getAlbum - Failed to retrieve album. SQL Error:", SHASHIN_L10N_NAME), true);
            }

            elseif (empty($row)) {
                return array(null, __("Album not found.", SHASHIN_L10N_NAME));
            }

            $row['description'] = htmlspecialchars($row['description'], ENT_COMPAT, 'UTF-8');
            $row['title'] = htmlspecialchars($row['title'], ENT_COMPAT, 'UTF-8');
            $this->data = $row;
        }

        else {
            return array(false, __("ShashinAlbum::getAlbum - Called with invalid arguments.", SHASHIN_L10N_NAME));
        }

        return array(true, __("Album retrieved."));
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
    function setAlbum($user_name, $album_identifier, $local_data = null) {
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

        if (!$sql_result) {
            return array(false, __("ShashinAlbum::setAlbum - Failed to insert/update database record for album. SQL Error:", SHASHIN_L10N_NAME), true);
        }

        // if the album insert/update seems ok, call getAlbum, which also
        // confirms if it really worked.
        return $this->getAlbum($album_identifier);
    }

    /**
     * Deletes an album and all its photos from Shashin. Works only if
     * getAlbum() was successfully called first.
     *
     * @access public
     * @uses ToppaWPFunctions::sqlDelete();
     * @return boolean|array true on success; array on failure (first element is message, second element is db error flag)
     */
    function deleteAlbum() {
        if (!$this->data['album_id']) {
            return array(false, __("ShashinAlbum::deleteAlbum - Album object not correctly set.", SHASHIN_L10N_NAME));
        }

        $sql_result = ToppaWPFunctions::sqlDelete(SHASHIN_PHOTO_TABLE, array('album_id' => $this->data['album_id']));

        if (!$sql_result) {
            return array(false, __("ShashinAlbum::deleteAlbum - Failed to delete photo records for album ID {$this->data['album_id']}. SQL Error:", SHASHIN_L10N_NAME), true);
        }

        $sql_result = ToppaWPFunctions::sqlDelete(SHASHIN_ALBUM_TABLE, array('album_id' => $this->data['album_id']));

        if (!$sql_result) {
            return array(false, __("ShashinAlbum::deleteAlbum - Failed to delete album record for album ID {$this->data['album_id']}. SQL Error:", SHASHIN_L10N_NAME), true);
        }

        return array(true, __("Album deleted.", SHASHIN_L10N_NAME));
    }

    /**
     * Retrieves all photos in the shashin_photo table for an album. Must call
     * getAlbum() first.
     *
     * @access public
     * @param string $order_by (optional) column name(s) to order by (default: photo_id)
     * @param integer $limit (optional) a max number of records to return (default: null)
     * @param boolean $include_deleted (optional) whether to include photos flagged as deleted (default: false)
     * @return array 0: true on success, false on failure, null if no records found; 1: message; 2: true if SQL error
     * @uses ToppaWPFunctions::sqlSelect
     */
    function getAlbumPhotos($order_by = 'photo_id', $limit = null, $include_deleted = false) {
        $conditions = array('album_id' => $this->data['album_id']);

        if ($include_deleted === false) {
            $conditions['deleted'] = 'N';
        }

        $other = "";

        if (is_string($order_by)) {
            $other = "order by $order_by";
        }

        if (is_numeric($limit)) {
            $other .= " limit $limit";
        }

        $rows = ToppaWPFunctions::sqlSelect(SHASHIN_PHOTO_TABLE, '*', $conditions, $other, 'get_results');

        if ($rows === false) {
            return array(false, __("ShashinAlbum::getAlbumPhotos - Failed to retrieve photos for Album ID {$this->data['album_id']}. Possible SQL Error:", SHASHIN_L10N_NAME), true);
        }

        else if (empty($rows)) {
            return array(null, __("No photos found.", SHASHIN_L10N_NAME));
        }

        $this->data['photos'] = $rows;
        return array(true, __("Album photos retrieved.", SHASHIN_L10N_NAME));
    }

    /**
     * Inserts or updates photos for a Picasa album. Must successfully
     * call getAlbum() or setAlbum() first.
     *
     * Reads the Picasa photo feed for a given album. Calls
     * getAlbumPhotos() to copmpare new data to old data. New photos
     * are inserted, and old photos are updated as needed (can be
     * flagged as deleted or moved to another album). On success, calls
     * getAlbumPhotos() to refresh the data in memory.
     *
     * @access public
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ShashinAlbum::getAlbumPhotos()
     * @uses ToppaWPFunctions::update()
     * @uses ToppaWPFunctions::insert()
     * @return array 0: true on success, false on failure; 1: message; 2: true if SQL error
     */
    function setAlbumPhotos() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        // read the feed for the album's photos
        $feed_url = $shashin_options['picasa_server'] . SHASHIN_ALBUM_RSS;
        $feed_url = str_replace("USERNAME", $this->data['user'], $feed_url);
        $feed_url = str_replace("ALBUMID", $this->data['album_id'], $feed_url);
        $feed_content = ToppaWPFunctions::readFeed($feed_url);
        $photo = new ShashinPhoto();
        $new_photos = ToppaWPFunctions::parseFeed($feed_content, $photo->ref_data);

        if (!$new_photos) {
            return array(false, __("ShashinAlbum::setAlbumPhotos - Failed to read Picasa RSS feed for album.", SHASHIN_L10N_NAME));
        }

        $current = $this->getAlbumPhotos();

        if ($current[0] === false) {
            return $current;
        }

        $update_ids = array();
        $found_ids = array();
        $unchanged_ids = array();
        $inserts = array();
        $updates = array();

        foreach ($new_photos as $new_photo) {
            // if the photo used to be in another album, it's delete flag will
            // be set to Y. Set it to N in the new_photo data to force an
            // update to the new album
            $new_photo['deleted'] = 'N';

            // for some reason Picasa pads the timestamps with extra zeroes
            // - strip them out.
            $new_photo['taken_timestamp'] = substr($new_photo['taken_timestamp'],0,10);
            $new_photo['uploaded_timestamp'] = substr($new_photo['uploaded_timestamp'],0,10);

            // see if the photo is already in the album, and then compare the
            // values, so we can decide whether to insert, update, or skip
            if (!empty($this->data['photos'])) {
                foreach ($this->data['photos'] as $old_photo) {
                    if ($old_photo['photo_id'] == $new_photo['photo_id']) {
                        // use this below to find old photos that need to be
                        // flagged as deleted
                        $found_ids[] = $old_photo['photo_id'];

                        // don't include these in the comparison
                        unset($old_photo['include_in_random']);
                        unset($old_photo['photo_key']);
                        $diff = array_diff_assoc($new_photo, $old_photo);

                        if (empty($diff)) {
                            $unchanged_ids[] = $new_photo['photo_id'];
                            break;
                        }

                        else {
                            $update_ids[] = $new_photo['photo_id'];
                            $updates[] = $new_photo;
                            break;
                        }
                    }
                }
            }

            if (!in_array($new_photo['photo_id'], $unchanged_ids) && !in_array($new_photo['photo_id'], $update_ids)) {
                $inserts[] = $new_photo;
            }
        }

        // do updates
        foreach($updates as $update) {
            $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_PHOTO_TABLE, $update, array('photo_id' => $update['photo_id']));

            if (!$sql_result) {
                return array(false, __("ShashinAlbum::setAlbumPhotos - Failed to update record for photo ID {$update['photo_id']}. SQL Error:", SHASHIN_L10N_NAME), true);
            }
        }

        // do inserts - do a duplicate key update check, so we can
        // switch an old photo to a new album if it's been moved
        foreach($inserts as $insert) {
            $sql_result = ToppaWPFunctions::sqlInsert(SHASHIN_PHOTO_TABLE, $insert, null, 'album_id');

            if (!$sql_result) {
                return array(false, __("ShashinAlbum::setAlbumPhotos - Failed to insert record for photo ID {$insert['photo_id']}. SQL Error:", SHASHIN_L10N_NAME), true);
            }
        }

        // do deletes (don't really delete, just flag as deleted)
        if (!empty($found_ids)) {
            foreach ($this->data['photos'] as $old_photo) {
                if (!in_array($old_photo['photo_id'], $found_ids)) {
                    $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_PHOTO_TABLE, array('deleted' => 'Y'), array('photo_id' => $update['photo_id']));

                    if (!$sql_result) {
                        return array(false, __("ShashinAlbum::setAlbumPhotos - Failed to flag photo ID {$update['photo_id']} as deleted. SQL Error:", SHASHIN_L10N_NAME), true);
                    }
                }
            }
        }

        // refresh the photos in memory
        return $this->getAlbumPhotos();
    }

    /**
     * Updates local album data (i.e. data that doesn't come from the
     * Picasa RSS feed).
     *
     * @access public
     * @param array $data A hash of album data (keys are column names)
     * @return array 0: true on success, false on failure; 1: message; 2: true if SQL error
     */
    function setAlbumLocal($data) {
        if (!is_array($data)) {
            return array(false, __("ShashinAlbum::setAlbumLocal - Called with invalid arguments.", SHASHIN_L10N_NAME));
        }

        $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_ALBUM_TABLE, $data, array('album_id' => $this->data['album_id']));

        if (!$sql_result) {
            return array(false, __("ShashinAlbum::setAlbumLocal - Failed to update record for photo ID {$this->data['photo_id']}. SQL Error:", SHASHIN_L10N_NAME), true);
        }

        return array(true, __("Album updated."));
    }

    /**
     * DEPRECATED - use getAlbumThumbsMarkup instead.
     *
     * Translates the "salbum" Shashin tag into xhtml displaying the thumbnail
     * for the album cover. Picasa thumbnail size is fixed at 160x160.
     *
     * @access public
     * @param array $match see detailed list in Shashin::parseContent()
     * @uses ShashinAlbum::getAlbum()
     * @return string the xhtml markup to replace the salbum tag with
     */
    function getAlbumMarkup($match) {
        if ($_REQUEST['shashin_album_key']) {
            return ShashinPhoto::getAlbumPhotosMarkup($match);
        }

        // get the album, if we don't have it already
        else if (!$this->data['album_id']) {
            list($result, $message, $db_error) = $this->getAlbum(array('album_key' => $match['album_key']));

            if (!$result) {
                return '<span class="shashin_error">Shashin Error: ' . $message . '</span>';
            }
        }

        return $this->_getDivMarkup($match);
    }

    /**
     * Generates the xhtml markup for an album thumbnail, with an appropriate
     * link for accessing the album photos
     *
     * @access private
     * @uses ShashinAlbum::_getAlbumLink()
     * @return string xhtml markup for album thumbnail
     */
     function _getAlbumThumbTag() {
        $markup = $this->_getAlbumLink();
        $markup .= '<img src="' . $this->data['cover_photo_url']
            . '" alt="' . $this->data['title']
            . '" width="' . SHASHIN_ALBUM_THUMB_SIZE
            . '" height="' . SHASHIN_ALBUM_THUMB_SIZE . '" />';
        $markup .= "</a>";
        return $markup;
    }

    /**
     * Generates a link to an album's photo, either at Picasa or locally,
     * depending on the option shashin_album_photos_url. The link includes an
     * opening anchor tag but not a closing one.
     *
     * @access private
     * @return string URL embedded in an opening anchor tag
     */
    function _getAlbumLink() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        // if you want to make album links behave differently from photo links,
        // override $shashin_options['image_display'] by uncommenting the
        // following line
        // $shashin_options['image_display'] = 'new_window';

        $markup = '<a href="';

        if ($shashin_options['image_display'] == 'highslide') {
            $permalink = get_permalink();
            $glue = strpos($permalink, "?") ? "&amp;" : "?";
            $markup .=  $permalink . $glue . 'shashin_album_key=' . $this->data['album_key'] . '"';
        }

        else {
            $markup .= $this->data['link_url'] . '"';

            if ($shashin_options['image_display'] == 'new_window') {
                $markup .= ' target="_blank"';
            }

        }

        $markup .= ">";
        return $markup;
    }

    /**
     * Retrieves data for multiple albums from the shashin_album table.
     *
     * @static
     * @access public
     * @param string|array $keywords the fields to return
     * @param string|array $conditions (optional) array of key-values pairs, or a string containing its own WHERE clause
     * @param string $other (optional) any additional conditions for the query (GROUP BY, etc.)
     * @return mixed passes along the return value of the $wpdb call in ToppaWPFunctions::sqlSelect
     * @uses ToppaWPFunctions::sqlSelect
     */
    function getAlbums($keywords = '*', $conditions = null, $other = null) {
        return ToppaWPFunctions::sqlSelect(SHASHIN_ALBUM_TABLE, $keywords, $conditions, $other, 'get_results');
    }

    /**
     * Inserts or updates all albums for a Picasa user.
     *
     * @static
     * @access public
     * @param string $user_name (required) the Picasa user_name of the album's owner
     * @param array $local_data (optional) A hash of local album data (data not from the Picasa feed)
     * @param boolean $add_only (optional) whether to sync existing albums when adding new albums (default: false)
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ToppaWPFunctions::sqlUpdate()
     * @uses ToppaWPFunctions::sqlInsert()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::setAlbumPhotos()
     * @return array 0: true on success, false on failure; 1: message; 2: true if SQL error
     */
    function setUserAlbums($user_name, $local_data = null, $sync_only = true) {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        if (is_string($user_name))  {
            // read the feed for the user
            $feed_url = $shashin_options['picasa_server'] . SHASHIN_USER_RSS;
            $feed_url = str_replace('USERNAME', $user_name, $feed_url);
            $feed_content = ToppaWPFunctions::readFeed($feed_url);
            $album = new ShashinAlbum();
            $albums_data = ToppaWPFunctions::parseFeed($feed_content, $album->ref_data);
        }

        else {
            return array(false, __("ShashinAlbum::setUserAlbums - First argument to setUserAlbums is not a valid username.", SHASHIN_L10N_NAME));
        }

        if (!$albums_data) {
            return array(false, __("ShashinAlbum::setUserAlbums - Failed to parse album feed.", SHASHIN_L10N_NAME));
        }

        for ($i=0; $i<count($albums_data); $i++) {
            $exists = false;
            $current = new ShashinAlbum();
            list($result, $message, $db_error) = $current->getAlbum(array('album_id' => $albums_data[$i]['album_id']));

            if ($result === false) {
                return array($result, $message, $db_error);
            }

            elseif ($result === true) {
                $exists = true;
            }

            if (is_array($local_data)) {
                foreach ($local_data as $k=>$v) {
                    $albums_data[$i][$k] = $v;
                }
            }

            // make data tweaks
            $albums_data[$i]['last_updated'] = time();
            $albums_data[$i]['pub_date'] = strtotime($albums_data[$i]['pub_date']);

            // if the album exists and we're syncing only, update it
            if ($exists && $sync_only) {
                $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_ALBUM_TABLE, $albums_data[$i], array('album_id' => $albums_data[$i]['album_id']));

                if (!$sql_result) {
                    return array(false, __("ShashinAlbum::setUserAlbums - Failed to update database record for album ID {$albums_data[$i]['album_id']}. Possible SQL Error:", SHASHIN_L10N_NAME), true);
                }
            }

            // if the album does not exist, and we're not only syncing, insert it
            elseif (!$exists && !$sync_only) {
                $sql_result = ToppaWPFunctions::sqlInsert(SHASHIN_ALBUM_TABLE, $albums_data[$i]);

                if (!$sql_result) {
                    return array(false, __("ShashinAlbum::setUserAlbums - Failed to insert database record for album ID {$albums_data[$i]['album_id']}. Possible SQL Error:", SHASHIN_L10N_NAME), true);
                }

                // get the album (now that it exists), and add its photos
                list($result, $message, $db_error) = $current->getAlbum(array('album_id' => $albums_data[$i]['album_id']));

                if ($result === false) {
                    return array($result, $message, $db_error);
                }
            }

            // if we're syncing only and this is an album that hasn't
            // been added, then skip to the next album in the feed
            else {
                continue;
            }

            list($result, $message, $db_error) = $current->setAlbumPhotos();

            if (!$result) {
                return array($result, $message, $db_error);
            }
        }

        return array(true, __("Album photos updated.", SHASHIN_L10N_NAME));
    }

    /**
     * Returns xhtml markup for displaying album thumbnails in a table.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::_getOrderedAlbums()
     * @uses ShashinAlbum::_getTableMarkup()
     * @param array $match see details in Shashin::parseContent()
     * @return string complete xhtml markup for displaying album thumbnails in a table
     */
    function getAlbumThumbsMarkup($match) {
        if ($_REQUEST['shashin_album_key']) {
            return ShashinPhoto::getAlbumPhotosMarkup($match);
        }

        $ordered = ShashinAlbum::_getOrderedAlbums($match);

        if (!is_array($ordered)) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve albums.") . '</span>';
        }

        return ShashinAlbum::_getTableMarkup($ordered, $match);
    }

    /**
     * Returns xhtml markup for displaying album thumbnails paired with the album
     * description, and optional other data to display.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::_getOrderedAlbums()
     * @uses ShashinAlbum::_getAlbumLink()
     * @uses ShashinAlbum::_getAlbumThumbTag()
     * @param array $match see details in Shashin::parseContent()
     * @return string complete xhtml markup for albums thumbnails and descriptions
     */
     function getAlbumListMarkup($match) {
        $albums = ShashinAlbum::_getOrderedAlbums($match);

        foreach ($albums as $al) {
            $album = new ShashinAlbum();
            list($result, $message, $db_error) = $album->getAlbum(null, $al);

            if (!$result) {
                return '<span class="shashin_error">' . __("Shashin Error:") . ' ' . $message . '</span>';
            }

            $replace = '<div class="shashin_album_list">' . "\n";
            $replace .= '<div class="shashin_album_list_thumb">' . $album->_getAlbumThumbTag() . "</div>\n";
            $link = $album->_getAlbumLink();
            $replace .= "<strong>$link" . $album->data['title'] . '</a></strong><br />';

            // option to show album info
            if ($match['info_yn'] == 'y') {
                $replace .= date("M j, Y", $album->data['pub_date']) . ' &mdash; '
                    . $album->data['photo_count'] . " picture"
                    . (($album->data['photo_count'] > 1) ? 's' : '');

                if ($album->data['location']) {
                    $replace .= ' &mdash; ' . $album->data['location'];

                    if ($album->data['geo_pos']) {
                        $replace .= ' <a href="' . SHASHIN_GOOGLE_MAPS_QUERY_URL
                            . str_replace(" ", "+", $album->data['geo_pos'])
                            . '"><img src="' . SHASHIN_DISPLAY_URL
                            . 'mapped_sm.gif" alt="Google Maps Location" width="15" height="12" style="vertical-align: bottom; border: none;" />'
                            . '</a>';
                    }
                }
            }

            if ($album->data['description']) {
                // need to remove line breaks so WordPress doesn't add tags
                $replace .= preg_replace("/\s/", " ", $album->data['description']);
            }

            $replace .= "</div>\n";
        }

        return $replace;
    }

    /**
     * Get all the user_names in the album table.
     *
     * @static
     * @access public
     * @uses ToppaWPFunctions::sqlSelect()
     * @return mixed passes along the return value of the $wpdb call in ToppaWPFunctions::sqlSelect
     */
    function getUsers() {
        return ToppaWPFunctions::sqlSelect(SHASHIN_ALBUM_TABLE, 'distinct user', null, 'order by user', 'get_col');
    }

    /**
     * Returns the requested albums in the desired order.
     *
     * @static
     * @access private
     * @param array $match see detailed list in Shashin::parseContent()
     * @return array ordered album data
     */
     function _getOrderedAlbums($match) {
        // a set of album keys
        if (strpos($match['album_key'], "|") !== false) {
            $album_keys = explode("|", $match['album_key']);
            $conditions .= "where album_key in (" . implode(",", $album_keys) . ")";
        }

        // a single album key
        else if (is_numeric($match['album_key'])) {
            $conditions .= "where album_key = " . $match['album_key'];
        }

        // an order by preference
        else {
            $other = "order by " . $match['album_key'];
        }

        $albums = ShashinAlbum::getAlbums('*', $conditions, $other);

        if (!is_array($albums)) {
            return false;
        }

        // the data doesn't come back from the database in the order it was
        // requested, so re-order it.
        if ($album_keys) {
            $ordered = array();
            foreach ($album_keys as $key) {
                foreach ($albums as $album) {
                    if ($key == $album['album_key']) {
                        $ordered[] = $album;
                    }
                }
            }
        }

        else {
            $ordered = $albums;
        }

        return $ordered;
    }

    /**
     * Generates the xhtml div for displaying an album thumbnail.
     *
     * @access private
     * @param array $match see detailed list in Shashin::parseContent()
     * @uses ShashinAlbum::_getAlbumLink()
     * @uses ShashinAlbum::_getAlbumThumbTag()
     * @return string the xhtml markup to display the image
     */
    function _getDivMarkup($match) {
        $shashin_options = unserialize(SHASHIN_OPTIONS);
        $replace = '<div class="shashin_album" style="width: '
            . (SHASHIN_ALBUM_THUMB_SIZE + $shashin_options['div_padding']) . 'px;';

        if ($match['float']) {
            $replace .= ' float: ' . $match['float'] . ';';
        }

        if ($match['clear']) {
            $replace .= ' clear: ' . $match['clear'] . ';';
        }

        $replace .=  '">';
        $replace .= $this->_getAlbumThumbTag();
        $replace .= '<span class="shashin_album_title">'
            . $this->_getAlbumLink()
            . $this->data['title']
            . "</a></span>";

        $replace .= '<span class="shashin_album_count">' . $this->data['photo_count'] . " picture";
        $replace .= ($this->data['photo_count'] > 1) ? 's' : '';
        $replace .= '</span>';

        if ($match['location_yn'] == 'y' && $this->data['location']) {
            $replace .= '<span class="shashin_album_location">'
            . ($this->data['geo_pos']
                ? (' <a href="' . SHASHIN_GOOGLE_MAPS_QUERY_URL
                    . str_replace(" ", "+", $this->data['geo_pos'])
                    . '"><img src="' . SHASHIN_DISPLAY_URL
                    . 'mapped_sm.gif" alt="Google Maps Location" width="15" height="12" style="border: none;" />')
                : '')
            . ($this->data['geo_pos'] ? '</a><br />' : '')
            . $this->data['location']
            . "</span>";
        }

        if ($match['pubdate_yn'] == 'y' && $this->data['pub_date']) {
            $replace .= '<span class="shashin_album_date">' . date("M j, Y", $this->data['pub_date']) . "</span>";
        }

        $replace .= "</div>";
        return $replace;
    }

    /**
     * Generates an xhtml table containing thumbnails of the the passed in
     * albums. Note that $albums is an array of arrays of album data, not
     * ShashinAlbum objects.
     *
     * @static
     * @access private
     * @param array $albums array of arrays containing album data
     * @param array $match see detailed list in Shashin::parseContent()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::_getDivMarkup()
     * @return string xhtml markup for the table containing the photos
     */
    function _getTableMarkup($albums, $match) {
        $replace = '<table class="shashin_album_thumbs_table"';

        if ($match['float'] || $match['clear']) {
            $replace .= ' style="';
            $replace .= $match['float'] ? "float:{$match['float']};" : '';
            $replace .= $match['clear'] ? "clear:{$match['clear']};" : '';
            $replace .= '"';
            // don't want these applied to the individual images when
            // calling _getDivMarkup
            unset($match['float']);
            unset($match['clear']);
        }

        $replace .= ">\n";
        $cell_count = 1;

        for ($i = 0; $i < count($albums); $i++) {
            if ($cell_count == 1) {
                $replace .= "<tr>\n";
            }

            $album = new ShashinAlbum();
            list($result, $message, $db_error) = $album->getAlbum(null, $albums[$i]);

            if (!$result) {
                return '<span class="shashin_error">' . __("Shashin Error:") . ' ' . $message . '</span>';
            }


            $markup = $album->_getDivMarkup($match);
            $replace .= "<td>$markup</td>\n";
            $cell_count++;

            if ($cell_count > $match['max_cols'] || $i == (count($albums) - 1)) {
                $replace .= "</tr>\n";
                $cell_count = 1;
            }
        }

        $replace .= "</table>\n";
        return $replace;
    }
}

?>
