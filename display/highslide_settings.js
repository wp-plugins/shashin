hs.graphicsDir = highslide_settings.graphics_dir;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = highslide_settings.outline_type;
hs.fadeInOut = true;
hs.dimmingOpacity = highslide_settings.dimming_opacity;

// Add the controlbar for slideshows
function addHSSlideshow(groupID) {
    hs.addSlideshow({
        slideshowGroup: groupID,
        interval: highslide_settings.interval,
        repeat: highslide_settings.repeat,
        useControls: true,
        fixedControls: true,
        overlayOptions: {
            opacity: .75,
            position: highslide_settings.position,
            hideOnMouseOut: highslide_settings.hide_controller
        }
    });
}

// for Flash
hs.outlineWhileAnimating = true;
hs.allowSizeReduction = false;
// always use this with flash, else the movie will not stop on close:
hs.preserveContent = false;



