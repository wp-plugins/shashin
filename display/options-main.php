<?php
/**
 * Set options for Shashin.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3.5
 * @package Shashin
 * @subpackage AdminPanels
 */

?>

<div class="wrap">
    <h2><?php echo SHASHIN_DISPLAY_NAME . " " . __("Settings", SHASHIN_L10N_NAME); ?></h2>

    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashin_action" value="update_options">
    <table class="form-table">
    <tr style="vertical-align: top;">
    <td nowrap="nowrap"><?php _e("Your Picasa server:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[picasa_server]" value="<?php echo $shashin_options['picasa_server']; ?>" size="30"></td>
    <td><?php _e("The base URL of your Picasa server. Be sure to include 'http://'", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Sync all albums daily:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[scheduled_update]" value="y"<?php
        if ($shashin_options['scheduled_update'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[scheduled_update]" value="n"<?php
        if ($shashin_options['scheduled_update'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("This will make Shashin sync all your albums automatically on a daily basis.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Image div padding:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[div_padding]" value="<?php echo $shashin_options['div_padding']; ?>" size="30"></td>
    <td><?php _e("Double the '.shashin_image img' padding value in shashin.css", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Thumbnail div padding:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[thumb_padding]" value="<?php echo $shashin_options['thumb_padding']; ?>" size="30"></td>
    <td><?php _e("Double the '.shashin_thumb img' padding value in shashin.css", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Prefix album titles on captions:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[prefix_captions]" value="y"<?php
        if ($shashin_options['prefix_captions'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[prefix_captions]" value="n"<?php
        if ($shashin_options['prefix_captions'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Clicking 'yes' means a photo's album title will be prefixed on its caption.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Full-size image display:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[image_display]" value="same_window"<?php
            if ($shashin_options['image_display'] == 'same_window') echo ' checked="checked"'; ?> />
            <?php _e("At Picasa, in same browser window", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="new_window"<?php
            if ($shashin_options['image_display'] == 'new_window') echo ' checked="checked"'; ?> />
            <?php _e("At Picasa, in a new browser window", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="highslide"<?php
            if ($shashin_options['image_display'] == 'highslide') echo ' checked="checked"'; ?> />
            <?php _e("Use Highslide", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="none"<?php
            if ($shashin_options['image_display'] == 'none') echo ' checked="checked"'; ?> />
            <?php _e("Do not make thumbnails clickable", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="other"<?php
            if ($shashin_options['image_display'] == 'other') echo ' checked="checked"'; ?> />
            <?php _e("Use Other Viewer (e.g. Lightbox)", SHASHIN_L10N_NAME); ?><br />
            <div style="margin-left: 20px;"><?php _e("Link 'rel' for images:", SHASHIN_L10N_NAME); ?><input type="text" name="shashin_options[other_image_rel]" value="<?php echo $shashin_options['other_image_rel']; ?>" size="12"><br />
            <?php _e("Link 'rel' for videos:", SHASHIN_L10N_NAME); ?><input type="text" name="shashin_options[other_video_rel]" value="<?php echo $shashin_options['other_video_rel']; ?>" size="12"><br />
            <?php _e("CSS class for link:", SHASHIN_L10N_NAME); ?><input type="text" name="shashin_options[other_class]" value="<?php echo $shashin_options['other_class']; ?>" size="12"><br />
            <?php _e("'rel' delimiter for image groups:", SHASHIN_L10N_NAME); ?><br />
            <input type="radio" name="shashin_options[other_delimiter]" value="brackets"<?php
            if ($shashin_options['other_delimiter'] == 'brackets') echo ' checked="checked"'; ?> />
            <?php _e("Brackets", SHASHIN_L10N_NAME); ?>
            <input type="radio" name="shashin_options[other_delimiter]" value="hyphen"<?php
            if ($shashin_options['other_delimiter'] == 'hyphen') echo ' checked="checked"'; ?> />
            <?php _e("Hyphen", SHASHIN_L10N_NAME); ?></div>
            </td>
    <td><?php _e("This determines how to display an image when its thumbnail is clicked. Highslide is included with Shashin and works 'out of the box.' If you select 'other', you are responsible for implementing your own image viewer. Shashin will give you link and image tags following the <a href='http://www.lokeshdhakar.com/projects/lightbox2/' target='_blank'>Lightbox 2</a> format. It also allows you to apply a class to the link (necessary for Thickbox), and to specify whether image groups should be delimited by brackets in the link's rel attribute (e.g. 'lightbox[28]') or a hyphen (e.g. 'thickbox-28').", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr>
    <td colspan="3"><strong><?php _e("Highslide Settings", SHASHIN_L10N_NAME); ?></strong><br /><?php _e("These settings apply only if you select 'Use Highslide' above, for the full-size image display.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Highslide image size:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[highslide_max]">
    <?php
        foreach ($shashin_image_sizes as $size) {
            echo '<option value="' . $size . '"';
            if ($shashin_options['highslide_max'] == $size) {
                echo ' selected="selected"';
            }
            echo ">$size</option>\n";
        }
    ?>
    </select></td>
    <td><?php _e("This determines the 'maximum dimension' of an image displayed by Highslide when its thumbnail is clicked. This size is applied to either the height or the width (whichever is greater) and then the correct size for the other dimension is calculated on the fly. The sizes listed in the drop-down menu are the only ones supported by Picasa. Note that the sizes", SHASHIN_L10N_NAME); ?>
    <?php echo implode(", ", $shashin_crop_sizes) ?>
    <?php _e("are cropped square.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Autoplay Highslide slideshows:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[highslide_autoplay]" value="true"<?php
        if ($shashin_options['highslide_autoplay'] == 'true') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[highslide_autoplay]" value="false"<?php
        if ($shashin_options['highslide_autoplay'] == 'false') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("If someone clicks an image in a slideshow group, this determines whether the slideshow plays automatically.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Highslide slideshow image display time:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[highslide_interval]" value="<?php echo $shashin_options['highslide_interval']; ?>" size="30"></td>
    <td><?php _e("How long each image is displayed in a slideshow (in milliseconds)", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Repeat slideshow:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[highslide_repeat]" value="1"<?php
        if ($shashin_options['highslide_repeat'] == '1') echo ' checked="checked"'; ?> />
            <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[highslide_repeat]" value="0"<?php
        if ($shashin_options['highslide_repeat'] == '0') echo ' checked="checked"'; ?> />
            <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Whether to start over from the first slide when going to the next from the last slide.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Highslide video dimensions:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><?php _e("Width:", SHASHIN_L10N_NAME); ?>
        <input type="text" name="shashin_options[highslide_video_width]" value="<?php echo $shashin_options['highslide_video_width']; ?>" size="3">
        <?php _e("Height:", SHASHIN_L10N_NAME); ?>
        <input type="text" name="shashin_options[highslide_video_height]" value="<?php echo $shashin_options['highslide_video_height']; ?>" size="3"></td>
    <td><?php _e("If you select Highslide for viewing images, it will also be used for displaying videos. This controls the height and width of the embedded video (unlike images, the dimensions cannot be calculated on the fly). A 4:3 (width:height) ratio is common for videos.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Outline type:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[highslide_outline_type]">
    <?php
        $outline_types = array('beveled', 'glossy-dark', 'rounded-black', 'drop-shadow', 'outer-glow', 'rounded-white', 'null');
        foreach ($outline_types as $type) {
            echo '<option value="' . $type . '"';
            if ($shashin_options['highslide_outline_type'] == $type) {
                echo ' selected="selected"';
            }
            echo ">$type</option>\n";
        }
    ?>
    </select></td>
    <td><?php _e("The graphic outline applied to expanded images. Select 'null' for no outline.", SHASHIN_L10N_NAME); ?></td>

    <tr style="vertical-align: top;">
    <td><?php _e("Controller Position:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[highslide_v_position]">
    <?php
        $v_position = array('above', 'top', 'middle', 'bottom', 'below');
        foreach ($v_position as $pos) {
            echo '<option value="' . $pos . '"';
            if ($shashin_options['highslide_v_position'] == $pos) {
                echo ' selected="selected"';
            }
            echo ">$pos</option>\n";
        }
    ?>
    </select>
    <select name="shashin_options[highslide_h_position]">
    <?php
        $h_position = array('leftpanel', 'left', 'center', 'right', 'rightpanel');
        foreach ($h_position as $pos) {
            echo '<option value="' . $pos . '"';
            if ($shashin_options['highslide_h_position'] == $pos) {
                echo ' selected="selected"';
            }
            echo ">$pos</option>\n";
        }
    ?>
    </select></td>

    <td><?php _e("Where to position the slideshow control bar.", SHASHIN_L10N_NAME); ?></td>

    <tr style="vertical-align: top;">
    <td><?php _e("Dimming opacity", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[highslide_dimming_opacity]" value="<?php echo $shashin_options['highslide_dimming_opacity']; ?>" size="4"></td>
    <td><?php _e("Enter a number between 0 and 1. Indicates how much to dim the background when an image is expanded (enter 0 for no dimming). In highslide.css, look for .highslide-dimming to change the color (default is black)", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Hide controller on mouseout:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[highslide_hide_controller]" value="1"<?php
        if ($shashin_options['highslide_hide_controller'] == '1') echo ' checked="checked"'; ?> />
            <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[highslide_hide_controller]" value="0"<?php
        if ($shashin_options['highslide_hide_controller'] == '0') echo ' checked="checked"'; ?> />
            <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Whether the slideshow controller should be hidden when the mouse leaves the full-size image.", SHASHIN_L10N_NAME); ?></td>
    </tr>


    <tr>
    <td colspan="3"><strong><?php _e("Album Photos Settings", SHASHIN_L10N_NAME); ?></strong><br /><?php _e("If you are using Highslide and the [salbumthumbs] or [salbumlist] tags, these settings control how the photos in an album are displayed when an album thumbnail is clicked.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Thumbnail size:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[album_photos_max]">
    <?php
        foreach ($shashin_image_sizes as $size) {
            echo '<option value="' . $size . '"';
            if ($shashin_options['album_photos_max'] == $size) {
                echo ' selected="selected"';
            }
            echo ">$size</option>\n";
        }
    ?>
    </select></td>
    <td><?php _e("The size to use for displaying the photos in the album. See 'Highslide image size' above for more information. Note that the sizes", SHASHIN_L10N_NAME); ?>
    <?php echo implode(", ", $shashin_crop_sizes) ?>
    <?php _e("are cropped square.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Number of columns:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[album_photos_cols]" value="<?php echo $shashin_options['album_photos_cols']; ?>" size="2"></td>
    <td><?php _e("The maximum number of columns for displaying the album photos. You will want to take into account the 'thumbnail size' you selected above, to make sure the overall display is not too wide for your WordPress theme.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Sort Order:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[album_photos_order]">
    <?php
        $order_options = array('title asc', 'title desc', 'uploaded_timestamp asc', 'uploaded_timestamp desc', 'taken_timestamp asc', 'taken_timestamp desc');
        foreach ($order_options as $option) {
            echo '<option value="' . $option . '"';
            if ($shashin_options['album_photos_order'] == $option) {
                echo ' selected="selected"';
            }
            echo ">$option</option>\n";
        }
    ?>
    </select></td>
    <td><?php _e("How to order the photos. 'asc' means ascending from lowest to highest (a-z, oldest to newest), 'desc' means descending from highest to lowest (z-a, newest to oldest).", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Display photo captions:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[album_photos_captions]" value="y"<?php
        if ($shashin_options['album_photos_captions'] == 'y') echo ' checked="checked"'; ?> />
            <?php _e("Yes", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[album_photos_captions]" value="n"<?php
        if ($shashin_options['album_photos_captions'] == 'n') echo ' checked="checked"'; ?> />
            <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Whether to display a caption under each thumbnail.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Display album description:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[album_photos_description]" value="y"<?php
        if ($shashin_options['album_photos_description'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[album_photos_description]" value="n"<?php
        if ($shashin_options['album_photos_description'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("The album title will appear above the photos. You can choose whether to also display the album description, after the title.", SHASHIN_L10N_NAME); ?></td>
    </tr>
    </table>
    <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php _e("Save Options", SHASHIN_L10N_NAME); ?>" /></p>
    </form>

    <div style="border: thin solid; padding: 5px;">
        <h3><?php _e("Uninstall Shashin", SHASHIN_L10N_NAME); ?></h3>

        <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
        <input type="hidden" name="shashin_action" value="uninstall">
        <table border="0" cellspacing="3" cellpadding="3" class="form-table">
        <tr style="vertical-align: top;">
        <td nowrap="nowrap"><?php _e("Uninstall Shashin?", SHASHIN_L10N_NAME); ?></td>
        <td><input type="checkbox" name="shashin_uninstall" value="y" /></td>
        <td><?php _e("Check this box if you want to completely remove Shashin. <strong>This will permanently break any Shashin tags that you still have in your posts or pages.</strong> After uninstalling, you can then deactivate Shashin on your plugins management page.", SHASHIN_L10N_NAME); ?></td>
        </tr>
        </table>

        <p class="submit"><input class="button-secondary" type="submit" name="save" value="<?php _e("Uninstall Shashin", SHASHIN_L10N_NAME); ?>" onclick="return confirm('<?php _e("Are you sure you want to uninstall Shashin?", SHASHIN_L10N_NAME); ?>');" /></p>
        </form>
    </div>
</div>
