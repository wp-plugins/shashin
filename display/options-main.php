<?php
/**
 * Set options for Shashin. This includes specifying your Picasa server, and
 * a couple of CSS settings.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.2
 * @package Shashin
 * @subpackage AdminPanels
 */
 ?>
 
<div class="wrap">
    <h2><?php echo SHASHIN_DISPLAY_NAME ?></h2>
    
    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>
    
    <h3>Options</h3>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashinAction" value="updateOptions">
    <table border="0" cellspacing="3" cellpadding="3" class="form-table">
    <tr valign="top">
    <td>Your Picasa server:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_picasa_server" value="<?php echo get_option('shashin_picasa_server') ?>" size="30"></td>
    <td>The base URL of your Picasa server. Be sure to include "http://"</td>
    </tr>
    
    <tr valign="top">
    <td>Image div padding:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_div_padding" value="<?php echo get_option('shashin_div_padding') ?>" size="30"></td>
    <td>Double the ".shashin_image img" padding value in shashin.css</td>
    </tr>

    <tr valign="top">
    <td>Thumbnail div padding:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_thumb_padding" value="<?php echo get_option('shashin_thumb_padding') ?>" size="30"></td>
    <td>Double the ".shashin_thumb img" padding value in shashin.css</td>
    </tr>       

    <tr valign="top">
    <td>Your page for album photos:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_album_photos_url" value="<?php echo get_option('shashin_album_photos_url') ?>" size="30"></td>
    <td>If you want to display all the photos in a Picasa album, create a Wordpress
    page containing the salbumphotos tag, and put the URL for that page here.</td>
    </tr>
    
    <tr valign="top">
    <td>Prefix album titles on captions:</td>
    <td nowrap="nowrap"><input type="radio" name="shashin_prefix_captions" value="y"<?php
        if (get_option('shashin_prefix_captions') == 'y') echo ' checked="checked"'; ?> /> Yes
        <input type="radio" name="shashin_prefix_captions" value="n"<?php
        if (get_option('shashin_prefix_captions') == 'n') echo ' checked="checked"'; ?> /> No</td>
    <td>Clicking "yes" means a photo's album title will be prefixed on its caption.</td>
    </tr>    
    
    <tr valign="top">
    <td>Full-size image display:</td>
    <td nowrap="nowrap"><input type="radio" name="shashin_image_display" value="same_window"<?php
            if (get_option('shashin_image_display') == 'same_window') echo ' checked="checked"'; ?> />
            At Picasa, in same browser window<br />
        <input type="radio" name="shashin_image_display" value="new_window"<?php
            if (get_option('shashin_image_display') == 'new_window') echo ' checked="checked"'; ?> />
            At Picasa, in a new browser window<br />
        <input type="radio" name="shashin_image_display" value="highslide"<?php
            if (get_option('shashin_image_display') == 'highslide') echo ' checked="checked"'; ?> />
            Use Highslide<br />
        <input type="radio" name="shashin_image_display" value="none"<?php
            if (get_option('shashin_image_display') == 'none') echo ' checked="checked"'; ?> />
            Do not make thumbnails clickable</td>
    <td>This determines how to display an image when its thumbnail is clicked</td>
    </tr>
    
    <tr>
    <td colspan="3"><strong>Highslide Settings</strong><br />These settings apply only if you select "Use Highslide" above, for the full-size image display.</td>
    </tr>

    <tr valign="top">
    <td>Highslide image size:</td>
    <td nowrap="nowrap"><select name="shashin_highslide_max">
    <?php
        foreach ($allowed as $size) {
            echo '<option value="' . $size . '"';
            if (get_option('shashin_highslide_max') == $size) {
                echo ' selected="selected"';
            }
            echo ">$size</option>\n";
        }
    ?>
    </select></td>
    <td>This determines the "maximum dimension" of an image displayed by Highslide when its
    thumbnail is clicked. This size is applied to either the height or the width (whichever
    is greater) and then the correct size for the other dimension is calculated on the fly.
    The sizes listed in the drop-down menu are the only ones supported by Picasa. Note that
    the sizes <?php echo implode(", ", eval(SHASHIN_CROP_SIZES)) ?> are cropped square.</td>
    </tr>

    <tr valign="top">
    <td>Autoplay Highslide slideshows:</td>
    <td nowrap="nowrap"><input type="radio" name="shashin_highslide_autoplay" value="true"<?php
        if (get_option('shashin_highslide_autoplay') == 'true') echo ' checked="checked"'; ?> />
            Yes<br />
        <input type="radio" name="shashin_highslide_autoplay" value="false"<?php
        if (get_option('shashin_highslide_autoplay') == 'false') echo ' checked="checked"'; ?> />
            No</td>
    <td>If someone clicks an image in a slideshow group, this determines whether the slideshow plays
    automatically.</td>
    </tr>
    
    <tr valign="top">
    <td>Highslide slideshow image display time:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_highslide_interval" value="<?php echo get_option('shashin_highslide_interval') ?>" size="30"></td>
    <td>How long each image is displayed in a slideshow (in milliseconds)</td>
    </tr>

    <tr valign="top">
    <td>Highslide video dimensions:</td>
    <td nowrap="nowrap">Width: <input type="text" name="shashin_highslide_video_width" value="<?php echo get_option('shashin_highslide_video_width') ?>" size="3">
    Height: <input type="text" name="shashin_highslide_video_height" value="<?php echo get_option('shashin_highslide_video_height') ?>" size="3"></td>
    <td>If you select Highslide for viewing images, it will also be used for displaying videos. This controls
    the height and width of the embedded video (unlike images, the dimensions cannot be calculated on the fly).
    A 4:3 (width:height) ratio is common for videos.</td>
    </tr>

    </table>

    <p><input type="submit" name="save" value="Save Options" /></p>
    </form>
</div>
