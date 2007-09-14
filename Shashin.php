<?php

/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa photos in WordPress.
Author: Michael Toppa
Version: 1.0.4
Author URI: http://www.toppa.com
*/

/**
 * Shashin Class File
 *
 * @author Michael Toppa
 * @version 1.0.4
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007 Michael Toppa
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

define('SHASHIN_PLUGIN_NAME', 'Shashin');
define('SHASHIN_FILE', basename(__FILE__));
define('SHASHIN_DIR', dirname(__FILE__));
define('SHASHIN_PATH', SHASHIN_DIR . '/' . SHASHIN_FILE);
define('SHASHIN_ADMIN_URL', $_SERVER[PHP_SELF] . "?page=" . basename(SHASHIN_DIR) . '/' . SHASHIN_FILE);
define('SHASHIN_DISPLAY_NAME', 'Shashin');
define('SHASHIN_VERSION', '1.0.4');
define('SHASHIN_ALBUM_THUMB_SIZE', 160);
define('SHASHIN_ALBUM_TABLE', $wpdb->prefix . 'shashin_album');
define('SHASHIN_PHOTO_TABLE', $wpdb->prefix . 'shashin_photo');
define('SHASHIN_PICASA_SERVER', get_option('shashin_picasa_server'));
define('SHASHIN_USER_RSS', SHASHIN_PICASA_SERVER . '/data/feed/api/user/USERNAME?kind=album&alt=rss');
define('SHASHIN_ALBUM_RSS', SHASHIN_PICASA_SERVER . '/data/feed/api/user/USERNAME/albumid/ALBUMID?kind=photo&alt=rss');
define('GOOGLE_MAPS_QUERY_URL', 'http://maps.google.com/maps?q=');
define('SHASHIN_IMG_URL', '/wp-content/plugins/' . basename(SHASHIN_DIR) . '/display/');
define('SHASHIN_DOWNLOAD_URL', 'http://www.toppa.com/shashin-wordpress-plugin/');
define('SHASHIN_DEFAULT_SERVER', 'http://picasaweb.google.com');
define('SHASHIN_DEFAULT_DIV_PADDING', 10);
define('SHASHIN_DEFAULT_THUMB_PADDING', 6);

// get required libraries
require_once(SHASHIN_DIR . '/ShashinAlbum.php');
require_once(SHASHIN_DIR . '/ShashinPhoto.php');
require_once(SHASHIN_DIR . '/ToppaWPFunctions.php');

/**
 * The main class - handles all incomign requests for displaying photos, as well
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
     * @uses getCSS
     */
	function bootstrap() {
		// Add the installation and uninstallation hooks
		register_activation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'install'));
		register_deactivation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'uninstall'));

		// Add the actions and filters
        add_action('admin_menu', array(SHASHIN_PLUGIN_NAME, 'initAdminMenus'));
        // the 0 priority flag gets the div in before the autoformatter
        // can wrap it in a paragraph
        add_filter('the_content', array(SHASHIN_PLUGIN_NAME, 'parseContent'), 0);
        add_filter('wp_head', array(SHASHIN_PLUGIN_NAME, 'getCSS'));
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

        update_option('shashin_version', SHASHIN_VERSION);

        // this controls how much width to add to the shashin_image div, to
        // accommodate the padding applied to the image (since the width can
        // be anything, you don't want to hardcode this in the css)
        if (!strlen(get_option('shashin_div_padding'))) {
            update_option('shashin_div_padding', SHASHIN_DEFAULT_DIV_PADDING);
        }
        
        // same as above, but for images in a thumbnail table
        if (!strlen(get_option('shashin_div_padding'))) {
            update_option('shashin_thumb_padding', SHASHIN_DEFAULT_THUMB_PADDING);
        }

        // set the Picasa server to use
        if (!strlen(get_option('shashin_picasa_server'))) {
            update_option('shashin_picasa_server', SHASHIN_DEFAULT_SERVER);
        }
        
        if ($wpdb->get_var("show tables like '" . SHASHIN_ALBUM_TABLE . "'") != SHASHIN_ALBUM_TABLE) {
            $album = new ShashinAlbum();
            ToppaWPFunctions::createTable($album, SHASHIN_ALBUM_TABLE);
        }
        
        if ($wpdb->get_var("show tables like '" . SHASHIN_PHOTO_TABLE . "'") != SHASHIN_PHOTO_TABLE) {
            $photo = new ShashinPhoto();
            ToppaWPFunctions::createTable($photo, SHASHIN_PHOTO_TABLE);
        }
    }

    /**
     * Not currently implemented
     *
     * @access public
     */
	function uninstall() {
	}
        
    /**
     * Adds the Shashin management and option pages
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
     * - updateAlbums (handles submit from main admin page)
     * - syncAlbum (for syncing local album and photo data with Picasa)
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
            
       		require(SHASHIN_DIR . '/display/admin-edit.php');
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
      		require(SHASHIN_DIR . '/display/admin-main.php');
        }
        
        
        // add an album
        elseif ($_REQUEST['shashinAction'] == 'addAlbum') {
            $linkURL = trim($_REQUEST['link_url']);
            
            // validate the URL
            $pieces = explode("/", $linkURL);
            
            // send user back to the form if there are validation errors
            if ((($pieces[0] . "//" . $pieces[2]) != SHASHIN_PICASA_SERVER)
              || count($pieces) != 5
              || !strlen($pieces[3])
              || !strlen($pieces[4])) {
                $message = "That is not a valid Picasa album URL";
        		require(SHASHIN_DIR . '/display/admin-main.php');
            }
            
            // if no validation errors, add the album
            else {
                $album = new ShashinAlbum();
                // insert/update the album
                if ($album->setAlbum($pieces[3], $pieces[4], null, $_REQUEST['include_in_random']) === false) {
                    $message = "Album not added. SQL Error: ";
                    $dbError = true;
                }                
                
                // sync the photos
                elseif ($album->setAlbumPhotos() === false) {
                    $message = "Album metadata added, but failed to add photos. SQL Error: ";
                    $dbError = true;
                }
                
                // all is well
                else {
                    $message = "Album added";
                    
                    // clear user inputs so they're not displayed again
                    unset($_REQUEST['include_in_random']);
                    unset($_REQUEST['link_url']);
    
                    // refresh $allAlbums so the new album will appear
                    $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
                }
                
          		require(SHASHIN_DIR . '/display/admin-main.php');
            }
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
            
            // need to refresh allAlbums
            $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
      		require(SHASHIN_DIR . '/display/admin-main.php');
        }

        // sync album and its photos
        elseif ($_REQUEST['shashinAction'] == 'syncAlbum') {
            $album = new ShashinAlbum();

            if ($album->setAlbum($_REQUEST['user'], null, $_REQUEST['albumID']) === false) {
                $message = "Album sync failed. SQL Error: ";
                $dbError = true;
            }

            elseif ($album->setAlbumPhotos() === false) {
                $message = "Album metadata synced, but failed to sync photos. SQL Error: ";
                $dbError = true;
            }

            else {
                $message = "Album synchronized.";
            }

            // photo counts may have changed
            
            if ($dbError !== true) {
                $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
            }
            
      		require(SHASHIN_DIR . '/display/admin-main.php');
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
            
            $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
      		require(SHASHIN_DIR . '/display/admin-main.php');
        }
        
        // show summary of albums, and form to add a new one
        else {
            $album = new ShashinAlbum(); # needed in admin-main, for refData
            $allAlbums = ShashinAlbum::getAlbums("ORDER BY TITLE");
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

        // validate form inputs
        if ($_REQUEST['shashinAction'] == 'updateOptions') {
            $pieces = explode("/", $_REQUEST['picasaServer']);
            
            // send user back to the form if there are validation errors
            if ($pieces[0] != "http:" || !strlen($pieces[2]) || strlen($pieces[3])) {
                $message = "Invalid URL for Picasa Server";
            }
            
            // otherwise save the options
            else {
                update_option('shashin_picasa_server', $pieces[0] . "//" . $pieces[2]);
                update_option('shashin_div_padding', $_REQUEST['divPadding']);
                update_option('shashin_thumb_padding', $_REQUEST['thumbPadding']);
                $message = "Options saved.";
            }
        }

        // get option values
        $picasaServer = get_option('shashin_picasa_server');
        $divPadding = get_option('shashin_div_padding');
        $thumbPadding = get_option('shashin_thumb_padding');

		// Get the markup and display
  		require(SHASHIN_DIR . '/display/options-main.php');
		$optionsMenuHTML = ob_get_contents();
		ob_end_clean();
		echo $optionsMenuHTML;
    }
    
    
    /**
     * Gets the Shashin CSS file, for inclusion in the document head
     *
     * @static
     * @access public
     */    
    function getCSS() {
        echo '        <link rel="stylesheet" type="text/css" href="' . SHASHIN_IMG_URL . 'shashin.css" />';
    }
    
    /**
     * Replaces Shashin tags in posts and pages with XHTML displaying the
     * requested images.
     *
     * Supported Shashin tags:
     * - [simage=photo_key,max_size,caption_yn,float,clear]
     * - [srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     * - [salbum=album_key,location_yn,pubdate_yn,float,clear]
     * - [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]
     * - [snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear]
     *
     * For srandom and snewest tags, you can use the word "any" instead of an
     * album key to get photos from any album.
     *
     * For salbum, note that the thumbnail size is fixed by Picasa at 160x160,
     * and that you automaticaly get the album title and photo count.
     *
     * @static
     * @access public
     * @param string The content of the page or post
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @uses ShashinPhoto::getRandomMarkup()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinPhoto::getAlbumMarkup()
     * @uses ShashinPhoto::getThumbsMarkup()
     * @uses ShashinPhoto::getNewestMarkup
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
                
        return $content;
    }
}

Shashin::bootstrap();

?>