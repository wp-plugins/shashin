<?php
/**
 * ShashinBrowser class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Sune Kibsgaard Pedersen
 * @version 2.6
 * @package Shashin
 * @subpackage Classes
 */

define('SHASHIN_BROWSER_URL', WP_PLUGIN_URL . '/' . basename(SHASHIN_DIR) . '/browser');

/**
 * Create a new instance of this class to add a Media Libray button for adding
 * photos and albums via an interface when editing posts
 *
 * @author Sune Kibsgaard Pedersen
 * @package Shashin
 * @subpackage Classes
 */
class ShashinBrowser {

    function ShashinBrowser() {
        add_action('media_buttons', array(&$this, 'actionMediaButtons'), 20);
        add_action('media_upload_shashin_photos', array(&$this, 'actionMediaUploadShashinPhotos'));
        add_action('media_upload_shashin_albums', array(&$this, 'actionMediaUploadShashinAlbums'));
        add_action('wp_ajax_shashin_browser_get_photos', array(&$this, 'actionWpAjaxGetPhotos'));
    }

    function actionWpAjaxGetPhotos() {
        // join album
        $conditions = " inner join " . SHASHIN_ALBUM_TABLE . " sa
                        on sa.album_id = sp.album_id
                        where sp.deleted = 'N' ";

        // where album && title
        $album = (isset($_POST['album']) && is_numeric($_POST['album'])) ? $_POST['album'] : 0;
        if($album > 0)
            $conditions .= ' AND sp.album_id = '.$album.' ';

        $title = (isset($_POST['title']) && strlen($_POST['title']) > 0) ? $_POST['title'] : false;
        if($title)
            $conditions .= ' AND sp.title like "%'.mysql_real_escape_string($title).'%" ';

        // order by
        $valid_order = array('uploaded_timestamp', 'taken_timestamp', 'photo_key', 'title', 'picasa_order');
        $order = (isset($_POST['order']) && in_array($_POST['order'], $valid_order)) ? $_POST['order'] : 'uploaded_timestamp';
        $desc = (isset($_POST['desc']) && $_POST['desc']) ? 'desc' : 'asc';
        $other = 'order by sp.'.$order.' '.$desc;

        // get total count for paging
        $count = ShashinPhoto::getPhotos(array('count(photo_key) as count'), $conditions, $other);
        $total_pages = ceil($count[0]['count'] / 32);

        // offset, limit
        $page = (isset($_POST['page']) && is_numeric($_POST['page'])) ? $_POST['page'] : 1;
        $offset = ($page - 1) * 32;
        $other .= ' limit '.$offset.', 32';

        // only get what we need
        $columns = array('sp.photo_key',
                         'sp.title',
                         'sp.enclosure_url',
                         'sa.title as album_title');

        // query and output json
        $photos = ShashinPhoto::getPhotos($columns, $conditions, $other);
        echo json_encode(array('images' => $photos, 'total_pages' => $total_pages, 'page' => $page, 'debug' => $condition.' '.$other));
        exit;
    }

    function actionMediaButtons() {
        global $post_ID, $temp_ID;
        $iframe_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

        $url = 'media-upload.php?post_id='.$iframe_id.'&amp;type=shashin&amp;tab=shashin_photos&amp;TB_iframe=true';
        $title = __('Add from Picasa', SHASHIN_L10N_NAME);
        $image = SHASHIN_BROWSER_URL.'/picasa.gif';

        $markup = '<a href="%s" class="thickbox" title="%s"><img src="%s" alt="%s"></a>';
        printf($markup, $url, $title, $image, $title);
    }

    function actionMediaUploadShashinPhotos() {
        add_action('admin_print_styles', array(&$this, 'actionAdminPrintStyles'));
        wp_iframe(array(&$this, 'renderPhotosTemplate'));
    }

    function actionMediaUploadShashinAlbums() {
        add_action('admin_print_styles', array(&$this, 'actionAdminPrintStyles'));
        wp_iframe(array(&$this, 'renderAlbumsTemplate'));
    }

    function actionAdminPrintStyles() {
        $filename = array_shift(explode('?', basename($_SERVER['REQUEST_URI'])));
        if($filename == 'media-upload.php' && strstr($_SERVER['REQUEST_URI'], 'type=shashin'))
            wp_admin_css('css/media');

        wp_enqueue_style('shashin_browser_css', SHASHIN_BROWSER_URL . '/shashin-browser.css', false, SHASHIN_VERSION);
    }

    function filterMediaUploadTabs() {
        return array(
            'shashin_photos' => __('Photos', SHASHIN_L10N_NAME),
            'shashin_albums' => __('Albums', SHASHIN_L10N_NAME)
        );
    }

    function renderPhotosTemplate() {
        add_filter('media_upload_tabs', array(&$this, 'filterMediaUploadTabs'));
        media_upload_header();

        $albums = ShashinAlbum::getAlbums(array('album_id', 'title'));
        require_once 'photos.template.php';
    }

    function renderAlbumsTemplate() {
        add_filter('media_upload_tabs', array(&$this, 'filterMediaUploadTabs'));
        media_upload_header();

        $albums = ShashinAlbum::getAlbums('*', null, 'order by title');
        require_once 'albums.template.php';
    }
}
