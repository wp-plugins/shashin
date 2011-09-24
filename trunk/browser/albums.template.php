<script type="text/javascript">
    jQuery(document).ready(function() {
        
        function get_selected() {
            var selected = new Array;
            jQuery('#shashin-browser-albums-selected li').each(function() {
                selected.push(jQuery(this).attr('id').replace('album-',''));
            });
            return selected;
        }
        
        function get_values(elm) {
            var values = {};
            jQuery('input, select', jQuery(elm).parent().parent().parent()).each(function() {
                if(jQuery(this).attr('type') == "checkbox") {
                    values[jQuery(this).attr('name')] = jQuery(this).attr('checked')?1:0;;
                }else{
                    values[jQuery(this).attr('name')] = jQuery(this).val();
                }
            });
            return values;
        }
        
        jQuery('#shashin-browser-albums-insert-list input[name="insert"]').click(function(e) {
            var values = get_values(this);
            if(values['to-show'] == "selected") {
                var selected = get_selected();
                if(selected.length == 0) return alert('<?php _e('No albums selected', SHASHIN_L10N_NAME) ?>');
                parent.send_to_editor('[salbumlist='+selected.join('|')+','+values['info']+']');
            }else{
                parent.send_to_editor('[salbumlist='+values['to-show']+','+values['info']+']');
            }
        });
        
        jQuery('#shashin-browser-albums-insert-thumbs input[name="insert"]').click(function(e) {
            var values = get_values(this);
            if(values['to-show'] == "selected") {
                var selected = get_selected();
                if(selected.length == 0) return alert('<?php _e('No albums selected', SHASHIN_L10N_NAME) ?>');
                parent.send_to_editor('[salbumthumbs='+selected.join('|')+','+values['cols']+','+values['location']+','+values['pubdate']+','+values['position']+','+values['clear']+']');
            }else{
                parent.send_to_editor('[salbumthumbs='+values['to-show']+','+values['cols']+','+values['location']+','+values['pubdate']+','+values['position']+','+values['clear']+']');
            }
        });
        
        jQuery('#shashin-browser-albums-insert-photos input[name="insert"]').click(function(e) {
            var values = get_values(this);
            var selected = get_selected();
            if(selected.length == 0) return alert('<?php _e('No albums selected', SHASHIN_L10N_NAME) ?>');
            
            if(values['desc'] == 1) {
                var desc = ' desc';
            }else{
                var desc = '';
            }
            
            var codes = '';
            for(var i=0; i<selected.length;i++) {
                codes += '[salbumphotos='+selected[i]+','+values['size']+','+values['cols']+','+values['caption']+','+values['description']+','+values['order']+desc+','+values['position']+','+values['clear']+']';
            }
            parent.send_to_editor(codes);            
        });
        
        jQuery('#shashin-browser-albums-list a').live('click', function(e) {
            e.preventDefault();
            jQuery('#shashin-browser-albums-selected ul').append(jQuery(this).parent());
        });
        
        jQuery('#shashin-browser-albums-selected a').live('click', function(e) {
            e.preventDefault();
            jQuery('#shashin-browser-albums-list ul').append(jQuery(this).parent());
        });
    });
</script>

