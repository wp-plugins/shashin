<?php
/**
 * ShashinPhoto class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 1.1
 * @package Shashin
 * @subpackage Classes
 */

/**
 * Instantiate this class and use its methods to manipulate Picasa photos in
 * Shashin. Also includes static methods for generating XHTML markup for
 * displaying photos.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinPhoto {
    var $refData;
    var $data;

    /**
     * The constructor sets $this->refData, which is used for creating the
     * shashin_photo table, afor mapping Picasa RSS feed params to table
     * field names, and for generating form input fields.
     *
     * @access public
     */
    function ShashinPhoto() {
        $this->refData = array(
            'photo_key' => array(
                'colParams' => array('type' => 'int unsigned', 'notNull' => 1,
                    'other' => 'auto_increment primary key'),
                'label' => 'Photo Key', 'source' => 'db'),        
            'photo_id' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Photo ID', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'id'),
            'album_id' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Album ID', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'albumid'),
            'title' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Title', 'source' => 'feed',
                'feedParam1' => 'title'),
            'description' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Description', 'source' => 'feed',
                'feedParam1' => 'description'),
            'link_url' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Link URL', 'source' => 'feed',
                'feedParam1' => 'link'),
            'content_url' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255', 'notNull' => 1),
                'label' => 'Image URL', 'source' => 'feed',
                'feedParam1' => 'media', 'feedParam2' => 'group_content@url'),
            'width' => array(
                'colParams' => array('type' => 'smallint unsigned', 'notNull' => 1),
                'label' => 'Width', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'width'),
            'height' => array(
                'colParams' => array('type' => 'smallint unsigned', 'notNull' => 1),
                'label' => 'Height', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'height'),
            'taken_timestamp' => array(
                'colParams' => array('type' => 'varchar', 'length' => '20',  'notNull' => 1),
                'label' => 'Date taken', 'source' => 'feed',
                'feedParam1' => 'exif', 'feedParam2' => 'tags_time'),
            'uploaded_timestamp' => array(
                'colParams' => array('type' => 'varchar', 'length' => '20',  'notNull' => 1),
                'label' => 'Date Uploaded', 'source' => 'feed',
                'feedParam1' => 'gphoto', 'feedParam2' => 'timestamp'),
            'tags' => array(
                'colParams' => array('type' => 'varchar', 'length' => '255'),
                'label' => 'Tags', 'source' => 'feed',
                'feedParam1' => 'media', 'feedParam2' => 'group_keywords'),
            'include_in_random' => array(
                'colParams' => array('type' => 'char', 'length' => '1', 'other' => "default 'Y'"),
                'label' => 'Include in random photo display', 'source' => 'user',
                'inputType' => 'radio',
                'inputSubgroup' => array('Y' => 'Yes', 'N' => 'No')),
        );
    }

    /**
     * Retrieves data stored in the shashin_photo table for the specified Picasa
     * photo. 
     *
     * Data is stored in $this->data. An album can be requested by Shashin key,
     * or a complete array of photo data can be passed in.
     *
     * @access public
     * @param int $photoKey the Shashin key for a Picasa photo
     * @param array $photoData a complete array of album data
     * @uses ToppaWPFunctions::select()
     * @return boolean true: retrieval succeeded; false: retrieval failed
     */
    function getPhoto($photoKey = null, $photoData = null) {
        if (strlen($photoKey)) {
            $where = "WHERE PHOTO_KEY = '$photoKey'";
            $row = ToppaWPFunctions::select(SHASHIN_PHOTO_TABLE, "*", $where);
            
            if (empty($row)) {
                return false;
            }
            
            else {
                $this->data = $row;
            }
        }
        
        elseif (!empty($photoData)) {
            $this->data = $photoData;
        }
        
        else {
            return false;
        }
        
        return true;
    }

    /**
     * Sets the include_in_random flag for a photo (y or n).
     *
     * @access public
     * @param string $randomFlag y or n
     * @uses ToppaWPFunctions::update()
     * @return boolean true: update succeeded; false: update failed
     */    
    function setIncludeInRandom($randomFlag) {
        $retVal = ToppaWPFunctions::update(SHASHIN_PHOTO_TABLE, "photo_id = '{$this->data['photo_id']}'",
            array('include_in_random' => $randomFlag));

        if ($retVal === false) {
            return false;
        }
        
        return true;
    }

    /**
     * A static method for retrieving an arbitrary set of photos. Accepts an
     * optional SQL where clause.
     *
     * @static
     * @access public
     * @param string $where the SQL where clause (can include order by, etc as well)
     * @uses ToppaWPFunctions::select()
     * @return array An array of hashes containing photo data
     */        
    function getPhotos($where = null) {
        return ToppaWPFunctions::select(SHASHIN_PHOTO_TABLE, '*', $where, 'get_results');
    }    

    /**
     * Translates the "simage" Shashin tag into xhtml displaying the specified
     * photo, with a hyperlink to the photo at Picasa.
     *
     * simage tag: [simage=photo_key,max_size,caption_yn,float,clear]
     * php call: echo $photo->getPhotoMarkup(array(null,photo_key,max_size,'caption_yn','float','clear'));
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete simage tag
     * - Photo Key (required): the Shashin photo_key (not the Picasa image ID)
     * - Size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * - Caption (optional): y or n to show the image description as a caption (defaults to n)
     * - float (optional): a css float value (left, right, or none) (no default)
     * - Clear (optional): a css clear value (left, right, or both) (no default)
     *
     * Example:
     * - [0] => [simage=1,640,y,left,both]
     * - [1] => 1
     * - [2] => 640
     * - [3] => y
     * - [4] => left
     * - [5] => both
     *
     * @static
     * @access public
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent() 
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::_getDivMarkup()
     * @return string the xhtml markup to replace the salbum tag with
     */
    function getPhotoMarkup($match) {
        // get the image, if we don't have it already
        if (!strlen($this->data['photo_id'])) {
            if ($this->getPhoto($match[1]) === false) {
                return '<span class="shashin_error">Error: unable to find photo key ' . $match[1] . '</span>';
            }
        }
        
        return $this->_getDivMarkup($match);
    }
    
    /**
     * Translates the "srandom" Shashin tag into xhtml displaying a table of
     * random photos, each with a hyperlink to the photo at Picasa.
     *
     * [srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     * php call: echo $photo::getRandomMarkup(array(null,album_key,max_size,max_cols,how_many,'caption_yn','float','clear'));
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete simage tag
     * - Album Key (required): a Shashin album_key (not the Picasa album ID) or "any" for pictures from any album
     * - Size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * - Max Cols (required): how many columns the table will have
     * - How Many (required): how many random pictures to show 
     * - Caption (optional): y or n to show the image description as a caption (defaults to n)
     * - float (optional): a css float value (left, right, or none) (no default)
     * - Clear (optional): a css clear value (left, right, or both) (no default)
     *      
     * Note that if an album has include_in_random=n, no photos from it will be
     * displayed, even if the photos have include_in_random=y.
     *
     * Example:
     * - [0] => [srandom=any,200,2,6,y,left,both]
     * - [1] => any
     * - [2] => 200
     * - [3] => 2
     * - [4] => 6
     * - [5] => y
     * - [6] => left
     * - [7] => both
     *
     * @static
     * @access public
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @uses ToppaWPFunctions::select()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the srandom tag with
     */
    function getRandomMarkup($match) {
        $albumKey = strtolower(trim($match[1]));
        $photosWhere = "";
        $albumWhere = "WHERE INCLUDE_IN_RANDOM != 'N'";
        
        // pick a random album if one wasn't specified
        if ($albumKey == 'any') {
            $albumKeys = ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, 'album_key', $albumWhere, 'get_col', ARRAY_N);
            
            if ($albumKeys === false) {
                return '<span class="shashin_error">Error: unable to get album keys for random photo</span>';
            }
    
            // overwrite $albumKey
            // need to do array_flip, as array_rand returns an array of the keys
            $albumKey = array_rand(array_flip($albumKeys));
        }

        $albumWhere .= " AND ALBUM_KEY = '$albumKey'";

        // the table join of albums to photos is done with the Picasa album id,
        // so we need to get it
        $albumID = ToppaWPFunctions::select(SHASHIN_ALBUM_TABLE, 'album_id', $albumWhere, 'get_var');

        // there could be no album if they gave a nonexistent key, or a key
        // for an album set to include_in_random = N
        if (!strlen($albumID)) {
            return '<span class="shashin_error">Error: unable to retrieve Picasa album ID based on album key '
                . $albumKey . '</span>';
        }
        
        $photosWhere = "WHERE INCLUDE_IN_RANDOM != 'N' AND ALBUM_ID = '$albumID'";

        // get the possible set of photo keys
        $photoKeys = ToppaWPFunctions::select(SHASHIN_PHOTO_TABLE, 'photo_key', $photosWhere, 'get_col', ARRAY_N);
        
        if ($photoKeys === false) {
            return '<span class="shashin_error">Error: unable to get photo keys for random photo</span>';
        }

        // need to do array_flip, as array_rand returns an array of the keys
        $randomPhotoKeys = array_rand(array_flip($photoKeys), $match[4]);

        // array_rand will automatically fail if the subject array is too small
        if (empty($randomPhotoKeys)) {
            return '<span class="shashin_error">Error: Not enough photos available to supply '
                . $match[4] . ' random photos</span>';
        }
        
        // if we only asked for 1 random photo, $randomPhotoKeys will be a
        // scalar - otherwise it's an array
        if (is_array($randomPhotoKeys)) {
            $where = "WHERE PHOTO_KEY IN ('" . implode("','", array_values($randomPhotoKeys)) . "')"; 
        }
        
        else {
            $where = "WHERE PHOTO_KEY = $randomPhotoKeys"; 
        }
        
        $photos = ShashinPhoto::getPhotos($where);

        if ($photos === false) {
            return '<span class="shashin_error">Error: unable to retrive photos</span>';
        }
        
        return ShashinPhoto::_getTableMarkup($photos, $match[2], $match[3], $match[5], $match[6], $match[7]);    
    }

    /**
     * Translates the "snewest" Shashin tag into xhtml displaying a table of
     * your newest photos, each with a hyperlink to the photo at Picasa.
     *
     * [snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     * php call: echo $photo->getNewestMarkup(array(null,album_key,max_size,max_cols,how_many,'caption_yn','float','clear'));
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete simage tag
     * - Album Key (required): a Shashin album_key (not the Picasa album ID) or "any" for pictures from any album
     * - Size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * - Max Cols (required): how many columns the table will have
     * - How Many (required): how many random pictures to show 
     * - Caption (optional): y or n to show the image description as a caption (defaults to n)
     * - float (optional): a css float value (left, right, or none) (no default)
     * - Clear (optional): a css clear value (left, right, or both) (no default)
     *      
     * Example:
     * - [0] => [snewest=3,288,2,5,y,none,both]
     * - [1] => 3
     * - [2] => 288
     * - [3] => 2
     * - [4] => 5
     * - [5] => y
     * - [6] => left
     * - [7] => both
     *
     * @static
     * @access public
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the snewest tag with
     */
    function getNewestMarkup($match) {
        $albumKey = strtolower(trim($match[1]));
        $where = "";
        
        // we need the picasa album id for the specified album key, if it's
        // not "any"
        if ($albumKey != 'any') {
            $album = new ShashinAlbum();
            $album->getAlbum(null, null, null, $albumKey);

            if ($album === false) {
                return '<span class="shashin_error">Error: unable to retrive album with album_key $albumKey</span>';
            }

            $where .= "WHERE ALBUM_ID = '" . $album->data['album_id'] . "'";
        }

        // set the limit, and order by timestamp
        $where .= " ORDER BY UPLOADED_TIMESTAMP DESC";
        $where .= " LIMIT " . $match[4];
        $photos = ShashinPhoto::getPhotos($where);

        if ($photos === false) {
            return '<span class="shashin_error">Error: unable to get photo IDs for random photo</span>';
        }
        
        return ShashinPhoto::_getTableMarkup($photos, $match[2], $match[3], $match[5], $match[6], $match[7]);
    }
    
    /**
     * Static method that translates the "sthumbs" Shashin tag into an xhtml
     * table displaying the specified thumbnails, each with a hyperlink to the
     * photo at Picasa.
     *
     * sthumbs tag: [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]
     * php call: echo ShashinPhoto::getThumbsMarkup(array(null,'photo_key1|photo_key2|etc',max_size,max_cols,caption_yn,'float','clear'));
     *
     * $match array elements are as follows:
     * - Tag (optional): the complete sthumbs tag
     * - Keys (required): Shashin photo keys, pipe delimited (not the Picasa image IDs)
     * - Size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * - Columns (required): the number of colums for the thumbnail table
     * - float (optional): a css float value (left, right, or none) (no default)
     * - Clear (optional): a css clear value (left, right, or both) (no default)
     *
     * Example:
     * - [0] => [sthumbs=1|2|3|5,288,2,y,none,both]
     * - [1] => 1|2|3|5
     * - [2] => 288
     * - [3] => 2
     * - [4] => y
     * - [5] => left
     * - [6] => both
     *
     * @static
     * @access public
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @uses ShashinPhoto::getPhotos()
     * @uses ShashinPhoto::_getTableMarkup()
     * @return string the xhtml markup to replace the sthumbs tag with
     */
    function getThumbsMarkup($match) {
        $photoKeys = explode("|", $match[1]);
        $where = "WHERE PHOTO_KEY IN ('" . implode("','", $photoKeys) . "')"; 
        $photos = ShashinPhoto::getPhotos($where);

        if ($photos === false) {
            return '<span class="shashin_error">Error: unable to retrive photos</span>';
        }

        return ShashinPhoto::_getTableMarkup($photos, $match[2], $match[3], $match[4], $match[5], $match[6]);    
    }

    /**
     * Generates an xhtml table containing the passed in photos. Note that
     * $photos is an array of arrays of photo data, not ShashinPhoto objects.
     *
     * @static
     * @access private
     * @param array $photos array of arrays containing photo data
     * @param int $size the desired max dimension - note Picasa allows only certain sizes
     * @param int $cols the number of columns for the table
     * @param string $caption y or n flag indicatig whether to show captions on the images
     * @param string $float an optional css float value
     * @param string $clear an optional css clear value
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::_getDivMarkup()
     * @return string xhtml markup for the table containing the photos
     */    
    function _getTableMarkup($photos, $size, $cols, $caption, $float = null, $clear = null) {
        $replace = '<table class="shashin_thumbs_table"';

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

        for ($i = 0; $i < count($photos); $i++) {
            if ($cellCount == 1) {
                $replace .= "<tr>\n";
            }
            
            $photo = new ShashinPhoto();
  
            if ($photo->getPhoto(null, $photos[$i]) === false) {
                return '<span class="shashin_error">Error: unable to set photo object for photo_id ' . $photos[$i]['photo_id'] . '</span>';
            }

            $markup = $photo->_getDivMarkup(array(null, null, $size, $caption), true);
            $replace .= "<td>$markup</td>\n";
            $cellCount++;
            
            if ($cellCount > $cols || $i == (count($photos) - 1)) {
                $replace .= "</tr>\n";
                $cellCount = 1;
            }
        }
            
        $replace .= "</table>";
        return $replace;
    }
    
    /**
     * Calculates the height or width for an image, based on $max. Allowed
     * values for $max are 32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512,
     * 576, 640, 720, 800. If 32, 48, 64, or 160 is used, the image will be
     * cropped square.
     *
     * $max is your desired maximum image dimension. It will be applied to the
     * width of the image if it has a landscape orientation, or to the height if
     * it has a portrait orientation (that's just how Picasa works). The
     * dimensions are added to $this->data as user_width, user_height, and
     * user_max
     *
     * @access private
     * @param int $max the maximum desired image dimension (could be width or height)
     * @return boolean true: set dimension successfully; false: failed to set dimension
     */   
    function _setDimensions($max) {
        // You have to use one of these  to get a picture back from Picasa
        // See http://code.google.com/apis/picasaweb/reference.html
        $allowed = eval(SHASHIN_IMAGE_SIZES);
        $cropped = eval(SHASHIN_CROP_SIZES);
        // $allowed = array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800);
        // $cropped = array(32, 48, 64, 160);

        if (!in_array($max, $allowed)) {
            return false;
        }

        // we'll need this later for constructing the url
        $this->data['user_max'] = $max;
        
        // if it's a cropped size, then $max applies to the height and width
        if (in_array($max, $cropped)) {
            $this->data['user_width'] = $max;
            $this->data['user_height'] = $max;
            $this->data['user_max'] = $this->data['user_max'] . '&amp;crop=1';
    
        }
        
        // see if $max should be applied to the height or the width
        elseif ($this->data['width'] > $this->data['height']) {
            $this->data['user_width'] = $max;
            $percentage = $max / $this->data['width'];
            $this->data['user_height'] = $percentage * $this->data['height'];
            # drop any decimals
            settype($this->data['user_height'], "int");
        }
        
        else {
            $this->data['user_height'] = $max;
            $percentage = $max / $this->data['height'];
            $this->data['user_width'] = $percentage * $this->data['width'];
            # drop any decimals
            settype($this->data['user_width'], "int");
        }
        
        return true;
    }
    
    /**
     * Generates the xhtml div for displaying an image.
     *
     * @access private
     * @param array $match the array returned by str_replace on the content in Shashin::parseContent()
     * @param boolean $thumb if true the shashion_thumb_padding class will be applied
     * @uses ShashinPhoto::_setDimensions()
     * @return string the xhtml markup to display the image
     */   
    function _getDivMarkup($match, $thumb = false) {
        // set the dimensions
        if ($this->_setDimensions($match[2]) === false) {
            return '<span class="shashin_error">Error: invalid size for image</span>';
        }
                
        // set the caption
        if (strtolower(trim($match[3])) == 'y') {
            $caption = $this->data['description'];
        }

        // set the float value
        if (strlen(trim($match[4]))) {
            $float = $match[4];
        }

        // set the clear value
        if (strlen(trim($match[5]))) {
            $clear = $match[5];
        }
    
        
        if ($thumb === true) {
            $class = 'class="shashin_thumb"';
            $padding = get_option("shashin_thumb_padding");
        }

        else {
            $class = 'class="shashin_image"';
            $padding = get_option("shashin_div_padding");
        }
        
        $markup = '<div ' . $class . ' style="width: '
            . ($this->data['user_width'] + $padding) . 'px;';
            
        if (strlen($float)) {
            $markup .= ' float: ' . $float . ';';
        }

        if (strlen($clear)) {
            $markup .= ' clear: ' . $clear . ';';
        }
        
        $markup .=  '">';
        $markup .= '<a href="' . $this->data['link_url'] .'">';
        $markup .= '<img src="' . $this->data['content_url']
            . '?imgmax='. $this->data['user_max']
            . '" alt="' . htmlspecialchars($this->data['description'])
            . '" width="' . $this->data['user_width']
            . '" height="' . $this->data['user_height'] . '" />';
        $markup .= '</a>';
        
        if (strlen($caption)) {
            $markup .= '<span class="shashin_caption">' . $caption . '</span>'; 
        }
        
        $markup .= "</div>";
        return $markup;
    }
}

?>