<?php
/**
 * Set options for Shashin.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 3.0
 * @package Shashin
 * @subpackage AdminPanels
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

    <h2><?php echo __("Shashin Settings", 'shashin'); ?></h2>

    <?php if (strlen($message)) {
        require (SHASHIN_DIR . '/display/include-message.php');
    } ?>


    <form  method="post">
    <input type="hidden" name="shashin_action" value="update_options">
    <div style="visibility:visible;" id="main_settings">
        <h3><a href="#" onclick="shashin_toggle_visibility('main_settings');">Main Settings</a></h3>
        <table class="form-table">
        <tr style="vertical-align: top;">
        <td><?php _e("Sync all albums daily:", 'shashin'); ?></td>
        <td nowrap="nowrap"><?php ToppaWPFunctions::displayInput(
            'shashin_options[scheduled_update]',
            array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->options['scheduled_update']); ?></td>
        <td><?php _e("Have Shashin sync all your albums automatically on a daily basis. NOTE: This may fail if you have several hundered albums, and/or several hundred photos per album.", 'shashin'); ?></td>
        </tr>

        <tr style="vertical-align: top;">
        <td><?php _e("Add to photo caption:", SHASHIN_L10N_NAME); ?></td>
        <td><?php _e("Album Title:", SHASHIN_L10N_NAME); ?><input type="radio" name="shashin_options[prefix_captions]" value="y"<?php if ($shashin_options['prefix_captions'] == 'y') echo ' checked="checked"'; ?> />
            <?php _e("Yes", SHASHIN_L10N_NAME); ?>
            <input type="radio" name="shashin_options[prefix_captions]" value="n"<?php
            if ($shashin_options['prefix_captions'] == 'n') echo ' checked="checked"'; ?> />
            <?php _e("No", SHASHIN_L10N_NAME); ?><br />
            EXIF Data: <input type="radio" name="shashin_options[caption_exif]" value="date"<?php if ($shashin_options['caption_exif'] == 'date') echo ' checked="checked"'; ?> />
            <?php _e("Date only", SHASHIN_L10N_NAME); ?>
            <input type="radio" name="shashin_options[caption_exif]" value="all"<?php if ($shashin_options['caption_exif'] == 'all') echo ' checked="checked"'; ?> />
            <?php _e("All", SHASHIN_L10N_NAME); ?>
            <input type="radio" name="shashin_options[caption_exif]" value="none"<?php if ($shashin_options['caption_exif'] == 'none' || !$shashin_options['caption_exif']) echo ' checked="checked"'; ?> />
            <?php _e("None", SHASHIN_L10N_NAME); ?></td>
        <td><?php _e('Album titles are prefixed to all captions. Dates are appended to expanded view captions only. EXIF data is appended to Highslide captions only, and includes the camera make, model, fstop, focal length, exposure time, and ISO.', SHASHIN_L10N_NAME); ?></td>
        </tr>
        </table>
    </div>
    </form>
</div>
