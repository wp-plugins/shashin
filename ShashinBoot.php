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


// based on http://nerdlife.net/wordpress-plugin-installation-hackery/
register_activation_hook(__FILE__, 'shashin_activate');

if ($_GET['action'] == 'error_scrape' && $_GET['plugin'] == 'shashin/ShashinBoot.php') {
    die("Sorry, Shashin requires PHP 5.1.2 or higher, and mySQL 4.1 or higher. Please deactivate Shashin.");
}

function shashin_activate() {
    global $wpdb;
    $mysql_version = $wpdb->get_var("select version()");

    // spl_autoload_register added in PHP 5.1.2
    if (!function_exists('spl_autoload_register') || version_compare($mysql_version, "4.1", "<")) {
        trigger_error('', E_USER_ERROR);
    }

    $options_defaults = array(
        'version' => '3.0',
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

    $options = get_option('shashin_options');

    // flag whether to add or update Shashin options below
    $add_options = empty($options);

    // update version number
    $options['version'] = $options_defaults['version'];

    // set Shashin options
    foreach ($options_defaults as $k=>$v) {
        if (!$options[$k]) {
            $options[$k] = $v;
        }
    }

    if ($add_options === false) {
        update_option('shashin_options', serialize($options));
    }

    else {
        add_option('shashin_options', serialize($options));
    }
}

function shashin_autoloader($class) {
    $path = dirname(__FILE__) . '/' . $class . '.php';

    if (file_exists($path)) {
        require_once($path);
    }
}

if (function_exists('spl_autoload_register')) {
    spl_autoload_register("shashin_autoloader");

    // from http://striderweb.com/nerdaphernalia/2008/09/hit-a-moving-target-in-your-wordpress-plugin/
    if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
    if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
    if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
    if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

    $shashin = new Shashin();
}

?>
