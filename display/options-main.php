<?php
/**
 * Set options for Shashin. This includes specifying your Picasa server, and
 * a couple of CSS settings.
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
 * @version 1.0
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
