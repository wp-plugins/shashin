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
    public $l10n_name = 'shashin';
    public $version = '3.0';
    public $filename;
    public $dirname;
    public $path;
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

/*        if (!class_exists('ToppaWPFunctions')) {
            require_once($this->dir . '/ToppaWPFunctions.php');
        }
*/
    }

    /**
     * Updates Shashin options and creates the Shashin tables if they don't
     * already exist.
     *
     * @access public
     * @uses ShashinAlbum::ShashinAlbum()
     * @uses ShashinPhoto::ShashinPhoto()
     * @uses ToppaWPFunctions::createTable()
     */
    public function install() {
            update_option('shashin_options', 'boo5');
/*
        $options_defaults = array(
            'div_padding' => 10,
            'thumb_padding' => 6,
            'image_display' => 'highslide',
            'prefix_captions' => 'n',
            'album_photos_cols' => 3,
            'album_photos_order' => 'natural',
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

        // update version number
        $this->options['version'] = $this->version;

        // set Shashin options
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
/*

        // create/update tables
        $album = new ShashinAlbum();

        if (!ToppaWPFunctions::createTable($album, SHASHIN_ALBUM_TABLE)) {
            $_SESSION['shashin_activate_error'] = __("Failed to create or update table ", SHASHIN_L10N_NAME) . SHASHIN_ALBUM_TABLE;
            trigger_error('', E_USER_ERROR);
        }

        $photo = new ShashinPhoto();

        if (!ToppaWPFunctions::createTable($photo, SHASHIN_PHOTO_TABLE)) {
            $_SESSION['shashin_activate_error'] = __("Failed to create or update table ", SHASHIN_L10N_NAME) . SHASHIN_PHOTO_TABLE;
            trigger_error('', E_USER_ERROR);
        } */

    }
}

//$shashin = new Shashin();

?>
