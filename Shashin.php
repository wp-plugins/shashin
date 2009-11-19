<?php
/**
 * Shashin is a WordPress plugin for displaying Picasa, Flickr, and Twitpic photos in WordPress.
 *
 * @author Michael Toppa
 * @version 3.0
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
/**
 * The main class - directs traffic for all incoming requests.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */

class Shashin {
    public $options;
    public $album_table;
    public $photo_table;
    public $name = 'Shashin';
    public $version = '3.0';
    public $faq_url = 'http://www.toppa.com/shashin-wordpress-plugin/';
    public $picasa_sizes = array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800);
    public $picasa_crop = array(32, 48, 64, 160);
    public $flickr_sizes = array(75, 100, 240, 500, 1024);
    public $flickr_crop = array(75);
    public $twitpic_sizes = array(75, 150, 600);
    public $twitpic_crop = array(75, 150);

    /**
     * Get options, register hooks.
     *
     * @access public
     * @uses install
     * @uses uninstall
     * @uses initAdminMenus
     * @uses parseContent
     * @uses getHeadTags
     */
    public function __construct() {
        global $wpdb;
        $this->options = unserialize(get_option('shashin_options'));

        // load localization
        load_plugin_textdomain('shashin', false, basename(SHASHIN_DIR) . '/languages/');
        // add Tools and Settings menus
        add_action('admin_menu', array($this, 'initAdminMenus'));
    }

    /**
     * Updates Shashin options and create/update the Shashin tables
     *
     * @static
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::createTable()
     */
    public function install() {
        $options_defaults = array(
            'scheduled_update' => 'n',
            'prefix_captions' => 'n',
            'caption_exif' => 'none',
            'div_padding' => 10,
            'thumb_padding' => 6,
            'theme_max_size' => 600,
            'image_display' => 'highslide',
            'picasa_max' => 640,
            'picasa_theme_max_single' => 576,
            'flickr_theme_max_single' => 500,
            'twitpic_theme_max_single' => 600,
            'highslide_autoplay' => 'false',
            'highslide_interval' => 5000,
            'highslide_repeat' => '1',
            'highslide_video_width' => 640,
            'highslide_video_height' => 480,
            'highslide_outline_type' => 'rounded-white',
            'highslide_v_position' => 'top',
            'highslide_h_position' => 'center',
            'highslide_dimming_opacity' => 0.75,
            'highslide_hide_controller' => '1',

        );

        // flag whether to add or update Shashin options below
        $add_options = empty($this->options);

        // set Shashin options
        $this->options['version'] = $this->version;

        foreach ($options_defaults as $k=>$v) {
            if (!$this->options[$k]) {
                $this->options[$k] = $v;
            }
        }

        if ($add_options === false) {
            update_option('shashin_options', serialize($this->options));
        }

        else {
            add_option('shashin_options', serialize($this->options));
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

        // create/update tables
        $album = new ShashinAlbum($this);

        if (!ToppaWPFunctions::createTable($album, SHASHIN_ALBUM_TABLE)) {
            trigger_error('', E_USER_ERROR);
        }

        $photo = new ShashinPhoto($this);

        if (!ToppaWPFunctions::createTable($photo, SHASHIN_PHOTO_TABLE)) {
            trigger_error('', E_USER_ERROR);
        }

        return true;
    }


    /**
     * Adds the Shashin management and option pages.
     *
     * @static
     * @access public
     * @uses getAdminMenu()
     * @uses getOptionsMenu()
     */
    public function initAdminMenus() {
        $options_page = add_options_page('Shashin', 'Shashin', 'manage_options', 'ShashinBoot', array($this, 'getSettingsMenu'));
        add_action("admin_print_styles-$options_page", array($this, 'getAdminHeadTags'));
        add_management_page('Shashin', 'Shashin', 'edit_posts', 'ShashinBoot', array($this, 'getToolsMenu'));
    }

    public function getSettingsMenu() {
        if ($_REQUEST['shashin_action'] && !check_admin_referer('shashin_nonce', 'shashin_nonce')) {
            return false;
        }

        // can't use $this->options as the options may have changed during the session
        $options = unserialize(get_option('shashin_options'));

        // Start the cache
        ob_start();

        switch ($_REQUEST['shashin_action']) {
        case 'uninstall':
            // make doubly sure they want to uninstall
            if ($_REQUEST['shashin_uninstall'] == 'y') {
                try {
                    $message = $this->uninstall();
                }

                catch (Exception $e) {
                    $message = $e->getMessage();
                    $db_error = true;
                }
            }

            else {
                $message = __("You must check the 'Uninstall Shashin' checkbox to confirm you want to uninstall Shashin", 'shashin');
            }

            break;
        case 'update_options':
            try {
                array_walk($_REQUEST['shashin_options'], array('ToppaWPFunctions', 'awHtmlentities'));
                array_walk($_REQUEST['shashin_options'], array('ToppaWPFunctions', 'awTrim'));

                // remove scheduled updates if scheduling is turned off
                if ($_REQUEST['shashin_options']['scheduled_update'] == 'n') {
                    wp_clear_scheduled_hook('shashin_scheduled_update_hook');
                }

                // deal with y/n checkbox inputs (better abstraction for this would be nice...)
                $checkboxes = array('other_link_title', 'other_image_title');

                foreach ($checkboxes as $checkbox) {
                    if (!$_REQUEST['shashin_options'][$checkbox]) {
                        $_REQUEST['shashin_options'][$checkbox] = 'n';
                    }
                }

                // determine the largest theme supported sizes for single images
                $this->options['picasa_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->picasa_sizes);
                $this->options['flickr_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->flickr_sizes);
                $this->options['twitpic_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->twitpic_sizes);
                $this->options = array_merge($this->options, $_REQUEST['shashin_options']);
                update_option('shashin_options', serialize($this->options));
                $message = __("Shashin settings saved.", 'shashin');
            }

            catch (Exception $e) {
                $message = $e->getMessage();
            }
            break;
        }

        // Get the markup and display
        require(SHASHIN_DIR . '/display/settings.php');
        $options_form = ob_get_contents();
        ob_end_clean();
        echo $options_form;
        return true;
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
    public function getToolsMenu() {
        if ($_REQUEST['shashin_action'] && !check_admin_referer('shashin_nonce', 'shashin_nonce')) {
            return false;
        }

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

            $display = 'tools';
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

            $display = 'tools';
            break;
        // sync all albums
        case 'sync_all':
            $user = htmlentities($_REQUEST['users']);
            list($result, $message, $db_error) = ShashinAlbum::setUserAlbums($user);

            if ($result === true) {
                $message = __("All albums synced for ", SHASHIN_L10N_NAME) . $user;
            }

            $display = 'tools';
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

            $display = 'tools';
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

            $display = 'tools';
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

            $display = 'tools';
            break;
        // default is to show summary of albums, and form to add a new one
        default:
            $display = 'tools';
        }

        // Start the cache
        ob_start();

        // display the selected tools page
        if ($display == 'admin-edit') {
               require(SHASHIN_DIR . '/display/admin-edit.php');
        }

        else {
            try {
                $users = ShashinAlbum::getUsers();
                $order_by = $_REQUEST['shashin_orderby'] ? $_REQUEST['shashin_orderby'] : 'title';
                $all_albums = ShashinAlbum::getAlbums('*', null, "order by $order_by");
                $album = new ShashinAlbum(); // needed in tools, for ref_data
                $user_names = array();

                foreach ($users as $user) {
                    $user_names[$user] = $user;
                }

                $sync_all = array('input_type' => 'select', 'input_subgroup' => $user_names);
            }

            catch (Exception $e) {
                $message = $e->getMessage();
                $db_error = true;
            }

            // check that re-activation has been done
            //if ($shashin_options['version'] != SHASHIN_VERSION) {
            //    $message = __("To complete the Shashin upgrade, please deactivate and reactivate Shashin from your plugins menu, and then re-sync all albums.", SHASHIN_L10N_NAME);
            //}

            require(SHASHIN_DIR . '/display/tools.php');
        }

        // Get the markup and display
        $adminMenuHTML = ob_get_contents();
        ob_end_clean();
        echo $adminMenuHTML;
    }


    /**
     * Gets the Shashin Admin CSS and JS files, for inclusion on the
     * widget management page and the Shashin admin pages.
     *
     * @access public
     */
    public function getAdminHeadTags() {
        wp_enqueue_style('shashin_admin_css', SHASHIN_DISPLAY_URL . '/admin.css', false, $this->version);
        wp_enqueue_script('shashin_admin_js', SHASHIN_DISPLAY_URL . '/admin.js', array('jquery'), $this->version);
        wp_localize_script('shashin_admin_js', 'shashin_display', array('url' => SHASHIN_DISPLAY_URL));
        return true;
    }

    /**
     * Deletes the Shashin tables and Shashin option setttings. This is
     * irrevocable!
     *
     * @access public
     * @return string Message that Shashin has been uninstalled
     * @throws Exception Query to drop Shashin tables failed
     */
    public function uninstall() {
        global $wpdb;
        $sql = "drop table if exists " . $this->photo_table . ", " . $this->album_table . ";";

        if ($wpdb->query($sql) === false) {
            throw new Exception(__("Uninstall of Shashin failed.", 'shashin'));
        }

        else {
            delete_option('shashin_options');
        }

        return __("Shashin has been uninstalled. You can now deactivate Shashin on your plugins management page.", 'shashin');
    }
}

?>
