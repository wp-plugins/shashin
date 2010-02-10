<?php
/**
 * This Shashin admin panel displays thumbnails and summary data for photos in
 * a specified album. It also allows for setting a flag for each photo,
 * indicating whether it should be included in any displays of random images. An
 * album object must be instantiated prior to displaying this panel.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ShashinPhoto::ShashinPhoto()
 * @uses ShashinPhoto::getPhoto()
 * @uses ToppaWPFunctions::displayInput()
 */

$shashin_image_sizes = unserialize(SHASHIN_IMAGE_SIZES);
$shashin_crop_sizes = unserialize(SHASHIN_CROP_SIZES);
$edit_link = SHASHIN_ADMIN_URL
    . '&amp;shashin_action=edit_album_photos&amp;album_id='
    . $album->data['album_id'] . '&amp;shashin_orderby=';
?>

 <div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="5378623" />
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e("Support Shashin", SHASHIN_L10N_NAME); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="<?php _e("Support Shashin", SHASHIN_L10N_NAME); ?>" title="<?php _e("Support Shashin", SHASHIN_L10N_NAME); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="<?php echo SHASHIN_FAQ_URL; ?>" target="_blank"><?php _e("Shashin Help", SHASHIN_L10N_NAME); ?></a>
        </form>
    </div>

    <?php
    echo '<h2>' . SHASHIN_DISPLAY_NAME . "</h2>\n";

    if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    }

    echo '<p><a href="' . SHASHIN_ADMIN_URL . '">&laquo; '
        . __("Shashin Admin", SHASHIN_L10N_NAME) . "</a></p>\n";
    echo '<h3>' . $album->data['title'] . "</h3>\n";
    echo '<p>' . __("See the <a href='#ref'>quick reference</a> below for help with Shashin photo tags. Click a column header to order the photo list by that column (and click again to reverse the order).", SHASHIN_L10N_NAME) . "</p>\n";
    echo '<form action="' . SHASHIN_ADMIN_URL . '" method="post">' . "\n";
    echo '<input type="hidden" name="shashin_action" value="update_album_photos" />' . "\n";
    echo '<input type="hidden" name="album_id" value="' . $album->data['album_id'] . '" />' . "\n";
    echo '<table style="border-collapse: seperate; border-spacing: 5px;">' . "\n";
    echo "<tr>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'picasa_order') ? 'picasa_order%20desc' : 'picasa_order')
        . '">' . __("Picasa", SHASHIN_L10N_NAME)
        . (($_GET['shashin_orderby'] == 'picasa_order desc') ? ' &uarr;' : '')
        . (($_GET['shashin_orderby'] == 'picasa_order' || !$_GET['shashin_orderby']) ? ' &darr;' : '')
        . "</a></th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'photo_key') ? 'photo_key%20desc' : 'photo_key')
        . '">' . __("Photo Key", SHASHIN_L10N_NAME)
        . (($_GET['shashin_orderby'] == 'photo_key desc') ? ' &uarr;' : '')
        . (($_GET['shashin_orderby'] == 'photo_key') ? ' &darr;' : '')
        . "</a></th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'title') ? 'title%20desc' : 'title')
        . '">' . __("Filename", SHASHIN_L10N_NAME)
        . (($_GET['shashin_orderby'] == 'title desc') ? ' &uarr;' : '')
        . (($_GET['shashin_orderby'] == 'title') ? ' &darr;' : '')
        . "</a></th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'taken_timestamp') ? 'taken_timestamp%20desc' : 'taken_timestamp')
        . '">' . __("Date Taken", SHASHIN_L10N_NAME)
        . (($_GET['shashin_orderby'] == 'taken_timestamp desc') ? ' &uarr;' : '')
        . (($_GET['shashin_orderby'] == 'taken_timestamp') ? ' &darr;' : '')
        . "</a></th>\n";
    echo '<th>' . __("Markup", SHASHIN_L10N_NAME) . "</th>\n";
    echo '<th>' . __("Include in Random?", SHASHIN_L10N_NAME) . "</th>\n";
    echo "</tr>\n";

    $i = 1;
    $markup_args = array('max_size' => 64, 'caption_yn' => 'n');

    foreach ($album->data['photos'] as $photos_data) {
        $photo = new ShashinPhoto();
        $photo->getPhoto(null, $photos_data);
        echo "<tr style='text-align: center;'";
        echo (($i % 2 == 0) ? ">\n" : " class='alternate'>\n");
        echo "<td>" . $photo->getPhotoMarkup($markup_args, true) . "</td>\n";
        echo "<td>" . $photo->data['photo_key'] . "</td>\n";
        echo "<td>" . $photo->data['title'] . "</td>\n";
        echo "<td>" . (is_numeric($photo->data['taken_timestamp'])
            ? date("d-M-y H:i", $photo->data['taken_timestamp']) : 'Unknown')
            . "</td>\n";
        echo "<td>[simage=" . $photo->data['photo_key'] . ",640,y,center]</td>\n";
        echo "<td>";
        ToppaWPFunctions::displayInput("include_in_random[{$photo->data['photo_id']}]", $photo->ref_data['include_in_random'], ($photo->data['include_in_random'] ? $photo->data['include_in_random'] : "Y"));
        echo "</td>\n";
        echo "</tr>\n";
        $i++;
    }
    ?>
    <tr>
    <td colspan="5">&nbsp;</td>
    <td><input type="submit" name="submit" class="button-primary" value="<?php _e("Update Random Display", SHASHIN_L10N_NAME); ?>" /></td>
    </tr>
    </table>
    </form>

    <hr />

    <h3><a name="ref"></a><?php _e("Photo Tags Quick Reference", SHASHIN_L10N_NAME); ?></h3>

    <dl class="shashin_help">
    <dt><?php _e("<strong>Valid Picasa image sizes:</strong> ", SHASHIN_L10N_NAME);
        echo implode(", ", $shashin_image_sizes) . "<br />";
        _e("For a cropped square image, use sizes", SHASHIN_L10N_NAME);
        echo " " . implode(", ", $shashin_crop_sizes); ?></dt>
    <dt><?php _e("Display a single image or video", SHASHIN_L10N_NAME); ?></dt>
    <dd><?php _e("[simage=photo_key,max_size,caption_yn,position,clear,alt_thumb]", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [simage=17,max,y,center] Photo with Shashin key 17, maximum possible size, show the caption, centered (the size of the image depends on the width of your theme's content area)", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [simage=22,200,n,left,none,33] Video with Shashin key 22, size of 200 pixels, no caption, float left, do not clear margins, use photo with Shashin key 33 as the thumbnail", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Notes: alt_thumb is especially useful for videos when you don't want to use the thumbnail generated by Picasa", SHASHIN_L10N_NAME); ?></dd>

    <dt><?php _e("Display a group of thumbnails", SHASHIN_L10N_NAME); ?></dt>
    <dd><?php _e("[sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [sthumbs=5|202|115|84|33|189,160,2,n,right] Display photos with Shashin keys 5, 202, 115, 84, 33, and 189, size of 160 pixels for each image, display in 2 columns, no captions, float right (this is a 2x3 grid of pictures, floating to the right of the main content area)", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [sthumbs=5|202|115|84,max,2,y,center] Display photos with Shashin keys 5, 202, 115, and 84, use the maximum possible size, 2 columns, show captions, centered (the size of the images depends on the width of your theme's content area)", SHASHIN_L10N_NAME); ?></dd>

    <dt><?php _e("Display random images and videos", SHASHIN_L10N_NAME); ?></dt>
    <dd><?php _e("[srandom=source,max_size,max_cols,how_many,caption_yn,position,clear]", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [srandom=3|7|9,288,2,6,n,left] Pick random photos from albums with Shashin keys 3, 7, and 9, size of 288 pixels for each image, display in 2 columns, 6 pictures total, no captions, float left (this is a 2x3 grid of pictures, floating to the left of the main content)", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("Example: [srandom=any,160,max,10,y,center] Pick random photos from any album, size of 160 pixels for each image, create the maximum number of columns possible, 10 pictures total, show captions, centered (the number of columns depends on the width of your theme's content area)", SHASHIN_L10N_NAME); ?></dd>

    <dt><?php _e("Display the newest images and videos", SHASHIN_L10N_NAME); ?></dt>
    <dd><?php _e("[snewest=source,max_size,max_cols,how_many,caption_yn,position,clear]", SHASHIN_L10N_NAME); ?></dd>
    <dd><?php _e("See the srandom examples above - the snewest tag works the same way", SHASHIN_L10N_NAME); ?></dd>

    <dt><?php _e("See the", SHASHIN_L10N_NAME); ?>
        <a href="<?php echo SHASHIN_FAQ_URL; ?>" target="_blank"><?php _e("Shashin page", SHASHIN_L10N_NAME); ?></a>
        <?php _e("for detailed instructions.", SHASHIN_L10N_NAME); ?></dt>
    </dl>

</div>
