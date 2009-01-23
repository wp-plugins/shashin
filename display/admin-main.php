<?php
/**
 * Displays a list of Picasa albums already loaded in Shashin, and a form to add a new album.
 *
 * It also allows for setting a flag for
 * each album, indicating whether its photos should be included in any displays
 * of random images. To display available albums, $all_albums must be set prior
 * to displaying this panel.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.3
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>

<div class="wrap">
    <h2><?php echo SHASHIN_DISPLAY_NAME ?></h2>

    <?php if ($message) {
        require (SHASHIN_DIR . '/display/include-message.php');
    }

    echo "<h3>" . __("Your Albums", SHASHIN_L10N_NAME) . "</h3>\n";

    if ($all_albums) {
        echo '<p>'
            . __("Click an album title to view its photos. Click a column header to order the album list by that column (and click again to reverse the order).", SHASHIN_L10N_NAME)
            . "</p>\n";

        echo '<form action="' . SHASHIN_ADMIN_URL . '" method="post">' . "\n";
        echo '<input type="hidden" name="shashin_action" value="update_albums" />' . "\n";
        echo '<table class="widefat">' . "\n";
        echo "<tr>\n";
        echo '<th class="manage-column"><a href="'
            . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'title') ? 'title%20desc' : 'title')
            . '">' . __("Title", SHASHIN_L10N_NAME) . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'album_key') ? 'album_key%20desc' : 'album_key')
            . '">' . __("Album Key", SHASHIN_L10N_NAME) . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Sync", SHASHIN_L10N_NAME) . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Delete", SHASHIN_L10N_NAME) . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Include in Random?", SHASHIN_L10N_NAME) . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'photo_count') ? 'photo_count%20desc' : 'photo_count')
            . '">' . __("Photo Count", SHASHIN_L10N_NAME) . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'pub_date') ? 'pub_date%20desc' : 'pub_date')
            . '">' . __("Pub Date", SHASHIN_L10N_NAME) . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'last_updated') ? 'last_updated%20desc' : 'last_updated')
            . '">' . __("Last Sync", SHASHIN_L10N_NAME). "</a></th>\n";
        echo "</tr>\n";

        $i = 1;
        foreach ($all_albums as $all_album) {
            echo(($i % 2 == 0) ? "<tr>\n" : "<tr class='alternate'>\n");
            $i++;
            echo '<td><a href="' . SHASHIN_ADMIN_URL
                . '&amp;shashin_action=edit_album_photos&amp;album_id='
                . $all_album['album_id'] . '">' . $all_album['title']
                . "</a></td>\n";
            echo '<td style="text-align: center;">'
                . $all_album['album_key'] . "</td>\n";
            echo '<td style="text-align: center;"><a href="'
                . SHASHIN_ADMIN_URL
                . '&amp;shashin_action=sync_album&amp;album_id='
                . $all_album['album_id'] . '&amp;user='
                . $all_album['user'] . '"><img src="'
                . SHASHIN_DISPLAY_URL
                . 'arrow_refresh.png" alt="Sync Album" width="16" height="16" border="0" />'
                . "</a></td>\n";
            echo '<td style="text-align: center;"><a href="'
                . SHASHIN_ADMIN_URL
                . '&amp;shashin_action=delete_album&amp;album_id='
                . $all_album['album_id']
                . '" onclick="return confirm(\'Are you sure you want to delete?\')">'
                . '<img src="' . SHASHIN_DISPLAY_URL
                . 'delete.png" alt="Delete Album" width="16" height="16" border="0" />'
                . "</a></td>\n";
            echo '<td style="text-align: center;">';
            ToppaWPFunctions::displayInput(
                "include_in_random[{$all_album['album_id']}]",
                $album->ref_data['include_in_random'],
                $all_album['include_in_random']);
            echo "</td>\n";
            echo '<td style="text-align: center;">'
                . $all_album['photo_count'] . "</td>\n";
            echo '<td style="text-align: center;">' . date("d-M-y", $all_album['pub_date']) . "</td>\n";
            echo '<td style="text-align: center;">' . date("d-M-y H:i", $all_album['last_updated']) . "</td>\n";
            echo "</tr>\n";
        } ?>

        <tr>
        <td colspan="4">&nbsp;</td>
        <td style="text-align: center;"><input class="button-secondary" type="submit" name="submit_form" value="<?php _e("Update Random Display", SHASHIN_L10N_NAME); ?>" /></td>
        <td colspan="3">&nbsp;</td>
        </tr>
        </table>
        </form>
    <?php }

    else {
        echo "<p>" . __("You have not added any albums yet.", SHASHIN_L10N_NAME) . "</p>\n";
    } ?>

    <hr />
    <h3><?php _e("Sync Multiple Albums", SHASHIN_L10N_NAME); ?></h3>

    <p><?php _e("You can sync all the albums for a Picasa user at the same time. This can take some time if you have many albums. Note this only sync albums you have already added to Shashin.", SHASHIN_L10N_NAME); ?></p>

    <?php if ($sync_all) { ?>
        <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
        <input type="hidden" name="shashin_action" value="sync_all" />

            <p><?php _e("Sync all albums for Picasa username:", SHASHIN_L10N_NAME); ?>
                <?php ToppaWPFunctions::displayInput('users', $sync_all) ?>
                <input class="button-primary" type="submit" name="submit_form" value="<?php _e("Sync All", SHASHIN_L10N_NAME); ?>" />
                </p>

        </form>
    <?php }

    else {
        echo "<p>" . __("You have not added any albums yet.", SHASHIN_L10N_NAME) . "</p>\n";
    } ?>

    <hr />

    <h3><?php _e("Add Albums", SHASHIN_L10N_NAME); ?></h3>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashin_action" value="add_album" />

    <p><?php _e("Please enter the URL for your &quot;My Photos&quot; page on Picasa if you want to add all your public albums to Shashin, or enter the URL of an individual album. Use the regular URL, <strong>not the RSS URL</strong>.", SHASHIN_L10N_NAME); ?></p>

    <p><?php _e("The URL should have one of these formats:", SHASHIN_L10N_NAME); ?><p>

    <p><?php echo $shashin_options['picasa_server'] ?>/<em>username</em><br />
    <?php _e("<strong>- OR -</strong>", SHASHIN_L10N_NAME); ?><br />
    <?php echo $shashin_options['picasa_server'] ?>/<em>username</em>/<em>albumname</em></p>

    <p><?php _e("Picasa URL:", SHASHIN_L10N_NAME); ?>
    <?php ToppaWPFunctions::displayInput('link_url', $album->ref_data['link_url'], $_REQUEST['link_url']) ?><br />
    <?php _e("Include photos in random photo display?", SHASHIN_L10N_NAME); ?>
    <?php ToppaWPFunctions::displayInput('include_in_random', $album->ref_data['include_in_random'], ($_REQUEST['include_in_random'] ? $_REQUEST['include_in_random'] : "Y")) ?></p>

    <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", SHASHIN_L10N_NAME); ?>" /></p>
    </form>

    <hr />

    <h3><?php _e("Album Tips", SHASHIN_L10N_NAME); ?></h3>

    <ul>
    <li><?php _e("<strong>Syncing:</strong> after you upload new photos to an album in Picasa, click the", SHASHIN_L10N_NAME); ?>
    <img src="<?php echo SHASHIN_DISPLAY_URL ?>arrow_refresh.png" alt="Sync Album" width="16" height="16" border="0" />
    <?php _e("icon for it above.", SHASHIN_L10N_NAME); ?></li>
    <li><?php _e("<strong>Display thumbnails for all your albums:</strong> you can choose the sort order - options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering). [salbumthumbs=order_option,max_cols,location_yn,pubdate_yn,float,clear]", SHASHIN_L10N_NAME); ?></li>
    <li><?php _e("<strong>Display thumbnails for selected albums:</strong> [salbumthumbs=album_key1|album_key2|etc,max_cols,location_yn,pubdate_yn,float,clear]", SHASHIN_L10N_NAME); ?></li>
    <li><?php _e("<strong>Display thumbnails and descriptions for all your albums:</strong> you can choose the sort order - options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering). [salbumlist=order_option,info_yn]", SHASHIN_L10N_NAME); ?></li>
    <li><?php _e("<strong>Display thumbnails and descriptions for selected albums:</strong> [salbumlist=album_key1|album_key2|etc,info_yn]", SHASHIN_L10N_NAME); ?></li>
    <li><?php _e("<strong>Random images:</strong> if you want to exclude an album's images when using the srandom tag, set 'Include in Random?' to 'No.'", SHASHIN_L10N_NAME); ?></li>
    <li><?php echo __("<strong>More Help:</strong> see the", SHASHIN_L10N_NAME)
        . ' <a href="' . SHASHIN_FAQ_URL . '">'
        . __("Shashin page", SHASHIN_L10N_NAME) . '</a> '
        . __("for detailed instructions", SHASHIN_L10N_NAME); ?></li>
    </ul>

    <hr />

    <h3><?php _e("Tipping: it isn't just for cows", SHASHIN_L10N_NAME); ?></h3>

    <p><?php _e("Shashin has taken hundreds of hours for me to develop and maintain. I do it for the love of course, but a tip would be nice :-) Thanks!", SHASHIN_L10N_NAME); ?></p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick" />
    <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></p>
    <p><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></p>
    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
    </form>


</div>

