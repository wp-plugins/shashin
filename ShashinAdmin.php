<?php
/**
 * ShashinAdmin class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage Classes
 */

/**
 * For Shashin's settings, tools, and widget admin pages.
 * Also for install and uninstall methods.
 *
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinAdmin {
    public $shashin;

    function __construct(&$shashin) {
        $this->shashin = &$shashin;
    }

    public function getSettingsMenu() {
        if ($_REQUEST['shashin_action'] && !check_admin_referer('shashin_nonce', 'shashin_nonce')) {
            return false;
        }

        // can't use $this->shashin->options as the options may have changed during the session
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
                $this->shashin->options['picasa_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->shashin->picasa_sizes);
                $this->shashin->options['flickr_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->shashin->flickr_sizes);
                $this->shashin->options['twitpic_theme_max_single'] = ShashinPhoto::setMaxThemeSize($_REQUEST['shashin_options']['theme_max_size'], 1, $this->shashin->twitpic_sizes);
                $this->shashin->options = array_merge($this->shashin->options, $_REQUEST['shashin_options']);
                update_option('shashin_options', serialize($this->shashin->options));
                $message = __("Shashin settings saved.", 'shashin');
            }

            catch (Exception $e) {
                $message = $e->getMessage();
            }
            break;
        }

        // check that re-activation has been done
        if ($this->shashin->version != $this->shashin->options['version']) {
            $message = __("To complete the Shashin upgrade, please deactivate and reactivate Shashin from your plugins menu.", 'shashin');
        }

        // Get the markup and display
        require(SHASHIN_DIR . '/display/settings.php');
        $settings_html = ob_get_contents();
        ob_end_clean();
        echo $settings_html;
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
            $rss_url = trim($_REQUEST['rss_url']);
            $include_in_random = htmlentities($_REQUEST['include_in_random']);

            try {
                // handle feed for all of a Picasa user's albums
                if (strpos($rss_url, 'kind=album') !== false) {
                    ShashinAlbum::setUserAlbums($pieces[3], array('include_in_random' => $_REQUEST['include_in_random']), false);
                }

                // handle individual albums
                else {
                    $album = new ShashinAlbum($self, true);
                    $album->setAlbum($rss_url, array('include_in_random' => $include_in_random));
                }
            }

            catch (Exception $e) {

            }
/*
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
*/
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
                // setup for Tools page display
                $users = ToppaWPFunctions::sqlSelect($this->shashin->album_table, 'distinct user', null, 'order by user', 'get_col');
                $order_by = $_REQUEST['shashin_orderby'] ? $_REQUEST['shashin_orderby'] : 'title';
                $all_albums = ToppaWPFunctions::sqlSelect($this->shashin->album_table, '*', null, "order by $order_by", 'get_results');
                $album = new ShashinAlbum($this->shashin); // needed for ref data
            }

            catch (Exception $e) {
                $message = $e->getMessage();
                $db_error = true;
            }

            // check that re-activation has been done
            if ($this->shashin->version != $this->shashin->options['version']) {
                $message = __("To complete the Shashin upgrade, please deactivate and reactivate Shashin from your plugins menu.", 'shashin');
            }

            require(SHASHIN_DIR . '/display/tools.php');
        }

        // Get the markup and display
        $tools_html = ob_get_contents();
        ob_end_clean();
        echo $tools_html;
        return true;
    }


    /**
     * Gets the Shashin Admin CSS and JS files.
     *
     * @access public
     */
    public function getHeadTags() {
        wp_enqueue_style('shashin_admin_css', SHASHIN_DISPLAY_URL . '/admin.css', false, $this->shashin->version);
        wp_enqueue_script('shashin_admin_js', SHASHIN_DISPLAY_URL . '/admin.js', array('jquery'), $this->shashin->version);
        wp_localize_script('shashin_admin_js', 'shashin_display', array('url' => SHASHIN_DISPLAY_URL));
        return true;
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
        $add_options = empty($this->shashin->options);

        // set Shashin options
        $this->shashin->options['version'] = $this->shashin->version;

        foreach ($options_defaults as $k=>$v) {
            if (!$this->shashin->options[$k]) {
                $this->shashin->options[$k] = $v;
            }
        }

        if ($add_options === false) {
            update_option('shashin_options', serialize($this->shashin->options));
        }

        else {
            add_option('shashin_options', serialize($this->shashin->options));
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
        $album = new ShashinAlbum($this->shashin);

        if (!ToppaWPFunctions::createTable($album, $this->shashin->album_table)) {
            trigger_error('', E_USER_ERROR);
        }

        $photo = new ShashinPhoto($this->shashin);

        if (!ToppaWPFunctions::createTable($photo, $this->shashin->photo_table)) {
            trigger_error('', E_USER_ERROR);
        }

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
        $sql = "drop table if exists " . $this->shashin->photo_table . ", " . $this->shashin->album_table . ";";

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
