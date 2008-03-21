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
 * @version 2.0.1
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ShashinPhoto::ShashinPhoto()
 * @uses ShashinPhoto::getPhoto()
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>
 
 <div class="wrap">
    <h2><?php echo SHASHIN_DISPLAY_NAME ?></h2>

    <h3><?php echo $album->data['title'] ?></strong></h3>
    
    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>

    <p><a href="<?php echo SHASHIN_ADMIN_URL ?>">&laquo; Shashin Admin</a></p>
    
    
    <h3>Photo Tips</h3>

    <ul>
    <li><strong>Display an image:</strong> copy and paste the code
    listed under <em>Markup</em>, and then edit it as needed.
    [simage=photo_key,max_size,caption_yn,float,clear]</li>
    <li><strong>Valid Picasa image sizes:</strong> <?php echo implode(", ", eval(SHASHIN_IMAGE_SIZES)); ?>. For a cropped square image,
    use sizes <?php echo implode(", ", eval(SHASHIN_CROP_SIZES)); ?>.</li>
    <li><strong>Display random images:</strong>
    [srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear] Enter
    "any" for the album_key for any album</li>
    <li><strong>Display a group of thumbnails:</strong>
    [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]</li>
    <li><strong>Display newest images:</strong>
    [snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear] Enter
    "any" for the album_key for any album</li>
    <li><strong>More Help:</strong> see the
    <a href="<?php echo SHASHIN_FAQ_URL ?>">Shashin page</a> for detailed
    instructions</li>
    </ul>
    
    <h3>Photos</h3>
    
    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashinAction" value="updateAlbumPhotos">
    <table border="0" cellspacing="0" cellpadding="3">   
    <tr>
    <th>Photo</th>
    <th>Photo Key</th>
    <th>Description</th>
    <th>Markup</th>
    <th>Include in Random?</th>
    </tr>
    
    <?php
    $i = 0;
    
    foreach ($album->data['photos'] as $photoArray) {

        $photo = new ShashinPhoto();
        $photo->getPhoto(null, $photoArray);
        if ($i % 2 == 0) {
            echo "<tr align=\"center\">\n";
        } 

        else {
            echo "<tr align=\"center\" class=\"alternate\">\n";
        }
        
        $i++;

        echo "<td>" . $photo->getPhotoMarkup(array(null,null,160,'n')) . "</td>\n";
        echo "<td>" . $photo->data['photo_key'] . "</td>\n";
        echo "<td>" . $photo->data['description'] . "</td>\n";
        echo "<td>[simage=" . $photo->data['photo_key'] . ",640,y,left]</td>\n";
        echo "<td>";
        ToppaWPFunctions::displayInput('include_in_random', $photo->refData['include_in_random'], ($photo->data['include_in_random'] ? $photo->data['include_in_random'] : "Y"), $photo->data['photo_id']);
        echo "</td>\n";
        echo "</tr>\n\n";
    }
    ?>
    <tr>
    <td colspan="4">&nbsp;</td>
    <td><input type="submit" name="submit" value="Submit" /></td>
    </tr>
    </table>
    </form>

    <p><a href="<?php echo SHASHIN_ADMIN_URL ?>">&laquo; Shashin Admin</a></p>
</div>
