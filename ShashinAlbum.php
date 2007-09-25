<?php
/**
 * ShashinAlbum class file.
 *
 * @author Michael Toppa
 * @version 1.0.6
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007 Michael Toppa
 * 
 * This file is part of Shashin.
 *
 * Shashin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shashin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
                'label' => 'Name', 'source' => 'user',
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
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Description', 'source' => 'feed',
                'feedParam1' => 'description'),
            'location' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Location', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'location'),
            'cover_photo_url' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Cover Photo URL', 'source' => 'feed',
                'feedParam1' => 'media', 'feedParam2' => 'group_thumbnail@url'),
            'last_updated_timestamp' => array(
                'colParams' => array('type' => 'varchar', 'length' => '20'),
                'label' => 'Last Updated', 'source' => 'user'),
            'photo_count' => array(
                'colParams' => array('type' => 'int unsigned',  'notNull' => 1),
                'label' => 'Photo Count', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'numphotos'),
            'pub_date' => array(
                'colParams' => array('type' => 'varchar', 'length' => '20', 'notNull' => 1),
                'label' => 'Pub Date', 'source' => 'feed',
                'feedParam1' => 'date_timestamp'),
            'geo_pos' => array(
                'colParams' => array('type' => 'varchar', 'length' => '25'),
                'label' => 'Pub Date', 'source' => 'feed',
                'feedParam1' => 'gml', 'feedParam2' => 'where_point_pos'),
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
            
            else {
                $this->data = $row;
            }
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
        $feedContent = ToppaWPFunctions::readFeed($feedURL, false);
        
        if (strlen($albumName)) {
            $albumData = ToppaWPFunctions::parseFeed($feedContent, $this->refData, 'name', $albumName);
            $exists = $this->getAlbum(null, $albumName);
            $albumData['name'] = $albumName;
        }
        
        elseif (strlen($albumID)) {
            $albumData = ToppaWPFunctions::parseFeed($feedContent, $this->refData, 'album_id', $albumID);
            $exists = $this->getAlbum($albumID);
            $albumData['album_id'] = $albumID;
        }
        
        else {
            return false;
        }
        
        $albumData['last_updated_timestamp'] = time();

        // don't alter the current include_in_random if no new value is provided
        // and make sure it's a valid length (1 char)
        if (strlen($includeRandom) == 1) {
            $albumData['include_in_random'] = $includeRandom;
        }

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
     * @uses ToppaWPFunctions::select()
     * @return boolean true: retrieval succeeded; false: retrieval failed
     */    
    function getAlbumPhotos($orderBy = 'PHOTO_ID', $limit = null) {
        $where = "WHERE ALBUM_ID = '" . $this->data['album_id'] . "' ORDER BY $orderBy";
        
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
        $feedContent = ToppaWPFunctions::readFeed($feedURL, false);
        $newPhotos = ToppaWPFunctions::parseFeed($feedContent, $photo->refData);

        if ($this->getAlbumPhotos() === false) {
            return false;
        }

        $updateIDs = array();
        $foundIDs = array();
        $unchangedIDs = array();
        $inserts = array();
        $updates = array();
        foreach ($newPhotos as $newPhoto) {
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
        
        // do inserts
        foreach($inserts as $insert) {
            $retVal = ToppaWPFunctions::insert(SHASHIN_PHOTO_TABLE, $insert);
            
            if ($retVal === false) {
                return false;
            } 
        }

        // do deletes
        if (!empty($foundIDs)) {
            foreach ($this->data['photos'] as $oldPhoto) {
                if (!in_array($oldPhoto['photo_id'], $foundIDs)) {
                    $where = "photo_id = '{$oldPhoto['photo_id']}'";
                    $retVal = ToppaWPFunctions::delete(SHASHIN_PHOTO_TABLE, $where);
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
     * @param string $randomFlag y or n
     * @uses ToppaWPFunctions::update()
     * @return boolean true: update succeeded; false: update failed
     */    
    function setIncludeInRandom($randomFlag) {
        $retVal = ToppaWPFunctions::update(SHASHIN_ALBUM_TABLE, "album_id = '{$this->data['album_id']}'",
            array('include_in_random' => $randomFlag, 'last_updated_timestamp' => time()));

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
     * @param string $where an optional SQL where clause (can include order by, etc as well)
     * @uses ToppaWPFunctions::select()
     * @return array An array of hashes containing album data
     */        
    // call this statically
    function getAlbums($where = null) {
        return ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, '*', $where, 'get_results');
    }

    /**
     * Translates the "salbum" Shashin tag into xhtml displaying the thumbnail
     * and title of an album, with a hyperlink to the album at Picasa. Thumbnail
     * size is fixed at 160x160.
     * 
     * salbum tag: [salbum=album_key,location_yn,pubdate_yn,float,clear]
     * php call: echo $photo->getAlbumMarkup(array(null,album_key,'location_yn','pubdate_yn','float','clear'));
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete salbum tag
     * - Album Key (required): the Shashin album_key (not the Picasa album ID)
     * - Location (optional): y or n to show the location of the image, with a link to Google Maps.
     * - Pub Date (optional): y or n to show the pub date of the album
     * - float (optional): a css float value (left, right, or none)
     * - Clear (optional): a css clear value (left, right, or both)
     *
     * Example:
     * - [0] => [salbum=1,y,y,left,both]
     * - [1] => 1
     * - [2] => y
     * - [3] => y
     * - [4] => left
     * - [5] => both
     *
     * @static
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
        if (strlen(trim($match[5]))) {
            $clear = $match[5];
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
        $replace .= '<a href="' . $this->data['link_url'] .'">';
        $replace .= '<img src="' . $this->data['cover_photo_url']
            . '" alt="' . htmlspecialchars($this->data['description'])
            . '" width="' . SHASHIN_ALBUM_THUMB_SIZE
            . '" height="' . SHASHIN_ALBUM_THUMB_SIZE . '" />';
        $replace .= '</a>';

        $replace .= '<span class="shashin_album_title">'
            . '<a href="' . $this->data['link_url'] .'">'
            . $this->data['title']
            . '</a> (' . $this->data['photo_count'] . ')</span>';

        if (strlen($location)) {
            $replace .= '<span class="shashin_album_location">'
            . $location
            . ($this->data['geo_pos']
                ? (' <a href="' . GOOGLE_MAPS_QUERY_URL
                    . str_replace(" ", "+", $this->data['geo_pos'])
                    . '"><img src="' . SHASHIN_DISPLAY_URL
                    . 'mapped_sm.gif" alt="Google Maps Location" width="15" height="12" style="vertical-align: text-bottom; border: none;" />')
                : '')
            . ($this->data['geo_pos'] ? '</a>' : '')
            . '</span>'; 
        }
        
        if (strlen($date)) {
            $replace .= '<span class="shashin_album_date">' . date("M j, Y", $date) . '</span>'; 
        }

        $replace .= "</div>";
        
        return $replace;
    }
}

?>