$(document).ready(function () {
    $('#post_description').keyup(function() {
        $("label[for='post_description']").html('Message - ' + (280 - $(this).val().length) + ' caractères restants');
    });

    $('.search-input').attr('placeholder', "Ex: Bonjour");
});