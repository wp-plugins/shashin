<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa and Twitpic photos in WordPress.
Author: Michael Toppa
Version: 3.0
Author URI: http://www.toppa.com
*/

// based on http://nerdlife.net/wordpress-plugin-installation-hackery/
register_activation_hook(__FILE__, 'shashin_activate');

if ($_GET['action'] == 'error_scrape') {
    die(__("Shashin failed to activate correctly. Shashin requires PHP 5.0 or higher, and mySQL 4.1 or higher. Please deactivate Shashin.", 'shashin'));
}

function shashin_activate() {
    global $wpdb;
    $dir = dirname(__FILE__);
    // load localization
    load_plugin_textdomain('shashin', false, basename($dir) . '/languages/');

    $mysql_version = $wpdb->get_var("select version()");

    if (version_compare(phpversion(), "5.0", "<") || version_compare($mysql_version, "4.1", "<")) {
        trigger_error('', E_USER_ERROR);
    }

    else {
        require_once($dir . '/Shashin.phl');
        require_once($dir . '/ShashinAdmin.phl;');
        $shashin = new Shashin();
        $shashinAdmin = new ShashinAdmin($shashin);
        $shashinAdmin->install();
    }
}

if (version_compare(phpversion(), "5.0", ">=")) {
    // from http://striderweb.com/nerdaphernalia/2008/09/hit-a-moving-target-in-your-wordpress-plugin/
    if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
    if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
    if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
    if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

    define('SHASHIN_DIR', dirname(__FILE__));
    define('SHASHIN_BASE', basename(__FILE__));
    define('SHASHIN_DISPLAY_URL', WP_PLUGIN_URL . '/' . basename(SHASHIN_DIR) . '/display');
    define('SHASHIN_ALBUM_TABLE', $wpdb->prefix . 'shashin_album');
    define('SHASHIN_PHOTO_TABLE', $wpdb->prefix . 'shashin_photo');

    // load localization
    load_plugin_textdomain('shashin', false, basename(SHASHIN_DIR) . '/languages/');

    // need to set these as constants, so they can be used in defining
    // properties of classes (e.g ShashinPhoto->refData)
    define('SHASHIN_YES', __("Yes", 'shashin'));
    define('SHASHIN_NO', __("No", 'shashin'));

    // get required libraries
    require_once(SHASHIN_DIR . '/Shashin.phl');
    require_once(SHASHIN_DIR . '/ShashinAlbum.phl');
    require_once(SHASHIN_DIR . '/ShashinPhoto.phl');

    if (!class_exists('ToppaWPFunctions')) {
        require_once(SHASHIN_DIR . '/ToppaWPFunctions.phl');
    }

    $shashin = new Shashin();
}

?>
