<?php
/**
 * ShashinWidget class file.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage Classes
 */

/**
 * A collection of static methods for Shashin widgets.
 * @author Michael Toppa
 * @package Shashin
 * @subpackage Classes
 */
class ShashinWidget {
    /**
     * Registers all the Shashin widgets and their controls.
     *
     * This function has several functions contained within it,
     * which PHPDoc cannot see. These functions get their respestive widget
     * $shashin_options and then display the widget. The functions are:
     *
     * - widgetSingle()
     * - widgetRandom()
     * - widgetAlbum()
     * - widgetThumbs()
     * - widgetNewest()
     * - widgetAlbumThumbs()
     *
     * @static
     * @access public
     */
    function initWidgets() {
        function widgetSingle($args) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $photo = new ShashinPhoto();
            $widget = $photo->getPhotoMarkup($shashin_options['widget_single']);
            ShashinWidget::_widgetDisplay($args, $shashin_options['widget_single']['title'], $widget);
        }

        function widgetRandom($args) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $widget = ShashinPhoto::getRandomMarkup($shashin_options['widget_random']);
            ShashinWidget::_widgetDisplay($args, $shashin_options['widget_random']['title'], $widget);
        }

        function widgetThumbs($args) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $widget = ShashinPhoto::getThumbsMarkup($shashin_options['widget_thumbs']);
            ShashinWidget::_widgetDisplay($args, $shashin_options['widget_thumbs']['title'], $widget);
        }

        function widgetNewest($args) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $widget = ShashinPhoto::getNewestMarkup($shashin_options['widget_newest']);
            ShashinWidget::_widgetDisplay($args, $shashin_options['widget_newest']['title'], $widget);
        }

        function widgetAlbumThumbs($args) {
            $shashin_options = unserialize(SHASHIN_OPTIONS);
            $shashin_options['widget_album_thumbs']['force_picasa'] = true;
            $widget = ShashinAlbum::getAlbumThumbsMarkup($shashin_options['widget_album_thumbs']);
            ShashinWidget::_widgetDisplay($args, $shashin_options['widget_album_thumbs']['title'], $widget);
        }

        register_sidebar_widget('Shashin: Single Image', 'widgetSingle', 'ShashinWidget');
        register_sidebar_widget('Shashin: Random Images', 'widgetRandom', 'ShashinWidget');
        register_sidebar_widget('Shashin: Single Album Thumbnail', 'widgetAlbum', 'ShashinWidget');
        register_sidebar_widget('Shashin: Image Thumbnails', 'widgetThumbs', 'ShashinWidget');
        register_sidebar_widget('Shashin: Newest Images', 'widgetNewest', 'ShashinWidget');
        register_sidebar_widget('Shashin: Album Thumbnails', 'widgetAlbumThumbs', 'ShashinWidget');
        register_widget_control('Shashin: Single Image', array('ShashinWidget', 'widgetSingleControl'), 500, 300);
        register_widget_control('Shashin: Random Images', array('ShashinWidget', 'widgetRandomControl'), 500, 300);
        register_widget_control('Shashin: Single Album Thumbnail', array('ShashinWidget', 'widgetAlbumControl'), 500, 300);
        register_widget_control('Shashin: Image Thumbnails', array('ShashinWidget', 'widgetThumbsControl'), 500, 300);
        register_widget_control('Shashin: Newest Images', array('ShashinWidget', 'widgetNewestControl'), 500, 300);
        register_widget_control('Shashin: Album Thumbnails', array('ShashinWidget', 'widgetAlbumThumbsControl'), 500, 300);
    }

    /**
     * Displays and processes the widget control form for single images.
     *
     * @static
     * @access public
     * @uses ShashinWidget::_widgetControl()
     */
    function widgetSingleControl() {
        ShashinWidget::_widgetControl('widget_single');
    }

    /**
     * Displays and processes the widget control form for random images.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getAlbums()
     * @uses ShashinWidget::_widgetControl()
     */
    function widgetRandomControl() {
        $albums_full = ShashinAlbum::getAlbums('*', null, "order by title");
        $albums = array('any' => 'Any');

        foreach ($albums_full as $album) {
            $albums[$album['album_key']] = $album['title'];
        }

        ShashinWidget::_widgetControl('widget_random', array($albums));
    }

    /**
     * Displays and processes the widget control form for a table of thumbnails.
     *
     * @static
     * @access public
     * @uses ShashinWidget::_widgetControl()
     */
    function widgetThumbsControl() {
        ShashinWidget::_widgetControl('widget_thumbs');
    }

    /**
     * Displays and processes the widget control form for newest images.
     *
     * @static
     * @access public
     * @uses ShashinAlbum::getAlbums()
     * @uses ShashinWidget::_widgetControl()
     */
    function widgetNewestControl() {
        $albums_full = ShashinAlbum::getAlbums('*', null, "order by title");
        $albums = array('any' => 'Any');

        foreach ($albums_full as $album) {
            $albums[$album['album_key']] = $album['title'];
        }

        ShashinWidget::_widgetControl('widget_newest', array($albums));
    }

    /**
     * Displays and processes the widget control form for a table of album thumbnails.
     *
     * @static
     * @access public
     * @uses ShashinWidget::_widgetControl()
     */
    function widgetAlbumThumbsControl() {
        ShashinWidget::_widgetControl('widget_album_thumbs');
    }

    /**
     * Displays the widget control form.
     *
     * @static
     * @access private
     */
    function _widgetControl($name, $args = null) {
        // can't use the SHASHIN_OPTIONS constant as this function can be
        // called more than once in a single request, so we need to get the
        // updates made in each call.
        $shashin_options = unserialize(get_option('shashin_options'));

        // for handing the control form submission.
        if (is_array($_REQUEST["shashin_$name"])) {
            foreach ($_REQUEST["shashin_$name"] as $k=>$v) {
                $shashin_options[$name][$k] = htmlspecialchars(trim($v));
            }

            update_option('shashin_options', serialize($shashin_options));
        }

        // need to list the image size options for all of the widget admin screens
        $shashin_image_sizes = unserialize(SHASHIN_IMAGE_SIZES);
        $shashin_crop_sizes = unserialize(SHASHIN_CROP_SIZES);
        $sizes = array();

        foreach ($shashin_image_sizes as $image_size) {
            if (in_array($image_size, $shashin_crop_sizes)) {
                $sizes[$image_size] = "$image_size (cropped square)";
            }

            else {
                $sizes[$image_size] = $image_size;
            }
        }

        $clear_options = array('none' => 'none', 'left' => 'left', 'right' => 'right', 'both' => 'both');
        $float_options = array('none' => 'none', 'center' => 'center', 'left' => 'left', 'right' => 'right');

        // for displaying the control form
        require(SHASHIN_DIR . "/display/$name.php");
    }

    /**
     * Extracts widget args and displays a widget.
     *
     * @static
     * @access private
     */
    function _widgetDisplay($args, $widget_title, $widget) {
        // get the theme widget vars
        extract($args);
        // display widget
        echo $before_widget;
        echo $before_title . $widget_title . $after_title;
        echo $widget;
        echo $after_widget;
    }
}

?>
