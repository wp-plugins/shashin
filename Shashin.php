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
    public $name = 'Shashin';
    public $version = '3.0';
    public $album_table;
    public $photo_table;
    public $faq_url = 'http://www.toppa.com/shashin-wordpress-plugin/';
    public $picasa_sizes = array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800);
    public $picasa_crop = array(32, 48, 64, 160);
    public $flickr_sizes = array(75, 100, 240, 500, 1024);
    public $flickr_crop = array(75);
    public $twitpic_sizes = array(75, 150, 600);
    public $twitpic_crop = array(75, 150);
    public $yes;
    public $no;

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

        // add Tools and Settings menus
        add_action('admin_menu', array($this, 'initAdminMenus'));
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
        require_once(SHASHIN_DIR . '/ShashinAdmin.php');
        $admin = new ShashinAdmin($this);
        $options_page = add_options_page('Shashin', 'Shashin', 'manage_options', 'ShashinBoot', array($admin, 'getSettingsMenu'));
        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        add_action("admin_print_styles-$options_page", array($admin, 'getHeadTags'));
        $tools_page = add_management_page('Shashin', 'Shashin', 'edit_posts', 'ShashinBoot', array($admin, 'getToolsMenu'));
        add_action("admin_print_styles-$tools_page", array($admin, 'getHeadTags'));
        return true;
    }
}

?>
