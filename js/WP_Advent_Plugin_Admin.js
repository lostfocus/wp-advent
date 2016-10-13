jQuery(document).ready(function() {
    var custom_uploader;

    jQuery('.jsonly').css('display','inline');

    jQuery('.delete').click(function(e){
        return confirm(wpadventplugin.are_you_sure);
    });

    jQuery('.wp_advent_plugin_add_image').click(function(e){

        calendarid = jQuery(this).data('calendar');

        e.preventDefault();

        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: wpadventplugin.choose_image,
            button: {
                text: wpadventplugin.choose_image
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            var data = {
                'action':   'wp_advent_set_calendar_image',
                'calendar': calendarid,
                'image':    attachment.id
            }
            jQuery.post(ajaxurl, data, function(response) {
                location.reload();
            });
        });

        //Open the uploader dialog
        custom_uploader.open();

    });


    jQuery('.handlediv').click(function(){
	    jQuery(this).parent().toggleClass('closed');
    });
});