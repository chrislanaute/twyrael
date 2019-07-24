$(document).ready(function () {
    // fonction qui s'exécute si des changements sont détectés sur la classe 'custom-file'
    $('.custom-file :file').change(function (input) {
        // variable contenant le type du fichier
        var imageType = /image.*/;
        // change le placeholder le l'input avec le nom de l'image
        var label = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
        $('.custom-file-label').text(label);
        // si un fichier est détecté et que c'est une image
        if (input.target.files && input.target.files[0] && input.target.files[0].type.match(imageType)) {
            // lit le fichier
            var reader = new FileReader();
            reader.onload = function (e) {
                // affiche les boutons supprimer et afficher
                $('#preview').show();
                // ajoute la source de l'image à la balise 'img'
                $('#img-selected').attr('src', e.target.result);
            }
            // lecture de l'image
            reader.readAsDataURL(input.target.files[0]);
            this.fileUpload = input.target.files[0];
        }
    });

    // au click sur le bouton supprimer
    $('#delImage').click(function() {
        // l'input contenant les données de l'image devient 'null'
        $('#registration_image').val(null);
        // supprime la source de l'image dans la balise 'img'
        $('#img-selected').removeAttr('src');
        // remet le placeholder d'origine
        $('.custom-file-label').text("Chemin de l'image");
        // cache les boutons supprimer et afficher
        $('#preview').hide();
    });
});