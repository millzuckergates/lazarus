const LARGEUR_ISO = 128;
const HAUTEUR_ISO = 64;

jQuery(document).ready(function init() {
    jQuery('#buttonDecoProfilTest').click(function () {
        deconnectProfilTest();
    });

    if (jQuery('#redirect')) {
        var redirect = jQuery('#redirect').val();
        if (redirect == "1" || redirect == 1) {
            var url = "accueil/annuleRedirect";
            jQuery.ajax({
                type: "POST",
                url: url,
                data: {},
                success: function (data) {
                    ouvreMsgBox("Vous n'avez pas les droits pour accéder à cette page.", "error");
                    jQuery('#redirect').val(0);
                }
            });
        }
    }
});

function deconnectProfilTest() {
    if (window.location.href.includes('/administration/')) {
        url = "deconnectProfilTest";
    } else if (window.location.href.includes('/wiki/article')) {
        url = "../Administration/deconnectProfilTest";
    } else {
        url = "administration/deconnectProfilTest";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            window.location.href = data;
        }
    });
}

//####### Fonctions pour rediriger vers l'accueil ##########//
function redirectAccueil() {
    var url = "Accueil/rediriger";
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {

        }
    });
}

//####### Fonctions pour l'Aide ##########//
function afficherAide(texte, idElement) {
    aide(texte);
}

function cacherAide() {
    jQuery('#aide_contenu').html('');
    jQuery('#aideBulle').hide();
}

function aide(texte) {
    var newTexte = replaceAll(texte, '\n', '<br/>')
    jQuery('#aide_contenu').html(newTexte);
    jQuery('#aideBulle').show();
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}