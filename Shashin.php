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
     * Called automatically (after the end of the class) to register hooks and
     * add the actions and filters.
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
        $options_defaults = array(
            'div_padding' => 10,
            'thumb_padding' => 6,
            'image_display' => 'highslide',
            'prefix_captions' => 'n',
            'video_width' => 640,
            'video_height' => 480,
            'album_photos_cols' => 3,
            'album_photos_order' => 'natural_order',
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
/*
        $photo = new ShashinPhoto($this);

        if (!ToppaWPFunctions::createTable($photo, $this->photo_table)) {
            trigger_error('', E_USER_ERROR);
        }
*/
    }


}

?>
