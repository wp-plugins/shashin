<?php
/**
 * Manage photo albums.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage AdminPanels
 * @uses ToppaWPFunctions::displayInput()
 */
 ?>

<div class="wrap">
    <div style="float: right; font-weight: bold; margin-top: 15px;">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="5378623">
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><?php _e("Support Shashin", 'shashin'); ?> &raquo;
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e("Support Shashin", 'shashin'); ?>" title="<?php _e("Support Shashin", 'shashin'); ?>" style="vertical-align: middle; padding-right: 20px;" />
        <a href="<?php echo $this->faq_url; ?>" target="_blank"><?php _e("Shashin Help", 'shashin'); ?></a>
        </form>
    </div>

    <h2><?php echo __("Manage Shashin Albums", 'shashin'); ?></h2>

    <?php if ($message) {
        require (SHASHIN_DIR . '/display/include-message.php');
    }

    echo "<h3>" . __("Your Albums", 'shashin') . "</h3>\n";

    if ($all_albums) {
        echo '<p>';
        _e("Click an album title to view its photos. Click a column header to order the album list by that column (and click again to reverse the order).", 'shashin');
        echo "</p>\n";
        echo '<form method="post">' . "\n";
        wp_nonce_field('shashin_nonce', 'shashin_nonce');
        echo '<input type="hidden" name="shashin_action" value="update_albums" />' . "\n";
        echo '<table class="widefat">' . "\n";
        echo "<tr>\n";
        echo '<th class="manage-column"><a href="&amp;shashin_orderby='
            . (($order_by == 'title') ? 'title%20desc' : 'title')
            . '">' . __("Title", 'shashin')
            . (($_GET['shashin_orderby'] == 'title desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'title' || !$_GET['shashin_orderby']) ? ' &darr;' : '')
            . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'album_key') ? 'album_key%20desc' : 'album_key')
            . '">' . __("Album Key", 'shashin')
            . (($_GET['shashin_orderby'] == 'album_key desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'album_key') ? ' &darr;' : '')
            . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Sync", 'shashin') . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Delete", 'shashin') . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Include in Random?", 'shashin') . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'photo_count') ? 'photo_count%20desc' : 'photo_count')
            . '">' . __("Photo Count", 'shashin')
            . (($_GET['shashin_orderby'] == 'photo_count desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'photo_count') ? ' &darr;' : '')
            . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'pub_date') ? 'pub_date%20desc' : 'pub_date')
            . '">' . __("Pub Date", 'shashin')
            . (($_GET['shashin_orderby'] == 'pub_date desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'pub_date') ? ' &darr;' : '')
            . "</a></th>\n";
        echo '<th class="manage-column" style="text-align: center;"><a href="' . SHASHIN_ADMIN_URL . '&amp;shashin_orderby='
            . (($order_by == 'last_updated') ? 'last_updated%20desc' : 'last_updated')
            . '">' . __("Last Sync", 'shashin')
            . (($_GET['shashin_orderby'] == 'last_updated desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'last_updated') ? ' &darr;' : '')
            . "</a></th>\n";
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
                . '/arrow_refresh.png" alt="Sync Album" width="16" height="16" border="0" />'
                . "</a></td>\n";
            echo '<td style="text-align: center;"><a href="'
                . SHASHIN_ADMIN_URL
                . '&amp;shashin_action=delete_album&amp;album_id='
                . $all_album['album_id']
                . '" onclick="return confirm(\''
                . __("Are you sure you want to delete this album?", 'shashin')
                . '\')"><img src="' . SHASHIN_DISPLAY_URL
                . '/delete.png" alt="Delete Album" width="16" height="16" border="0" />'
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
        <td style="text-align: center;"><input class="button-secondary" type="submit" name="submit_form" value="<?php _e("Update Random Display", 'shashin'); ?>" /></td>
        <td colspan="3">&nbsp;</td>
        </tr>
        </table>
        </form>
    <?php }

    else {
        echo "<p>" . __("You have not added any albums yet.", 'shashin') . "</p>\n";
    } ?>

    <hr />
    <h3><?php _e("Sync Multiple Albums", 'shashin'); ?></h3>

    <p><?php _e("You can synchronize all the albums for a Picasa user at the same time. This can take some time if you have many albums. Note this only synchronizes albums you have already added to Shashin.", 'shashin'); ?></p>

    <?php if ($sync_all) { ?>
        <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
        <input type="hidden" name="shashin_action" value="sync_all" />

            <p><?php _e("Sync all albums for Picasa username:", 'shashin'); ?>
                <?php ToppaWPFunctions::displayInput('users', $sync_all) ?>
                <input class="button-primary" type="submit" name="submit_form" value="<?php _e("Sync All", 'shashin'); ?>" />
                </p>

        </form>
    <?php }

    else {
        echo "<p>" . __("You have not added any albums yet.", 'shashin') . "</p>\n";
    } ?>

    <hr />

    <h3><?php _e("Add Albums", 'shashin'); ?></h3>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashin_action" value="add_album" />

    <p><?php _e("Please enter the URL for your &quot;My Photos&quot; page on Picasa if you want to add all your public albums to Shashin, or enter the URL of an individual album. Use the regular URL, <strong>not the RSS URL</strong>.", 'shashin'); ?></p>

    <p><?php _e("The URL should have one of these formats:", 'shashin'); ?></p>

    <p><?php echo $shashin_options['picasa_server'] ?>/<em><?php _e("username", 'shashin'); ?></em>
    <strong><?php _e("OR", 'shashin'); ?></strong>
    <?php echo $shashin_options['picasa_server'] ?>/<em><?php _e("username", 'shashin'); ?></em>/<em><?php _e("albumname", 'shashin'); ?></em></p>

    <p><?php _e("Picasa URL:", 'shashin'); ?>
    <?php ToppaWPFunctions::displayInput('link_url', $album->ref_data['link_url'], $_REQUEST['link_url']) ?><br />
    <?php _e("Include photos in random photo display?", 'shashin'); ?>
    <?php ToppaWPFunctions::displayInput('include_in_random', $album->ref_data['include_in_random'], ($_REQUEST['include_in_random'] ? $_REQUEST['include_in_random'] : "Y")) ?></p>

    <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", 'shashin'); ?>" /></p>
    </form>

    <hr />

    <h3><a name="ref"></a><?php _e("Album Tags Quick Reference", 'shashin'); ?></h3>

    <dl class="shashin_help">
    <dt><?php _e("Display thumbnails for your albums in a table layout", 'shashin'); ?></dt>
    <dd><?php _e("[salbumthumbs=to_show,max_cols,location_yn,pubdate_yn,position,clear]", 'shashin'); ?></dd>
    <dd><?php _e("Example: [salbumthumbs=pub_date desc,3,y,n,center] All albums in reverse order by publication date, 3 columns of thumbnails, show albums locations, don't show publication dates, center on the page", 'shashin'); ?></dd>
    <dd><?php _e("Example: [salbumthumbs=2|24|33,1,n,y,left] Albums with Shashin keys 2, 24, and 33 only, 1 column of thumbnails, don't show album locations, show publication dates, float left", 'shashin'); ?></dd>
    <dd><?php _e("Notes: For ordering, options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering).", 'shashin'); ?></dd>

    <dt><?php _e("Display thumbnails and descriptions for your albums in a list layout", 'shashin'); ?></dt>
    <dd><?php _e("[salbumlist=to_show,info_yn]", 'shashin'); ?></dd>
    <dd><?php _e("Example: [salbumlist=pub_date desc,y]  All albums in reverse order by publication date, with additional album information shown between the title and description (photo count, publication date, and location)", 'shashin'); ?></dd>
    <dd><?php _e("Example: [salbumlist=2|24|33,n] Albums with Shashin keys 2, 24, and 33 only, no additional information shown.", 'shashin'); ?></dd>
    <dd><?php _e("Notes: For ordering, options are 'pub_date', 'title', or 'last_updated' (add ' desc' for reverse ordering).", 'shashin'); ?></dd>
    <dt><?php _e("See the", 'shashin'); ?>
        <a href="<?php echo SHASHIN_FAQ_URL; ?>" target="_blank"><?php _e("Shashin page", 'shashin'); ?></a>
        <?php _e("for detailed instructions.", 'shashin'); ?></dt>
    </dl>
</div>

