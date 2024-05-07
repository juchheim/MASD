jQuery(document).ready(function($) {
    console.log('Script loaded successfully'); // Check if this message appears in the console
    $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'get_books'
        },
        success: function(response) {
            $('#books-container').html(response);
        },
        error: function(xhr, status, error) {
            console.log('the error occurred');
            console.log(xhr.responseText);
        }
    });
});

