<?php
/**
 * ShashinPhoto class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
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
                    'not_null' => true, 'unique_key' => true),
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
                'feed_param_1' => 'pubDate'),
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
            'picasa_order' => array(
                'col_params' => array('type' => 'int unsigned'),
                'label' => 'Picasa Order', 'source' => 'db'),
            'fstop' => array(
                'col_params' => array('type' => 'varchar', 'length' => '10'),
                'label' => 'F-Stop', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'fstop'),
            'make' => array(
                'col_params' => array('type' => 'varchar', 'length' => '20'),
                'label' => 'Make', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'make'),
            'model' => array(
                'col_params' => array('type' => 'varchar', 'length' => '20'),
                'label' => 'Model', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'model'),
            'exposure' => array(
                'col_params' => array('type' => 'varchar', 'length' => '10'),
                'label' => 'Model', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'exposure'),
            'focal_length' => array(
                'col_params' => array('type' => 'varchar', 'length' => '10'),
                'label' => 'Focal Length', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'focallength'),
            'iso' => array(
                'col_params' => array('type' => 'varchar', 'length' => '10'),
                'label' => 'ISO', 'source' => 'feed',
                'feed_param_1' => 'exif', 'feed_param_2' => 'iso'),
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

        /* for future use
        if ($match['alt_thumbs']) {
            $alt_keys = explode(",", $match['alt_thumbs']);
            $conditions = "where photo_key in ('" . implode("','", $alt_keys) . "') and deleted = 'N'";
            $alt_photos = ShashinPhoto::getPhotos('*', $conditions);

            if (!$alt_photos) {
                return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve photo thumbnails.", SHASHIN_L10N_NAME) . '</span>';
            }
        }
        */

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

        /* for future use
        if ($alt_photos) {
            $alt_ordered = array();
            foreach ($alt_keys as $key) {
                foreach ($alt_photos as $photo) {
                    if ($key == $photo['photo_key']) {
                        $alt_ordered[] = $photo;
                    }
                }
            }
        }

        return ShashinPhoto::_getTableMarkup($ordered, $match, null, $alt_ordered);
        */
        return ShashinPhoto::_getTableMarkup($ordered, $match);
    }

    /**
     * Static method that display thumbnails for photos in an album. Can be
     * invoked by the salbumphotos tag, or with $_REQUEST['shashin_album_key']
     * when an album cover thumbnail is clicked.
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
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        // check to see if we're making a list of photos from salbumphotos
        // (which has max_size), or from salbumthumbs/salbumlist (which doesn't)
        $salbumphotos = $match['max_size'] ? true : false;

        // the is_numeric check also provides a de facto check on XSS attacks
        if (is_numeric($_REQUEST['shashin_album_key'])) {
            $match['album_key'] = $_REQUEST['shashin_album_key'];
            $match['max_size'] = $shashin_options['album_photos_max'];
            $match['max_cols'] = $shashin_options['album_photos_cols'];
            $match['caption_yn'] = $shashin_options['album_photos_captions'];
            $match['description_yn'] = $shashin_options['album_photos_description'];
            $match['order_by'] = $shashin_options['album_photos_order'];
        }

        $order = $match['order_by'] ? $match['order_by'] : 'picasa_order';
        $conditions = " inner join " . SHASHIN_ALBUM_TABLE
            . " sa where sa.album_id = sp.album_id and sp.deleted = 'N' and sa.album_key = "
            . $match['album_key'];

        $other = "order by $order";

        // limit photos per page if requested
        if (is_numeric($shashin_options['photos_per_page'])) {
            $shashin_page = is_numeric($_REQUEST['shashin_page']) ? $_REQUEST['shashin_page'] : 1;
            $other .= " limit " . $shashin_options['photos_per_page']
                . " offset " . ($shashin_page - 1) * $shashin_options['photos_per_page'];
        }

        $photos = ShashinPhoto::getPhotos('sp.*, sa.title as album_title, sa.description as album_description', $conditions, $other);

        if (!$photos) {
            return '<span class="shashin_error">' . __("Shashin Error: unable to retrieve album photos.", SHASHIN_L10N_NAME) . '</span>';
        }

        // for accessing the album title and description
        $photo = current($photos);

        $desc = "";

        if (!$salbumphotos) {
            $desc .= '<span class="shashin_caption_return"><a href="' . get_permalink() . '">' . __("Return to album list", SHASHIN_L10N_NAME) . '</a></span>';
        }

        $desc .= '<span class="shashin_caption_title">' . $photo['album_title']  . '</span>';

        if ($photo['album_description'] &&  $match['description_yn'] == 'y') {
            $desc .= ' <span class="shashin_caption_description">' . preg_replace("/\s/", " ", $photo['album_description']) . '</span>';
        }

        // build next and previous links if we're limiting photos per page
        if ($shashin_page) {
            if (!$_SESSION['shashin_last_page_' . $match['album_key']]) {
                $where = 'where album_id in (select album_id from ' . SHASHIN_ALBUM_TABLE
                    . ' where album_key = ' . $match['album_key'] . ')';
                $row_count = ToppaWPFunctions::sqlSelect(SHASHIN_PHOTO_TABLE, 'count(photo_id)', $where, null, 'get_var');
                $_SESSION['shashin_last_page_' . $match['album_key']] = ceil($row_count / $shashin_options['photos_per_page']);
            }

            $permalink = get_permalink();
            $glue = strpos($permalink, "?") ? "&amp;" : "?";
            $link =  $permalink . $glue . 'shashin_album_key=' . $match['album_key'];
            $desc .= '<span class="shashin_nav">';

            if ($shashin_page > 1) {
                $link_back = $link . '&amp;shashin_page=' . ($shashin_page - 1);
                $desc .= '<span class="shashin_nav_previous"><a href="' . $link_back . '">&laquo; ' . __('Previous', SHASHIN_L10N_NAME) . '</a></span>';
            }

            if ($shashin_page < $_SESSION['shashin_last_page_' . $match['album_key']]) {
                $link_next = $link . '&amp;shashin_page=' . ($shashin_page + 1);
                $desc .= '<span class="shashin_nav_next"><a href="' . $link_next . '">' . __('Next', SHASHIN_L10N_NAME) . ' &raquo;</a></span>';
            }

            $desc .= "</span>\n";
        }

        return ShashinPhoto::_getTableMarkup($photos, $match, $desc);
    }


    /**
     * Finds the maximum possible Picasa image size for a given number
     * of thumbnail columns. Assumes 10px of padding/margin per image.
     *
     * NOTE: The calculation will be incorrect for pictures with a
     * portrait orientation
     *
     * @static
     * @access private
     * @param int $theme_max The content width of the theme, minus padding
     * @param int $cols The number of columns of thumbnails
     * @return int The largest possible allowed Picasa size
     */
    function _setMaxPicasaSize($theme_max, $cols) {
        if (!is_numeric($theme_max) || !is_numeric($cols)) {
            return 0;
        }

        $max_size = $theme_max / $cols;
        $max_size -= 10; // guess for padding/margins per image

        // figure out which allowed Picasa size is closest, but not larger
        // $sizes is ordered from smallest to largest
        $sizes = unserialize(SHASHIN_IMAGE_SIZES);

        for($i=0; $i<count($sizes); $i++) {
            // stop on the first size that's bigger and go back one
            if ($max_size < $sizes[$i]) {
                $picasa_max = $sizes[$i-1];
                break;
            }
        }

        return $picasa_max;
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
    function _getTableMarkup($photos, $match, $desc = null, $alt_thumbs = null) {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        // counter for distinguishing groups of images on a page
        if (!$_SESSION['shashin_group_counter']) {
           $_SESSION['shashin_group_counter'] = 1;
        }

        if ($match['max_size'] == 'max' && $match['max_cols'] == 'max') {
                return '<span class="shashin_error">' . __("Shashin Error: image size and the number of columns cannot both be 'max'", SHASHIN_L10N_NAME) . '</span>';
        }

        // if 'max' is the width, figure out the size for the thumbnails
        // (this will be imperfect if they're all portrait orientation...)
        else if ($match['max_size'] == 'max') {
            $match['max_size'] = ShashinPhoto::_setMaxPicasaSize($shashin_options['theme_max_size'], $match['max_cols']);
        }

        else if ($match['max_cols'] == 'max') {
            $max_cols = $shashin_options['theme_max_size'] / ($match['max_size'] + 10);
            $match['max_cols'] = floor($max_cols);
        }

        $replace = '<table class="shashin_thumbs_table"';

        if ($match['float'] || $match['clear']) {
            $replace .= ' style="';

            if ($match['float'] == 'center') {
                $replace .= "margin-left: auto; margin-right: auto;";
            }

            else if ($match['float']) {
                $replace .= "float: {$match['float']};";
            }

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

            if ($alt_thumbs && ($alt_thumb[$i] != $photos[$i])) {
                $match['alt_thumb'] = $alt_thumbs[$i];
            }

            $markup = $photo->_getDivMarkup($match, true, $_SESSION['shashin_group_counter'], true);
            $replace .= "<td>$markup</td>\n";
            $cell_count++;

            if ($cell_count > $match['max_cols'] || $i == (count($photos) - 1)) {
                $replace .= "</tr>\n";
                $cell_count = 1;
            }
        }

        $replace .= "</table>";

        if ($shashin_options['image_display'] == 'highslide') {
            $replace .= "\n<script type=\"text/javascript\">\naddHSSlideshow('group" . $_SESSION['shashin_group_counter'] . "');\n</script>\n";
        }

        $_SESSION['shashin_group_counter']++;
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
        $shashin_options = unserialize(SHASHIN_OPTIONS);
        $shashin_image_sizes = unserialize(SHASHIN_IMAGE_SIZES);
        $shashin_crop_sizes = unserialize(SHASHIN_CROP_SIZES);

        if ($max == 'max') {
            $max = $shashin_options['theme_max_single'];
        }

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

        // counter for assigning unique IDs to images
        if (!$_SESSION['shashin_id_counter']) {
           $_SESSION['shashin_id_counter'] = 1;
        }

        // set the dimensions
        if ($this->_setDimensions($match['max_size']) === false) {
            return '<span class="shashin_error">Shashin Error: invalid size for image</span>';
        }

        $caption = "";

        // set the caption to include the album name if requested
        if ($shashin_options['prefix_captions'] == 'y') {
            $album = new ShashinAlbum();
            $album->getAlbum($this->data['album_id']);
            $caption .= $album->data['title'] . " &ndash; ";
        }

        $caption .= $this->data['description'];

        // a caption on the thumbnail is optional
        if ($match['caption_yn'] == 'y') {
            $opt_caption = $caption;
        }

        // set the caption to include the date if requested
        if ($shashin_options['caption_exif'] == 'date' && $this->data['taken_timestamp']) {
            $caption .= " &ndash; " . date_i18n("d-M-Y", $this->data['taken_timestamp']);
        }

        // 'enlarge' or 'play' as a thumbnail caption option
        else if ($match['caption_yn'] == 'c') {
            $opt_caption = $this->_isVideo()
                ? __('Click picture to play video', SHASHIN_L10N_NAME)
                : __('Click picture to enlarge', SHASHIN_L10N_NAME);
        }

        $class = $thumb ? 'shashin_thumb' : 'shashin_image';
        $padding = $thumb ? $shashin_options['thumb_padding'] : $shashin_options['div_padding'];
        $markup = '<div class="' . $class . '" style="width: ' . ($this->data['user_width'] + $padding) . 'px;';

        if ($match['float'] == 'center') {
            $markup .= " margin-left: auto; margin-right: auto;";
        }

        else if ($match['float']) {
            $markup .= " float: {$match['float']};";
        }

        $markup .= $match['clear'] ? " clear: {$match['clear']};" : "";
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
            $markup .= "<a href=\"$video_url\" id=\"shashin_thumb_link_"
                . $_SESSION['shashin_id_counter']
                . "\" onclick=\"return hs.htmlExpand(this,{ objectType:'swf', minWidth: "
                . ($width+20) . ", minHeight: " . ($height+20)
                . ", objectWidth: $width, objectHeight: $height, allowSizeReduction: false, preserveContent: false";

            if ($group) {
                $markup .= ", autoplay: $autoplay, slideshowGroup: 'group" . $_SESSION['shashin_group_counter'] . "'";
            }

            $markup .= ' } )" class="highslide">';
        }

        // images in highslide
        else if ($shashin_options['image_display'] == 'highslide') {
            $markup .= '<a href="' . $this->data['enclosure_url']
                . '?imgmax=' . $shashin_options['highslide_max']
                . '" class="highslide" id="shashin_thumb_link_' . $_SESSION['shashin_id_counter']
                . '" onclick="return hs.expand(this';

            if ($group) {
                $markup .= ", { autoplay: $autoplay, slideshowGroup: 'group" . $_SESSION['shashin_group_counter'] . "' }";
            }

            $markup .= ')">';
        }

        // other viewer
        else if ($shashin_options['image_display'] == 'other') {
            $markup .= '<a href="' . $this->data['enclosure_url']
                . '?imgmax=' . $shashin_options['highslide_max']
                . '" id="shashin_thumb_link_' . $_SESSION['shashin_id_counter']
                . '" rel="';

            if ($this->_isVideo()) {
                $markup .= $shashin_options['other_rel_video'];
            }

            else {
                $markup .= $shashin_options['other_rel_image'];
            }

            if ($group) {
                if ($shashin_options['other_rel_delimiter'] == 'brackets') {
                    $markup .= "[" . $_SESSION['shashin_group_counter'] . "]";
                }

                else {
                    $markup .= "-" . $_SESSION['shashin_group_counter'];
                }
            }

            $markup .= '"';

            if ($shashin_options['other_link_class']) {
                $markup .= ' class="' . $shashin_options['other_link_class'] . '"';
            }

            if ($shashin_options['other_link_title']) {
                $markup .= ' title="' . $caption . '"';
            }

            $markup .= '>';
        }

        // images or videos at Picasa
        else {
            $markup .= '<a href="' . $this->data['link_url'] . '"';

            if ($shashin_options['image_display'] == 'new_window') {
                $markup .= ' target="_blank"';
            }

            $markup .= '>';
        }

        // get alternate thumbnail image if one was specified
        if ($match['alt_thumb']) {
            $alt_thumb = new ShashinPhoto();
            list($result, $message, $db_error) = $alt_thumb->getPhoto(array('photo_key' => $match['alt_thumb']));

            if (!$result) {
                return '<span class="shashin_error">' . __("Shashin error:", SHASHIN_L10N_NAME) . ' ' . $message . '</span>';
            }

            // set the dimensions
            if ($alt_thumb->_setDimensions($match['max_size']) === false) {
                return '<span class="shashin_error">Shashin Error: invalid size for image</span>';
            }

            $src = $alt_thumb->data['enclosure_url'];
            $imgmax = $alt_thumb->data['user_max'];
            $width = $alt_thumb->data['user_width'];
            $height = $alt_thumb->data['user_height'];
        }

        else {
            $src = $this->data['enclosure_url'];
            $imgmax = $this->data['user_max'];
            $width = $this->data['user_width'];
            $height = $this->data['user_height'];
        }

        $markup .= '<img src="' . $src
            . '?imgmax='. $imgmax
            . '" alt="' . $caption
            . '" width="' . $width
            . '" height="' . $height
            . '" id="shashin_thumb_image_' . $_SESSION['shashin_id_counter'] . '"';

        if (($shashin_options['image_display'] == 'other' && $shashin_options['other_image_title'])
            || $shashin_options['image_display'] == 'highslide') {
            $markup .= ' title="' . $caption . '"';
        }

        if ($shashin_options['image_display'] == 'other' && $shashin_options['other_image_class']) {
            $markup .= ' class="' . $shashin_options['other_image_class'] . '"';
        }

        $markup .= ' />';

        if ($shashin_options['image_display'] != 'none' && !$admin) {
            $markup .= '</a>';
        }

        // whether to display the caption under the photo
        if ($opt_caption && !$admin) {
            $markup .= '<span class="shashin_caption">' . $opt_caption . '</span>';
        }

        // add date and exif data to the highslide caption if requested
        // and data exists
        if ($shashin_options['caption_exif'] == 'all' && $this->data['taken_timestamp']) {
            $caption .= '<span class="shashin_caption_exif">';
            $caption .= $this->data['taken_timestamp'] ? date_i18n("d-M-Y H:i", $this->data['taken_timestamp'] . " &ndash; ") : '';
            $caption .= $this->data['make'] ? ($this->data['make'] . " " . $this->data['model'] . ", ") : '';
            $caption .= $this->data['fstop'] ? ($this->data['fstop'] . ", ") : '';
            $caption .= $this->data['focal_length'] ? ($this->data['focal_length'] . "mm, ") : '';
            $caption .= $this->data['exposure'] ? ($this->data['exposure'] . " sec, ") : '';
            $caption .= $this->data['iso'] ? ("ISO " . $this->data['iso']) : '';
            $caption .= '</span>';
        }

        if ($caption && $shashin_options['image_display'] == 'highslide' && !$admin) {
            $markup .= '<div class="highslide-caption">' . $caption . '</div>';
        }

        $markup .= "</div>";
        $_SESSION['shashin_id_counter']++;
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