<form id="shashin-browser" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div class="shashin-browser-albums" id="shashin-browser-albums-list">
        <h3><?php _e('Albums', SHASHIN_L10N_NAME) ?></h3>
        <ul>
            <?php foreach ($albums as $album): ?>
                <li id="album-<?php echo $album['album_key'] ?>">
                    <a href=""><?php echo $album['title'] ?><br>
                        <span>
                            <?php _e('Photos', SHASHIN_L10N_NAME) ?>: <?php echo $album['photo_count'] ?> -
                            <?php _e('Published', SHASHIN_L10N_NAME) ?>: <?php echo date("Y-m-d H:i", $album['pub_date']) ?>
                        </span>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="shashin-browser-albums" id="shashin-browser-albums-selected">
        <h3><?php _e('Selected', SHASHIN_L10N_NAME) ?></h3>
        <ul></ul>
    </div>
    
    <br style="clear: both">
    
    <div class="shashin-browser-albums-insert" id="shashin-browser-albums-insert-thumbs">
        <h3><?php _e('Insert album thumbs', SHASHIN_L10N_NAME) ?></h3>
        <table class="describe">
            <tbody>
                <tr>
                    <td class="label"><?php _e('To show', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="to-show">
                            <option value="selected"><?php _e('Selected', SHASHIN_L10N_NAME) ?></option>
                            <option value="title"><?php _e('Title', SHASHIN_L10N_NAME) ?></option>
                            <option value="location"><?php _e('Location', SHASHIN_L10N_NAME) ?></option>
                            <option value="pub_date"><?php _e('Pub. date', SHASHIN_L10N_NAME) ?></option>
                            <option value="last_updated"><?php _e('Last update', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Cols', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="cols" style="width: 80px">
                            <option value="max">Max</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                            <option>13</option>
                            <option>14</option>
                            <option>15</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Location', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="location" style="width: 80px">
                            <option value="n"><?php _e('No', SHASHIN_L10N_NAME) ?></option>
                            <option value="y"><?php _e('Yes', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Pub. date', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="pubdate" style="width: 80px">
                            <option value="n"><?php _e('No', SHASHIN_L10N_NAME) ?></option>
                            <option value="y"><?php _e('Yes', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Position', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="position" style="width: 80px">
                            <option value="center">Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="">None</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Clear', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="clear" style="width: 80px">
                            <option value="">None</option>
                            <option value="both">Both</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="inherit">Inherit</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"></td>
                    <td class="field" style="padding-top: 5px"><input type="button" class="button" name="insert" value="<?php _e('Insert', SHASHIN_L10N_NAME) ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="shashin-browser-albums-insert" id="shashin-browser-albums-insert-list">
        <h3><?php _e('Insert album list', SHASHIN_L10N_NAME) ?></h3>
        <table class="describe">
            <tbody>
                <tr>
                    <td class="label"><?php _e('To show', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="to-show">
                            <option value="selected"><?php _e('Selected', SHASHIN_L10N_NAME) ?></option>
                            <option value="title"><?php _e('Title', SHASHIN_L10N_NAME) ?></option>
                            <option value="location"><?php _e('Location', SHASHIN_L10N_NAME) ?></option>
                            <option value="pub_date"><?php _e('Pub. date', SHASHIN_L10N_NAME) ?></option>
                            <option value="last_updated"><?php _e('Last update', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Info', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="info" style="width: 80px">
                            <option value="n"><?php _e('No', SHASHIN_L10N_NAME) ?></option>
                            <option value="y"><?php _e('Yes', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"></td>
                    <td class="field" style="padding-top: 5px"><input type="button" class="button" name="insert" value="<?php _e('Insert', SHASHIN_L10N_NAME) ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="shashin-browser-albums-insert" id="shashin-browser-albums-insert-photos">
        <h3><?php _e('Insert album photos', SHASHIN_L10N_NAME) ?></h3>
        <table class="describe">
            <tbody>
                <tr>
                    <td class="label"><label for="size"><?php _e('Size', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select style="width: 80px" name="size"><option value="max">Max</option>
                            <?php foreach (unserialize(SHASHIN_IMAGE_SIZES) as $size): ?>
                                <option><?php echo $size ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Cols', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="cols" style="width: 80px">
                            <option value="max">Max</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                            <option>13</option>
                            <option>14</option>
                            <option>15</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="caption"><?php _e('Caption', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select style="width: 80px" name="caption">
                            <option value="n">No</option>
                            <option value="y">Yes</option>
                            <option value="c">Click to enlarge</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Description', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="description" style="width: 80px">
                            <option value="n"><?php _e('No', SHASHIN_L10N_NAME) ?></option>
                            <option value="y"><?php _e('Yes', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Order', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select style="width: 90px" name="order">
                            <option value="uploaded_timestamp"><?php _e('Uploaded', SHASHIN_L10N_NAME) ?></option>
                            <option value="taken_timestamp"><?php _e('Taken', SHASHIN_L10N_NAME) ?></option>
                            <option value="title"><?php _e('Title', SHASHIN_L10N_NAME) ?></option>
                            <option value="picasa_order"><?php _e('Picasa order', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Desc', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <input type="checkbox" name="desc" value="1">
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Position', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="position" style="width: 80px">
                            <option value="center">Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="">None</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php _e('Clear', SHASHIN_L10N_NAME) ?></td>
                    <td class="field">
                        <select name="clear" style="width: 80px">
                            <option value="">None</option>
                            <option value="both">Both</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="inherit">Inherit</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"></td>
                    <td class="field" style="padding-top: 5px"><input type="button" class="button" name="insert" value="<?php _e('Insert', SHASHIN_L10N_NAME) ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>


