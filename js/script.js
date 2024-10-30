jQuery(document).ready(function ($) {
    $('.mher-list-subpages-media-button').on('click', function () {
        var mediaUploader;

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Select Image', button: {
                text: 'Choose Image'
            }, multiple: false // Set to true for multiple image selection
        });

        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mher-list-subpages-image-id').val(attachment.id);
            $('#mher-list-subpages-image-preview').attr('src', attachment.sizes.thumbnail.url);
        });

        mediaUploader.open();
    });

    $('#mher-list-subpages-delete-image').on('click', function (event) {
        event.preventDefault();
        const fallbackimageurl = $(this).data('fallback-image-url');
        // remove value from hidden input field with id= mher-list-subpages-image-id
        $('#mher-list-subpages-image-id').val('');
        // remove delete button with id mher-list-subpages-delete-image
        $(this).remove();
        // show build-in fallback image instead
        $('#mher-list-subpages-image-selected').attr('src', fallbackimageurl);
    });

    $('#mher-list-subpages-template-add-button').on('click', function (event) {
        event.preventDefault();
        const nextkey = $(this).data('next-key');
        const scaffold = $(this).data('scaffold');
        const newformrow = scaffold.replaceAll("%d", nextkey);

        $('#mher-list-subpages-templates').append(newformrow);
        $(this).data('next-key', nextkey + 1);
    });

    $(document).on('click', '.mher-list-subpages-template-delete-button', function (event) {
        event.preventDefault();
        $(this).parent().remove();
    });
});