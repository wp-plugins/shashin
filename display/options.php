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
    <h3><a href="#" id="shashin_main_toggle" class="shashin_toggle"><img src="<?php echo SHASHIN_DISPLAY_URL; ?>/images/minus.gif" />Main Settings</a></h3>
    <div id="shashin_main">
        <table class="form-table">
        <tr style="vertical-align: top;">
        <th><?php _e("Sync all albums daily", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php ToppaWPFunctions::displayInput(
            'shashin_options[scheduled_update]',
            array('input_type' => 'radio',
                'input_subgroup' => array('y' => SHASHIN_YES, 'n' => SHASHIN_NO)),
            $this->options['scheduled_update']); ?></td>
        <td><span class="description"><?php _e("Have Shashin sync all your albums automatically on a daily basis. NOTE: This may fail if you have several hundered albums, and/or several hundred photos per album.", 'shashin'); ?></span></td>
        </tr>

        <tr style="vertical-align: top;">
        <th><?php _e("Add to photo caption", 'shashin'); ?>:</th>
        <td style="white-space: nowrap;"><?php _e("Album Title", 'shashin'); ?>: <input type="radio" name="shashin_options[prefix_captions]" value="y"<?php if ($this->options['prefix_captions'] == 'y') echo ' checked="checked"'; ?> />
            <?php _e(SHASHIN_YES); ?>
            <input type="radio" name="shashin_options[prefix_captions]" value="n"<?php
            if ($this->options['prefix_captions'] == 'n') echo ' checked="checked"'; ?> />
            <?php _e(SHASHIN_NO); ?><br />
            <?php _e("EXIF Data", 'shashin'); ?>: <input type="radio" name="shashin_options[caption_exif]" value="date"<?php if ($this->options['caption_exif'] == 'date') echo ' checked="checked"'; ?> />
            <?php _e("Date only", 'shashin'); ?>
            <input type="radio" name="shashin_options[caption_exif]" value="all"<?php if ($this->options['caption_exif'] == 'all') echo ' checked="checked"'; ?> />
            <?php _e("All", 'shashin'); ?>
            <input type="radio" name="shashin_options[caption_exif]" value="none"<?php if ($this->options['caption_exif'] == 'none' || !$this->options['caption_exif']) echo ' checked="checked"'; ?> />
            <?php _e("None", 'shashin'); ?></td>
        <td><span class="description"><?php _e('Album titles are prefixed to all captions. Dates are appended to expanded view captions only. EXIF data is appended to Highslide captions only, and includes the camera make, model, fstop, focal length, exposure time, and ISO.', 'shashin'); ?></span></td>
        </tr>
        </table>
    </div>
    </form>
</div>
