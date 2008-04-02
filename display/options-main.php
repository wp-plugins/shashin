<?php
/**
 * Set options for Shashin. This includes specifying your Picasa server, and
 * a couple of CSS settings.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.0.4
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
    <table border="0" cellspacing="3" cellpadding="3">
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
    <td>Full-size image display:</td>
    <td nowrap="nowrap"><input type="radio" name="shashin_image_display" value="same_window"<?php
        if (get_option('shashin_image_display') == 'same_window') echo ' checked="checked"'; ?> />
            At Picasa, in same browser window<br />
        <input type="radio" name="shashin_image_display" value="new_window"<?php
        if (get_option('shashin_image_display') == 'new_window') echo ' checked="checked"'; ?> />
            At Picasa, in a new browser window<br />
        <input type="radio" name="shashin_image_display" value="highslide"<?php
            if (get_option('shashin_image_display') == 'highslide') echo ' checked="checked"'; ?> /> Use Highslide</td>
    <td>This determines how to display an image when its thumbnail is clicked</td>
    </tr>
    
    <tr valign="top">
    <td>Highslide image size:</td>
    <td nowrap="nowrap"><input type="text" name="shashin_highslide_max" value="<?php echo get_option('shashin_highslide_max') ?>" size="30"></td>
    <td>This determines the size of an image displayed by Highslide when its
    thumbnail is clicked. The size must be one supported by Picasa (the options
    are: <?php echo implode(", ", $allowed); ?>).</td>
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
    </table>

    <p><input type="submit" name="save" value="Save Options" /></p>
    </form>
</div>
