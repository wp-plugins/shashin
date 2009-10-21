<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa and Twitpic photos in WordPress.
Author: Michael Toppa
Version: 3.0
Author URI: http://www.toppa.com
*/

// note to self... for generating pot file...
// find -name "*.php"  ! -path "*.svn*" > /home/toppa/Scratch/shashin_files.txt
// xgettext --from-code=utf-8 --keyword=__ --keyword=_e --output=/opt/lampp/htdocs/wordpress/wp-content/plugins/shashin/languages/shashin.pot --files-from=/home/toppa/Scratch/shashin_files.txt

define('SHASHIN_DIR', dirname(__FILE__));

// based on http://nerdlife.net/wordpress-plugin-installation-hackery/
register_activation_hook(__FILE__, 'shashin_activate');

if ($_GET['action'] == 'error_scrape') {
    $error = $_SESSION['shashin_activate_error'];
    unset($_SESSION['shashin_activate_error']);
    die($error);
}

function shashin_activate() {
    global $wpdb;

    // load localization
    load_plugin_textdomain('shashin', false, basename(SHASHIN_DIR) . '/languages/');

    $mysql_version = $wpdb->get_var("select version()");

    if (version_compare(phpversion(), "5.0", "<") || version_compare($mysql_version, "4.1", "<")) {
        $_SESSION['shashin_activate_error'] = __("Sorry, Shashin requires PHP 5.0 or higher, and mySQL 4.1 or higher. Please deactivate Shashin.", 'shashin');
        trigger_error('', E_USER_ERROR);
    }

    else {
        require_once(dirname(__FILE__) . "/Shashin.php");
        $shashin = new Shashin();
        $shashin->install();
    }
}

if (version_compare(phpversion(), "5.0", ">=")) {
    // from http://striderweb.com/nerdaphernalia/2008/09/hit-a-moving-target-in-your-wordpress-plugin/
    if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
    if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
    if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
    if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

    // get required libraries
    require_once(SHASHIN_DIR . "/Shashin.php");
    require_once(SHASHIN_DIR . '/ShashinAlbum.php');
    //require_once(SHASHIN_DIR . '/ShashinPhoto.php');
    //require_once(SHASHIN_DIR . '/ShashinWidget.php');

    if (!class_exists('ToppaWPFunctions')) {
        require_once(SHASHIN_DIR . '/ToppaWPFunctions.php');
    }

    //if (!class_exists('ToppaXMLParser')) {
    //    require_once(SHASHIN_DIR . '/ToppaXMLParser.php');
    //}

    $shashin = new Shashin();
}

?>
