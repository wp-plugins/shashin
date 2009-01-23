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
 * @version 2.3.1
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
    <?php
    echo '<h2>' . SHASHIN_DISPLAY_NAME . "</h2>\n";

    if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    }

    echo '<p><a href="' . SHASHIN_ADMIN_URL . '">&laquo; '
        . __("Shashin Admin", SHASHIN_L10N_NAME) . "</a></p>\n";
    echo '<h3>' . $album->data['title'] . "</h3>\n";
    echo '<p>' . __("Click a column header to order the photo list by that column (and click again to reverse the order).", SHASHIN_L10N_NAME) . "</p>\n";
    echo '<form action="' . SHASHIN_ADMIN_URL . '" method="post">' . "\n";
    echo '<input type="hidden" name="shashin_action" value="update_album_photos">' . "\n";
    echo '<table style="border-collapse: seperate; border-spacing: 5px;">' . "\n";
    echo "<tr>\n";
    echo '<th>' . __("Photo", SHASHIN_L10N_NAME) . "</th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'photo_key') ? 'photo_key%20desc' : 'photo_key')
        . '">' . __("Photo Key", SHASHIN_L10N_NAME) . "</th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'title') ? 'title%20desc' : 'title')
        . '">' . __("Filename", SHASHIN_L10N_NAME) . "</th>\n";
    echo '<th><a href="' . $edit_link
        . (($order_by == 'taken_timestamp') ? 'taken_timestamp%20desc' : 'taken_timestamp')
        . '">' . __("Date Taken", SHASHIN_L10N_NAME) . "</th>\n";
    echo "<th>Markup</th>\n";
    echo "<th>Include in Random?</th>\n";
    echo "</tr>\n";

    $i = 1;
    $markup_args = array('max_size' => 160, 'caption_yn' => 'y');

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
        echo "<td>[simage=" . $photo->data['photo_key'] . ",640,y,left]</td>\n";
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

    <h3>Photo Tips</h3>

    <?php
    echo "<ul>\n";
    echo '<li>' . __("<strong>Display an image:</strong> copy and paste the code listed under <em>Markup</em>, and then edit it as needed. [simage=photo_key,max_size,caption_yn,float,clear]", SHASHIN_L10N_NAME) . "</li>\n";
    echo '<li>' . __("<strong>Valid Picasa image sizes:</strong>", SHASHIN_L10N_NAME)
        . implode(", ", $shashin_image_sizes) . ". "
        . __("For a cropped square image, use sizes", SHASHIN_L10N_NAME)
        . " " . implode(", ", $shashin_crop_sizes) . ".</li>\n";
    echo '<li>' . __("<strong>Display random images:</strong> [srandom=album_key1|album_key2|etc,max_size,max_cols,how_many,caption_yn,float,clear] Enter 'any' for the album_key for any album", SHASHIN_L10N_NAME) . "</li>\n";
    echo '<li>' . __("<strong>Display a group of thumbnails:</strong>[sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]", SHASHIN_L10N_NAME) . "</li>\n";
    echo '<li>' . __("<strong>Display newest images:</strong> [snewest=album_key1|album_key2|etc,max_size,max_cols,how_many,caption_yn,float,clear] Enter 'any' for the album_key for any album", SHASHIN_L10N_NAME) . "</li>\n";
    echo '<li>' . __("<strong>More Help:</strong> see the", SHASHIN_L10N_NAME)
        . ' <a href="' . SHASHIN_FAQ_URL . '">'
        . __("Shashin page", SHASHIN_L10N_NAME) . '</a> '
        . __("for detailed instructions", SHASHIN_L10N_NAME) . "</li>\n";
    echo "</ul>\n";
    echo '<p><a href="' . SHASHIN_ADMIN_URL . '">&laquo; '
        . __("Shashin Admin", SHASHIN_L10N_NAME) . "</a></p>\n";
    ?>
</div>
