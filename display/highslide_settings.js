/**
 * Accepts variables passed in from Shashin, and customizes the display of Highslide.
 *
 * This file is part of Shashin. Please see the Shashin.php file for
 * copyright and license information.
 *
 * @author Michael Toppa
 * @version 2.6
 */

// The "-0" and "!!" are for type casting, as all vars brought over
// from wp_localize_script come in as strings
hs.graphicsDir = highslide_settings.graphics_dir;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = ((highslide_settings.outline_type == "none") ? null : highslide_settings.outline_type);
hs.fadeInOut = true;
hs.dimmingOpacity = highslide_settings.dimming_opacity-0;

// Add the controlbar for slideshows
function addHSSlideshow(groupID) {
    hs.addSlideshow({
        slideshowGroup: groupID,
        interval: highslide_settings.interval-0,
        repeat: !!(highslide_settings.repeat-0),
        useControls: true,
        fixedControls: true,
        overlayOptions: {
            opacity: .75,
            position: highslide_settings.position,
            hideOnMouseOut: !!(highslide_settings.hide_controller-0)
        }
    });
}

// for Flash
hs.outlineWhileAnimating = true;
hs.allowSizeReduction = false;
// always use this with flash, else the movie will not stop on close:
hs.preserveContent = false;



