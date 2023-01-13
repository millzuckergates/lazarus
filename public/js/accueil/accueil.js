jQuery(document).ready(function init() {
    jQuery('#showAddNews').click(function () {
        showAddNews();
    });
    jQuery('#hideAddNews').click(function () {
        hideAddNews();
    });
    jQuery('#hideAddNews').hide();
});

function myFunction() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

//########## Gestion des News ############//
function showAddNews() {
    if (window.location.href.includes('/accueil/')) {
        url = "showAddNews";
    } else {
        url = "accueil/showAddNews";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            jQuery('#informationTechniqueNews').html(data);
            jQuery('#informationTechniqueNews').show();
            jQuery('#showAddNews').hide();
            jQuery('#hideAddNews').show();
            initBoutonNews();
        }
    });
}

function hideAddNews() {
    jQuery('#informationTechniqueNews').html("");
    jQuery('#informationTechniqueNews').hide();
    jQuery('#showAddNews').show();
    jQuery('#hideAddNews').hide();
}

function initBoutonNews() {
    //Effacement des balises inutiles
    jQuery('#baliseMJ').hide();
    jQuery('#baliseDEV').hide();
    jQuery('#baliseLienWiki').hide();
    jQuery('#divBaliseRoyaume').hide();
    jQuery('#divBaliseRace').hide();
    jQuery('#divBaliseReligion').hide();

    jQuery('#creerNews').click(function () {
        creerNews();
    });
    initBoutonsBalise();
}

function displayBlocDestinataire() {
    var type = jQuery('#typeNews').val();
    if (type == "RP") {
        jQuery('#blocDestinataireNews').show();
    } else {
        jQuery('#blocDestinataireNews').hide();
    }
}

function displayListeDestinataire() {
    var destinataire = jQuery('#destinataireNews').val();
    if (destinataire == "0" || destinataire == 0) {
        jQuery('#selectIdDestinataire').html("");
        jQuery('#selectIdDestinataire').hide();
    } else {
        if (window.location.href.includes('/accueil/')) {
            url = "displayDestinataire";
        } else {
            url = "accueil/displayDestinataire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                type: destinataire
            },
            success: function (data) {
                jQuery('#selectIdDestinataire').html(data);
                jQuery('#selectIdDestinataire').show();
            }
        });
    }

}

function creerNews() {
    var type = jQuery('#typeNews').val();
    var typeDestinataire = null;
    var idDestinataire = null;
    if (type == "RP") {
        typeDestinataire = jQuery('#destinataireNews').val();
        if (typeDestinataire == "0" || typeDestinataire == 0) {
            typeDestinataire = null;
        } else {
            idDestinataire = jQuery('#idDestinataireNews').val();
        }
    }
    var titre = jQuery('#titreNews').val();
    var texte = jQuery('#texteNews').val();
    var auteur = jQuery('#nomAuteur').val();

    //Contrôle des champs
    var erreur = "";
    var auteurSansEspace = auteur.replace(/\s/g, "");
    if (auteurSansEspace == "") {
        erreur += "Il faut renseigner un auteur.\n";
        jQuery('#nomAuteur').addClass("erreur");
    }
    var titreSansEspace = titre.replace(/\s/g, "");
    if (titreSansEspace == "") {
        erreur += "Un titre est obligatoire.\n";
        jQuery('#titreNews').addClass("erreur");
    }
    var texteSansEspace = texte.replace(/\s/g, "");
    if (texteSansEspace == "") {
        erreur += "Il faut renseigner un texte pour la news.\n";
        jQuery('#texteNews').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/accueil/')) {
            url = "creerNews";
        } else {
            url = "accueil/creerNews";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                type: type,
                texte: texte,
                titre: titre,
                auteur: auteur,
                typeDestinataire: typeDestinataire,
                idDestinataire: idDestinataire
            },
            success: function (data) {
                if (data == "errorProduit") {
                    ouvreMsgBox("Une erreur s'est produite.", "error");
                } else {
                    jQuery('#divListeNews').html(data);
                }
            }
        });
    }
}

