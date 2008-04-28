<?php
/**
 * ShashinAlbum class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.1
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
    var $refData;
    var $data;
    
    /**
     * The constructor sets $this->refData, which is used for creating the
     * shashin_album table, afor mapping Picasa RSS feed params to table
     * field names, and for generating form input fields.
     *
     * @access public
     */
     function ShashinAlbum() {
        // link_url is set from the feed but we give it input params since
        // we initially gather it from user input
        $this->refData = array(
            'album_key' => array(
                'colParams' => array('type' => 'int unsigned', 'notNull' => 1,
                    'other' => 'auto_increment primary key'),
                'label' => 'Album Key', 'source' => 'db'),
            'album_id' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Album ID', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'id'),
            'user' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'User', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'user'),
            'name' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Name', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'name'),
            'link_url' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Link URL', 'source' => 'feed',
                'feedParam1' => 'link',
                'inputType' => 'text', 'inputSize' => '60'),
            'title' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Title', 'source' => 'feed',
                'feedParam1' => 'title'),
            'description' => array(
                'colParams' => array('type' => 'text'),
                'label' => 'Description', 'source' => 'feed',
                'feedParam1' => 'description'),
            'location' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Location', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'location'),
            'cover_photo_url' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Cover Photo URL', 'source' => 'feed',
                'feedParam1' => 'media', 'feedParam2' => 'thumbnail', 'attrs' => 'url'),
            'last_updated' => array(
                'colParams' => array('type' => 'bigint unsigned'),
                'label' => 'Last Updated', 'source' => 'user'),
            'photo_count' => array(
                'colParams' => array('type' => 'int unsigned',  'notNull' => 1),
                'label' => 'Photo Count', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'numphotos'),
            'pub_date' => array(
                'colParams' => array('type' => 'bigint unsigned', 'notNull' => 1),
                'label' => 'Pub Date', 'source' => 'feed',
                'feedParam1' => 'pubDate'),
            'geo_pos' => array(
                'colParams' => array('type' => 'varchar', 'length' => '25'),
                'label' => 'Pub Date', 'source' => 'feed',
                'feedParam1' => 'gml', 'feedParam2' => 'pos'),
            'include_in_random' => array(
                'colParams' => array('type' => 'char', 'length' => '1', 'other' => "default 'Y'"),
                'label' => 'Include in random photo display', 'source' => 'user',
                'inputType' => 'radio',
                'inputSubgroup' => array('Y' => 'Yes', 'N' => 'No')),
        );
    }

    /**
     * Retrieves data stored in the shashin_album table for the specified Picasa
     * album. 
     *
     * Data is stored in $this->data. An album can be requested by Picasa ID,
     * name, Shashin key, or a complete array of album data can also be passed
     * in.
     *
     * @access public
     * @param int $albumID the Picasa ID of an album
     * @param int $albumName the name of a Picasa album
     * @param array $albumData a complete array of album data
     * @param int $albumKey the Shashin key for a Picasa album
     * @uses ToppaWPFunctions::select()
     * @return boolean true: retrieval succeeded; false: retrieval failed
     */
    function getAlbum($albumID = null, $albumName = null, $albumData = null, $albumKey = null) {
        if (strlen($albumID)) {
            $where = "WHERE ALBUM_ID = '$albumID'";
        }
        
        elseif (strlen($albumName)) {
            $where = "WHERE NAME = '$albumName'";
        }

        elseif (!empty($albumData)) {
            $this->data = $albumData;
        }

        elseif (strlen($albumKey)) {
            $where = "WHERE ALBUM_KEY = '$albumKey'";
        }
        
        else {
            return false;
        }
        
        if (strlen($where)) {
            $row = ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, "*", $where);
            
            if (empty($row)) {
                return false;
            }
            
            $row['description'] = htmlspecialchars($row['description'], ENT_COMPAT, 'UTF-8');
            $row['title'] = htmlspecialchars($row['title'], ENT_COMPAT, 'UTF-8');
            $this->data = $row;
        }
        
        return true;
    }
    

    /**
     * Inserts or updates a Picasa album in Shashin, based on the album name and
     * the username.
     * 
     * Reads the user's Picasa feed of all albums and retieves data for the
     * specified album. Does not insert/update album photos. On success, calls
     * getAlbum() to populate the album object.
     *
     * @access public
     * @param string $userName (required) the Picasa username of the album's owner
     * @param string $albumName (optional) the name of the Picasa album
     * @param string $albumID (optional) the ID of the Picasa album
     * @param string $includeRandom (optional) y or n, indicating whether the album's photos should be included in displays of random images
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ToppaWPFunctions::update()
     * @uses ToppaWPFunctions::insert()
     * @uses ShashinAlbum::getAlbum()
     * @return boolean true: insert/update successful; false: insert/updated failed
     */
    function setAlbum($userName, $albumName = null, $albumID = null, $includeRandom = null) {
        // read the feed for the user
        $feedURL = str_replace("USERNAME", $userName, SHASHIN_USER_RSS);
        $feedContent = ToppaWPFunctions::readFeed($feedURL);

        if (strlen($albumName)) {
            $albumData = ToppaWPFunctions::parseFeed($feedContent, $this->refData, 'name', $albumName);

            if (empty($albumData)) {
                return false;
            }

            $exists = $this->getAlbum(null, $albumName);
            $albumData['name'] = $albumName;
        }
        
        elseif (strlen($albumID)) {
            $albumData = ToppaWPFunctions::parseFeed($feedContent, $this->refData, 'album_id', $albumID);

            if (empty($albumData)) {
                return false;
            }

            $exists = $this->getAlbum($albumID);
            $albumData['album_id'] = $albumID;
        }
        
        else {
            return false;
        }
        
        $albumData['last_updated'] = time();

        // don't alter the current include_in_random if no new value is provided
        // and make sure it's a valid length (1 char)
        if (strlen($includeRandom) == 1) {
            $albumData['include_in_random'] = $includeRandom;
        }

        // make the pubdate a timestamp
        $albumData['pub_date'] = strtotime($albumData['pub_date']);
        
        // if the album exists, do an update and return
        if ($exists === true) {
            strlen($albumName) ? $where = "name = '$albumName'" : $where = "album_id = '$albumID'";
            
            if (ToppaWPFunctions::update(SHASHIN_ALBUM_TABLE, $where, $albumData) === false) {
                return false;
            }
        }
        
        // if the album does not exist, insert it
        elseif (ToppaWPFunctions::insert(SHASHIN_ALBUM_TABLE, $albumData) === false) {
            return false;
        }

        // if the album insert seems ok, call getAlbum, which also confirms if
        // the insert/update really worked.
        if (strlen($albumName)) {
            return $this->getAlbum(null, $albumName);
        }
        
        return $this->getAlbum($albumID);
    }

    
    /**
     * Inserts or updates all albums for a user. Note it also calls
     * setAlbumPhotos() for each album.
     * 
     * @static
     * @access public
     * @param string $username (required) the Picasa username of the album's owner
     * @param string $includeRandom (optional) y or n, indicating whether album photos should be included in displays of random images
     * @param boolean $addOnly (optional) defaults to false, indicating whether to sync existing albums when adding new albums
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ToppaWPFunctions::update()
     * @uses ToppaWPFunctions::insert()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::setAlbumPhotos()
     * @return boolean true: insert/update successful; false: insert/updated failed
     */
    function setAlbums($userName, $includeRandom = null, $addOnly = false) {
        // read the feed for the user
        $feedURL = str_replace("USERNAME", $userName, SHASHIN_USER_RSS);
        $feedContent = ToppaWPFunctions::readFeed($feedURL);
        $album = new ShashinAlbum();
        $albumsData = ToppaWPFunctions::parseFeed($feedContent, $album->refData);

        if (empty($albumsData)) {
            return false;
        }
        
        for ($i=0; $i<=count($albumsData)-1; $i++) {
            $current = new ShashinAlbum();
            $exists = $current->getAlbum($albumsData[$i]['album_id']);
            
            // make data tweaks
            $albumsData[$i]['last_updated'] = time();

            if (strlen($includeRandom) == 1) {
                $albumsData[$i]['include_in_random'] = $includeRandom;
            }
            
            $albumsData[$i]['pub_date'] = strtotime($albumsData[$i]['pub_date']);
        
            // if the album exists and we're allowing updates, update it
            if ($exists === true && $addOnly === false) {
                $where = "album_id = '" . $albumsData[$i]['album_id'] . "'";
            
                if (ToppaWPFunctions::update(SHASHIN_ALBUM_TABLE, $where, $albumsData[$i]) === false) {
                    return false;
                }
                
                if ($current->setAlbumPhotos() === false) {
                    return false;
                }
            }
        
            // if the album does not exist, insert it
            elseif ($exists === false) {
                if (ToppaWPFunctions::insert(SHASHIN_ALBUM_TABLE, $albumsData[$i]) === false) {
                    return false;
                }

                // get the album again, now that it exists, and add its photos
                $current->getAlbum($albumsData[$i]['album_id']);
                
                if ($current->setAlbumPhotos() === false) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Deletes an album from Shashin. Works only if getAlbum() was successfully
     * called first.
     *
     * @access public
     * @uses ToppaWPFunctions::delete();
     * @return boolean true: delete successful; false: delete failed
     */
    function deleteAlbum() {
        $where = "album_id = '" . $this->data['album_id'] . "'"; 
        if (ToppaWPFunctions::delete(SHASHIN_PHOTO_TABLE, $where) !== false) {
            return ToppaWPFunctions::delete(SHASHIN_ALBUM_TABLE, $where);
        }
        
        return false;
    }
    
    /**
     * Retrieves all photos in the shashin_photo table for an album. Must
     * successfully call getAlbum() first.
     *
     * @access public
     * @param string $orderBy (optional) column name(s) to order by
     * @param integer $limit (optional) a max number of records to return
     * @param boolean $excludeDeleted (optional) whether to exclude photos flagged as deleted
     * @uses ToppaWPFunctions::select()
     * @return boolean true: retrieval succeeded; false: retrieval failed
     */    
    function getAlbumPhotos($orderBy = 'PHOTO_ID', $limit = null, $excludeDeleted = true) {
        $where = "WHERE ALBUM_ID = '" . $this->data['album_id'] . "'";
        
        if ($excludeDeleted === true) {
            $where .= " AND DELETED = 'N'";
        }    
        
        $where .= " ORDER BY $orderBy";
        
        if (strlen($limit)) {
            $where .= " LIMIT $limit";
        }
        
        $retVal = ToppaWPFunctions::select(SHASHIN_PHOTO_TABLE, '*', $where, 'get_results');

        if ($retVal === false) {
            return false;
        }
        
        $this->data['photos'] = $retVal;
        return true;        
    }    
    
    /**
     * Inserts or updates photos for a Picasa album in Shashin. Must
     * successfully call getAlbum() or setAlbum() first.
     * 
     * Reads the Picasa photo feed for a given album. Calls getAlbumPhotos() to
     * copmpare new data to old data. New photos are inserted, and old photos
     * are updated, deleted, or left untouched. On success, calls
     * getAlbumPhotos() again to refresh the data in memory.
     *
     * @access public
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::readFeed()
     * @uses ToppaWPFunctions::parseFeed()
     * @uses ShashinAlbum::getAlbumPhotos()
     * @uses ToppaWPFunctions::update()
     * @uses ToppaWPFunctions::insert()
     * @uses ToppaWPFunctions::delete()
     * @return boolean true: successful; false: failed
     */
    function setAlbumPhotos() {
        // read the feed for the album's photos
        $feedURL = str_replace("USERNAME", $this->data['user'], SHASHIN_ALBUM_RSS);
        $feedURL = str_replace("ALBUMID", $this->data['album_id'], $feedURL);
        $photo = new ShashinPhoto();
        $feedContent = ToppaWPFunctions::readFeed($feedURL);
        $newPhotos = ToppaWPFunctions::parseFeed($feedContent, $photo->refData);

        if ($this->getAlbumPhotos('PHOTO_ID', null, false) === false || empty($newPhotos)) {
            return false;
        }

        $updateIDs = array();
        $foundIDs = array();
        $unchangedIDs = array();
        $inserts = array();
        $updates = array();
        foreach ($newPhotos as $newPhoto) {
            // if the photo used to be in another album, it's delete flag will be
            // set. Unset it in the newPhoto data to force an update to the new album
            $newPhoto['deleted'] = 'N'; 

            // see if the photo is already in the album, and then compare the
            // values, so we can decide whether to insert, update, delete, or
            // skip
            if (!empty($this->data['photos'])) {
                foreach ($this->data['photos'] as $oldPhoto) {
                    if ($oldPhoto['photo_id'] == $newPhoto['photo_id']) {
                        // we'll delete any old photos that don't have a
                        // new photo match
                        $foundIDs[] = $oldPhoto['photo_id'];

                        // don't include these in the comparison
                        unset($oldPhoto['include_in_random']);
                        unset($oldPhoto['photo_key']);
                        $diff = array_diff_assoc($newPhoto, $oldPhoto);

                        if (empty($diff)) {
                            $unchangedIDs[] = $newPhoto['photo_id'];
                            break;
                        }

                        else {
                            $updateIDs[] = $newPhoto['photo_id'];
                            $updates[] = $newPhoto;
                            break;
                        }
                    }
                }
            }
            
            if (!in_array($newPhoto['photo_id'], $unchangedIDs)
              && !in_array($newPhoto['photo_id'], $updateIDs)) {
                $inserts[] = $newPhoto;
            }
        }

        // do updates
        foreach($updates as $update) {
            $retVal = ToppaWPFunctions::update(SHASHIN_PHOTO_TABLE, "photo_id = '{$update['photo_id']}'", $update);
            
            if ($retVal === false) {
                return false;
            } 
        } 
        
        // do inserts - include the update option for the album_id, so we can
        // switch an old photo to a new album if it's been moved
        foreach($inserts as $insert) {
            $retVal = ToppaWPFunctions::insert(SHASHIN_PHOTO_TABLE, $insert, null, null, 'album_id');
            
            if ($retVal === false) {
                return false;
            } 
        }

        // do deletes (don't really delete, just flag as deleted)
        if (!empty($foundIDs)) {
            foreach ($this->data['photos'] as $oldPhoto) {
                if (!in_array($oldPhoto['photo_id'], $foundIDs)) {
                    $where = "photo_id = '{$oldPhoto['photo_id']}'";
                    $update = array('deleted' => 'Y');
                    $retVal = ToppaWPFunctions::update(SHASHIN_PHOTO_TABLE, $where, $update);
                }
            }
        }
        
        // refresh the photos in memory
        if ($this->getAlbumPhotos() === false) {
            return false;
        }
        
        return true;
    }

    /**
     * Sets the include_in_random flag for an album (y or n).
     *
     * @access public
     * @param string $randomFlag (required) y or n
     * @uses ToppaWPFunctions::update()
     * @return boolean true: update succeeded; false: update failed
     */    
    function setIncludeInRandom($randomFlag) {
        $retVal = ToppaWPFunctions::update(SHASHIN_ALBUM_TABLE, "album_id = '{$this->data['album_id']}'",
            array('include_in_random' => $randomFlag, 'last_updated' => time()));

        if ($retVal === false) {
            return false;
        }
        
        return true;
    }

    /**
     * A static method for retrieving data on a user's Picasa albums, as stored
     * in shashin_album. Accepts an optional SQL where clause.
     *
     * @static
     * @access public
     * @param string $where (optional) SQL "where" clause (can include order by, etc as well)
     * @uses ToppaWPFunctions::select()
     * @return array An array of hashes containing album data
     */        
    function getAlbums($where = null) {
        return ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, '*', $where, 'get_results');
    }

    /**
     * Translates the "salbum" Shashin tag into xhtml displaying the thumbnail
     * and title of an album, with a hyperlink to the album at Picasa. Thumbnail
     * size is fixed at 160x160.
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete salbum tag
     * - Album Key (required): the Shashin album_key (not the Picasa album ID)
     * - Location (optional): y or n to show the location of the image, with a link to Google Maps.
     * - Pub Date (optional): y or n to show the pub date of the album
     * - float (optional): a css float value (left, right, or none)
     * - Clear (optional): a css clear value (left, right, or both)
     *
     * @access public
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent() 
     * @uses ShashinAlbum::getAlbum()
     * @return string the xhtml markup to replace the salbum tag with
     */
    function getAlbumMarkup($match) {
        // get the album, if we don't have it already
        if (!strlen($this->data['album_id'])) {
            if ($this->getAlbum(null, null, null, $match[1]) === false) {
                return '<span class="shashin_error">Error: unable to find Album Key ' . $match[1] . '</span>';
            }
        }

        return $this->_getDivMarkup($match);
    }
    
    /**
     * Returns xhtml markup for displaying album thumbnails in a table.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::_getOrderedAlbums()
     * @uses ShashinAlbum::_getTableMarkup()
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @return string complete xhtml markup for displaying album thumbnails in a table
     */
     function getAlbumThumbsMarkup($match) {
        $ordered = ShashinAlbum::_getOrderedAlbums($match);
        return ShashinAlbum::_getTableMarkup($ordered, $match[2], $match[3], $match[4], $match[5], $match[6]);    
    }

    /**
     * Returns xhtml markup for displaying album thumbnails paired with the album
     * description, and optional other data to display.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::_getOrderedAlbums()
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @return string complete xhtml markup for albums thumbnails and descriptions
     */
     function getAlbumListMarkup($match) {
        $albums = ShashinAlbum::_getOrderedAlbums($match);
        
        foreach ($albums as $al) {
            $album = new ShashinAlbum();

            if ($album->getAlbum(null, null, $al) === false) {
                return '<span class="shashin_error">Error: unable to set album object for album_key ' . $al['album_key'] . '</span>';
            }

            $replace .= '<div class="shashin_album_list">' . "\n";
            $replace .= '<div class="shashin_album_list_thumb">' . $album->getAlbumThumbTag() . "</div>\n";
            $link = $album->getAlbumLink();
            $replace .= '<strong><a href="' . $link . '">' . $album->data['title'] . '</a></strong><br />';
 
            // option to show album info
            if (strtolower(trim($match[2])) == 'y') {
                $replace .= date("M j, Y", $album->data['pub_date']) . ' &mdash; '
                    . $album->data['photo_count'] . " picture"
                    . (($album->data['photo_count'] > 1) ? 's' : '');
                    
                if ($album->data['location']) {
                    $replace .= ' &mdash; ' . $album->data['location'];
                
                    if ($album->data['geo_pos']) {
                        $replace .= ' <a href="' . GOOGLE_MAPS_QUERY_URL
                            . str_replace(" ", "+", $album->data['geo_pos'])
                            . '"><img src="' . SHASHIN_DISPLAY_URL
                            . 'mapped_sm.gif" alt="Google Maps Location" width="15" height="12" style="vertical-align: bottom; border: none;" />'
                            . '</a>';
                    }
                }
            }
            
            if ($album->data['description']) {
                // need to remove line breaks so WordPress doesn't mess up
                // the dl tagging
                $replace .= preg_replace("/\s/", " ", $album->data['description']);
            }
            
            $replace .= "</div>\n";
        }
        
        return $replace;
    }

    /**
     * Get all the usernames in the album table.
     *
     * @static
     * @access public
     * @uses ToppaWPFunctions::select()
     * @return array a list of all the usernames in the album table, or false on failure
     */   
    function getUsers() {
        return ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, "user", null, 'get_col', null, 'DISTINCT');
    }

    /**
     * Returns the requested albums (either by specific keys, or a sort preference
     * for all albums), in the desired order.
     *
     * @static
     * @access private
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @return array ordered album data
     */
     function _getOrderedAlbums($match) {
        $useKeys = strpos($match[1], '|');
        if ($useKeys) {
            $albumKeys = explode('|', $match[1]);
            $conditions = "WHERE ALBUM_KEY IN ('" . implode("','", $albumKeys) . "')";
        }
        
        else {
            $conditions = "ORDER BY " . $match[1];
        } 
        
        $albums = ShashinAlbum::getAlbums($conditions);

        if ($albums === false) {
            return '<span class="shashin_error">Error: unable to retrive albums</span>';
        }

        // the data doesn't come back from the database in the order it was
        // requested, so re-order it.
        if ($useKeys) {
            $ordered = array();
            foreach ($albumKeys as $key) {
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
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @return string the xhtml markup to display the image
     */   
    function _getDivMarkup($match) {
        // set the location
        if (strtolower(trim($match[2])) == 'y') {
            $location = $this->data['location'];
        }

        // set the pubdate
        if (strtolower(trim($match[3])) == 'y') {
            $date = $this->data['pub_date'];
        }

        // set the float value
        if (strlen(trim($match[4]))) {
            $float = $match[4];
        }

        // set the clear value
        if (strlen(trim($match[4]))) {
            $clear = $match[4];
        }
        
        $divPadding = get_option("shashin_div_padding");
        $replace = '<div class="shashin_album" style="width: '
            . (SHASHIN_ALBUM_THUMB_SIZE + $divPadding) . 'px;';
            
        if (strlen($float)) {
            $replace .= ' float: ' . $float . ';';
        }

        if (strlen($clear)) {
            $replace .= ' clear: ' . $clear . ';';
        }
        
        $replace .=  '">';
        
        $replace .= $this->getAlbumThumbTag();

        $replace .= '<span class="shashin_album_title">'
            . '<a href="' . $this->getAlbumLink() .'">'
            . $this->data['title']
            . "</a></span>";
            
        $replace .= '<span class="shashin_album_count">' . $this->data['photo_count'] . " picture";
        $replace .= ($this->data['photo_count'] > 1) ? 's' : '';
        $replace .= '</span>';

        if (strlen($location)) {
            $replace .= '<span class="shashin_album_location">'
            . ($this->data['geo_pos']
                ? (' <a href="' . GOOGLE_MAPS_QUERY_URL
                    . str_replace(" ", "+", $this->data['geo_pos'])
                    . '"><img src="' . SHASHIN_DISPLAY_URL
                    . 'mapped_sm.gif" alt="Google Maps Location" width="15" height="12" style="border: none;" />')
                : '')
            . ($this->data['geo_pos'] ? '</a><br />' : '')
            . $location
            . "</span>"; 
        }
        
        if (strlen($date)) {
            $replace .= '<span class="shashin_album_date">' . date("M j, Y", $date) . "</span>"; 
        }

        $replace .= "</div>";

        return $replace;
    }
    
    /**
     * Generates the xhtml markup for an album thumbnail, with an appropriate
     * link for accessing the album photos
     *
     * @uses ShashinAlbum::getAlbumLink()
     * @return string xhtml markup for album thumbnail
     */
     function getAlbumThumbTag() {
        $link = $this->getAlbumLink();
        $markup = '<a href="' . $link .'">';
        $markup .= '<img src="' . $this->data['cover_photo_url']
            . '" alt="' . $this->data['title']
            . '" width="' . SHASHIN_ALBUM_THUMB_SIZE
            . '" height="' . SHASHIN_ALBUM_THUMB_SIZE . '" />';
        $markup .= "</a>";
    
        return $markup;
    }
    
    /**
     * Generates a link to an album's photo, either at Picasa or locally,
     * depending on the option shashin_album_photos_url
     *
     * @return string url ready for use in an anchor tag
     */
    function getAlbumLink() {
        $albumUrl = get_option('shashin_album_photos_url');
        
        if (strlen($albumUrl)) {
            
            $link = $albumUrl
                . ((strpos($albumUrl, "?") === false) ? "?" : "&amp;")
                . "album_id=" . $this->data['album_id']
                . '&amp;title=' . urlencode($this->data['title']);
        }
        
        else {
            $link = $this->data['link_url'];
        }

        return $link;
    }
    
    /**
     * Generates an xhtml table containing thumbnails of the the passed in
     * albums. Note that $albums is an array of arrays of album data, not
     * ShashinAlbum objects.
     *
     * @static
     * @access private
     * @param array $albums array of arrays containing album data
     * @param int $cols the number of columns for the table
     * @param string $locationYN y or n flag indicating whether to show the album location
     * @param string $pubdateYN y or n flag indicating whether to show the album pubdate
     * @param string $float an optional css float value
     * @param string $clear an optional css clear value
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::_getDivMarkup()
     * @return string xhtml markup for the table containing the photos
     */    
    function _getTableMarkup($albums, $cols, $locationYN, $pubdateYN, $float = null, $clear = null) {
        $replace = '<table class="shashin_album_thumbs_table"';

        if (strlen($float) || strlen($clear)) {
            $replace .= ' style="';

            if (strlen($float)) {
                $replace .= "float:$float;";
            }

            if (strlen($clear)) {
                $replace .= "clear:$clear;";
            }
            
            $replace .= '"';
        }

        $replace .= '>' . "\n";
        $cellCount = 1;

        for ($i = 0; $i < count($albums); $i++) {
            if ($cellCount == 1) {
                $replace .= "<tr>\n";
            }
            
            $album = new ShashinAlbum();
  
            if ($album->getAlbum(null, null, $albums[$i]) === false) {
                return '<span class="shashin_error">Error: unable to set album object for album_key ' . $albums[$i]['album_key'] . '</span>';
            }

            $markup = $album->_getDivMarkup(array(null, null, $locationYN, $pubdateYN));
            $replace .= "<td>$markup</td>\n";
            $cellCount++;
            
            if ($cellCount > $cols || $i == (count($albums) - 1)) {
                $replace .= "</tr>\n";
                $cellCount = 1;
            }
        }
            
        $replace .= "</table>";
        return $replace;
    }
}

?>