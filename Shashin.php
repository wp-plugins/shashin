<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa photos in WordPress.
Author: Michael Toppa
Version: 2.6.2
Author URI: http://www.toppa.com
*/

/**
 * Shashin is a WordPress plugin for integrating Picasa photos in WordPress.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage Classes
 *
 * Copyright 2007-2009 Michael Toppa
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

// note to self... for generating pot file...
// find -name "*.php"  ! -path "*.svn*" > /home/toppa/Scratch/shashin_files.txt
// xgettext --from-code=utf-8 --keyword=__ --keyword=_e --output=/opt/lampp/htdocs/wordpress/wp-content/plugins/shashin/languages/shashin.pot --files-from=/home/toppa/Scratch/shashin_files.txt

global $wpdb;
// from http://striderweb.com/nerdaphernalia/2008/09/hit-a-moving-target-in-your-wordpress-plugin/
if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
if (!defined('WP_PLUGIN_DIR'))  define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

define('SHASHIN_OPTIONS', get_option('shashin_options'));
define('SHASHIN_PLUGIN_NAME', 'Shashin');
define('SHASHIN_DISPLAY_NAME', 'Shashin');
define('SHASHIN_L10N_NAME', 'shashin');
define('SHASHIN_FILE', basename(__FILE__));
define('SHASHIN_DIR', dirname(__FILE__));
define('SHASHIN_PATH', SHASHIN_DIR . '/' . SHASHIN_FILE);
define('SHASHIN_ADMIN_URL', $_SERVER['PHP_SELF'] . "?page=" . basename(SHASHIN_DIR) . '/' . SHASHIN_FILE);
define('SHASHIN_VERSION', '2.4.2');
define('SHASHIN_ALBUM_THUMB_SIZE', 160); // Picasa offers album thumbnails at only 160x160
define('SHASHIN_ALBUM_TABLE', $wpdb->prefix . 'shashin_album');
define('SHASHIN_PHOTO_TABLE', $wpdb->prefix . 'shashin_photo');
define('SHASHIN_USER_RSS', '/data/feed/api/user/USERNAME?kind=album&alt=rss&hl=en_US');
define('SHASHIN_ALBUM_RSS', '/data/feed/api/user/USERNAME/albumid/ALBUMID?kind=photo&alt=rss');
define('SHASHIN_GOOGLE_MAPS_QUERY_URL', 'http://maps.google.com/maps?q=');
define('SHASHIN_DISPLAY_URL', WP_PLUGIN_URL . '/' . basename(SHASHIN_DIR) . '/display');
define('SHASHIN_FAQ_URL', 'http://www.toppa.com/shashin-wordpress-plugin');
define('SHASHIN_GOOGLE_PLAYER_URL', 'http://video.google.com/googleplayer.swf?videoUrl=');
define('SHASHIN_IMAGE_SIZES', serialize(array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800)));
define('SHASHIN_CROP_SIZES', serialize(array(32, 48, 64, 160)));
define('SHASHIN_PICASA_VIDEO_TYPES', serialize(array('MPG', 'AVI', 'ASF', 'WMV', 'MOV', 'MP4')));

// get required libraries
require_once(SHASHIN_DIR . '/ShashinAlbum.php');
require_once(SHASHIN_DIR . '/ShashinPhoto.php');
require_once(SHASHIN_DIR . '/ShashinWidget.php');
require_once(SHASHIN_DIR . '/browser/ShashinBrowser.php');

if (!in_array('ToppaWPFunctions', get_declared_classes())) {
    require_once(SHASHIN_DIR . '/ToppaWPFunctions.php');
}

if (!in_array('ToppaXMLParser', get_declared_classes())) {
    require_once(SHASHIN_DIR . '/ToppaXMLParser.php');
}

/**
 * The main class - directs traffic for all incoming requests.
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
     * @uses initAdminMenus
     * @uses parseContent
     * @uses getHeadTags
     */
    function bootstrap() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);
        $shashin_browser = new ShashinBrowser();

        // Add the activation and deactivation hooks
        register_activation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'install'));
        register_deactivation_hook(SHASHIN_PATH, array(SHASHIN_PLUGIN_NAME, 'unscheduleUpdate'));

        // For handling errors on install
        if ($_GET['action'] == 'error_scrape') {
            echo $_SESSION['shashin_activate_error'];
            unset($_SESSION['shashin_activate_error']);
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            deactivate_plugins($_GET['plugin']); // not working for some reason
            die();
        }

        // load localization
        load_plugin_textdomain(SHASHIN_L10N_NAME, false, basename(SHASHIN_DIR) . '/languages/');

        // Add the actions and filters
        add_action('admin_menu', array(SHASHIN_PLUGIN_NAME, 'initAdminMenus'));
        add_action('plugins_loaded', array('ShashinWidget', 'initWidgets'));
        add_action('admin_print_scripts-widgets.php', array(SHASHIN_PLUGIN_NAME, 'getAdminHeadTags'));
        add_action('template_redirect', array(SHASHIN_PLUGIN_NAME, 'getHeadTags'));

        // the 0 priority flag gets the shashin div in before the autoformatter
        // can wrap it in a paragraph
        add_filter('the_content', array(SHASHIN_PLUGIN_NAME, 'parseContent'), 0);

        // check whether we should update all albums every 10 hours
        // (Picasa video URLs expire every 11 hours)
        if ($shashin_options['scheduled_update'] == 'y' && $_REQUEST['shashin_options']['scheduled_update'] != 'n') {
            add_filter('cron_schedules', array(SHASHIN_PLUGIN_NAME, 'cron10Hours'));
            add_action('shashin_scheduled_update_hook', array(SHASHIN_PLUGIN_NAME, 'scheduledUpdate'));

            if (!wp_next_scheduled('shashin_scheduled_update_hook')) {
                wp_schedule_event(time(), 'every10hours', 'shashin_scheduled_update_hook');
            }
        }
    }

    /**
     * Updates Shashin options and creates the Shashin tables if they don't
     * already exist.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::createTable()
     */
    function install() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);
        $shashin_options_defaults = array(
            'picasa_server' => 'http://picasaweb.google.com',
            'picasa_auth_server' => 'https://www.google.com',
            'div_padding' => 10,
            'thumb_padding' => 6,
            'image_display' => 'highslide',
            'highslide_max' => 640,
            'prefix_captions' => 'n',
            'highslide_video_width' => 640,
            'highslide_video_height' => 480,
            'highslide_autoplay' => 'false',
            'highslide_interval' => 5000,
            'highslide_outline_type' => 'rounded-white',
            'highslide_dimming_opacity' => 0.75,
            'highslide_repeat' => '1',
            'highslide_v_position' => 'top',
            'highslide_h_position' => 'center',
            'highslide_hide_controller' => '0',
            'other_rel_image' => null,
            'other_rel_video' => null,
            'other_rel_delimiter' => null,
            'other_link_class' => null,
            'other_link_title' => null,
            'other_image_class' => null,
            'other_image_title' => null,
            'album_photos_max' => 160,
            'album_photos_cols' => 3,
            'album_photos_order' => 'picasa_order',
            'album_photos_captions' => 'n',
            'album_photos_description' => 'n',
            'scheduled_update' => 'n',
            'theme_max_size' => 600,
            'theme_max_single' => 576,
            'photos_per_page' => null,
            'caption_exif' => 'n',
            'picasa_username' => null,
            'picasa_password' => null,
            'group_by_user' => 'n',
        );

        // create/update tables
        $album = new ShashinAlbum();

        // the only way to handle errors during plugin activation is to force
        // a fatal PHP error - stupid WordPress!
        if (!ToppaWPFunctions::createTable($album, SHASHIN_ALBUM_TABLE)) {
            $_SESSION['shashin_activate_error'] = __("Failed to create or update table ", SHASHIN_L10N_NAME) . SHASHIN_ALBUM_TABLE;
            trigger_error('', E_USER_ERROR);
        }

        $photo = new ShashinPhoto();

        if (!ToppaWPFunctions::createTable($photo, SHASHIN_PHOTO_TABLE)) {
            $_SESSION['shashin_activate_error'] = __("Failed to create or update table ", SHASHIN_L10N_NAME) . SHASHIN_PHOTO_TABLE;
            trigger_error('', E_USER_ERROR);
        }

        // flag whether to add or update Shashin options below
        $add_options = empty($shashin_options);

        // set Shashin options
        $shashin_options['version'] = SHASHIN_VERSION;

        foreach ($shashin_options_defaults as $k=>$v) {
            if (!$shashin_options[$k]) {
                $shashin_options[$k] = $v;
            }
        }

        if ($add_options === false) {
            update_option('shashin_options', serialize($shashin_options));
        }

        else {
            add_option('shashin_options', serialize($shashin_options));
        }

        // delete old-style Shashin options if necessary
        $test = get_option('shashin_version');
        if ($test) {
            delete_option('shashin_album_photos_url');
            delete_option('shashin_div_padding');
            delete_option('shashin_highslide_autoplay');
            delete_option('shashin_highslide_interval');
            delete_option('shashin_highslide_max');
            delete_option('shashin_highslide_video_height');
            delete_option('shashin_highslide_video_width');
            delete_option('shashin_image_display');
            delete_option('shashin_picasa_server');
            delete_option('shashin_prefix_captions');
            delete_option('shashin_thumb_padding');
            delete_option('shashin_version');
            delete_option('shashin_widget_single');
            delete_option('shashin_widget_random');
            delete_option('shashin_widget_album');
            delete_option('shashin_widget_thumbs');
            delete_option('shashin_widget_newest');
            delete_option('shashin_widget_album_thumbs');
        }
    }

    /**
     * Deletes the Shashin tables and Shashin option setttings. This is
     * irrevocable!
     *
     * @static
     * @access public
     * @return boolean true: uninstall successful; false: uninstall failed
     */
    function uninstall() {
        global $wpdb;
        $sql = "drop table if exists " . SHASHIN_PHOTO_TABLE . ", " . SHASHIN_ALBUM_TABLE . ";";

        if ($wpdb->query($sql) === false) {
            return false;
        }

        else {
            delete_option('shashin_options');
        }

        return true;
    }

    /**
     * Adds a "every10hours" option to WP's cron array.
     * See http://codex.wordpress.org/Plugin_API/Filter_Reference/cron_schedules
     * @static
     * @access public
     */
    function cron10Hours($schedules) {
        $schedules['every10hours'] = array('interval' => 36000, 'display' => __('Every 10 Hours'));
        return $schedules;
    }

    /**
     * Updates all albums on a scheduled basis.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getUsers()
     * @uses ShashinAlbum::setUserAlbums()
     */
    function scheduledUpdate() {
        $users = ShashinAlbum::getUsers();

        if (!is_array($users)) {
            return false;
        }

        foreach ($users as $user) {
            list($result, $message, $db_error) = ShashinAlbum::setUserAlbums($user);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes the hook for scheduled album syncing.
     *
     * @static
     * @access public
     */
    function unscheduleUpdate() {
        wp_clear_scheduled_hook('shashin_scheduled_update_hook');
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
        if (strpos(basename($_SERVER['REQUEST_URI']), SHASHIN_FILE) !== false) {
            add_action("admin_print_styles", array(SHASHIN_PLUGIN_NAME, 'getAdminHeadTags'));
        }
    }

    /**
     * Performs the requested admin action, based on the value of
     * $_REQUEST['shashin_action'] values.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbum()
     * @uses ShashinAlbum::getAlbumPhotos()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhoto()
     * @uses ShashinPhoto::setPhotoLocal()
     * @uses ShashinAlbum::setAlbum()
     * @uses ShashinAlbum::setAlbumPhotos()
     * @uses ShashinAlbum::getAlbums()
     * @uses ShashinAlbum::setAlbumLocal()
     * @uses ShashinAlbum::deleteAlbum()
     */
    function getAdminMenu() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        switch ($_REQUEST['shashin_action']) {
        // show selected album for editing
        case 'edit_album_photos':
            if (is_numeric($_REQUEST['album_id'])) {
                $album = new ShashinAlbum();
                list($result, $message, $db_error) = $album->getAlbum(array('album_id' => $_REQUEST['album_id']));

                if ($result === true) {
                    $order_by = $_REQUEST['shashin_orderby'] ? $_REQUEST['shashin_orderby'] : 'picasa_order';
                    list($result, $message, $db_error) = $album->getAlbumPhotos($order_by);
                    unset($message); // no need to display a message in this case.
                }
            }

            $display = 'admin-edit';
            break;
        // save updated local photo data
        case 'update_album_photos':
            if (is_numeric($_REQUEST['album_id'])) {
                $album = new ShashinAlbum();
                list($result, $message, $db_error) = $album->getAlbum(array('album_id' => $_REQUEST['album_id']));

                if ($result === true) {
                    list($result, $message, $db_error) = $album->getAlbumPhotos();
                    unset($message); // no need to display a message in this case.

                    // compare new values to old to see which records need updating
                    // (better than running a bunch of unneeded updates)
                    foreach ($album->data['photos'] as $photos_data) {
                        foreach($_REQUEST['include_in_random'] as $k=>$v) {
                            if ($photos_data['photo_id'] == $k && $photos_data['include_in_random'] != $v) {
                                $photo = new ShashinPhoto();
                                list($result, $message, $db_error) = $photo->getPhoto(null, $photos_data);

                                if ($result !== true) {
                                    break;
                                }

                                list($result, $message, $db_error) = $photo->setPhotoLocal(array('include_in_random' => $v));

                                if ($result !== true) {
                                    break;
                                }
                            }
                        }

                        if ($db_error === true) {
                            break;
                        }
                    }

                    if ($db_error !== true) {
                        $message = __("Updates saved.", SHASHIN_L10N_NAME);
                    }
                }
            }

            else {
                $message = __("No valid album ID supplied", SHASHIN_L10N_NAME);
            }

            $display = 'admin-main';
            break;
        // add an album (or all of a user's albums)
        case 'add_album':
            $link_url = trim($_REQUEST['link_url']);

            // remove any trailing # from the url - these often appear in Picasa
            // album links, and they trip up the RSS feed
            if (strpos($link_url, "#") == strlen($link_url) - 1) {
                $link_url = substr($link_url, 0, -1);
            }

            $pieces = explode("/", $link_url);

            // validate the URL
            if ((($pieces[0] . "//" . $pieces[2]) != $shashin_options['picasa_server']) || !$pieces[3]) {
                $message = __("That is not a valid URL for your Picasa server.", SHASHIN_L10N_NAME);
            }

            // if no validation errors, and we have a single album, add it
            else if ($pieces[4]) {
                $album = new ShashinAlbum();
                list($result, $message, $db_error) = $album->setAlbum($pieces[3], array('name' => $pieces[4]), array('include_in_random' => $_REQUEST['include_in_random']));

                if ($result === true) {
                    list($result, $message, $db_error) = $album->setAlbumPhotos();

                    // all is well
                    if ($result === true) {
                        $message = __("Album added.", SHASHIN_L10N_NAME);
                        // clear user inputs so they're not displayed again
                        unset($_REQUEST['include_in_random']);
                        unset($_REQUEST['link_url']);
                    }
                }
            }

            // if no validation errors, and we're adding all albums
            else {
                list($result, $message, $db_error) = ShashinAlbum::setUserAlbums($pieces[3], array('include_in_random' => $_REQUEST['include_in_random']), false);

                if ($result === true) {
                    $message = __("Albums added.", SHASHIN_L10N_NAME);
                }
            }

            $display = 'admin-main';
            break;
        // sync all albums
        case 'sync_all':
            $user = htmlentities($_REQUEST['users']);
            list($result, $message, $db_error) = ShashinAlbum::setUserAlbums($user);

            if ($result === true) {
                $message = __("All albums synced for ", SHASHIN_L10N_NAME) . $user;
            }

            $display = 'admin-main';
            break;
        // update albums' include_in_random flag
        case 'update_albums':
            $all_albums = ShashinAlbum::getAlbums('*', null, "order by title");

            if (is_array($all_albums)) {
                foreach ($all_albums as $album_data) {
                    $album = new ShashinAlbum();
                    list($result, $message, $db_error) = $album->getAlbum(null, $album_data);

                    if ($result !== true) {
                        break;
                    }

                    foreach($_REQUEST['include_in_random'] as $k=>$v) {
                        if ($album->data['album_id'] == $k && $album->data['include_in_random'] != $v) {
                            list($result, $message, $db_error) = $album->setAlbumLocal(array('include_in_random' => $v));

                            if ($result !== true) {
                                break;
                            }
                        }
                    }

                    if ($db_error === true) {
                       break;
                    }
                }
            }

            else {
                $message = __("Failed to retrieve album data.", SHASHIN_L10N_NAME);
                $db_error = true;
            }

            if (!$db_error) {
                $message = __("Updates saved.", SHASHIN_L10N_NAME);
            }

            $display = 'admin-main';
            break;
        // sync album and its photos
        case 'sync_album':
            $album = new ShashinAlbum();
            list($result, $message, $db_error) = $album->setAlbum($_REQUEST['user'], array('album_id' => $_REQUEST['album_id']));

            if ($result === true) {
                list($result, $message, $db_error) = $album->setAlbumPhotos();

                if ($result === true) {
                    $message = __("Album synchronized.", SHASHIN_L10N_NAME);
                }
            }

            $display = 'admin-main';
            break;
        // delete requested album
        case 'delete_album':
            $album = new ShashinAlbum();
            list($result, $message, $db_error) = $album->getAlbum(array('album_id' => $_REQUEST['album_id']));

            if ($result === true) {
                list($result, $message, $db_error) = $album->deleteAlbum();

                if ($result === true) {
                    $message = __("Album deleted.");
                }
            }

            $display = 'admin-main';
            break;
        // show summary of albums, and form to add a new one
        default:
            $display = 'admin-main';
        }

        // Start the cache
        ob_start();

        // decide which admin menu to show
        if ($display == 'admin-edit') {
               require(SHASHIN_DIR . '/display/admin-edit.php');
        }

        else {
            $users = ShashinAlbum::getUsers();
            $order_by = $_REQUEST['shashin_orderby'] ? $_REQUEST['shashin_orderby'] : 'title';
            $all_albums = ShashinAlbum::getAlbums('*', null, "order by $order_by");

            if (!is_array($users)) {
                $message = __("Failed to retrieve user data.", SHASHIN_L10N_NAME);
                $db_error = true;
            }

            elseif ($all_albums === false) {
                $message = __("Failed to retrieve album data.", SHASHIN_L10N_NAME);
                $db_error = true;
            }

            else {
                $album = new ShashinAlbum(); // needed in admin-main, for ref_data
                $user_names = array();

                foreach ($users as $user) {
                    $user_names[$user] = $user;
                }

                $sync_all = array('input_type' => 'select', 'input_subgroup' => $user_names);
            }

            // check that re-activation has been done
            //if ($shashin_options['version'] != SHASHIN_VERSION) {
            //    $message = __("To complete the Shashin upgrade, please deactivate and reactivate Shashin from your plugins menu, and then re-sync all albums.", SHASHIN_L10N_NAME);
            //}

            require(SHASHIN_DIR . '/display/admin-main.php');
        }

        // Get the markup and display
        $adminMenuHTML = ob_get_contents();
        ob_end_clean();
        echo $adminMenuHTML;
    }

    /**
     * Generates and echoes the HTML for the Shashin settings menu and sets
     * Shashin options in WordPress.
     *
     * @static
     * @access public
     */
    function getOptionsMenu() {
        // can't use the SHASHIN_OPTIONS constant as the options may have changed
        $shashin_options = unserialize(get_option('shashin_options'));

        // Start the cache
        ob_start();

        switch ($_REQUEST['shashin_action']) {
        case 'uninstall':
            // make doubly sure they want to uninstall
            if ($_REQUEST['shashin_uninstall'] == 'y') {
                if (Shashin::uninstall() == true) {
                    $message = __("Shashin has been uninstalled. You can now deactivate Shashin on your plugins management page.", SHASHIN_L10N_NAME);
                }

                else {
                    $message = __("Uninstall of Shashin failed. Database error:", SHASHIN_L10N_NAME);
                    $db_error = true;
                }
            }

            else {
                $message = __("You must check the 'Uninstall Shashin' checkbox to confirm you want to uninstall Shashin", SHASHIN_L10N_NAME);
            }

            break;
        case 'update_options':
            // make sure the Picasa URL looks valid
            $pieces = explode("/", trim($_REQUEST['shashin_options']['picasa_server']));

            if ($pieces[0] != "http:" || !strlen($pieces[2]) || strlen($pieces[3])) {
                $message = __("Invalid URL for Picasa Server", SHASHIN_L10N_NAME);
            }

            // save the options
            else {
                $_REQUEST['shashin_options']['picasa_server'] = "http://" . $pieces[2];
                array_walk($_REQUEST['shashin_options'], array(SHASHIN_PLUGIN_NAME, '_htmlentities'));
                array_walk($_REQUEST['shashin_options'], array(SHASHIN_PLUGIN_NAME, '_trim'));

                // remove scheduled updates if scheduling is turned off
                if ($_REQUEST['shashin_options']['scheduled_update'] == 'n') {
                    Shashin::unscheduleUpdate();
                }

                // deal with y/n checkbox inputs (better abstraction for this would be nice...)
                $checkboxes = array('other_link_title', 'other_image_title');

                foreach ($checkboxes as $checkbox) {
                    if (!$_REQUEST['shashin_options'][$checkbox]) {
                        $_REQUEST['shashin_options'][$checkbox] = 'n';
                    }
                }

                // determine the largest Picasa size for single images
                $shashin_options['theme_max_single'] = ShashinPhoto::_setMaxPicasaSize($_REQUEST['shashin_options']['theme_max_size'], 1);

                // determine the authentication server
                $more_pieces = explode(".", $pieces[2]);
                $shashin_options['picasa_auth_server'] = 'https://www.google.' . $more_pieces[count($more_pieces)-1];
                $shashin_options = array_merge($shashin_options, $_REQUEST['shashin_options']);
                update_option('shashin_options', serialize($shashin_options));
                $message = __("Shashin settings saved.", SHASHIN_L10N_NAME);
            }
            break;
        }

        $shashin_image_sizes = unserialize(SHASHIN_IMAGE_SIZES);
        $shashin_crop_sizes = unserialize(SHASHIN_CROP_SIZES);

        // check that re-activation has been done
        //if ($shashin_options['version'] != SHASHIN_VERSION) {
        //    $message = __("To complete the Shashin upgrade, please deactivate and reactivate Shashin from your plugins menu, and then re-sync all albums.", SHASHIN_L10N_NAME);
        //}

        // Get the markup and display
        require(SHASHIN_DIR . '/display/options-main.php');
        $options_form = ob_get_contents();
        ob_end_clean();
        echo $options_form;
    }


    /**
     * Gets the Shashin CSS file, and the optional Highslide CSS and
     * JS, for inclusion in the document head. Will get a copy of shashin.css
     * from your theme directory if you put a custom one there.
     *
     * @static
     * @access public
     */
    function getHeadTags() {
        $shashin_options = unserialize(SHASHIN_OPTIONS);

        if (file_exists(TEMPLATEPATH . '/shashin.css')) {
            $shashin_css = get_bloginfo('template_directory') . '/shashin.css';
        }

        else {
            $shashin_css = SHASHIN_DISPLAY_URL . '/shashin.css';
        }

        wp_enqueue_style('shashin_css', $shashin_css, false, SHASHIN_VERSION);

        if ($shashin_options['image_display'] == 'highslide') {
            if (file_exists(TEMPLATEPATH . '/highslide.css')) {
                $highslide_css = get_bloginfo('template_directory') . '/highslide.css';
            }

            else {
                $highslide_css = SHASHIN_DISPLAY_URL . '/highslide.css';
            }

            wp_enqueue_style('highslide_css', $highslide_css, false, '4.1.4');
            wp_enqueue_script('highslide', SHASHIN_DISPLAY_URL . '/highslide/highslide.js', false, '4.1.4');
            wp_enqueue_script('swfobject', SHASHIN_DISPLAY_URL . '/highslide/swfobject.js', false, '2.1');
            wp_enqueue_script('highslide_settings', SHASHIN_DISPLAY_URL . '/highslide_settings.js', false, SHASHIN_VERSION);
            wp_localize_script('highslide_settings', 'highslide_settings', array(
                'graphics_dir' => SHASHIN_DISPLAY_URL . '/highslide/graphics/',
                'outline_type' => $shashin_options['highslide_outline_type'],
                'dimming_opacity' => $shashin_options['highslide_dimming_opacity'],
                'interval' => $shashin_options['highslide_interval'],
                'repeat' => $shashin_options['highslide_repeat'],
                'position' => $shashin_options['highslide_v_position'] . ' ' . $shashin_options['highslide_h_position'],
                'hide_controller' => $shashin_options['highslide_hide_controller']
            ));
        }
    }

    /**
     * Gets the Shashin Admin CSS file, for inclusion on the widget management
     * page and the Shashin admin pages.
     *
     * @static
     * @access public
     */
    function getAdminHeadTags() {
        wp_enqueue_style('shashin_admin_css', SHASHIN_DISPLAY_URL . '/shashin-admin.css', false, SHASHIN_VERSION);
    }

    /**
     * Replaces Shashin tags in posts and pages with XHTML displaying
     * the requested images.
     *
     * Supported Shashin tags:
     * - [simage=photo_key,max_size,caption_yn,float,clear]
     * - [srandom=album_key1|album_key2|etc,max_size,max_cols,how_many,caption_yn,float,clear]
     * - DEPRECATED - [salbum=album_key,location_yn,pubdate_yn,float,clear]
     * - [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]
     * - [snewest=album_key1|album_key2|etc,max_size,max_cols,how_many,caption_yn,float,clear]
     * - [salbumthumbs=album_key1|album_key2|etc,max_cols,location_yn,pubdate_yn,float,clear]
     * - [salbumphotos=album_key,max_size,max_cols,caption_yn,description_yn,order_by,float,clear]
     * - [salbumlist=album_key1|album_key2|etc,info_yn]
     *
     * For srandom and snewest tags, you can use the word "any" instead
     * of an album key to get photos from any album. For salbum,
     * salbumthumbs, and salbumlist, note that the thumbnail size is
     * fixed by Picasa at 160x160. For salbumthumbs and salbumlist, if you
     * want to view all the albums, you can substitute a column name to
     * order by for the keys.
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
        $simage = "/\[simage=(\d+),(\d{2,4}|max),?(\w?),?(\w{0,6}),?(\w{0,5}),?(\d*)\]/";

        if (preg_match_all($simage, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'photo_key' => $match[1], 'max_size' => $match[2],
                    'caption_yn' => $match[3], 'float' => $match[4], 'clear' => $match[5], 'alt_thumb' => $match[6]);
                $photo = new ShashinPhoto();
                $markup = $photo->getPhotoMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $srandom = "/\[srandom=([\w\|]+),(\d{2,4}|max),(\d+|max),(\d+),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($srandom, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'max_size' => $match[2],
                    'max_cols' => $match[3], 'how_many' => $match[4],
                    'caption_yn' => $match[5], 'float' => $match[6], 'clear' => $match[7]);
                $markup = ShashinPhoto::getRandomMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $salbum = "/\[salbum=(\d+),?(\w?),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($salbum, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'location_yn' => $match[2],
                    'pubdate_yn' => $match[3], 'float' => $match[4], 'clear' => $match[5]);
                $album = new ShashinAlbum();
                $markup = $album->getAlbumMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $sthumbs = "/\[sthumbs=([\d\|]+),(\d{2,4}|max),(\d+|max),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sthumbs, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'photo_key' => $match[1],
                    'max_size' => $match[2], 'max_cols' => $match[3],
                    'caption_yn' => $match[4], 'float' => $match[5],
                    'clear' => $match[6]);
                $markup = ShashinPhoto::getThumbsMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $snewest = "/\[snewest=([\w\|]+),(\d{2,4}|max),(\d+|max),(\d+),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($snewest, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'max_size' => $match[2],
                    'max_cols' => $match[3], 'how_many' => $match[4],
                    'caption_yn' => $match[5], 'float' => $match[6], 'clear' => $match[7]);
                $markup = ShashinPhoto::getNewestMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $salbumthumbs = "/\[salbumthumbs=([\w\|\ ]+),(\d+),?(\w?),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($salbumthumbs, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'max_cols' => $match[2],
                    'location_yn' => $match[3], 'pubdate_yn' => $match[4],
                    'float' => $match[5], 'clear' => $match[6]);
                $markup = ShashinAlbum::getAlbumThumbsMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $salbumphotos = "/\[salbumphotos=(\d+),(\d{2,4}|max),(\d+|max),?(\w?),?(\w?),?([\w ]{0,}),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($salbumphotos, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'max_size' => $match[2],
                    'max_cols' => $match[3], 'caption_yn' => $match[4],
                    'description_yn' => $match[5], 'order_by' => $match[6],
                    'float' => $match[7], 'clear' => $match[8]);
                $markup = ShashinPhoto::getAlbumPhotosMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        $salbumlist = "/\[salbumlist=([\w+\|\ ]+),?(\w?)\]/";

        if (preg_match_all($salbumlist, $content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
                $named = array('tag' => $match[0], 'album_key' => $match[1], 'info_yn' => $match[2]);
                $markup = ShashinAlbum::getAlbumListMarkup($named);
                $content = str_replace($named['tag'], $markup, $content);
            }
        }

        return $content;
    }

    /**
     * Wrapper for ShashinPhoto->getPhotoMarkup()
     *
     * @static
     * @access public
     * @param int $photo_key (required): the Shashin photo_key (not the Picasa image ID)
     * @param int $max_size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param string $caption_yn (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ShashinPhoto::getPhotoMarkup()
     * @return string xhtml to display photo
     */
    function getSingle($photo_key, $max_size, $caption_yn = null, $float = null, $clear = null) {
        $named = compact('photo_key', 'max_size', 'caption_yn', 'float',
            'clear');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        $photo = new ShashinPhoto();
        return $photo->getPhotoMarkup($named);
    }

    /**
     * Wrapper for ShashinPhoto::getRandomMarkup()
     *
     * @static
     * @access public
     * @param int $album_key (required): a Shashin album_key (not the Picasa album ID) or "any" for pictures from any album
     * @param int $max_size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $max_cols (required): how many columns the table will have
     * @param int $how_many (required): how many random pictures to show
     * @param string $caption_yn (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getRandomMarkup()
     * @return string xhtml to display table of random photos
     */
    function getRandom($album_key, $max_size, $max_cols, $how_many, $caption_yn = null, $float = null, $clear = null) {
        $named = compact('album_key', 'max_size', 'max_cols', 'how_many',
            'caption_yn', 'float', 'clear');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        return ShashinPhoto::getRandomMarkup($named);
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumMarkup()
     *
     * @static
     * @access public
     * @param int $album_key (required): a Shashin album_key (not the Picasa album ID)
     * @param string $location_yn (optional): y or n to show the location of the image, with a link to Google Maps.
     * @param string $pubdate_yn (optional): y or n to show the pub date of the album
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinAlbum::getAlbumMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbum($album_key, $location_yn = null, $pubdate_yn = null, $float = null, $clear = null) {
        $named = compact('album_key', 'location_yn', 'pubdate_yn', 'float',
            'clear');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        $album = new ShashinAlbum;
        return $album->getAlbumMarkup($named);
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumListMarkup()
     *
     * @static
     * @access public
     * @param string $album_key (required): Shashin album keys (not the Picasa album ID), or a column name to order by
     * @param string $info_yn (optional): y or n to show the album location, pub date, and number of pictures.
     * @param boolean $force_picasa (optional): force the album link to point to Picasa (default: true)
     * @uses ShashinAlbum::getAlbumThumbsMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbumList($album_key, $info_yn = null, $force_picasa = true) {
        $named = compact('album_key', 'info_yn', 'force_picasa');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        return ShashinAlbum::getAlbumListMarkup($named);
    }

    /**
     * Wrapper for ShashinPhoto::getThumbsMarkup()
     *
     * @static
     * @access public
     * @param string $photo_key (required): Shashin photo keys, pipe delimited (not the Picasa image IDs)
     * @param int $max_size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $max_cols (required): how many columns the table will have
     * @param int $how_many (required): how many columns the table will have
     * @param string $caption_yn (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getThumbsMarkup()
     * @return string xhtml to display table of thumbnails
     */
    function getThumbs($photo_key, $max_size, $max_cols, $caption_yn = null, $float = null, $clear = null) {
        $named = compact('photo_key', 'max_size', 'max_cols', 'caption_yn',
            'float', 'clear');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        return ShashinPhoto::getThumbsMarkup($named);
    }

    /**
     * Wrapper for ShashinPhoto::getNewestMarkup()
     *
     * @static
     * @access public
     * @param int $album_key (required): a Shashin album_key (not the Picasa album ID)
     * @param int $max_size (required): the desired max dimension. Note Picasa allows only certain sizes.
     * @param int $max_cols (required): how many columns the table will have
     * @param int $how_many (required): how many random pictures to show
     * @param string $caption_yn (optional): y or n to show the image description as a caption (defaults to n)
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @uses ShashinPhoto::getNewestMarkup()
     * @return string xhtml to display table of newest photos
     */
    function getNewest($album_key, $max_size, $max_cols, $how_many, $caption_yn = null, $float = null, $clear = null) {
        $named = compact('album_key', 'max_size', 'max_cols', 'how_many',
            'caption_yn', 'float', 'clear');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        return ShashinPhoto::getNewestMarkup($named);
    }

    /**
     * Wrapper for ShashinAlbum->getAlbumThumbsMarkup()
     *
     * @static
     * @access public
     * @param string $album_key (required): Shashin album keys (not the Picasa album ID), or a column name to order by
     * @param int $max_cols (required): how many columns the table will have
     * @param string $location_yn (optional): y or n to show the location of the image, with a link to Google Maps.
     * @param string $pubdate_yn (optional): y or n to show the pub date of the album
     * @param string $float (optional): a css float value (left, right, or none) (no default)
     * @param string $clear (optional): a css clear value (left, right, or both) (no default)
     * @param boolean $force_picasa (optional): force the album link to point to Picasa (default: true)
     * @uses ShashinAlbum::getAlbumThumbsMarkup()
     * @return string xhtml to display album thumbnail
     */
    function getAlbumThumbs($album_key, $max_cols, $location_yn = null, $pubdate_yn = null, $float = null, $clear = null, $force_picasa = true) {
        $named = compact('album_key', 'max_cols', 'location_yn', 'pubdate_yn', 'float', 'clear', 'force_picasa');
        array_walk($named, array(SHASHIN_PLUGIN_NAME, '_strtolower'));
        return ShashinAlbum::getAlbumThumbsMarkup($named);
    }

    /**
     * array_walk callback method for htmlentities()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    function _htmlentities(&$string, $key) {
        $string = htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * array_walk callback method for trim()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    function _trim(&$string, $key) {
        $string = trim($string);
    }

    /**
     * array_walk callback method for strtolower()
     *
     * @static
     * @access private
     * @param string $string (required): the string to update
     * @param mixed $key (ignored): the array key of the string (not needed but passed automatically by array_walk)
     */
    function _strtolower(&$string, $key) {
        $string = strtolower($string);
    }
}

Shashin::bootstrap();

?>
