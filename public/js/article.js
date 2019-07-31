$(document).ready(function () {
    $('#edit_post_description').keyup(function() {
        $("label[for='edit_post_description']").html('Message - ' + (280 - $(this).val().length) + ' caractères restants');
    });

    $("label[for='edit_post_description']").html('Message - ' + (280 - $('#edit_post_description').val().length) + ' caractères restants');
});