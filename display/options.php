<?php
/**
 * Set options for Shashin.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage AdminPanels
 */

?>

<div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5378623">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e("Support Shashin", 'shashin'); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e("Support Shashin", 'shashin'); ?>" title="<?php _e("Support Shashin", 'shashin'); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="<?php echo $this->faq_url; ?>" target="_blank"><?php _e("Shashin Help", 'shashin'); ?></a>
        </form>
    </div>

    <h2><?php echo __("Shashin Settings", 'shashin'); ?></h2>

    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>


    <form  method="post">
    <input type="hidden" name="shashin_action" value="update_options">
    <h3><a href="#" id="shashin_main" class="shashin_option_heading"><img src="<?php echo SHASHIN_DISPLAY_URL; ?>/images/minus.gif" id="shashin_main_button" />Main Settings</a></h3>
    <div id="shashin_main_section">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th><?php _e("Sync all albums daily", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php ToppaWPFunctions::displayInput(
            'shashin_options[scheduled_update]',
            array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->options['scheduled_update']); ?></td>
        <td><span class="description"><?php _e("Have Shashin sync all your albums automatically on a daily basis. NOTE: This may fail if you have several hundered albums, and/or several hundred photos per album.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><?php _e("Add to photo caption", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php _e("Album Title", 'shashin'); ?>:
            <?php ToppaWPFunctions::displayInput(
                'shashin_options[prefix_captions]',
                array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->options['prefix_captions']); ?><br />
            <?php _e("EXIF Data", 'shashin'); ?>:
            <?php $date_option = __("Date only", 'shashin');
                $all_option = __("All", 'shashin');
                $none_option = __("None", 'shashin');
                ToppaWPFunctions::displayInput(
                'shashin_options[caption_exif]',
                array('input_type' => 'radio',
                'input_subgroup' => array('date' => $date_option, 'all' => $all_option, 'none' => $none_option)),
                $this->options['caption_exif']); ?></td>
        <td><span class="description"><?php _e('Album titles are prefixed to all captions. Dates are appended to expanded view captions only. EXIF data is appended to Highslide captions only, and includes the camera make, model, fstop, focal length, exposure time, and ISO.', 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_div_padding"><?php _e("Image div padding:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php ToppaWPFunctions::displayInput(
            'shashin_options[div_padding]',
            array('input_type' => 'text', 'input_size' => 30),
            $this->options['div_padding']); ?></td>
        <td><span class="description"><?php _e("The padding (in pixels) around single images. It must be 2x the '.shashin_image img' padding value in shashin.css", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_thumb_padding"><?php _e("Thumbnail div padding:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php ToppaWPFunctions::displayInput(
            'shashin_options[thumb_padding]',
            array('input_type' => 'text', 'input_size' => 30),
            $this->options['thumb_padding']); ?></td>
        <td><span class="description"><?php _e("The padding (in pixels) around images in tables of thumbnails. It must be 2x the '.shashin_thumb img' padding value in shashin.css", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_theme_max_size"><?php _e("Maximum image width for your theme:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php ToppaWPFunctions::displayInput(
            'shashin_options[theme_max_size]',
            array('input_type' => 'text', 'input_size' => 30),
            $this->options['theme_max_size']); ?></td>
        <td><span class="description"><?php _e("The width of your theme's content area in pixels, minus any padding. If you use the word 'max' for the size in your Shashin tags, Shashin will use the closest, smaller supported Picasa size for the images. You can use 'max' for the image size with [sthumbs] and other tags. Shashin estimates 10px of total horizontal margin/padding per image.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_image_display"><?php _e("Expanded image display:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php
            $same_window_option = __("At Picasa, in same browser window", 'shashin');
            $new_window_option = __("At Picasa, in a new browser window", 'shashin');
            $highslide_option =  __("Use Highslide", 'shashin');
            $lightbox_option =  __("Use prettyPhoto (jQuery Lightbox)", 'shashin');
            $other_option = __("Use Other Viewer", 'shashin');
            $none_option = __("Do not make thumbnails clickable", 'shashin');
            ToppaWPFunctions::displayInput(
                'shashin_options[image_display]',
                array('input_type' => 'radio',
                'input_subgroup' => array(
                    'same_window' => $same_window_option,
                    'new_window' => $new_window_option,
                    'highslide' => $highslide_option,
                    'lightbox' => $lightbox_option,
                    'other' => $other_option,
                    'none' => $none_option)),
                $this->options['image_display'], "<br />"); ?></td>
        <td><span class="description"><?php _e('This determines how to display an image when its thumbnail is clicked. Highslide and prettyPhoto are included with Shashin and work "out of the box." <strong>If you select "Use Other Viewer," you are responsible for implementing your own image viewer.</strong> See "Viewer Settings" below.', 'shashin'); ?></span></td>
        </tr>


        </table>
    </div>
    </form>
</div>
