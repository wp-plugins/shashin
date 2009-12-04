<?php
/**
 * Manage photo albums.
 *
 * This file is part of Shashin. Please see the Shashin.phl file for
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

    <?php require (SHASHIN_DIR . '/display/donate.inc'); ?>

    <h2><?php echo __("Manage Shashin Albums", 'shashin'); ?></h2>

    <?php if ($message) {
        require (SHASHIN_DIR . '/display/message.inc');
    }

    echo "<h3>" . __("Your Albums", 'shashin') . "</h3>" . PHP_EOL;

    if ($all_albums) {
        echo '<p>';
        _e("Click an album title to view its photos. Click a column header to order the album list by that column (and click again to reverse the order).", 'shashin');
        echo "</p>" . PHP_EOL;
        echo '<form method="post">' . PHP_EOL;
        wp_nonce_field('shashin_nonce', 'shashin_nonce');
        echo '<input type="hidden" name="shashin_action" value="update_albums" />' . PHP_EOL;
        echo '<table class="widefat">' . PHP_EOL;
        echo "<tr>" . PHP_EOL;
        echo '<th class="manage-column"><a href="&amp;shashin_orderby='
            . (($order_by == 'title') ? 'title%20desc' : 'title')
            . '">' . __("Title", 'shashin')
            . (($_GET['shashin_orderby'] == 'title desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'title' || !$_GET['shashin_orderby']) ? ' &darr;' : '')
            . "</a></th>" . PHP_EOL;
        echo '<th class="manage-column" style="text-align: center;"><a href="' . $_SERVER['PHP_SELF'] . '&amp;shashin_orderby='
            . (($order_by == 'album_key') ? 'album_key%20desc' : 'album_key')
            . '">' . __("Album Key", 'shashin')
            . (($_GET['shashin_orderby'] == 'album_key desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'album_key') ? ' &darr;' : '')
            . "</a></th>" . PHP_EOL;
        echo '<th class="manage-column" style="text-align: center;">' . __("Sync", 'shashin') . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Delete", 'shashin') . "</th>\n";
        echo '<th class="manage-column" style="text-align: center;">' . __("Include in Random?", 'shashin') . "</th>" . PHP_EOL;
        echo '<th class="manage-column" style="text-align: center;"><a href="' . $_SERVER['PHP_SELF'] . '&amp;shashin_orderby='
            . (($order_by == 'photo_count') ? 'photo_count%20desc' : 'photo_count')
            . '">' . __("Photo Count", 'shashin')
            . (($_GET['shashin_orderby'] == 'photo_count desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'photo_count') ? ' &darr;' : '')
            . "</a></th>" . PHP_EOL;
        echo '<th class="manage-column" style="text-align: center;"><a href="' . $_SERVER['PHP_SELF'] . '&amp;shashin_orderby='
            . (($order_by == 'pub_date') ? 'pub_date%20desc' : 'pub_date')
            . '">' . __("Pub Date", 'shashin')
            . (($_GET['shashin_orderby'] == 'pub_date desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'pub_date') ? ' &darr;' : '')
            . "</a></th>" . PHP_EOL;
        echo '<th class="manage-column" style="text-align: center;"><a href="' . $_SERVER['PHP_SELF'] . '&amp;shashin_orderby='
            . (($order_by == 'last_updated') ? 'last_updated%20desc' : 'last_updated')
            . '">' . __("Last Sync", 'shashin')
            . (($_GET['shashin_orderby'] == 'last_updated desc') ? ' &uarr;' : '')
            . (($_GET['shashin_orderby'] == 'last_updated') ? ' &darr;' : '')
            . "</a></th>" . PHP_EOL;
        echo "</tr>" . PHP_EOL;

        $i = 1;
        foreach ($all_albums as $all_album) {
            echo(($i % 2 == 0) ? "<tr>\n" : "<tr class='alternate'>\n");
            $i++;
            echo '<td><a href="' . $_SERVER['PHP_SELF']
                . '&amp;shashin_action=edit_album_photos&amp;album_id='
                . $all_album['album_id'] . '">' . $all_album['title']
                . "</a></td>" . PHP_EOL;
            echo '<td style="text-align: center;">'
                . $all_album['album_key'] . "</td>" . PHP_EOL;
            echo '<td style="text-align: center;"><a href="'
                . $_SERVER['PHP_SELF']
                . '&amp;shashin_action=sync_album&amp;album_id='
                . $all_album['album_id'] . '&amp;user='
                . $all_album['user'] . '"><img src="'
                . $_SERVER['PHP_SELF']
                . '/arrow_refresh.png" alt="Sync Album" width="16" height="16" border="0" />'
                . "</a></td>" . PHP_EOL;
            echo '<td style="text-align: center;"><a href="'
                . $_SERVER['PHP_SELF']
                . '&amp;shashin_action=delete_album&amp;album_id='
                . $all_album['album_id']
                . '" onclick="return confirm(\''
                . __("Are you sure you want to delete this album?", 'shashin')
                . '\')"><img src="' . $_SERVER['PHP_SELF']
                . '/delete.png" alt="Delete Album" width="16" height="16" border="0" />'
                . "</a></td>" . PHP_EOL;
            echo '<td style="text-align: center;">';
            ToppaWPFunctions::displayInput(
                "include_in_random[{$all_album['album_id']}]",
                $album->ref_data['include_in_random'],
                $all_album['include_in_random']);
            echo "</td>" . PHP_EOL;
            echo '<td style="text-align: center;">'
                . $all_album['photo_count'] . "</td>" . PHP_EOL;
            echo '<td style="text-align: center;">' . date("d-M-y", $all_album['pub_date']) . "</td>" . PHP_EOL;
            echo '<td style="text-align: center;">' . date("d-M-y H:i", $all_album['last_updated']) . "</td>" . PHP_EOL;
            echo "</tr>" . PHP_EOL;
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
        echo "<p><em>" . __("You have not added any albums yet.", 'shashin') . "</em></p>" . PHP_EOL;
    } ?>

    <hr />
    <h3><?php _e("Sync Multiple Albums", 'shashin'); ?></h3>
    <p><?php _e("You can synchronize all your albums at once. This can take some time if you have many albums. Note this only synchronizes albums you have already added to Shashin.", 'shashin'); ?></p>

    <?php
        if (!empty($users)) {
            $user_names = array();
            foreach ($users as $user) {
                $user_names[$user] = $user;
            }

            echo '<form method="post">' . PHP_EOL;
            wp_nonce_field('shashin_nonce', 'shashin_nonce');
            echo '<input type="hidden" name="shashin_action" value="sync_all" />' . PHP_EOL;
            echo "<p>" . __("Sync all albums for Picasa username: ", 'shashin');
            echo ToppaWPFunctions::displayInput('users', array(
                'input_type' => 'select',
                'input_subgroup' => $user_names));
            echo '<input class="button-primary" type="submit" name="submit_form" value="';
            echo __("Sync All", 'shashin') . '" /></p>' . PHP_EOL;
            echo '</form>' . PHP_EOL;
        }

        else {
            echo "<p><em>" . __("You have not added any albums yet.", 'shashin') . "</em></p>" . PHP_EOL;
        } ?>

    <hr />

    <h3><?php _e("Add Albums", 'shashin'); ?></h3>

    <form method="post">
    <?php wp_nonce_field('shashin_nonce', 'shashin_nonce'); ?>
    <input type="hidden" name="shashin_action" value="add_album" />

    <p><?php _e("Shashin can display photos from public <em>Picasa albums</em>, <em>Flickr photostreams</em>, <em>Flickr sets</em>, and <em>Twitpic photostreams</em> by importing their RSS feeds. Note that <em>Flickr galleries</em> are not currently supported. Please enter an RSS URL below.", 'shashin'); ?></p>

    <h4>Supported Feed Types</h4>

    <dl class="shashin_examples">
    <dt><strong><?php _e("All the Picasa albums for a user", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link on the bottom right of the Picasa user's home page", 'shashin'); ?></dt>
        <dd style="font-size: smaller;">Example: http://picasaweb.google.com/data/feed/base/user/michaeltoppa?alt=rss&amp;kind=album&amp;hl=en_US</dd>
    <dt><strong><?php _e("A single Picasa album", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link in the sidebar of the album's main page", 'shashin'); ?></dt>
        <dd style="font-size: smaller;">Example: http://picasaweb.google.com/data/feed/base/user/michaeltoppa/albumid/5269449390714706417?alt=rss&amp;kind=photo&amp;hl=en_US</dd>
    <dt><strong><?php _e("A Flickr set", 'shashin'); ?>:</strong> <?php _e("Look for the 'Feed' link on the bottom left of the set's main page.", 'shashin'); ?></dt>
        <dd style="font-size: smaller;">Example: http://api.flickr.com/services/feeds/photoset.gne?set=72157622514276629&amp;nsid=65384822@N00&amp;lang=en-us</dd>
    <dt><strong><?php _e("A Flickr photostream", 'shashin'); ?>:</strong> <?php _e("Look for the 'Latest' link near the RSS icon on the bottom left of the photostream's main page.", 'shashin'); ?></dt>
        <dd style="font-size: smaller;">Example: http://api.flickr.com/services/feeds/photos_public.gne?id=65384822@N00&amp;lang=en-us&amp;format=rss_200</dd>
    <dt><strong><?php _e("A Twitpic photostream", 'shashin'); ?>:</strong> <?php _e("Look for the RSS icon on the top right of the photostream page", 'shashin'); ?></dt>
        <dd style="font-size: smaller;">Example: http://twitpic.com/photos/mtoppa/feed.rss</dd>
    </dl>

    <p><strong><?php _e("RSS URL:", 'shashin'); ?></strong>
    <?php echo ToppaWPFunctions::displayInput('rss_url', $album->ref_data['rss_url'])); ?><br />
    <?php _e("Include album's photos in random photo displays?", 'shashin'); ?>
    <?php echo ToppaWPFunctions::displayInput('include_in_random', $album->ref_data['include_in_random'], "Y")) ?></p>

    <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", 'shashin'); ?>" /></p>
    </form>
</div>

