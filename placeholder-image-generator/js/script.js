jQuery(document).ready(function($) {
    // handle generate button click
    $('#generate-button').click(function() {
        var data = {
            action: 'generate_placeholder_image',
            width: $('input[name="width"]').val(),
            height: $('input[name="height"]').val()
        };

        // generate placeholder image
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                // update preview image
                var img = $('<img>').attr('src', response.data.url);
                $('#preview').html(img);

                // enable save button
                $('#save-button').removeAttr('disabled');
            }
        });
    });

    // handle save button click
    $('#save-button').click(function() {
        var data = {
            action: 'save_placeholder_image',
            url: $('#preview img').attr('src')
        };

        // save placeholder image
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                alert('Image saved to media library with ID ' + response.data.id);
            }
        });
    });
});