function editerNote(idNews) {
    openPopupNews(idNews);
}

function openPopupNews(idNews) {
    if (window.location.href.includes('/accueil/')) {
        url = "openPopupNews";
    } else {
        url = "accueil/openPopupNews";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idNews
        },
        success: function (data) {
            jQuery('#popupNews').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupNews").before('<div id="graybackNews"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupNews").height();
            var popupW = jQuery("#popupNews").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupNews").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupNews").css("margin-left", "-" + popupW / 2 + "px");

            jQuery("#graybackNews").click(function () {
                fermerPopupNews();
            });

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#graybackNews").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupNews").fadeIn(500);
            });
            initBoutonPopupNews();
        }
    });
}

function fermerPopupNews() {
    jQuery("#graybackNews").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupNews").html("");
    jQuery("#popupNews").hide();
}

function boxSupprimerNews(idNews) {
    jQuery('#idNewsDelete').val(idNews);
    ouvreMsgBox("Supprimer cette actualité la fera définitivement disparaître", "question", "ouinon", supprimerNews, "");
}

function supprimerNews() {
    var idNews = jQuery('#idNewsDelete').val();
    if (window.location.href.includes('/accueil/')) {
        url = "supprimerNews";
    } else {
        url = "accueil/supprimerNews";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idNews
        },
        success: function (data) {
            if (data == "errorProduit") {
                ouvreMsgBox("Une erreur s'est produite.", "error");
            } else {
                jQuery('#divListeNews').html(data);
            }
        }
    });
}

function initBoutonPopupNews() {
    //Effacement des balises inutiles
    jQuery('#baliseMJ').hide();
    jQuery('#baliseDEV').hide();
    jQuery('#baliseLienWiki').hide();
    jQuery('#divBaliseRoyaume').hide();
    jQuery('#divBaliseRace').hide();
    jQuery('#divBaliseReligion').hide();
    initBoutonsBalise();

    jQuery('#fermerNews').click(function () {
        fermerPopupNews();
    });
    jQuery('#modifierNews').click(function () {
        modifierNews();
    });
}

function modifierNews() {
    var idNews = jQuery('#idNews').val();
    var type = jQuery('#typeNews').val();
    var typeDestinataire = null;
    var idDestinataire = null;
    if (type == "RP") {
        typeDestinataire = jQuery('#destinataireNews').val();
        if (typeDestinataire == "0" || typeDestinataire == 0) {
            typeDestinataire = null;
        } else {
            idDestinataire = jQuery('#idDestinataireNews').val();
        }
    }
    var titre = jQuery('#titreNews').val();
    var texte = jQuery('#texteNews').val();
    var auteur = jQuery('#nomAuteur').val();

    //Contrôle des champs
    var erreur = "";
    var auteurSansEspace = auteur.replace(/\s/g, "");
    if (auteurSansEspace == "") {
        erreur += "Il faut renseigner un auteur.\n";
        jQuery('#nomAuteur').addClass("erreur");
    }
    var titreSansEspace = titre.replace(/\s/g, "");
    if (titreSansEspace == "") {
        erreur += "Un titre est obligatoire.\n";
        jQuery('#titreNews').addClass("erreur");
    }
    var texteSansEspace = texte.replace(/\s/g, "");
    if (texteSansEspace == "") {
        erreur += "Il faut renseigner un texte pour la news.\n";
        jQuery('#texteNews').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/accueil/')) {
            url = "modifierNews";
        } else {
            url = "accueil/modifierNews";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                type: type,
                texte: texte,
                titre: titre,
                auteur: auteur,
                typeDestinataire: typeDestinataire,
                idDestinataire: idDestinataire,
                id: idNews
            },
            success: function (data) {
                if (data == "errorProduit") {
                    ouvreMsgBox("Une erreur s'est produite.", "error");
                } else {
                    jQuery('#divListeNews').html(data);
                    ouvreMsgBox("L'actualité a été correctement modifiée.", "error");
                }
            }
        });
    }
}