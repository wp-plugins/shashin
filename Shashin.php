<?php

/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa photos in WordPress.
Author: Michael Toppa
Version: 2.2.1
Author URI: http://www.toppa.com
*/

/**
 * Shashin Class File
 *
 * @author Michael Toppa
 * @version 2.2.1
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007-2008 Michael Toppa
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

global $wpdb;
define('SHASHIN_PLUGIN_NAME', 'Shashin');
define('SHASHIN_FILE', basename(__FILE__));
define('SHASHIN_DIR', dirname(__FILE__));
define('SHASHIN_PATH', SHASHIN_DIR . '/' . SHASHIN_FILE);
define('SHASHIN_ADMIN_URL', $_SERVER['PHP_SELF'] . "?page=" . basename(SHASHIN_DIR) . '/' . SHASHIN_FILE);
define('SHASHIN_VERSION', '2.2.1');
define('SHASHIN_DISPLAY_NAME', 'Shashin');
define('SHASHIN_ALBUM_THUMB_SIZE', 160);
define('SHASHIN_ALBUM_TABLE', $wpdb->prefix . 'shashin_album');
define('SHASHIN_PHOTO_TABLE', $wpdb->prefix . 'shashin_photo');
define('SHASHIN_PICASA_SERVER', get_option('shashin_picasa_server'));
define('SHASHIN_USER_RSS', SHASHIN_PICASA_SERVER . '/data/feed/api/user/USERNAME?kind=album&alt=rss');
define('SHASHIN_ALBUM_RSS', SHASHIN_PICASA_SERVER . '/data/feed/api/user/USERNAME/albumid/ALBUMID?kind=photo&alt=rss');
define('SHASHIN_GOOGLE_MAPS_QUERY_URL', 'http://maps.google.com/maps?q=');
define('SHASHIN_DISPLAY_URL', get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(SHASHIN_DIR) . '/display/');
define('SHASHIN_FAQ_URL', 'http://www.toppa.com/shashin-wordpress-plugin');
define('SHASHIN_DEFAULT_SERVER', 'http://picasaweb.google.com');
define('SHASHIN_DEFAULT_DIV_PADDING', 10);
define('SHASHIN_DEFAULT_THUMB_PADDING', 6);
define('SHASHIN_DEFAULT_IMAGE_DISPLAY', 'same_window');
define('SHASHIN_DEFAULT_HIGHSLIDE_MAX', 640);
define('SHASHIN_DEFAULT_PREFIX_CAPTIONS', 'n');
define('SHASHIN_DEFAULT_HIGHSLIDE_VIDEO_WIDTH', 640);
define('SHASHIN_DEFAULT_HIGHSLIDE_VIDEO_HEIGHT', 480);
define('SHASHIN_DEFAULT_HIGHSLIDE_AUTOPLAY', 'false');
define('SHASHIN_DEFAULT_HIGHSLIDE_INTERVAL', 5000);
define('SHASHIN_GOOGLE_PLAYER_URL', 'http://video.google.com/googleplayer.swf?videoUrl=');

// workaround for constants not allowing arrays
// use with eval, e.g. $imageSizes = eval(SHASHIN_IMAGE_SIZES);
define('SHASHIN_IMAGE_SIZES', 'return '
    . var_export(
        array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800), 1)
    . ';');
define('SHASHIN_CROP_SIZES', 'return ' . var_export(array(32, 48, 64, 160), 1) . ';');
define('SHASHIN_PICASA_VIDEO_TYPES', 'return ' . var_export(array('MPG', 'AVI', 'ASF', 'WMV', 'MOV', 'MP4'), 1) . ';');


// get required libraries
require_once(SHASHIN_DIR . '/ShashinAlbum.php');
require_once(SHASHIN_DIR . '/ShashinPhoto.php');
require_once(SHASHIN_DIR . '/ToppaWPFunctions.php');
require_once(SHASHIN_DIR . '/ToppaXMLParser.php');

/**
 * The main class - handles all incoming requests for displaying photos, as well
 * as the admin and options menus.
 *
 * All methods in this class are static. The methods handle installation,
 * configuration, display of administrative menus, administrative requests,
 * parsing of content, and directing requests to the ShashinAlbum and
 * ShashinPhoto classes as needed.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class Shashin {
    /**
     * Called automatically (after the end of the class) to register hooks and
     * add the actions and filters.
     *
     * @static
     * @access public
     * @uses install
     * @uses uninstall
     * @uses initAdminMenu
     * @uses parseContent
     * @uses getHeadTags
     */
    function bootstrap() {
        // Add the installation and uninstallation hooks
        register_activation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'install'));
        register_deactivation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'uninstall'));

        // Add the actions and filters
        add_action('admin_menu', array(SHASHIN_PLUGIN_NAME, 'initAdminMenus'));
        add_action('admin_head', array(SHASHIN_PLUGIN_NAME, 'getAdminCSS'));
        add_action('plugins_loaded', array(SHASHIN_PLUGIN_NAME, 'initWidgets'));
        add_action('wp_head', array(SHASHIN_PLUGIN_NAME, 'getHeadTags'));
        // the 0 priority flag gets the div in before the autoformatter
        // can wrap it in a paragraph
        add_filter('the_content', array(SHASHIN_PLUGIN_NAME, 'parseContent'), 0);

        if (get_option('shashin_image_display') == 'highslide') {
            // counter for assigning unique IDs to highslide images
            if (!$_SESSION['hs_id_counter']) {
               $_SESSION['hs_id_counter'] = 1;
            }

            // counter for distinguishing groups of Highslide photos on a page
            if (!$_SESSION['hs_group_counter']) {
               $_SESSION['hs_group_counter'] = 1;
            }
        }
    }

    /**
     * Updates options and creates the Shashin tables if they don't already
     * exist.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::createTable()
     */
    function install() {
        global $wpdb;

        // make needed table changes for upgraders, or create tables for new users
        if ($wpdb->get_var("show tables like '" . SHASHIN_ALBUM_TABLE . "'") == SHASHIN_ALBUM_TABLE) {
            $describeAlbum = $wpdb->get_results('DESCRIBE ' . SHASHIN_ALBUM_TABLE, ARRAY_A);
            
            foreach ($describeAlbum as $describe) {
                if ($describe['Field'] == 'description' && strpos($describe['Type'], 'text') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_ALBUM_TABLE . ' MODIFY description text');
                }
    
                if ($describe['Field'] == 'last_updated_timestamp') {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_ALBUM_TABLE . ' MODIFY last_updated_timestamp bigint unsigned');
                    $wpdb->query('ALTER TABLE ' . SHASHIN_ALBUM_TABLE . ' CHANGE last_updated_timestamp last_updated bigint unsigned');
                }
    
                if ($describe['Field'] == 'pub_date' && strpos($describe['Type'], 'bigint') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_ALBUM_TABLE . ' MODIFY pub_date bigint unsigned');
                }
            }
            
            // latin-1 is a common default charset, which may be causing
            // problems for some users with multibyte characters
            $albumStruct = $wpdb->get_row('SHOW CREATE TABLE ' . SHASHIN_ALBUM_TABLE, ARRAY_A);
            
            if (strpos($albumStruct['Create Table'], 'DEFAULT CHARSET=utf8') === false) {
                $wpdb->query('ALTER TABLE ' . SHASHIN_ALBUM_TABLE . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
            }
        }

        else {
            $album = new ShashinAlbum();
            ToppaWPFunctions::createTable($album, SHASHIN_ALBUM_TABLE);
        }

        if ($wpdb->get_var("show tables like '" . SHASHIN_PHOTO_TABLE . "'") == SHASHIN_PHOTO_TABLE) {
            $describePhotos = $wpdb->get_results('DESCRIBE ' . SHASHIN_PHOTO_TABLE, ARRAY_A);
    
            $foundDeleted = false;
            $foundEnclosure = false;    
    
            foreach ($describePhotos as $describe) {
                if ($describe['Field'] == 'photo_id' && strpos($describe['Key'], 'UNI') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' ADD UNIQUE (photo_id)');
                }
    
                if ($describe['Field'] == 'taken_timestamp' && strpos($describe['Type'], 'bigint') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' MODIFY taken_timestamp bigint unsigned');
                }
    
                if ($describe['Field'] == 'uploaded_timestamp' && strpos($describe['Type'], 'bigint') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' MODIFY uploaded_timestamp bigint unsigned');
                }
    
                if ($describe['Field'] == 'description' && strpos($describe['Type'], 'text') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' MODIFY description text');
                }

                if ($describe['Field'] == 'content_url' && strpos($describe['Type'], 'text') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' MODIFY content_url text');
                }

                if ($describe['Field'] == 'link_url' && strpos($describe['Type'], 'text') === false) {
                    $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' MODIFY link_url text');
                }
                
                if ($describe['Field'] == 'deleted') {
                    $foundDeleted = true;
                }

                if ($describe['Field'] == 'enclosure_url') {
                    $foundEnclosure = true;
                }
            }
    
            if ($foundEnclosure === false) {
                $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' ADD enclosure_url text NOT NULL');
                $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' ADD enclosure_type varchar(255) NOT NULL');
            }

            if ($foundDeleted === false) {
                $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' ADD deleted char(1) DEFAULT "N"');
            }
                
            $photoStruct = $wpdb->get_row('SHOW CREATE TABLE ' . SHASHIN_PHOTO_TABLE, ARRAY_A);
            
            if (strpos($photoStruct['Create Table'], 'DEFAULT CHARSET=utf8') === false) {
                $wpdb->query('ALTER TABLE ' . SHASHIN_PHOTO_TABLE . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
            }
        }
        
        else {
            $photo = new ShashinPhoto();
            ToppaWPFunctions::createTable($photo, SHASHIN_PHOTO_TABLE);
        }

        update_option('shashin_version', SHASHIN_VERSION);

        // this controls how much width to add to the shashin_image div, to
        // accommodate the padding applied to the image (since the width can
        // be anything, you don't want to hardcode this in the css)
        if (!strlen(get_option('shashin_div_padding'))) {
            update_option('shashin_div_padding', SHASHIN_DEFAULT_DIV_PADDING);
        }

        // same as above, but for images in a thumbnail table
        if (!strlen(get_option('shashin_thumb_padding'))) {
            update_option('shashin_thumb_padding', SHASHIN_DEFAULT_THUMB_PADDING);
        }

        // set the Picasa server to use
        if (!strlen(get_option('shashin_picasa_server'))) {
            update_option('shashin_picasa_server', SHASHIN_DEFAULT_SERVER);
        }

        // set the fullsize image display option (same window, new window, or highslide)
        if (!strlen(get_option('shashin_image_display'))) {
            update_option('shashin_image_display', SHASHIN_DEFAULT_IMAGE_DISPLAY);
        }

        // set the highslide preferred fullsize dimension
        if (!strlen(get_option('shashin_highslide_max'))) {
            update_option('shashin_highslide_max', SHASHIN_DEFAULT_HIGHSLIDE_MAX);
        }

        // set the default option for prefixing album titles to photo captions
        if (!strlen(get_option('shashin_prefix_captions'))) {
            update_option('shashin_prefix_captions', SHASHIN_DEFAULT_PREFIX_CAPTIONS);
        }

        // set the highslide width for videos
        if (!strlen(get_option('shashin_highslide_video_width'))) {
            update_option('shashin_highslide_video_width', SHASHIN_DEFAULT_HIGHSLIDE_VIDEO_WIDTH);
        }

        // set the highslide height for videos
        if (!strlen(get_option('shashin_highslide_video_height'))) {
            update_option('shashin_highslide_video_height', SHASHIN_DEFAULT_HIGHSLIDE_VIDEO_HEIGHT);
        }

        // set highslide autoplay
        if (!strlen(get_option('shashin_highslide_autoplay'))) {
            update_option('shashin_highslide_autoplay', SHASHIN_DEFAULT_HIGHSLIDE_AUTOPLAY);
        }

        // set highslide interval for slideshow image display
        if (!strlen(get_option('shashin_highslide_interval'))) {
            update_option('shashin_highslide_interval', SHASHIN_DEFAULT_HIGHSLIDE_INTERVAL);
        }
    }

    /**
     * Nothing to see here - it would be nice if WordPress supported
     * options for deactivation, so users could choose whether to drop
     * the shashin tables or not.
     *
     * @static
     * @access public
     */
    function uninstall() {
    }

    /**
     * Adds the Shashin management and option pages.
     *
     * @static
     * @access public
     * @uses getAdminMenu()
     * @uses getOptionsMenu()
     */
    function initAdminMenus() {
        add_options_page(SHASHIN_DISPLAY_NAME, SHASHIN_DISPLAY_NAME, 6, __FILE__, array(SHASHIN_PLUGIN_NAME, 'getOptionsMenu'));
        add_management_page(SHASHIN_DISPLAY_NAME, SHASHIN_DISPLAY_NAME, 6, __FILE__, array(SHASHIN_PLUGIN_NAME, 'getAdminMenu'));
    }

    /**
     * Generates and echoes the HTML for the appropriate admin page, based on
     * $_REQUEST values.
     *
     * Accepted $_REQUEST values are listed below. If no value is provided, the
     * main admin menu is shown, listing all albums.
     *
     * - editAlbumPhotos (shows album photos for updating)
     * - updateAlbumPhotos (handles submit from editAlbumPhotos)
     * - addAlbum
     * - syncAll (for syncing all local albums with Picasa)
     * - updateAlbums (handles submit from main admin page)
     * - syncAlbum (for syncing one local album with Picasa)
     * - deleteAlbum
     *
     * @static
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::getAlbumPhotos()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::setIncludeInRandom()
     * @uses ShashinAlbum::setAlbum()
     * @uses ShashinAlbum::setAlbumPhotos()
     * @uses ShashinAlbum::getAlbums()
     * @uses ShashinAlbum::setIncludeInRandom()
     * @uses ShashinAlbum::deleteAlbum()
     */
    function getAdminMenu() {
        // Start the cache
        ob_start();

        // show selected album for editing
        if ($_REQUEST['shashinAction'] == 'editAlbumPhotos') {
            $album = new ShashinAlbum();

            if ($album->getAlbum($_REQUEST['albumID']) === false) {
                $message = "Unable to retrieve album";
            }

            elseif ($album->getAlbumPhotos() === false) {
                $message = "Unable to retrieve album photos";
            }

            // so we don't have to query for the album again on the next page
            $_SESSION['album'] = $album;
            $display = 'admin-edit';
        }

        // save updated album photos - the only thing editable is the
        // include_in_random flag
        elseif ($_REQUEST['shashinAction'] == 'updateAlbumPhotos') {
            // compare new values to old to see which records need updating
            // (better than running a bunch of unneeded updates)
            foreach ($_SESSION['album']->data['photos'] as $photoArray) {
                foreach($_REQUEST['include_in_random'] as $k=>$v) {
                    if ($photoArray['photo_id'] == $k
                      && $photoArray['include_in_random'] != $v) {
                        $photo = new ShashinPhoto();
                        $photo->getPhoto(null, $photoArray);

                        if ($photo->setIncludeInRandom($v) === false) {
                            $message = "Unable to update record for Photo "
                                . $photo->data['photo_key'] . '. SQL Error:';
                            $dbError = true;
                            break;
                        }
                    }
                }

                if ($dbError === true) {
                    break;
                }
            }

            if ($dbError !== true) {
                $message = "Updates saved.";
            }

            // don't need the album in the session anymore
            unset($_SESSION['album']);
            $display = 'admin-main';
        }


        // add an album (or all albums)
        elseif ($_REQUEST['shashinAction'] == 'addAlbum') {
            $linkURL = trim($_REQUEST['link_url']);

            // validate the URL
            $pieces = explode("/", $linkURL);

            // send user back to the form if there are validation errors
            if ((($pieces[0] . "//" . $pieces[2]) != SHASHIN_PICASA_SERVER) || !$pieces[3]) {
                $message = "That is not a valid Picasa URL";
            }

            // if no validation errors, and we have a single album, add it
            else if (strlen($pieces[4])) {
                $album = new ShashinAlbum();
                // insert/update the album
                if ($album->setAlbum($pieces[3], $pieces[4], null, $_REQUEST['include_in_random']) === false) {
                    $message = "Failed to add album. This was probably a bad connection trying to read the RSS feed. Please try again.";
                }

                // sync the photos
                elseif ($album->setAlbumPhotos() === false) {
                    $message = "Album metadata added, but failed to add photos.
                        This was probably a bad connection trying to read the RSS feed. Please try again.";
                }

                // all is well
                else {
                    $message = "Album added";
                    // clear user inputs so they're not displayed again
                    unset($_REQUEST['include_in_random']);
                    unset($_REQUEST['link_url']);
                }
            }

            // if no validation errors, and we're adding all albums
            else {
                if (ShashinAlbum::setAlbums($pieces[3], $_REQUEST['include_in_random'], true) === false) {
                    $message = "Failed to add albums. This was probably a bad connection trying to read the RSS feed. Please try again.";
                }

                else {
                    $message = "Albums added";
                }
            }

            $display = 'admin-main';
        }

        // sync all albums
        elseif ($_REQUEST['shashinAction'] == 'syncAll') {
            if (ShashinAlbum::setAlbums($_REQUEST['users']) === false) {
                $message = "Failed to sync albums. This was probably a bad connection trying to read the RSS feed. Please try again.";
            }

            else {
                $message = "All albums synced for " . $_REQUEST['users'];
            }

            $display = 'admin-main';
        }

        // update albums' include_in_random flag
        elseif ($_REQUEST['shashinAction'] == 'updateAlbums') {
            $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
            foreach ($allAlbums as $albumArray) {
                $album = new ShashinAlbum();
                $album->getAlbum(null, null, $albumArray);

                foreach($_REQUEST['include_in_random'] as $k=>$v) {
                    if ($album->data['album_id'] == $k
                      && $album->data['include_in_random'] != $v) {
                        if ($album->setIncludeInRandom($v) === false) {
                            $message = "Unable to update record for Album "
                                . $album->data['album_key'] . '. SQL Error:';
                            $dbError = true;
                            break;
                        }
                    }
                }

                if ($dbError === true) {
                   break;
                }
            }

            if ($dbError !== true) {
                $message = "Updates saved.";
            }

            $display = 'admin-main';
        }

        // sync album and its photos
        elseif ($_REQUEST['shashinAction'] == 'syncAlbum') {
            $album = new ShashinAlbum();

            if ($album->setAlbum($_REQUEST['user'], null, $_REQUEST['albumID']) === false) {
                $message = "Album sync failed. This was probably a bad connection trying to read the RSS feed. Please try again.";
            }

            elseif ($album->setAlbumPhotos() === false) {
                $message = "Album metadata synced, but failed to sync photos.
                    This was probably a bad connection trying to read the RSS feed. Please try again.";
            }

            else {
                $message = "Album synchronized.";
            }

            $display = 'admin-main';
        }

        // delete requested album
        elseif ($_REQUEST['shashinAction'] == 'deleteAlbum') {
            $album = new ShashinAlbum();
            $album->getAlbum($_REQUEST['albumID']);

            if ($album->deleteAlbum() === false) {
                $message = "Failed to delete album. SQL Error: ";
                $dbError = true;
            }

            else {
                $message = "Album deleted.";
            }

            $display = 'admin-main';
        }

        // show summary of albums, and form to add a new one
        else {
            $display = 'admin-main';
        }

        // decide which admin menu to show
        if ($display == 'admin-edit') {
               require(SHASHIN_DIR . '/display/admin-edit.php');
        }

        else {
            $users = ShashinAlbum::getUsers();
            $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
            $album = new ShashinAlbum(); // needed in admin-main, for refData

            foreach ($users as $user) {
                $usernames[$user] = $user;
            }
            
            if (!empty($users)) {
                $syncAll = array('inputType' => 'select', 'inputSubgroup' => $usernames);
            }

              require(SHASHIN_DIR . '/display/admin-main.php');
        }

        // Get the markup and display
        $adminMenuHTML = ob_get_contents();
        ob_end_clean();
        echo $adminMenuHTML;
    }

    /**
     * Generates and echoes the HTML for the options menu and sets Shashin
     * options in WordPress.
     *
     * There are 3 options: the Picasa server, the image div padding, and the
     * thumbnail padding. The image div padding should be double the value of
     * the padding for ".shashin_image img" in shashin.css, and the thumbnail
     * padding should be double the padding value for "shashin_thumb img."
     *
     * @static
     * @access public
     */
    function getOptionsMenu() {
        // Start the cache
        ob_start();

        $allowed = eval(SHASHIN_IMAGE_SIZES);
        
        // validate form inputs
        if ($_REQUEST['shashinAction'] == 'updateOptions') {
            $pieces = explode("/", trim($_REQUEST['shashin_picasa_server']));

            // send user back to the form if there are validation errors
            if ($pieces[0] != "http:" || !strlen($pieces[2]) || strlen($pieces[3])) {
                $message = "Invalid URL for Picasa Server";
            }

            else if (!in_array(trim($_REQUEST['shashin_highslide_max']), $allowed)) {
                $message = "Invalid Picasa image size. Size must be one of the following: "
                    . implode(", ", $allowed);
            }

            // otherwise save the options
            else {
                foreach ($_REQUEST as $k=>$v) {
                    if (strpos($k, 'shashin') !== false && $k != 'shashinAction') {
                        update_option($k, htmlspecialchars(stripslashes(trim($v))));
                    }
                }

                $message = "Options saved.";
            }
        }

        // Get the markup and display
          require(SHASHIN_DIR . '/display/options-main.php');
        $optionsMenuHTML = ob_get_contents();
        ob_end_clean();
        echo $optionsMenuHTML;
    }


    /**
     * Gets the Shashin CSS file, and the optional Highslide CSS and
     * JS, for inclusion in the document head.
     *
     * @static
     * @access public
     */
    function getHeadTags() {
        if (file_exists(TEMPLATEPATH . '/shashin.css')) {
            $shashinCSS = get_stylesheet_directory_uri() . '/shashin.css';
        }
        
        else {
            $shashinCSS = SHASHIN_DISPLAY_URL . '/shashin.css';
        }
        
        echo '<link rel="stylesheet" type="text/css" href="' . $shashinCSS . '" />' . "\n";

        if (get_option('shashin_image_display') == 'highslide') {
            if (file_exists(TEMPLATEPATH . '/highslide.css')) {
                $highslideCSS = get_stylesheet_directory_uri() . '/highslide.css';
            }
            
            else {
                $highslideCSS = SHASHIN_DISPLAY_URL . '/highslide.css';
            }

            echo '
                <link rel="stylesheet" type="text/css" href="' . $highslideCSS . '" />
                <script type="text/javascript" src="' . SHASHIN_DISPLAY_URL . 'highslide/highslide.js"></script>
                <script type="text/javascript" src="' . SHASHIN_DISPLAY_URL . 'highslide/swfobject.js"></script>
                <script type="text/javascript">
                    hs.graphicsDir = \'' . SHASHIN_DISPLAY_URL . 'highslide/graphics/\';
                    hs.align = \'center\';
                    hs.transitions = [\'expand\', \'crossfade\'];
                    hs.outlineType = \'rounded-white\';
                    hs.fadeInOut = true;
                    //hs.dimmingOpacity = 0.75;
                    
                    // Add the controlbar for slideshows
                    function addHSSlideshow(groupID) {
                        hs.addSlideshow({
                            slideshowGroup: groupID,
                            interval: ' . get_option('shashin_highslide_interval') . ',
                            repeat: true,
                            useControls: true,
                            fixedControls: true,
                            overlayOptions: {
                                opacity: .75,
                                position: \'top center\',
                                hideOnMouseOut: false
                            }
                        });
                    }
                    
                    // for Flash
	                hs.outlineWhileAnimating = true;
	                hs.allowSizeReduction = false;
	                // always use this with flash, else the movie will not stop on close:
	                hs.preserveContent = false;
                </script>
            ';
        }
    }

    /**
     * Gets the Shashin Admin CSS file, for inclusion in the document head. This
     * CSS supports the display of the widget form.
     *
     * @static
     * @access public
     */
    function getAdminCSS() {
        echo '        <link rel="stylesheet" type="text/css" href="' . SHASHIN_DISPLAY_URL . 'shashin-admin.css" />';
    }

    /**
     * Replaces Shashin tags in posts and pages with XHTML displaying
     * the requested images.
     *
     * Supported Shashin tags:
     * - [simage=photo_key,max_size,caption_yn,float,clear]
     * - [srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     * - [salbum=album_key,location_yn,pubdate_yn,float,clear]
     * - [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]
     * - [snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     * - [salbumthumbs=album_key1|album_key2|etc,max_cols,location_yn,pubdate_yn,float,clear]
     * - [salbumphotos=max_size,max_cols,caption_yn,description_yn,order_by,float,clear]
     * - [salbumlist=album_key1|album_key2|etc,info_yn]
     *
     * For srandom and snewest tags, you can use the word "any" instead
     * of an album key to get photos from any album. For salbum,
     * salbumthumbs, and salbumlist, note that the thumbnail size is
     * fixed by Picasa at 160x160. For salbumthumbs and salbumlist, you
     * can substitute a column name to order by for the keys, if you
     * want all the albums.
     *
     * @static
     * @access public
     * @param string The content of the page or post
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @uses ShashinPhoto::getRandomMarkup()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbumMarkup()
     * @uses ShashinPhoto::getThumbsMarkup()
     * @uses ShashinPhoto::getNewestMarkup()
     * @uses ShashinAlbum::getAlbumThumbsMarkup()
     * @uses ShashinPhoto::getAlbumPhotosMarkup()
     * @uses ShashinAlbum::getAlbumListMarkup()
     */
    function parseContent($content) {
        $onePhoto = "/\[simage=(\d+),(\d{2,4}),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($onePhoto, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $photo = new ShashinPhoto;
                $markup = $photo->getPhotoMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $randomPhoto = "/\[srandom=(\w+),(\d{2,4}),(\d+),(\d+),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($randomPhoto, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinPhoto::getRandomMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $oneAlbum = "/\[salbum=(\d+),?(\w?),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($oneAlbum, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $album = new ShashinAlbum;
                $markup = $album->getAlbumMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $thumbPhotos = "/\[sthumbs=([\d\|]+),(\d{2,4}),(\d+),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($thumbPhotos, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinPhoto::getThumbsMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $newestPhotos = "/\[snewest=(\w+),(\d{2,4}),(\d+),(\d+),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($newestPhotos, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinPhoto::getNewestMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $albumsPhotos = "/\[salbumthumbs=([\w+\|\ ]+),(\d+),?(\w?),?(\w?),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($albumsPhotos, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinAlbum::getAlbumThumbsMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $albumsAllPhotos = "/\[salbumphotos=(\d+),(\d+),?(\w?),?(\w?),?(\w{0,}),?(\w{0,5}),?(\w{0,5})\]/";

        if (preg_match_all($albumsAllPhotos, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinPhoto::getAlbumPhotosMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        $albumList = "/\[salbumlist=([\w+\|\ ]+),?(\w?)\]/";

        if (preg_match_all($albumList, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $markup = ShashinAlbum::getAlbumListMarkup($match);
                $content = str_replace($match[0], $markup, $content);
            }
        }

        return $content;
    }

    /**
     * Wrapper for ShashinPhoto->getPhotoMarkup()
     *
     * @static
     * @access public
     * @param int $photoKey (required): the Shashin photo_key (not the Picasa image ID)
     * @param int $maxSize (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param string $captionYN (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @return string xhtml to display photo
     */
    function getSingle($photoKey, $maxSize, $captionYN = null, $float = null, $clear = null) {
        $photo = new ShashinPhoto();
        return $photo->getPhotoMarkup(array(null,$photoKey,$maxSize,$captionYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinPhoto::getRandomMarkup()
     *
     * @static
     * @access public
     * @param int $albumKey (required): a Shashin album_key (not the Picasa album ID) or "any" for pictures from any album
     * @param int $maxSize (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $maxCols (required): how many columns the table will have
     * @param int $howMany (required): how many random pictures to show
     * @param string $captionYN (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getRandomMarkup()
     * @return string xhtml to display table of random photos
     */
    function getRandom($albumKey, $maxSize, $maxCols, $howMany, $captionYN = null, $float = null, $clear = null) {
        return ShashinPhoto::getRandomMarkup(array(null,$albumKey,$maxSize,$maxCols,$howMany,$captionYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumMarkup()
     *
     * @static
     * @access public
     * @param int $albumKey (required): a Shashin album_key (not the Picasa album ID)
     * @param string $location (optional): y or n to show the location of the image, with a link to Google Maps.
     * @param string $pubDate (optional): y or n to show the pub date of the album
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbumMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbum($albumKey, $locationYN = null, $pubdateYN = null, $float = null, $clear = null) {
        $album = new ShashinAlbum;
        return $album->getAlbumMarkup(array(null,$albumKey,$locationYN,$pubdateYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinPhoto::getThumbsMarkup()
     *
     * @static
     * @access public
     * @param string $photoKeys (required): Shashin photo keys, pipe delimited (not the Picasa image IDs)
     * @param int $maxSize (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $maxCols (required): how many columns the table will have
     * @param string $captionYN (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getThumbsMarkup()
     * @return string xhtml to display table of thumbnails
     */
    function getThumbs($photoKeys, $maxSize, $maxCols, $captionYN = null, $float = null, $clear = null) {
        return ShashinPhoto::getThumbsMarkup(array(null,$photoKeys,$maxSize,$maxCols,$captionYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinPhoto::getNewestMarkup()
     *
     * @static
     * @access public
     * @param int $albumKey (required): a Shashin album_key (not the Picasa album ID)
     * @param int $maxSize (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $maxCols (required): how many columns the table will have
     * @param int $howMany (required): how many random pictures to show
     * @param string $captionYN (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getNewestMarkup()
     * @return string xhtml to display table of newest photos
     */
    function getNewest($albumKey, $maxSize, $maxCols, $howMany, $captionYN = null, $float = null, $clear = null) {
        return ShashinPhoto::getNewestMarkup(array(null,$albumKey,$maxSize,$maxCols,$howMany,$captionYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumThumbsMarkup()
     *
     * @static
     * @access public
     * @param string $albumKeys (required): Shashin album keys (not the Picasa album ID), or a column name to order by
     * @param int $maxCols (required): how many columns the table will have
     * @param string $locationYN (optional): y or n to show the location of the image, with a link to Google Maps.
     * @param string $pubDateYN (optional): y or n to show the pub date of the album
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinAlbum::getAlbumThumbsMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbumThumbs($albumKeys, $maxCols, $locationYN = null, $pubdateYN = null, $float = null, $clear = null) {
        return ShashinAlbum::getAlbumThumbsMarkup(array(null,$albumKeys,$maxCols,$locationYN,$pubdateYN,$float,$clear));
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumListMarkup()
     *
     * @static
     * @access public
     * @param string $albumKeys (required): Shashin album keys (not the Picasa album ID), or a column name to order by
     * @param string $infoYN (optional): y or n to show the album location, pub date, and number of pictures.
     * @uses ShashinAlbum::getAlbumThumbsMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbumList($albumKeys, $infoYN = null) {
        return ShashinAlbum::getAlbumListMarkup(array(null, $albumKeys, $infoYN, $picsYN));
    }

    /**
     * Registers all the Shashin widgets and their controls.
     *
     * It first checks to make sure widgets are available in this WordPress
     * installation. This function has several functions contained within it,
     * which PHPDoc cannot see. These functions get their respestive widget
     * options and then display the widget. The functions are:
     *
     * - widgetSingle()
     * - widgetRandom()
     * - widgetAlbum()
     * - widgetThumbs()
     * - widgetNewest()
     * - widgetAlbumThumbs()
     *
     * @static
     * @access public
     */
    function initWidgets() {
        // Check to see required Widget API functions are defined...
        if (!function_exists('register_sidebar_widget')
          || !function_exists('register_widget_control')) {
            return false;
        }

        function widgetSingle($args) {
            // collect widget options, or define defaults.
            $options = get_option('shashin_widget_single');
            $widgetTitle = empty($options['shashin_single_title']) ? '' : $options['shashin_single_title'];
            $photoKey = empty($options['shashin_single_photo_key']) ? 1 : $options['shashin_single_photo_key'];
            $maxSize = empty($options['shashin_single_max_size']) ? 160 : $options['shashin_single_max_size'];
            $captionYN = empty($options['shashin_single_caption_yn']) ? 'n' : $options['shashin_single_caption_yn'];
            $photo = new ShashinPhoto;
            $widget = $photo->getPhotoMarkup(array(null,$photoKey,$maxSize, $captionYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        function widgetRandom($args) {
            $options = get_option('shashin_widget_random');
            $widgetTitle = empty($options['shashin_random_title']) ? 'Random Image' : $options['shashin_random_title'];
            $albumKey = empty($options['shashin_random_album_key']) ? 'any' : $options['shashin_random_album_key'];
            $maxSize = empty($options['shashin_random_max_size']) ? 160 : $options['shashin_random_max_size'];
            $maxCols = empty($options['shashin_random_max_cols']) ? 1 : $options['shashin_random_max_cols'];
            $howMany = empty($options['shashin_random_how_many']) ? 1 : $options['shashin_random_how_many'];
            $captionYN = empty($options['shashin_random_caption_yn']) ? 'n' : $options['shashin_random_caption_yn'];
            $widget = ShashinPhoto::getRandomMarkup(array(null,$albumKey,$maxSize,$maxCols,$howMany,$captionYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        function widgetAlbum($args) {
            $options = get_option('shashin_widget_album');
            $widgetTitle = empty($options['shashin_album_title']) ? '' : $options['shashin_album_title'];
            $albumKey = empty($options['shashin_album_album_key']) ? 1 : $options['shashin_album_album_key'];
            $locationYN = empty($options['shashin_album_location_yn']) ? 'n' : $options['shashin_album_location_yn'];
            $pubdateYN = empty($options['shashin_album_pubdate_yn']) ? 'n' : $options['shashin_album_pubdate_yn'];
            $album = new ShashinAlbum;
            $widget = $album->getAlbumMarkup(array(null,$albumKey, $locationYN, $pubdateYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        function widgetThumbs($args) {
            $options = get_option('shashin_widget_thumbs');
            $widgetTitle = empty($options['shashin_thumbs_title']) ? '' : $options['shashin_thumbs_title'];
            $photoKeys = empty($options['shashin_thumbs_photo_keys']) ? 1 : $options['shashin_thumbs_photo_keys'];
            $maxSize = empty($options['shashin_thumbs_max_size']) ? 160 : $options['shashin_thumbs_max_size'];
            $maxCols = empty($options['shashin_thumbs_max_cols']) ? 1 : $options['shashin_thumbs_max_cols'];
            $captionYN = empty($options['shashin_thumbs_caption_yn']) ? 'n' : $options['shashin_thumbs_caption_yn'];
            $widget = ShashinPhoto::getThumbsMarkup(array(null,$photoKeys,$maxSize,$maxCols,$captionYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        function widgetNewest($args) {
            $options = get_option('shashin_widget_newest');
            $widgetTitle = empty($options['shashin_newest_title']) ? 'Random Image' : $options['shashin_newest_title'];
            $albumKey = empty($options['shashin_newest_album_key']) ? 'any' : $options['shashin_newest_album_key'];
            $maxSize = empty($options['shashin_newest_max_size']) ? 160 : $options['shashin_newest_max_size'];
            $maxCols = empty($options['shashin_newest_max_cols']) ? 1 : $options['shashin_newest_max_cols'];
            $howMany = empty($options['shashin_newest_how_many']) ? 1 : $options['shashin_newest_how_many'];
            $captionYN = empty($options['shashin_newest_caption_yn']) ? 'n' : $options['shashin_newest_caption_yn'];
            $widget = ShashinPhoto::getNewestMarkup(array(null,$albumKey,$maxSize,$maxCols,$howMany,$captionYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        function widgetAlbumThumbs($args) {
            $options = get_option('shashin_widget_album_thumbs');
            $widgetTitle = empty($options['shashin_album_thumbs_title']) ? '' : $options['shashin_album_thumbs_title'];
            $albumKeys = empty($options['shashin_album_thumbs_keys']) ? 1 : $options['shashin_album_thumbs_keys'];
            $maxCols = empty($options['shashin_album_thumbs_max_cols']) ? 1 : $options['shashin_album_thumbs_max_cols'];
            $locationYN = empty($options['shashin_album_thumbs_location_yn']) ? 'n' : $options['shashin_album_thumbs_location_yn'];
            $pubdateYN = empty($options['shashin_album_thumbs_pubdate_yn']) ? 'n' : $options['shashin_album_thumbs_pubdate_yn'];
            $widget = ShashinAlbum::getAlbumThumbsMarkup(array(null,$albumKeys,$maxCols,$locationYN,$pubdateYN));
            Shashin::_widgetDisplay($args, $widgetTitle, $widget);
        }

        register_sidebar_widget('Shashin: Single Image', widgetSingle, SHASHIN_PLUGIN_NAME);
        register_sidebar_widget('Shashin: Random Images', widgetRandom, SHASHIN_PLUGIN_NAME);
        register_sidebar_widget('Shashin: Single Album Thumbnail', widgetAlbum, SHASHIN_PLUGIN_NAME);
        register_sidebar_widget('Shashin: Image Thumbnails', widgetThumbs, SHASHIN_PLUGIN_NAME);
        register_sidebar_widget('Shashin: Newest Images', widgetNewest, SHASHIN_PLUGIN_NAME);
        register_sidebar_widget('Shashin: Album Thumbnails', widgetAlbumThumbs, SHASHIN_PLUGIN_NAME);
        register_widget_control('Shashin: Single Image', array(SHASHIN_PLUGIN_NAME, 'widgetSingleControl'), 500, 300);
        register_widget_control('Shashin: Random Images', array(SHASHIN_PLUGIN_NAME, 'widgetRandomControl'), 500, 300);
        register_widget_control('Shashin: Single Album Thumbnail', array(SHASHIN_PLUGIN_NAME, 'widgetAlbumControl'), 500, 300);
        register_widget_control('Shashin: Image Thumbnails', array(SHASHIN_PLUGIN_NAME, 'widgetThumbsControl'), 500, 300);
        register_widget_control('Shashin: Newest Images', array(SHASHIN_PLUGIN_NAME, 'widgetNewestControl'), 500, 300);
        register_widget_control('Shashin: Album Thumbnails', array(SHASHIN_PLUGIN_NAME, 'widgetAlbumThumbsControl'), 500, 300);
    }

    /**
     * Displays and processes the widget control form for single images.
     *
     * @static
     * @access public
     * @uses Shashin::_widgetControl()
     */
    function widgetSingleControl() {
        Shashin::_widgetControl('single');
    }

    /**
     * Displays and processes the widget control form for random images.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getAlbums()
     * @uses Shashin::_widgetControl()
     */
    function widgetRandomControl() {
        $albumsFull = ShashinAlbum::getAlbums("ORDER BY TITLE");
        $albums = array('any' => 'Any');

        foreach ($albumsFull as $album) {
            $albums[$album['album_key']] = $album['title'];
        }

        Shashin::_widgetControl('random', array($albums));
    }

    /**
     * Displays and processes the widget control form for album thumbnails.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getAlbums()
     * @uses Shashin::_widgetControl()
     */
    function widgetAlbumControl() {
        $albumsFull = ShashinAlbum::getAlbums("ORDER BY TITLE");

        foreach ($albumsFull as $album) {
            $albums[$album['album_key']] = $album['title'];
        }

        Shashin::_widgetControl('album', array($albums));
    }

    /**
     * Displays and processes the widget control form for a table of thumbnails.
     *
     * @static
     * @access public
     * @uses Shashin::_widgetControl()
     */
    function widgetThumbsControl() {
        // strip out any whitespace
        if ($_REQUEST["shashin_thumbs_photo_keys"]) {
            $_REQUEST["shashin_thumbs_photo_keys"] = str_replace(" ","",$_REQUEST["shashin_thumbs_photo_keys"]);
        }

        Shashin::_widgetControl('thumbs');
    }

    /**
     * Displays and processes the widget control form for newest images.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getAlbums()
     * @uses Shashin::_widgetControl()
     */
    function widgetNewestControl() {
        $albumsFull = ShashinAlbum::getAlbums("ORDER BY TITLE");
        $albums = array('any' => 'Any');

        foreach ($albumsFull as $album) {
            $albums[$album['album_key']] = $album['title'];
        }

        Shashin::_widgetControl('newest', array($albums));
    }

    /**
     * Displays and processes the widget control form for a table of album thumbnails.
     *
     * @static
     * @access public
     * @uses Shashin::_widgetControl()
     */
    function widgetAlbumThumbsControl() {
        if ($_REQUEST["shashin_album_thumbs_keys"]) {
            $_REQUEST["shashin_album_thumbs_keys"] = trim($_REQUEST["shashin_album_thumbs_keys"]);
        }

        Shashin::_widgetControl('album_thumbs');
    }

    /**
     * Displays the widget control form.
     *
     * @static
     * @access private
     */
    function _widgetControl($name, $args = null) {
        $options = $newOptions = get_option("shashin_widget_{$name}");

        // for handing the control form submission.
        if ($_REQUEST["shashin_{$name}_submit"]) {
            unset($_REQUEST["shashin_{$name}_submit"]);

            foreach ($_REQUEST as $k=>$v) {
                // make sure we're only capturing shashin data
                if (strpos($k, 'shashin') !== false && strpos($k, 'submit') === false) {
                    $newOptions[$k] = htmlspecialchars(stripslashes(trim($v)));
                }
            }

            if ($options != $newOptions) {
                update_option("shashin_widget_{$name}", $newOptions);
                // so the form data will be current if you re-open the form
                // widget without reloading the page
                $options = $newOptions;
            }
        }

        // for displaying the control form
          require(SHASHIN_DIR . "/display/widget-{$name}.php");
    }

    /**
     * Extracts widget args and displays a widget.
     *
     * @static
     * @access private
     */
    function _widgetDisplay($args, $widgetTitle, $widget) {
        // get the theme widget vars
        extract($args);
        // display widget
        echo $before_widget;
        echo $before_title . $widgetTitle . $after_title;
        echo $widget;
        echo $after_widget;
    }
}

Shashin::bootstrap();

?>
