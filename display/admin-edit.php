<?php
/**
 * This Shashin admin panel displays thumbnails and summary data for photos in
 * a specified album. It also allows for setting a flag for each photo,
 * indicating whether it should be included in any displays of random images. An
 * album object must be instantiated prior to displaying this panel. 
 *
 * Copyright 2007 Michael Toppa
 * 
 * This file is part of Shashin.
 *
 * Shashin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shashin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Michael Toppa
 * @version 1.0.2
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
    
    
    <h3>Tips</h3>

    <ul>
    <li><strong>Display an image:</strong> copy and paste the code
    listed under <em>Markup</em>, and then edit it as needed.
    [simage=photo_key,max_size,caption_yn,float,clear]</li>
    <li><strong>Valid Picasa image sizes:</strong> 32, 48, 64, 72, 144, 160,
    200, 288, 320, 400, 512, 576, 640, 720, 800. For a cropped square image,
    use sizes 32, 48, 64, or 160.</li>
    <li><strong>Display random images:</strong>
    [srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear] Enter
    "any" for the album_key for any album</li>
    <li><strong>Display table of thumbnails:</strong>
    [sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,float,clear]</li>
    <li><strong>Display newest images:</strong>
    [snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear] Enter
    "any" for the album_key for any album</li>
    <li><strong>More Help:</strong> see the
    <a href="<?php echo SHASHIN_DOWNLOAD_URL ?>">Shashin page</a> for detailed
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
    foreach ($album->data['photos'] as $photoArray) {
        $photo = new ShashinPhoto();
        $photo->getPhoto(null, $photoArray);
        echo "<tr align=\"center\">\n";
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
