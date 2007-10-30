<?php
/**
 * Set options for Shashin. This includes specifying your Picasa server, and
 * a couple of CSS settings.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 1.1
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
    
    <h3>Options</h3>

    <form action="<?php echo SHASHIN_ADMIN_URL ?>" method="post">
    <input type="hidden" name="shashinAction" value="updateOptions">
    <table border="0" cellspacing="3" cellpadding="3">
    <tr>
    <td>Your Picasa server:</td>
    <td><input type="text" name="picasaServer" value="<?php echo $picasaServer ?>" size="30"></td>
    <td>The base URL of your Picasa server. Be sure to include "http://"</td>
    </tr>
    
    <tr>
    <td>Image div padding:</td>
    <td><input type="text" name="divPadding" value="<?php echo $divPadding ?>" size="30"></td>
    <td>Double the ".shashin_image img" padding value in shashin.css</td>
    </tr>

    <tr>
    <td>Thumbnail div padding:</td>
    <td><input type="text" name="thumbPadding" value="<?php echo $thumbPadding ?>" size="30"></td>
    <td>Double the ".shashin_thumb img" padding value in shashin.css</td>
    </tr>       
    </table>

    <p><input type="submit" name="save" value="Save Options" /></p>
    </form>
</div>
