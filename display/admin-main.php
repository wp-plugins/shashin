<?php
/**
 * This Shashin admin panel displays a list of Picasa albums already loaded in
 * Shashin, and a form to add a new album. It also allows for setting a flag for
 * each album, indicating whether its photos should be included in any displays
 * of random images. To display available albums, $allAlbums must be set prior
 * to displaying this panel.
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
 * @version 1.0.7
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>
 
<div class="wrap">
    <h2><?php echo SHASHIN_DISPLAY_NAME ?></h2>
    
    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>


    <?php if (isset($allAlbums)) { ?>
    
    <h3>Album Tips</h3>
    
    <ul>
    <li><strong>Display an album thumbnail:</strong> copy and paste the code
    listed under <em>Markup</em>, and then edit it as needed.
    [salbum=album_key,location_yn,pubdate_yn,float,clear] Note album
    thumbnail sizes are set by Picasa at 160x160.</li>
    <li><strong>Syncing:</strong> sync an album after you upload new photos
    to it in Picasa. Wait at least a few minutes between syncs for an album -
    it seems the RSS feed doesn't always update immediately.</li>
    <li><strong>Random images:</strong> if you want to exclude an album's
    images when using the srandom tag, set "Include in Random?" to "No."</li>
    <li><strong>More Help:</strong> see the
    <a href="<?php echo SHASHIN_FAQ_URL ?>">Shashin FAQ page</a> for detailed
    instructions</li>
    </ul>

    <?php } ?>
    
    <h3>Your Albums</h3>

    <?php if (isset($allAlbums)) { ?>
        <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
        <input type="hidden" name="shashinAction" value="updateAlbums">
        <table border="0" cellspacing="3" cellpadding="3">
        <tr>
        <th align="left">Title</th>
        <th>Album Key</th>
        <th>Photo Count</th>
        <th>Last Updated</th>
        <th>Markup</th>
        <th>Include in Random?</th>
        <th>Sync</th>
        <th>Delete</th>
        </tr>
        
        <?php foreach ($allAlbums as $allAlbum) { ?>
            <tr>
            <td><a href="<?php
                echo SHASHIN_ADMIN_URL ?>&amp;shashinAction=editAlbumPhotos&amp;albumID=<?php
                echo $allAlbum['album_id'] ?>"><?php echo $allAlbum['title'] ?></a></td>
            <td align="center"><?php echo $allAlbum['album_key'] ?></td>    
            <td align="center"><?php echo $allAlbum['photo_count'] ?></td>
            <td align="center"><?php echo date("d-M-y H:i", $allAlbum['last_updated_timestamp']) ?></td>
            <td align="center">[salbum=<?php echo $allAlbum['album_key'] ?>,y,y,left]</td>
            <td align="center"><?php echo ToppaWPFunctions::displayInput('include_in_random', $album->refData['include_in_random'], $allAlbum['include_in_random'], $allAlbum['album_id']) ?></td>
            <td align="center"><a href="<?php
                echo SHASHIN_ADMIN_URL ?>&amp;shashinAction=syncAlbum&amp;albumID=<?php
                echo $allAlbum['album_id'] ?>&amp;user=<?php
                echo $allAlbum['user'] ?>"><img src="<?php
                echo SHASHIN_DISPLAY_URL ?>arrow_refresh.png" alt="Sync Album" width="16" height="16" border="0" /></a></td>    
            <td align="center"><a href="<?php
                echo SHASHIN_ADMIN_URL ?>&amp;shashinAction=deleteAlbum&amp;albumID=<?php
                echo $allAlbum['album_id'] ?>" onclick="return confirm('Are you sure you want to delete?')"><img src="<?php
                echo SHASHIN_DISPLAY_URL ?>delete.png" alt="Delete Album" width="16" height="16" border="0" /></a></td>    
            </tr>
        <?php } ?>
        
        <tr>
        <td colspan="6"><input type="submit" name="submit" value="Submit" /></td>
        </tr>
        </table>
        </form>
    <?php }
    
    else { ?>
        <p>You have not added any albums yet.</p>
    <?php } ?>

    <hr />
    
    <h3>Add an Album</h3>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashinAction" value="addAlbum" />
    
    <p>Please enter the URL for viewing an album on Picasa, <strong>not the RSS URL</strong>.</p>
    
    <p>The URL should have this format:
    <?php echo SHASHIN_PICASA_SERVER ?>/<strong>username</strong>/<strong>albumname</strong></p>

    <p>Picasa Album URL: <?php ToppaWPFunctions::displayInput('link_url', $album->refData['link_url'], $_REQUEST['link_url']) ?><br />
    Include photos from this album in random photo display?
    <?php ToppaWPFunctions::displayInput('include_in_random', $album->refData['include_in_random'], ($_REQUEST['include_in_random'] ? $_REQUEST['include_in_random'] : "Y")) ?></p>

    <p><input type="submit" name="submit" value="Add Album" /></p>
    </form>

    <h3>Tip Jar</h3>
    
    <p>Many Bothans died to bring you this plugin. If you tip your cab driver
    and your pizza delivery guy, please consider tipping your plugin programmer.
    Thanks!</p>
    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick" />
    <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></p>
    <p><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></p>
    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
    </form>

    
</div>
