<?php
/**
 * Manage settings for Shashin.
 *
 * This file is part of Shashin. Please see the Shashin.phl file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */

?>

<div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5378623">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e("Support Shashin", 'shashin'); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e("Support Shashin", 'shashin'); ?>" title="<?php _e("Support Shashin", 'shashin'); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="<?php echo $this->shashin->faq_url; ?>" target="_blank"><?php _e("Shashin Help", 'shashin'); ?></a>
        </form>
    </div>

    <h2><?php echo __("Shashin Settings", 'shashin'); ?></h2>

    <p><?php _e("Fill out the Main Settings section. Then for the other sections that are relevant to how you will use Shashin, click their headings to expand them, and fill them out. If you are not sure which photo viewer to use, Highslide is recommended.", 'shashin'); ?></p>

    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/message.inc');
    } ?>

    <form method="post">
    <?php wp_nonce_field('shashin_nonce', 'shashin_nonce'); ?>
    <input type="hidden" name="shashin_action" value="update_options">
    <h3><a href="#/" id="shashin_main" class="shashin_option_heading"><img src="<?php echo SHASHIN_DISPLAY_URL; ?>/images/minus.gif" id="shashin_main_button" />Main Settings</a></h3>
    <div id="shashin_main_section">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th><?php _e("Sync all albums daily", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[scheduled_update]',
            array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->shashin->options['scheduled_update']); ?></td>
        <td><span class="description"><?php _e("Have Shashin sync all your albums automatically on a daily basis. NOTE: This may fail if you have several hundered albums, and/or several hundred photos per album.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><?php _e("Add to photo caption", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php _e("Album Title", 'shashin'); ?>:
            <?php echo ToppaWPFunctions::displayInput(
                'shashin_options[prefix_captions]',
                array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->shashin->options['prefix_captions']); ?><br />
            <?php _e("EXIF Data", 'shashin'); ?>:
            <?php $date_option = __("Date only", 'shashin');
                $all_option = __("All", 'shashin');
                $none_option = __("None", 'shashin');
                echo ToppaWPFunctions::displayInput(
                'shashin_options[caption_exif]',
                array('input_type' => 'radio',
                'input_subgroup' => array('date' => $date_option, 'all' => $all_option, 'none' => $none_option)),
                $this->shashin->options['caption_exif']); ?></td>
        <td><span class="description"><?php _e("Dates and EXIF data are appended to expanded view captions only, and include the camera make, model, fstop, focal length, exposure time, and ISO, if available.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_div_padding"><?php _e("Single image padding", 'shashin'); ?>:</label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[div_padding]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['div_padding']); ?></td>
        <td><span class="description"><?php _e("The padding (in pixels) around single images. It must be 2x the '.shashin_image img' padding value in shashin.css (you don't need to change the default value here if you haven't edited shashin.css)", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_thumb_padding"><?php _e("Thumbnail padding", 'shashin'); ?>:</label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[thumb_padding]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['thumb_padding']); ?></td>
        <td><span class="description"><?php _e("The padding (in pixels) around images in tables of thumbnails. It must be 2x the '.shashin_thumb img' padding value in shashin.css (you don't need to change the default value here if you haven't edited shashin.css)", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_theme_max_size"><?php _e("Maximum image width for your theme:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[theme_max_size]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['theme_max_size']); ?></td>
        <td><span class="description"><?php _e("The width of your theme's content area in pixels, minus any padding. If you use the word 'max' for the size in your Shashin tags, Shashin will use the closest, smaller available size for the images. You can use 'max' for the image size with [sthumbs] and other tags. Shashin estimates 10px of total horizontal margin/padding per image.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_image_display"><?php _e("Expanded image display", 'shashin'); ?>:</label></th>
        <td style="white-space: nowrap;"><?php
            $same_window_option = __("Show at photo site, in same browser window", 'shashin');
            $new_window_option = __("Show at photo site, in a new browser window", 'shashin');
            $highslide_option =  __("Show in Highslide", 'shashin');
            $lightbox_option =  __("Show in prettyPhoto (jQuery Lightbox)", 'shashin');
            $other_option = __("Show in other viewer", 'shashin');
            $none_option = __("Do not make thumbnails clickable", 'shashin');
            echo ToppaWPFunctions::displayInput(
                'shashin_options[image_display]',
                array('input_type' => 'radio',
                    'input_subgroup' => array(
                        'same_window' => $same_window_option,
                        'new_window' => $new_window_option,
                        'highslide' => $highslide_option,
                        'lightbox' => $lightbox_option,
                        'other' => $other_option,
                        'none' => $none_option)),
                $this->shashin->options['image_display'], "<br />"); ?></td>
        <td><span class="description"><?php _e('This determines how to display an image when its thumbnail is clicked. Highslide and prettyPhoto are included with Shashin and work "out of the box." <strong>If you select "Show in other viewer," you are responsible for implementing your own image viewer.</strong> See "Other Viewer Settings" below.', 'shashin'); ?></span></td>
        </tr>
        </table>
    </div>

    <h3><a href="#" id="shashin_picasa" class="shashin_option_heading"><img src="<?php echo SHASHIN_DISPLAY_URL; ?>/images/plus.gif" id="shashin_picasa_button" />Picasa Settings</a></h3>
    <div id="shashin_picasa_section" style="display: none;">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th><label for="shashin_options_picasa_max"><?php _e("Expanded image size", 'shashin'); ?>:</label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[picasa_max]',
            array('input_type' => 'select',
                'input_subgroup' => array_combine($this->shashin->picasa_sizes, $this->shashin->picasa_sizes)),
            $this->shashin->options['picasa_max']); ?></td>
        <td><span class="description"><?php _e("The size for expanded display of Picasa photos.", 'shashin'); ?></span></td>
        </tr>
        </table>
    </div>

    <h3><a href="#" id="shashin_highslide" class="shashin_option_heading"><img src="<?php echo SHASHIN_DISPLAY_URL; ?>/images/plus.gif" id="shashin_highslide_button" />Highslide Settings</a></h3>
    <div id="shashin_highslide_section" style="display: none;">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th><?php _e("Autoplay slideshows", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_autoplay]',
            array('input_type' => 'radio',
                'input_subgroup' => array('true' => SHASHIN_YES, 'false' => SHASHIN_NO)),
            $this->shashin->options['highslide_autoplay']); ?>
        <td><span class="description"><?php _e("If someone clicks an image in a slideshow group, this determines whether the slideshow plays automatically.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_highslide_interval"><?php _e("Slideshow image display time", 'shashin'); ?>:</label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_interval]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['highslide_interval']); ?></td>
        <td><span class="description"><?php _e("How long each image is displayed in a slideshow (in milliseconds)", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><?php _e("Repeat slideshow", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_repeat]',
            array('input_type' => 'radio',
                'input_subgroup' => array('1' => SHASHIN_YES, '0' => SHASHIN_NO)),
            $this->shashin->options['highslide_repeat']); ?></td>
        <td><span class="description"><?php _e("Whether to start over from the first image after a slideshow displays the last image.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_highslide_video_width"><?php _e("Video dimensions:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php _e("Width:", 'shashin'); ?>
            <?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_video_width]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['highslide_video_width']); ?>
            <?php _e("Height:", 'shashin'); ?>
            <?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_video_height]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['highslide_video_height']); ?></td>
        <td><span class="description"><?php _e("Unlike images, video dimensions cannot be calculated on the fly. A 4:3 (width:height) ratio is common for videos.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_highslide_outline_type"><?php _e("Outline type:", 'shashin'); ?></label></td>
        <td style="white-space: nowrap;"><?php
            $outline_types = array('beveled' => 'Beveled', 'glossy-dark' => 'Glossy Dark', 'rounded-black' => 'Rounded Black', 'drop-shadow' => 'Drop Shadow', 'outer-glow' => 'Outer Glow', 'rounded-white' => 'Rounded White', 'none' => 'None');
            echo ToppaWPFunctions::displayInput(
                'shashin_options[highslide_outline_type]',
                array('input_type' => 'select',
                    'input_subgroup' => $outline_types),
                $this->shashin->options['highslide_outline_type']); ?></td>
        <td><span class="description"><?php _e("The graphic outline applied to expanded images. Select 'none' for no outline.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_highslide_v_position"><?php _e("Controller Position:", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php
            $v_positions = array('top' => 'Top', 'middle' => 'Middle', 'bottom' => 'Bottom');
            echo ToppaWPFunctions::displayInput(
                'shashin_options[highslide_v_position]',
                array('input_type' => 'select',
                    'input_subgroup' => $v_positions),
                $this->shashin->options['highslide_v_position']);

            $h_positions = array('left' => 'Left', 'center' => 'Center', 'right' => 'Right');
            echo ToppaWPFunctions::displayInput(
                'shashin_options[highslide_h_position]',
                array('input_type' => 'select',
                    'input_subgroup' => $h_positions),
                $this->shashin->options['highslide_h_position']); ?></td>
        <td><span class="description"><?php _e("Where to position the slideshow control bar.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><label for="shashin_options_highslide_dimming_opacity"><?php _e("Dimming opacity", 'shashin'); ?></label></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_dimming_opacity]',
            array('input_type' => 'text', 'input_size' => 4),
            $this->shashin->options['highslide_dimming_opacity']); ?></td>
        <td><span class="description"><?php _e("Enter a number between 0 and 1. Indicates how much to dim the background when an image is expanded (enter 0 for no dimming). In highslide.css, look for .highslide-dimming to change the color (default is black)", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><?php _e("Hide controller on mouseout:", 'shashin'); ?></th>
        <td style="white-space: nowrap;"><?php echo ToppaWPFunctions::displayInput(
            'shashin_options[highslide_hide_controller]',
            array('input_type' => 'radio',
                'input_subgroup' => array('1' => SHASHIN_YES, '0' => SHASHIN_NO)),
            $this->shashin->options['highslide_hide_controller']); ?></td>
        <td><span class="description"><?php _e("Whether the slideshow controller should be hidden when the mouse leaves the expanded image.", 'shashin'); ?></span></td>
        </tr>
        </table>
    </div>

    <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e("Save Settings", 'shashin'); ?>" /></p>
    </form>

    <div style="border: thin solid; padding: 5px;">
        <form method="post">
        <input type="hidden" name="shashin_action" value="uninstall">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th style="font-weight: bold;"><?php _e("Uninstall Shashin", 'shashin'); ?></th>
        <td style="white-space: nowrap;"><?php
            $uninstall_label = __("Uninstall Shashin?", 'shashin');
            echo ToppaWPFunctions::displayInput(
            'shashin_uninstall',
            array('input_type' => 'checkbox',
                'input_subgroup' => array('y' => $uninstall_label))); ?></td>
        <td><span class="description"><?php _e("Check this box and click the 'Uninstall Shashin' button below if you want to completely remove Shashin. This will <strong>permanently</strong> break any Shashin tags that you may still have in your posts or pages. After uninstalling, you can then deactivate Shashin on your plugins management page.", 'shashin'); ?></span></td>
        </tr>
        </table>

        <p class="submit"><input class="button-secondary" type="submit" name="save" value="<?php _e("Uninstall Shashin", 'shashin'); ?>" onclick="return confirm('<?php _e("Are you sure you want to uninstall Shashin?", 'shashin'); ?>');" /></p>
        </form>
    </div>
</div>
