<script type="text/javascript">
    jQuery(document).ready(function() {
        
        function load_images() {
            jQuery('#shashin-browser-loading').show();
            var url = '<?php echo admin_url("admin-ajax.php"); ?>';
            
            var filterRoot = jQuery('#shashin-browser-filter').get();
            
            var album = jQuery('select[name="filter-album"]', filterRoot).val();
            var title = jQuery('input[name="filter-title"]', filterRoot).val();
            var order = jQuery('select[name="filter-order"]', filterRoot).val();
            var desc = jQuery('input[name="filter-desc"]', filterRoot).attr('checked')?1:0;
            var page = get_page();
            
            var data = {
            	action: 'shashin_browser_get_photos',
            	album: album,
            	title: title,
            	order: order,
            	desc: desc,
            	page: page
            };

            jQuery.post(url, data, function(resp) {
                jQuery('#shashin-browser-list img').remove();
                jQuery('#shashin-browser-paging span').text(resp['page']+'/'+resp['total_pages']);
                for(i in resp['images']) {
                    jQuery('#shashin-browser-list').append('<img id="photo-'+resp['images'][i]['photo_key']+'" title="'+resp['images'][i]['title']+'" src="'+resp['images'][i]['enclosure_url']+'?imgmax=72&crop=1">');
                }
                jQuery('#shashin-browser-loading').hide();
            }, 'json');
        }
        
        function get_page() {
            var page = jQuery('#shashin-browser-paging span').text().split('/');
            page[0] = parseFloat(page[0]);
            page[1] = parseFloat(page[1]);
            return page[0];
        }
        
        function get_total_pages() {
            var page = jQuery('#shashin-browser-paging span').text().split('/');
            page[0] = parseFloat(page[0]);
            page[1] = parseFloat(page[1]);
            return page[1];
        }
        
        jQuery('#shashin-browser-selected img').live('click', function() {
            jQuery(this).remove();
        })
        
        jQuery('#shashin-browser-list img').live('mouseover', function(e) {
            var src = jQuery(this).attr('src');
            src = src.replace('?imgmax=72&crop=1', '?imgmax=288');
            var title = jQuery(this).attr('title');

            var offset = jQuery('#shashin-browser-list').offset();
            offset.left = offset.left + 2;
            offset.top = offset.top + 2;
            if(e.clientX < (offset.left + 304)) offset.left = offset.left + 304;
            jQuery('#shashin-browser-preview').html('<img src="'+src+'">')
                                              .css('top', offset.top)
                                              .css('left', offset.left)
                                              .show();
        })
        
        jQuery('#shashin-browser-list img').live('mouseout', function(e) {
            jQuery('#shashin-browser-preview').hide();
        });
        
        jQuery('#shashin-browser-list img').live('click', function(e) {
            var src = jQuery(this).attr('src');
            var title = jQuery(this).attr('title');
            var id = jQuery(this).attr('id');
            id = 'selected-photo-'+id.replace('photo-','');
            jQuery('#shashin-browser-selected').append('<img id="'+id+'" title="'+title+'" src="'+src+'">');
        });
        
        jQuery('#shashin-browser-insert input[type="button"]').click(function() {
            var selected = new Array;
            jQuery('#shashin-browser-selected img').each(function() {
                selected.push(jQuery(this).attr('id').replace('selected-photo-',''));
            });
            
            if(selected.length == 0) return alert('<?php _e('No photos selected', SHASHIN_L10N_NAME) ?>');
            
            var insert      = jQuery('#shashin-browser-insert select[name="insert"]').val();
            var size        = jQuery('#shashin-browser-insert select[name="size"]').val();
            var cols        = jQuery('#shashin-browser-insert select[name="cols"]').val();
            var caption     = jQuery('#shashin-browser-insert select[name="caption"]').val();
            var position    = jQuery('#shashin-browser-insert select[name="position"]').val();
            var clear       = jQuery('#shashin-browser-insert select[name="clear"]').val();
            
            if(insert == "s") {
                var codes = '';
                for(var i=0; i<selected.length;i++) {
                    codes += '[simage='+selected[i]+','+size+','+caption+','+position+','+clear+']';
                }
                parent.send_to_editor(codes);
            }else{
                parent.send_to_editor('[sthumbs='+selected.join('|')+','+size+','+cols+','+caption+','+position+','+clear+']');
            }
        });
        
        jQuery('#shashin-browser-filter input[name="filter-update"]').click(function() {
            load_images();
        });
        
        jQuery('#shashin-browser-filter input[name="prevpage"]').click(function() {
            page = get_page() - 1;
            total_pages = get_total_pages();
            if(page < 1) return;
            jQuery('#shashin-browser-paging span').text(page+'/'+total_pages);
            load_images();
        });
        
        jQuery('#shashin-browser-filter input[name="nextpage"]').click(function() {
            page = get_page() + 1;
            total_pages = get_total_pages();
            if(page > total_pages) return;
            jQuery('#shashin-browser-paging span').text(page+'/'+total_pages);
            load_images();
        });
        
        load_images();
    })
