<?php
/**
 * Shashin is a WordPress plugin for integrating Picasa and Twitpic photos in WordPress.
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
    public $name = 'Shashin';
    public $version = '3.0';
    public $options;
    public $picasa_options;
    public $twitpic_options;
    public $highslide_options;
    public $lightbox_options;
    public $other_viewer_options;
    public $album_table;
    public $photo_table;

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
        $this->album_table = $wpdb->prefix . 'shashin_album';
        $this->photo_table = $wpdb->prefix . 'shashin_photo';

        // load localization
        load_plugin_textdomain('shashin', false, basename(SHASHIN_DIR) . '/languages/');
        // add Tools and Settings menus
        add_action('admin_menu', array(SHASHIN_PLUGIN_NAME, 'initAdminMenus'));

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
    function install() {
        $options_defaults = array(
            'div_padding' => 10,
            'thumb_padding' => 6,
            'image_display' => 'highslide',
            'prefix_captions' => 'n',
            'video_width' => 640,
            'video_height' => 480,
            'album_photos_cols' => 3,
            'album_photos_order' => 'picasa_order',
            'album_photos_captions' => 'n',
            'album_photos_description' => 'n',
            'scheduled_update' => 'n',
            'theme_max_size' => 600,
            'theme_max_single' => 576,
            'photos_per_page' => null,
            'caption_exif' => 'n',
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

        if (!ToppaWPFunctions::createTable($album, $this->album_table)) {
            trigger_error('', E_USER_ERROR);
        }

        $photo = new ShashinPhoto($this);

        if (!ToppaWPFunctions::createTable($photo, $this->photo_table)) {
            trigger_error('', E_USER_ERROR);
        }
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
        add_options_page('Shashin', 'Shashin', 'manage_options', 'ShashinBoot', array($this, 'getOptionsMenu'));
        //add_management_page('Shashin', 'Shashin', 6, __FILE__, array($this, 'getAdminMenu'));
        //if (strpos($_SERVER['REQUEST_URI'], __FILE__) !== false) {
        //    add_action("admin_print_styles", array(SHASHIN_PLUGIN_NAME, 'getAdminHeadTags'));
        //}
    }

    function getOptionsMenu() {
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
            // make sure the Picasa URL looks valid
            $pieces = explode("/", trim($_REQUEST['shashin_options']['picasa_server']));

            if ($pieces[0] != "http:" || !strlen($pieces[2]) || strlen($pieces[3])) {
                $message = __("Invalid URL for Picasa Server", SHASHIN_L10N_NAME);
            }

            // save the options
            else {
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
     * Deletes the Shashin tables and Shashin option setttings. This is
     * irrevocable!
     *
     * @access public
     * @return boolean true: uninstall successful; false: uninstall failed
     */
    function uninstall() {
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
