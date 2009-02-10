<?php
/**
 * ShashinPhoto class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3.4
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
    var $ref_data;
    var $data;

    /**
     * The constructor sets $this->ref_data, which maps Picasa photo
     * properties to ShashinPhoto object properties. It's also used for
     * creating the shashin_photo table and for generating form input
     * fields.
     *
     * @access public
     */
    function ShashinPhoto() {
        $this->ref_data = array(
            'photo_key' => array(
                'col_params' => array('type' => 'int unsigned', 'not_null' => true,
                    'primary_key' => true, 'other' => 'AUTO_INCREMENT'),
                'label' => 'Photo Key', 'source' => 'db'),
            'photo_id' => array(
                'col_params' => array('type' => 'bigint unsigned',
                    'not_null' => true, 'other' => 'UNIQUE'),
                'label' => 'Photo ID', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'id'),
            'album_id' => array(
                'col_params' => array('type' => 'bigint unsigned', 'not_null' => true),
                'label' => 'Album ID', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'albumid'),
            'title' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Title', 'source' => 'feed',
                'feed_param_1' => 'title'),
            'description' => array(
                'col_params' => array('type' => 'text'),
                'label' => 'Description', 'source' => 'feed',
                'feed_param_1' => 'description'),
            'link_url' => array(
                'col_params' => array('type' => 'text', 'not_null' => true),
                'label' => 'Link URL', 'source' => 'feed',
                'feed_param_1' => 'link'),
            'content_url' => array(
                'col_params' => array('type' => 'text', 'not_null' => true),
                'label' => 'Content URL', 'source' => 'feed',
                'feed_param_1' => 'media', 'feed_param_2' => 'content', 'attrs' => 'url'),
            'width' => array(
                'col_params' => array('type' => 'smallint unsigned', 'not_null' => true),
                'label' => 'Width', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'width'),
            'height' => array(
                'col_params' => array('type' => 'smallint unsigned', 'not_null' => true),
                'label' => 'Height', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'height'),
            'taken_timestamp' => array(
                'col_params' => array('type' => 'int unsigned', 'not_null' => true),
                'label' => 'Date taken', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'time'),
            'uploaded_timestamp' => array(
                'col_params' => array('type' => 'int unsigned', 'not_null' => true),
                'label' => 'Date Uploaded', 'source' => 'feed',
                'feed_param_1' => 'gphoto', 'feed_param_2' => 'timestamp'),
            'tags' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Tags', 'source' => 'feed',
                'feed_param_1' => 'media', 'feed_param_2' => 'keywords'),
            'include_in_random' => array(
                'col_params' => array('type' => 'char', 'length' => '1', 'other' => "default 'Y'"),
                'label' => 'Include in random photo display', 'source' => 'user',
                'input_type' => 'radio',
                'input_subgroup' => array('Y' => __("Yes", SHASHIN_L10N_NAME), 'N' => __("No", SHASHIN_L10N_NAME))),
            'deleted' => array(
                'col_params' => array('type' => 'char', 'length' => '1', 'other' => "default 'N'"),
                'label' => 'Deleted flag', 'source' => 'db'),
            'enclosure_url' => array(
                'col_params' => array('type' => 'text', 'not_null' => true),
                'label' => 'Enclosure URL', 'source' => 'feed',
                'feed_param_1' => 'enclosure', 'attrs' => 'url'),
            'enclosure_type' => array(
                'col_params' => array('type' => 'varchar', 'length' => '255', 'not_null' => true),
                'label' => 'Enclosure Type', 'source' => 'feed',
                'feed_param_1' => 'enclosure', 'attrs' => 'type'),
        );
    }

    /**
     * Populates a ShashinPhoto object based on an identifier (can be a
     * Picasa ID, photo title, or Shashin key) or a passed-in array of photo
     * data.
     *
     * @access public
     * @param array $photo_identifier a key-value pair (e.g. 'photo_id' => 37)
     * @param array $photo_data a complete array of photo data
     * @return array 0: true on success, false on failure, null if photo not found; 1: message; 2: true if SQL error
     */
    function getPhoto($photo_identifier = null, $photo_data = null) {
        if (is_array($photo_identifier)) {
            $row = ToppaWPFunctions::sqlSelect(SHASHIN_PHOTO_TABLE, '*', $photo_identifier);

            if ($row === false) {
                return array(false, __("ShashinPhoto::getPhoto - Failed to retrieve photo. SQL Error:", SHASHIN_L10N_NAME), true);
            }

            elseif (empty($row)) {
                return array(null, __("Photo not found.", SHASHIN_L10N_NAME));
            }

            $row['description'] = htmlspecialchars($row['description'], ENT_COMPAT, 'UTF-8');
            $this->data = $row;
        }

        elseif (is_array($photo_data)) {
            $photo_data['description'] = htmlspecialchars($photo_data['description'], ENT_COMPAT, 'UTF-8');
            $this->data = $photo_data;
        }

        else {
            return array(false, __("ShashinPhoto::getPhoto - Called with invalid arguments.", SHASHIN_L10N_NAME));
        }

        return array(true, __("Photo retrieved."));
    }

    /**
     * Updates local photo data (i.e. data that doesn't come from the
     * Picasa RSS feed).
     *
     * @access public
     * @param array $data A hash of photo data (keys are column names)
     * @return array 0: true on success, false on failure; 1: message; 2: true if SQL error
     */
    function setPhotoLocal($data) {
        if (!is_array($data)) {
            return array(false, __("ShashinPhoto::setPhotoLocal - Called with invalid arguments.", SHASHIN_L10N_NAME));
        }

        $sql_result = ToppaWPFunctions::sqlUpdate(SHASHIN_PHOTO_TABLE, $data, array('photo_id' => $this->data['photo_id']));

        if ($sql_result === false) {
            return array(false, sprintf(__("ShashinPhoto::getPhotoLocal - Failed to update record for photo ID %d. SQL Error:", SHASHIN_L10N_NAME), $this->data['photo_id']), true);
        }

        return array(true, __("Photo updated."));
    }

    /**
     * A static method for retrieving an arbitrary set of photos.
     *
     * @static
     * @access public
     * @param string|array $keywords the fields to return
     * @param string|array $conditions (optional) array of key-values pairs, or a string containing its own WHERE clause
     * @param string $other (optional) any additional conditions for the query (GROUP BY, etc.)
     * @uses ToppaWPFunctions::sqlSelect()
     * @return array An array of hashes containing photo data
     */
    function getPhotos($keywords = 'sp.*', $conditions = null, $other = null) {
        return ToppaWPFunctions::sqlSelect(SHASHIN_PHOTO_TABLE . " sp", $keywords, $conditions, $other, 'get_results');
    }

    /**
     * Translates the "simage" Shashin tag into xhtml displaying the
     * specified photo, with a hyperlink to the photo (either in
     * Highslide or at Picasa).
     *
     * @access public
     * @param array $match see detailed list in Shashin::parseContent()
     * @param boolean $admin whether we are displaying thumbnails on the admin page
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::_getDivMarkup()
     * @return string xhtml markup for displaying a single image
     */
    function getPhotoMarkup($match, $admin = false) {
        // get the image, if we don't have it already
        if (!$this->data['photo_id']) {
            list($result, $message, $db_error) = $this->getPhoto(array('photo_key' => $match['photo_key']));

            if (!$result) {
                return '<span class="shashin_error">' . __("Shashin error:", SHASHIN_L10N_NAME) . ' ' . $message . '</span>';
            }
        }

        // for display in the admin menu, set the last arg (for the
        // highslide controller) to true. We don't actually have one on
        // the admin side, but this will force the correct behavior for
        // captions, since we don't have highslide running on the admin
        // side.
        return $this->_getDivMarkup($match, false, null, false, $admin);
    }

    /**
     * Translates the "srandom" Shashin tag into xhtml displaying a
     * table of random photos. If album keys are not provided, then it will pick
     * from among all photos.
     *
     * Note that if an album has include_in_random=n, no photos from it
     * will be displayed, even if the photos in it have include_in_random=y.
     *
     * @static
     * @access public
     * @param array $match see details in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the srandom tag with
     */
    function getRandomMarkup($match) {
        $conditions = " inner join " . SHASHIN_ALBUM_TABLE
            . " sa where sp.include_in_random = 'Y' and "
            . "sa.include_in_random = 'Y' and sp.deleted = 'N' "
            . "and sa.album_id = sp.album_id";

        if ($match['album_key'] != 'any') {
            $conditions .= " and album_key in (" . implode(",", explode("|", $match['album_key'])) . ")";
        }

        $other = "order by rand() limit " . $match['how_many'];
        $photos = ShashinPhoto::getPhotos('sp.*', $conditions, $other);

        if (!$photos) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve random photos.", SHASHIN_L10N_NAME) . '</span>';
        }

        return ShashinPhoto::_getTableMarkup($photos, $match);
    }

    /**
     * Translates the "snewest" Shashin tag into xhtml displaying a
     * table of thumbnails of your newest photos (by upload date). If album
     * keys are not provided, then it will pick from among all photos.
     *
     * @static
     * @access public
     * @param array $match see details in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the snewest tag with
     */
    function getNewestMarkup($match) {
        if ($match['album_key'] != 'any') {
            $conditions = " inner join " . SHASHIN_ALBUM_TABLE
                . " sa where sa.album_id = sp.album_id and album_key in ("
                . implode(",", explode("|", $match['album_key'])) . ")";
        }

        $other = "order by uploaded_timestamp desc limit " . $match['how_many'];
        $photos = ShashinPhoto::getPhotos('sp.*', $conditions, $other);

        if (!$photos) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve newest photos.", SHASHIN_L10N_NAME) . '</span>';
        }

        return ShashinPhoto::_getTableMarkup($photos, $match);
    }

    /**
     * Static method that translates the "sthumbs" Shashin tag into an
     * xhtml table displaying the specified thumbnails, each with a
     * hyperlink to the photo (either in Highslide or at Picasa).
     *
     * @static
     * @access public
     * @param array $match see details in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the sthumbs tag with
     */
    function getThumbsMarkup($match) {
        $photo_keys = explode("|", $match['photo_key']);
        $conditions = "where photo_key in ('" . implode("','", $photo_keys) . "') and deleted = 'N'";
        $photos = ShashinPhoto::getPhotos('*', $conditions);

        if (!$photos) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve photo thumbnails.", SHASHIN_L10N_NAME) . '</span>';
        }

        // the data doesn't come back from the database in the order it was
        // requested, so re-order it.
        $ordered = array();
        foreach ($photo_keys as $key) {
            foreach ($photos as $photo) {
                if ($key == $photo['photo_key']) {
                    $ordered[] = $photo;
                }
            }
        }

        return ShashinPhoto::_getTableMarkup($ordered, $match);
    }

    /**
     * Static method that display thumbnails for an album. Can be invoked by
     * the salbumphotos tag, or by a $_REQUEST flag when an album cover
     * thumbnail is clicked.
     *
     * @static
     * @access public
     * @param array $match see details in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @return string the xhtml markup to replace the sthumbs tag with
     */
    function getAlbumPhotosMarkup($match) {
        // the is_numeric check also provides a de facto check on XSS attacks
        if (is_numeric($_REQUEST['shashin_album_key'])) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $match['album_key'] = $_REQUEST['shashin_album_key'];
            $match['max_size'] = $shashin_options['album_photos_max'];
            $match['max_cols'] = $shashin_options['album_photos_cols'];
            $match['caption_yn'] = $shashin_options['album_photos_captions'];
            $match['description_yn'] = $shashin_options['album_photos_description'];
            $match['order_by'] = $shashin_options['album_photos_order'];
        }

        $order = $match['order_by'] ? $match['order_by'] : 'taken_timestamp';
        $conditions = " inner join " . SHASHIN_ALBUM_TABLE
            . " sa where sa.album_id = sp.album_id and sp.deleted = 'N' and sa.album_key = "
            . $match['album_key'];
        $other = "order by $order";
        $photos = ShashinPhoto::getPhotos('sp.*, sa.title as album_title, sa.description as album_description', $conditions, $other);

        if (!$photos) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve album photos.", SHASHIN_L10N_NAME) . '</span>';
        }

        // for accessing the album title and description
        $photo = current($photos);

        $desc = '<span class="shashin_caption_return"><a href="' . get_permalink() . '">&laquo; ' . __("Go back", SHASHIN_L10N_NAME) . '</a></span>';
        $desc .= '<span class="shashin_caption_title">' . $photo['album_title']  . '</span>';

        if ($photo['album_description'] &&  $match['description_yn'] == 'y') {
            $desc .= ' <span class="shashin_caption_description">' . preg_replace("/\s/", " ", $photo['album_description']) . '</span>';
        }

        return ShashinPhoto::_getTableMarkup($photos, $match, $desc);
    }

    /**
     * Generates an xhtml table containing the passed in photos. Note that
     * $photos is an array of arrays of photo data, not ShashinPhoto objects.
     *
     * @static
     * @access private
     * @param array $photos array of arrays containing photo data
     * @param array $match data for customizing the markup
     * @param string $desc an optional album description to display (as a table caption)
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::_getDivMarkup()
     * @return string xhtml markup for the table containing the photos
     */
    function _getTableMarkup($photos, $match, $desc = null) {
        $replace = '<table class="shashin_thumbs_table"';

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
        $replace .=  $desc ? "<caption>$desc</caption>\n" : '';
        $cell_count = 1;

        for ($i = 0; $i < count($photos); $i++) {
            $replace .= ($cell_count == 1) ? "<tr>\n" : '';
            $photo = new ShashinPhoto();
            list($result, $message, $db_error) = $photo->getPhoto(null, $photos[$i]);

            if (!$result) {
                return '<span class="shashin_error">' . __("Shashin Error:", SHASHIN_L10N_NAME) . ' ' . $message . '</span>';
            }

            $markup = $photo->_getDivMarkup($match, true, $_SESSION['hs_group_counter'], true);
            $replace .= "<td>$markup</td>\n";
            $cell_count++;

            if ($cell_count > $match['max_cols'] || $i == (count($photos) - 1)) {
                $replace .= "</tr>\n";
                $cell_count = 1;
            }
        }

        $replace .= "</table>";
        $replace .= "\n<script type=\"text/javascript\">\naddHSSlideshow('group" . $_SESSION['hs_group_counter'] . "');\n</script>\n";
        $_SESSION['hs_group_counter']++;
        return $replace;
    }

    /**
     * Calculates the height or width for an image, based on $max. See
     * See http://code.google.com/apis/picasaweb/reference.html for an
     * explanation of the supported image sizes.
     *
     * $max is your desired maximum image dimension. It will be applied to the
     * width of the image if it has a landscape orientation, or to the height if
     * it has a portrait orientation (that's how Picasa works). The
     * dimensions are added to $this->data as user_width, user_height, and
     * user_max
     *
     * @access private
     * @param int $max the maximum desired image dimension (could be width or height)
     * @return boolean true: set dimension successfully; false: failed to set dimension
     */
    function _setDimensions($max) {
        $shashin_image_sizes = unserialize(SHASHIN_IMAGE_SIZES);
        $shashin_crop_sizes = unserialize(SHASHIN_CROP_SIZES);

        if (!in_array($max, $shashin_image_sizes)) {
            return false;
        }

        // we'll need this later for constructing the url
        $this->data['user_max'] = $max;

        // if it's a cropped size, then $max applies to the height and width
        if (in_array($max, $shashin_crop_sizes)) {
            $this->data['user_width'] = $max;
            $this->data['user_height'] = $max;
            $this->data['user_max'] = $this->data['user_max'] . '&amp;crop=1';
        }

        // see if $max should be applied to the height or the width
        elseif ($this->data['width'] > $this->data['height']) {
            $this->data['user_width'] = $max;
            $percentage = $max / $this->data['width'];
            $this->data['user_height'] = $percentage * $this->data['height'];
            settype($this->data['user_height'], "int"); // drop any decimals
        }

        else {
            $this->data['user_height'] = $max;
            $percentage = $max / $this->data['height'];
            $this->data['user_width'] = $percentage * $this->data['width'];
            settype($this->data['user_width'], "int"); // drop any decimals
        }

        return true;
    }

    /**
     * Generates the xhtml div for displaying an image.
     *
     * @access private
     * @param array $match see detailed list in ShashinPhoto::getPhotoMarkup()
     * @param boolean $thumb if true the shashin_thumb_padding class will be applied
     * @param integer $group a group ID number for Highslide
     * @param boolean $controller whether the image display has a Highslide controller
     * @uses ShashinPhoto::_setDimensions()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @return string the xhtml markup to display the image
     */
    function _getDivMarkup($match, $thumb = false, $group = null, $controller = false, $admin = false) {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        // set the dimensions
        if ($this->_setDimensions($match['max_size']) === false) {
            return '<span class="shashin_error">Shashin Error: invalid size for image</span>';
        }

        // set the caption to include the album name if that option is set
        if ($shashin_options['prefix_captions'] == 'y') {
            $album = new ShashinAlbum();
            $album->getAlbum($this->data['album_id']);
            $caption = $album->data['title'] . " &ndash; " . $this->data['description'];
        }

        else {
            $caption = $this->data['description'];
        }

        // a caption on the thumbnail is optional
        if ($match['caption_yn'] == 'y') {
            $opt_caption = $caption;
        }

        // 'enlarge' or 'play' as a caption option
        else if ($match['caption_yn'] == 'c') {
            $opt_caption = $this->_isVideo() ? 'Click picture to play video' : 'Click picture to enlarge';
        }

        $class = $thumb ? 'shashin_thumb' : 'shashin_image';
        $padding = $thumb ? $shashin_options['thumb_padding'] : $shashin_options['div_padding'];
        $markup = '<div class="' . $class . '" style="width: ' . ($this->data['user_width'] + $padding) . 'px;';
        $markup .= is_string($match['float']) ? " float: {$match['float']};" : "";
        $markup .= is_string($match['clear']) ? " clear: {$match['clear']};" : "";
        $markup .=  '">';
        $autoplay = $shashin_options['highslide_autoplay'];

        if ($shashin_options['image_display'] == 'none' || $admin) {
            $markup .= ''; // no link! :-)
        }

        // videos in highslide
        else if ($shashin_options['image_display'] == 'highslide' && $this->_isVideo()) {
            $video_url = SHASHIN_GOOGLE_PLAYER_URL
                . urlencode(html_entity_decode($this->data['content_url']))
                . '&amp;autoPlay=true';
            $width = $shashin_options['highslide_video_width'];
            $height = $shashin_options['highslide_video_height'];

            // need minWidth because width was not autosizing for content
            // need "preserveContent: false" so the video and audio will stop when the window is closed
            $markup .= "<a href=\"$video_url\" onclick=\"return hs.htmlExpand(this,{ objectType:'swf', minWidth: "
                . ($width+20) . ", minHeight: " . ($height+20)
                . ", objectWidth: $width, objectHeight: $height, allowSizeReduction: false, preserveContent: false";

            if ($group) {
                $markup .= ", autoplay: $autoplay, slideshowGroup: 'group" . $_SESSION['hs_group_counter'] . "'";
            }

            $markup .= ' } )" class="highslide">';
            $_SESSION['hs_id_counter']++;
        }

        // images in highslide
        else if ($shashin_options['image_display'] == 'highslide') {
            $markup .= '<a href="' . $this->data['enclosure_url']
                . '?imgmax=' . $shashin_options['highslide_max']
                . '" class="highslide" id="thumb' . $_SESSION['hs_id_counter']
                . '" onclick="return hs.expand(this';

            if ($group) {
                $markup .= ", { autoplay: $autoplay, slideshowGroup: 'group" . $_SESSION['hs_group_counter'] . "' }";
            }

            $markup .= ')">';
            $_SESSION['hs_id_counter']++;
        }

        // images or videos at Picasa
        else {
            $markup .= '<a href="' . $this->data['link_url'] . '"';

            if ($shashin_options['image_display'] == 'new_window') {
                $markup .= ' target="_blank"';
            }

            $markup .= '>';
        }

        $markup .= '<img src="' . $this->data['enclosure_url']
            . '?imgmax='. $this->data['user_max']
            . '" alt="' . $caption
            . '" title="' . $caption
            . '" width="' . $this->data['user_width']
            . '" height="' . $this->data['user_height'] . '" />';

        if ($shashin_options['image_display'] != 'none') {
            $markup .= '</a>';
        }

        // whether to display the caption under the photo
        if ($opt_caption) {
            $markup .= '<span class="shashin_caption">' . $opt_caption . '</span>';
        }

        if ($caption && $shashin_options['image_display'] == 'highslide' && !$admin) {
            $markup .= '<div class="highslide-caption">' . $caption . '</div>';
        }

        $markup .= "</div>";
        return $markup;
    }

    /**
     * Test to see whether we're dealing with a picture or a video
     *
     * @access private
     * @return boolean
     */
    function _isVideo() {
        $video_types = unserialize(SHASHIN_PICASA_VIDEO_TYPES);
        $ext = strtoupper(substr($this->data['title'], -3));
        if (in_array($ext, $video_types)) {
            return true;
        }
        return false;
    }
}

?>