</script>

<form id="shashin-browser" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div id="shashin-browser-filter">
        <div><?php _e('Album', SHASHIN_L10N_NAME) ?><br>
            <select style="width: 100px" name="filter-album">
                <option value="0"><?php _e('All albums', SHASHIN_L10N_NAME) ?></option>
                <?php foreach ($albums as $album): ?>
                    <option value="<?php echo $album['album_id'] ?>"><?php echo $album['title'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        
        <div><?php _e('Title', SHASHIN_L10N_NAME) ?><br> 
            <input type="text" name="filter-title" style="width: 100px">
        </div>
        
        <div><?php _e('Order', SHASHIN_L10N_NAME) ?><br>
            <select style="width: 100px" name="filter-order">
                <option value="uploaded_timestamp"><?php _e('Uploaded', SHASHIN_L10N_NAME) ?></option>
                <option value="taken_timestamp"><?php _e('Taken', SHASHIN_L10N_NAME) ?></option>
                <option value="photo_key"><?php _e('Key', SHASHIN_L10N_NAME) ?></option>
                <option value="title"><?php _e('Title', SHASHIN_L10N_NAME) ?></option>
                <option value="picasa_order"><?php _e('Picasa order', SHASHIN_L10N_NAME) ?></option>
            </select>
        </div>
        
        <div><?php _e('Desc', SHASHIN_L10N_NAME) ?><br>
            <input type="checkbox" checked="checked" name="filter-desc" value="1">
        </div>
        
        <div>&nbsp;<br>
            <input type="button" class="button" name="filter-update" value="<?php _e('Update', SHASHIN_L10N_NAME) ?>">
        </div>
        
        <div id="shashin-browser-paging">&nbsp;<br>
            <input type="button" class="button" name="prevpage" value="<?php _e('Prev', SHASHIN_L10N_NAME) ?>">
                <span>1/1</span>
            <input type="button" class="button" name="nextpage" value="<?php _e('Next', SHASHIN_L10N_NAME) ?>">    
        </div>
    </div>
    <div id="shashin-browser-loading">
        <?php _e('Loading photos', SHASHIN_L10N_NAME) ?>
        <img src="<?php echo SHASHIN_BROWSER_URL . '/loader.gif' ?>" alt="Loading...">
    </div>
    <div id="shashin-browser-preview">&nbsp;</div>
    <div id="shashin-browser-list"></div>
    
    <div id="shashin-browser-selected"><h3><?php _e('Selected', SHASHIN_L10N_NAME) ?></h3></div>
    <div id="shashin-browser-insert">
        <h3><?php _e('Insert', SHASHIN_L10N_NAME) ?></h3>    
        <table class="describe">
            <tbody>
                <tr>
                    <td class="label"><label for="insert"><?php _e('Insert', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field" colspan="3">
                        <select name="insert" style="width: 240px">
                            <option value="s"><?php _e('Single images', SHASHIN_L10N_NAME) ?></option>
                            <option value="t"><?php _e('Table of thumbs', SHASHIN_L10N_NAME) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="size"><?php _e('Size', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select name="size"><option value="max">Max</option>
                            <?php foreach (unserialize(SHASHIN_IMAGE_SIZES) as $size): ?>
                                <option><?php echo $size ?></option>
                            <?php endforeach ?>
                        </select>
                    </td>
                    <td class="label"><label for="cols"><?php _e('Cols', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select name="cols">
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
                        <select name="caption">
                            <option value="n">No</option>
                            <option value="y">Yes</option>
                            <option value="c">Click to enlarge</option>
                        </select>
                    </td>
                    <td class="label"><label for="position"><?php _e('Position', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select name="position">
                            <option value="center">Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="">None</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="clear"><?php _e('Clear', SHASHIN_L10N_NAME) ?></label></td>
                    <td class="field">
                        <select name="clear">
                            <option value="">None</option>
                            <option value="both">Both</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="inherit">Inherit</option>
                        </select>
                    </td>
                    <td colspan="2"><input type="button" class="button" value="<?php _e('Insert and return', SHASHIN_L10N_NAME) ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    
</form>


