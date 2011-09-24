<?php
/**
 * Set options for Shashin.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage AdminPanels
 */
?>

<div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5378623">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e("Support Shashin", SHASHIN_L10N_NAME); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e("Support Shashin", SHASHIN_L10N_NAME); ?>" title="<?php _e("Support Shashin", SHASHIN_L10N_NAME); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="<?php echo SHASHIN_FAQ_URL; ?>" target="_blank"><?php _e("Shashin Help", SHASHIN_L10N_NAME); ?></a>
        </form>
    </div>

    <h2><?php echo SHASHIN_DISPLAY_NAME . " " . __("Settings", SHASHIN_L10N_NAME); ?></h2>

    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashin_action" value="update_options">
    <table class="form-table">
    <tr style="vertical-align: top;">
    <td nowrap="nowrap"><?php _e("Your Picasa server:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[picasa_server]" value="<?php echo $shashin_options['picasa_server']; ?>" size="30" /></td>
    <td><?php _e("The base URL of your Picasa server. Be sure to include 'http://' - do not include your username", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Sync all albums every 10 hours:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[scheduled_update]" value="y"<?php
        if ($shashin_options['scheduled_update'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[scheduled_update]" value="n"<?php
        if ($shashin_options['scheduled_update'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("If you have any videos in your albums, choose 'Yes' - Picasa video URLs expire every 11 hours and need to be refreshed.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Add to photo caption:", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Album Title:", SHASHIN_L10N_NAME); ?><input type="radio" name="shashin_options[prefix_captions]" value="y"<?php if ($shashin_options['prefix_captions'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[prefix_captions]" value="n"<?php
        if ($shashin_options['prefix_captions'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?><br />
        EXIF Data: <input type="radio" name="shashin_options[caption_exif]" value="date"<?php if ($shashin_options['caption_exif'] == 'date') echo ' checked="checked"'; ?> />
        <?php _e("Date only", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[caption_exif]" value="all"<?php if ($shashin_options['caption_exif'] == 'all') echo ' checked="checked"'; ?> />
        <?php _e("All", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[caption_exif]" value="none"<?php if ($shashin_options['caption_exif'] == 'none' || !$shashin_options['caption_exif']) echo ' checked="checked"'; ?> />
        <?php _e("None", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e('Album titles are prefixed to all captions. Dates are appended to expanded view captions only. EXIF data is appended to Highslide captions only, and includes the camera make, model, fstop, focal length, exposure time, and ISO.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Image div padding:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[div_padding]" value="<?php echo $shashin_options['div_padding']; ?>" size="30" /></td>
    <td><?php _e("Make this 2x the '.shashin_image img' padding value in shashin.css", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Thumbnail div padding:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[thumb_padding]" value="<?php echo $shashin_options['thumb_padding']; ?>" size="30" /></td>
    <td><?php _e("Make this 2x the '.shashin_thumb img' padding value in shashin.css", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Maximum image width for your theme:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[theme_max_size]" value="<?php echo $shashin_options['theme_max_size']; ?>" size="30" /></td>
    <td><?php _e("The width of your theme's content area in pixels, minus any padding. If you use the word 'max' for the size in your Shashin tags, Shashin will use the closest, smaller supported Picasa size for the images. You can use 'max' for the image size with [sthumbs] and other tags. Shashin estimates 10px of total horizontal margin/padding per image.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Expanded image display:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[image_display]" value="same_window"<?php
            if ($shashin_options['image_display'] == 'same_window') echo ' checked="checked"'; ?> />
            <?php _e("At Picasa, in same browser window", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="new_window"<?php
            if ($shashin_options['image_display'] == 'new_window') echo ' checked="checked"'; ?> />
            <?php _e("At Picasa, in a new browser window", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="highslide"<?php
            if ($shashin_options['image_display'] == 'highslide') echo ' checked="checked"'; ?> />
            <?php _e("Use Highslide", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="other"<?php
            if ($shashin_options['image_display'] == 'other') echo ' checked="checked"'; ?> />
            <?php _e("Use Other Viewer (e.g. Lightbox)", SHASHIN_L10N_NAME); ?><br />
        <input type="radio" name="shashin_options[image_display]" value="none"<?php
            if ($shashin_options['image_display'] == 'none') echo ' checked="checked"'; ?> />
            <?php _e("Do not make thumbnails clickable", SHASHIN_L10N_NAME); ?>
            </td>
    <td><?php _e('This determines how to display an image when its thumbnail is clicked. Highslide is included with Shashin and works "out of the box." <strong>If you select "Use Other Viewer," you are responsible for implementing your own image viewer.</strong> See "Highslide Settings" and "Other Viewer Settings" below.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Picasa Email:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[picasa_username]" value="<?php echo $shashin_options['picasa_username']; ?>" size="30" /></td>
    <td rowspan="2"><?php _e("Enter your Picasa email address and password only if you have unlisted albums. Do not enter them if you do not have any unlisted albums. Note you must have the <a href='http://www.php.net/manual/en/book.curl.php'>curl</a> extension for PHP installed to access unlisted albums.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Picasa Password:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="password" name="shashin_options[picasa_password]" value="<?php echo $shashin_options['picasa_password']; ?>" size="30" /></td>
    </tr>

    <tr>
    <td colspan="3"><strong><?php _e("Album Photos Settings", SHASHIN_L10N_NAME); ?></strong><br /><?php _e("When using the [salbumthumbs] or [salbumlist] tags, these settings control how the photos in an album are displayed on your site when an album thumbnail is clicked.", SHASHIN_L10N_NAME); ?></td>
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
    <td><?php _e("This determines the 'maximum dimension' of the thumbnails. This size is applied to either the height or the width (whichever is greater). The sizes listed in the drop-down menu are the only ones supported by Picasa. Note that the sizes", SHASHIN_L10N_NAME); ?>
    <?php echo implode(", ", $shashin_crop_sizes) ?>
    <?php _e("are cropped square.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Number of columns:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[album_photos_cols]" value="<?php echo $shashin_options['album_photos_cols']; ?>" size="2" /></td>
    <td><?php _e("The maximum number of columns for displaying the album photos. You will want to take into account the 'thumbnail size' you selected above, to make sure the overall display is not too wide for your WordPress theme.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Sort Order:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[album_photos_order]">
    <?php
        $order_options = array(
            'Picasa Order' => 'picasa_order',
            'Reverse Picasa Order' => 'picasa_order desc',
            'Filename A-Z' =>  'title',
            'Filename Z-A' =>  'title desc',
            'Upload Date (old to new)' => 'uploaded_timestamp',
            'Upload Date (new to old)' =>  'uploaded_timestamp desc',
            'Taken Date (old to new)' => 'taken_timestamp',
            'Taken Date (new to old)' => 'taken_timestamp desc');
        foreach ($order_options as $label=>$option) {
            echo '<option value="' . $option . '"';
            if ($shashin_options['album_photos_order'] == $option) {
                echo ' selected="selected"';
            }
            echo ">$label</option>\n";
        }
    ?>
    </select></td>
    <td><?php _e("Select the display order for the photos.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Photos per page:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="text" name="shashin_options[photos_per_page]" value="<?php echo $shashin_options['photos_per_page']; ?>" size="30" /></td>
    <td><?php _e("The maximum number of photos to show per page. Leave blank to show all album photos on one page.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Display photo captions:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[album_photos_captions]" value="y"<?php
        if ($shashin_options['album_photos_captions'] == 'y') echo ' checked="checked"'; ?> />
            <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[album_photos_captions]" value="n"<?php
        if ($shashin_options['album_photos_captions'] == 'n') echo ' checked="checked"'; ?> />
            <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Whether to display a caption under each thumbnail.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Display album description:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[album_photos_description]" value="y"<?php
        if ($shashin_options['album_photos_description'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[album_photos_description]" value="n"<?php
        if ($shashin_options['album_photos_description'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("The album title will appear above the photos. You can choose whether to also display the album description, after the title.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Group albums by user:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><input type="radio" name="shashin_options[group_by_user]" value="y"<?php
        if ($shashin_options['group_by_user'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Yes", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[group_by_user]" value="n"<?php
        if ($shashin_options['group_by_user'] == 'n') echo ' checked="checked"'; ?> />
        <?php _e("No", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e("Use this only if you have albums from more than one Picasa user. This will also add a Username column on the Shashin Tools page.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr>
    <td colspan="3"><strong><?php _e("Highslide Settings", SHASHIN_L10N_NAME); ?></strong><br /><?php _e('These settings apply only if you select "Use Highslide" above.', SHASHIN_L10N_NAME); ?></td>
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
    <td><?php _e("The maximum size to use for displaying the photos. See 'Thumbnail size' above for more information.", SHASHIN_L10N_NAME); ?></td>
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
    <td nowrap="nowrap"><input type="text" name="shashin_options[highslide_interval]" value="<?php echo $shashin_options['highslide_interval']; ?>" size="30" /></td>
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
        <input type="text" name="shashin_options[highslide_video_width]" value="<?php echo $shashin_options['highslide_video_width']; ?>" size="3" />
        <?php _e("Height:", SHASHIN_L10N_NAME); ?>
        <input type="text" name="shashin_options[highslide_video_height]" value="<?php echo $shashin_options['highslide_video_height']; ?>" size="3" /></td>
    <td><?php _e("If you select Highslide for viewing images, it will also be used for displaying videos. This controls the height and width of the embedded video (unlike images, the dimensions cannot be calculated on the fly). A 4:3 (width:height) ratio is common for videos.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Outline type:", SHASHIN_L10N_NAME); ?></td>
    <td nowrap="nowrap"><select name="shashin_options[highslide_outline_type]">
    <?php
        $outline_types = array('beveled', 'glossy-dark', 'rounded-black', 'drop-shadow', 'outer-glow', 'rounded-white', 'none');
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
        $v_position = array('top', 'middle', 'bottom');
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
        $h_position = array('left', 'center', 'right');
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
    <td nowrap="nowrap"><input type="text" name="shashin_options[highslide_dimming_opacity]" value="<?php echo $shashin_options['highslide_dimming_opacity']; ?>" size="4" /></td>
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
    <td><?php _e("Whether the slideshow controller should be hidden when the mouse leaves the expanded image.", SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr>
    <td colspan="3"><strong><?php _e("Other Viewer Settings", SHASHIN_L10N_NAME); ?></strong><br /><?php _e('These settings apply only if you select "Use Other Viewer" above. There are a wide variety of configuration requirements for different viewers. Shashin accomodates them by letting you control the attributes for the link and image tags used for its thumbnails. All links and thumbnails automatically get unique IDs (e.g. "shashin_thumb_link_24", "shashin_thumb_image_24").', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e('Link "rel" for images:', SHASHIN_L10N_NAME); ?></td>
    <td><input type="text" name="shashin_options[other_rel_image]" value="<?php echo $shashin_options['other_rel_image']; ?>" size="30" /></td>
    <td><?php _e('The "rel" attribute for image links; e.g. "lightbox" if you are using Lightbox 2.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e('Link "rel" for videos:', SHASHIN_L10N_NAME); ?></td>
    <td><input type="text" name="shashin_options[other_rel_video]" value="<?php echo $shashin_options['other_rel_video']; ?>" size="30" /></td>
    <td><?php _e('The "rel" attribute for links if displaying a video; e.g. "vidbox" if you are using Videobox.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e('"rel" delimiter for image groups:', SHASHIN_L10N_NAME); ?></td>
    <td><input type="radio" name="shashin_options[other_rel_delimiter]" value="brackets"<?php if ($shashin_options['other_rel_delimiter'] == 'brackets') echo ' checked="checked"'; ?> />
        <?php _e("Brackets", SHASHIN_L10N_NAME); ?>
        <input type="radio" name="shashin_options[other_rel_delimiter]" value="hyphen"<?php if ($shashin_options['other_rel_delimiter'] == 'hyphen') echo ' checked="checked"'; ?> />
        <?php _e("Hyphen", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e('How to delimit image groups in a rel tag. Lightbox 2 uses brackets (e.g. "lightbox[33]"). Many other viewers use hyphens (e.g. Slimbox: "lightbox-33").', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Class for links:", SHASHIN_L10N_NAME); ?></td>
    <td><input type="text" name="shashin_options[other_link_class]" value="<?php echo $shashin_options['other_link_class']; ?>" size="30" /></td>
    <td><?php _e('A CSS class to apply to the link tags. Leave blank for none.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e('Use photo caption as "title" in:', SHASHIN_L10N_NAME); ?></td>
    <td><input type="checkbox" name="shashin_options[other_link_title]" value="y"<?php if ($shashin_options['other_link_title'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Link tags", SHASHIN_L10N_NAME); ?>
        <input type="checkbox" name="shashin_options[other_image_title]" value="y"<?php if ($shashin_options['other_image_title'] == 'y') echo ' checked="checked"'; ?> />
        <?php _e("Image tags", SHASHIN_L10N_NAME); ?></td>
    <td><?php _e('You can use the photo\'s caption as the "title" for for its link tag, its image tag, or both.', SHASHIN_L10N_NAME); ?></td>
    </tr>

    <tr style="vertical-align: top;">
    <td><?php _e("Class for thumbnails:", SHASHIN_L10N_NAME); ?></td>
    <td><input type="text" name="shashin_options[other_image_class]" value="<?php echo $shashin_options['other_image_class']; ?>" size="30" /></td>
    <td><?php _e('A CSS class to apply to the thumbnails image tags. Leave blank for none.', SHASHIN_L10N_NAME); ?></td>
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
