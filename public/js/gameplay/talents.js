//Variable contenant les informations des talents pour l'administrateur
var listeTalentsAdmin = [];
var listeGenealogieAdmin = [];
var idTalentDrag = null;
var init = false;
var simulation = true;
var showDescriptionTalent = true;

function initBoutonTalents(indicateurInit = null) {
    jQuery('#boutonAjouterNouvelleCategorie').click(function () {
        ajouterNouvelleCategorie();
    });
    jQuery('#boutonModifierCategorie').click(function () {
        editerCategorieTalentFromPage();
    });
    jQuery('#boutonSupprimerCategorie').click(function () {
        boxSupprimerCategorie();
    });
    jQuery('#afficherAjouterFamille').click(function () {
        afficherAjouterFamille();
    });
    jQuery('#boutonAjouterNouvelArbre').click(function () {
        ajouterNouvelArbre();
    });
    if (indicateurInit != null) {
        init = false;
    }
    setTimeout(function () {
        if (init == false) {
            initListeTalentsAdmin();
            setTimeout(function () {
                getGenealogieAdmin();
            }, 50);
        }
    }, 100);
}

function initBoutonCategorie() {
    jQuery('#chargerNewImageCategorieTalent').click(function () {
        chargerNewImageCategorieTalent();
    });
    jQuery('#creerCategorieTalent').click(function () {
        creerCategorieTalent();
    });
    jQuery('#fermerPopupTalentCategorie').click(function () {
        fermerPopupTalent("categorie");
    });
    jQuery('#editerCategorieTalent').click(function () {
        editerCategorieTalent();
    });
    jQuery('#modifierCategorieTalent').click(function () {
        boxModifierCategorieTalent();
    });
}

function initBoutonFamille() {
    jQuery('#fermerPopupTalentFamille').click(function () {
        fermerPopupTalent("famille");
    });
    jQuery('#creerFamilleTalent').click(function () {
        creerFamilleTalent();
    });
    jQuery('#modifierFamilleTalent').click(function () {
        boxModifierFamilleTalent();
    });
    jQuery('#editerFamilleTalent').click(function () {
        editerFamilleTalent();
    });
    jQuery('#showlisteContraintesFamille').click(function () {
        showlisteContraintesFamille();
    });
    jQuery('#hidelisteContraintesFamille').click(function () {
        hidelisteContraintesFamille();
    });
    jQuery('#hidelisteContraintesFamille').hide();
    jQuery('#boutonAjouterNouvelArbre').click(function () {
        ajouterNouvelArbre();
    });
}

function initBoutonArbre() {
    jQuery('#fermerPopupTalentArbre').click(function () {
        fermerPopupTalent("arbre");
    });
    jQuery('#chargerNewImageArbreTalent').click(function () {
        chargerNewImageArbreTalent();
    });
    jQuery('#creerArbreTalent').click(function () {
        creerArbreTalent();
    });
    jQuery('#modifierArbreTalent').click(function () {
        boxModifierArbreTalent();
    });
    jQuery('#editerArbreTalent').click(function () {
        editerArbreTalent();
    });
    jQuery('#showlisteContraintesArbre').click(function () {
        showlisteContraintesArbre();
    });
    jQuery('#hidelisteContraintesArbre').click(function () {
        hidelisteContraintesArbre();
    });
    jQuery('#hidelisteContraintesArbre').hide();
}

function initBoutonTalent() {
    jQuery('#fermerPopupTalent').click(function () {
        fermerPopupTalent("talent");
    });
    jQuery('#chargerNewImageTalent').click(function () {
        chargerNewImageTalent();
    });
    jQuery('#maxTalent').change(function () {
        maxTalent();
    });
    jQuery('#creerTalent').click(function () {
        creerTalent();
    });
    jQuery('#modifierTalent').click(function () {
        boxModifierTalent();
    });
    jQuery('#editerTalent').click(function () {
        editerTalent();
    });
    jQuery('#showlisteEffetTalent').click(function () {
        showlisteEffetTalent();
    });
    jQuery('#hidelisteEffetTalent').click(function () {
        hidelisteEffetTalent();
    });
    jQuery('#hidelisteEffetTalent').hide();
    jQuery('#showlisteContraintesTalent').click(function () {
        showlisteContraintesTalent();
    });
    jQuery('#hidelisteContraintesTalent').click(function () {
        hidelisteContraintesTalent();
    });
    jQuery('#hidelisteContraintesTalent').hide();
}

// Affichage des popup
function afficherPopupFamille(data) {
    jQuery('#popupFamille').html(data);

    // Ici on insère dans notre page html notre div gris
    jQuery("#popupFamille").before('<div id="graybackFamille"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupFamille").height();
    var popupW = jQuery("#popupFamille").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupFamille").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupFamille").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackFamille").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupFamille").fadeIn(500);
    });
    cleanErreurFamille();
    setTimeout(function () {
        initBoutonFamille();
    }, 10);
}

function afficherPopupCategorie(data) {
    jQuery('#popupCategorie').html(data);
    // Ici on insère dans notre page html notre div gris
    jQuery("#popupCategorie").before('<div id="graybackCategorie"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupCategorie").height();
    var popupW = jQuery("#popupCategorie").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupCategorie").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupCategorie").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackCategorie").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupCategorie").fadeIn(500);
    });
    cleanErreurCategorie();
    setTimeout(function () {
        initBoutonCategorie();
    }, 10);
}

function afficherPopupArbre(data) {
    jQuery('#popupArbre').html(data);

    // Ici on insère dans notre page html notre div gris
    jQuery("#popupArbre").before('<div id="graybackArbre"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupArbre").height();
    var popupW = jQuery("#popupArbre").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupArbre").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupArbre").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackArbre").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupArbre").fadeIn(500);
    });
    cleanErreurArbre();
    setTimeout(function () {
        initBoutonArbre();
    }, 10);
}

function afficherPopupTalent(data) {
    jQuery('#popupTalent').html(data);

    // Ici on insère dans notre page html notre div gris
    jQuery("#popupTalent").before('<div id="graybackTalent"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupTalent").height();
    var popupW = jQuery("#popupTalent").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupTalent").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupTalent").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackTalent").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupTalent").fadeIn(500);
    });
    cleanErreurTalent();
    setTimeout(function () {
        initBoutonTalent();
    }, 10);
}

function fermerPopupTalent(element) {
    if (element == "categorie") {
        // on fait disparaître le gris de fond rapidement
        jQuery("#graybackCategorie").remove();//fadeOut('fast', function () { jQuery(this).remove() });
        // on fait disparaître le popup à la même vitesse
        jQuery("#popupCategorie").hide();
    } else if (element == "famille") {
        // on fait disparaître le gris de fond rapidement
        jQuery("#graybackFamille").remove();//fadeOut('fast', function () { jQuery(this).remove() });
        // on fait disparaître le popup à la même vitesse
        jQuery("#popupFamille").hide();
    } else if (element == "arbre") {
        // on fait disparaître le gris de fond rapidement
        jQuery("#graybackArbre").remove();//fadeOut('fast', function () { jQuery(this).remove() });
        // on fait disparaître le popup à la même vitesse
        jQuery("#popupArbre").hide();
    } else if (element == "talent") {
        // on fait disparaître le gris de fond rapidement
        jQuery("#graybackTalent").remove();//fadeOut('fast', function () { jQuery(this).remove() });
        // on fait disparaître le popup à la même vitesse
        jQuery("#popupTalent").hide();
        getListeTalentAdmin();
    }
}

//######################  Categorie #####################//
function chargerNewImageCategorieTalent() {
    var id = jQuery('#idCategorie').val();
    var type = "categorie";
    var urlFile = jQuery('#newImageCategorieTalent').val();

    // Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function creerCategorieTalent() {
    var nom = jQuery('#nomCategorie').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionCategorieTalent').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomCategorie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCategorieTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerCategorie";
        } else {
            url = "gameplay/creerCategorie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("La creation est effective.", "info");
                    setTimeout(function () {
                        afficherDetailCategorie(data);
                        updateListeCategorie();
                        cleanErreurCategorie();
                    }, 10);
                }
            }
        });
    }
}

function boxModifierCategorieTalent() {
    ouvreMsgBox("Modifier cette catégorie de talents peut avoir un impact sur l'intégralité du jeu. Confirmez-vous vos modifications ?", "question", "ouinon", modifierCategorieTalent, "");
}

function modifierCategorieTalent() {
    var idCategorie = jQuery('#idCategorieTalent').val();
    var nom = jQuery('#nomCategorie').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionCategorieTalent').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomCategorie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCategorieTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierCategorie";
        } else {
            url = "gameplay/modifierCategorie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                id: idCategorie
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("Les modification a été prise en compte.", "info");
                    setTimeout(function () {
                        updateListeCategorie();
                        cleanErreurCategorie();
                    }, 20);
                }
            }
        });
    }
}

function ajouterNouvelleCategorie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCategorie";
    } else {
        url = "gameplay/detailCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            if (jQuery('#popupCategorie').is(':visible')) {
                jQuery('#popupCategorie').html(data);
                initBoutonCategorie();
            } else {
                afficherPopupCategorie(data);
            }
        }
    });
}

function modifierCategorie() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCategorie";
    } else {
        url = "gameplay/detailCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCategorie
        },
        success: function (data) {
            if (jQuery('#popupCategorie').is(':visible')) {
                jQuery('#popupCategorie').html(data);
                initBoutonCategorie();
            } else {
                afficherPopupCategorie(data);
            }
        }
    });
}

function editerCategorieTalent() {
    var idCategorie = jQuery('#idCategorie').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCategorie";
    } else {
        url = "gameplay/detailCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCategorie
        },
        success: function (data) {
            if (jQuery('#popupCategorie').is(':visible')) {
                jQuery('#popupCategorie').html(data);
                initBoutonCategorie();
            } else {
                afficherPopupCategorie(data);
            }
        }
    });
}

function editerCategorieTalentFromPage() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCategorie";
    } else {
        url = "gameplay/detailCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCategorie
        },
        success: function (data) {
            if (jQuery('#popupCategorie').is(':visible')) {
                jQuery('#popupCategorie').html(data);
                initBoutonCategorie();
            } else {
                afficherPopupCategorie(data);
            }
        }
    });
}

function afficherDetailCategorie(id) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCategorie";
    } else {
        url = "gameplay/detailCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: id
        },
        success: function (data) {
            if (jQuery('#popupCategorie').is(':visible')) {
                jQuery('#popupCategorie').html(data);
                initBoutonCategorie();
            } else {
                afficherPopupCategorie(data);
            }
        }
    });
}

function cleanErreurCategorie() {
    if (jQuery('#nomCategorie').hasClass("erreur")) {
        jQuery('#nomCategorie').removeClass("erreur");
    }
    if (jQuery('#descriptionCategorieTalent').hasClass("erreur")) {
        jQuery('#descriptionCategorieTalent').removeClass("erreur");
    }
}

function boxSupprimerCategorie() {
    ouvreMsgBox("Supprimer cette catégorie de talents peut avoir un impact sur l'intégralité du jeu. Les familles, arbres et talents associés à cette dernière seront également supprimées. Confirmez-vous votre action ?", "question", "ouinon", supprimerCategorie, "");
}

function supprimerCategorie() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerCategorie";
    } else {
        url = "gameplay/supprimerCategorie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCategorie
        },
        success: function (data) {
            ouvreMsgBox("La catégorie et toutes les informations en découlant a bien été supprimée.", "info");
            setTimeout(function () {
                updateListeCategorie();
            }, 10);
        }
    });
}

function chargeCategorie(idCategorie) {
    if (window.location.href.includes('/gameplay/')) {
        url = "chargerPageTalent";
    } else {
        url = "gameplay/chargerPageTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCategorie
        },
        success: function (data) {
            jQuery('#blocGameplay').html(data);
            init = true;
            initBoutonTalents();
            setTimeout(function () {
                getListeTalentAdmin();
            }, 10);
        }
    });
}

function changerImageCategorie() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageCategorie").attr("src", src);
}

function updateListeCategorie() {
    openOngletGameplay('talents');
}

function afficherDescriptionCategorie(idCategorie) {
    var pos = jQuery('#divCategorieTalent_' + idCategorie).position();
    jQuery('#divDescriptionCategorie_' + idCategorie).show();
    jQuery('#divDescriptionCategorie_' + idCategorie).css({top: pos.top + 20, left: pos.left + 180});
}

function hideDescriptionCategorie(idCategorie) {
    jQuery('#divDescriptionCategorie_' + idCategorie).hide();
}

//################## Familles ######################//
function afficherAjouterFamille() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailFamille";
    } else {
        url = "gameplay/detailFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null,
            idCategorie: idCategorie
        },
        success: function (data) {
            afficherPopupFamille(data);
        }
    });
}

function afficherDetailFamille(id) {
    var idCategorie = jQuery('#idCategorieSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailFamille";
    } else {
        url = "gameplay/detailFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: id,
            idCategorie: idCategorie
        },
        success: function (data) {
            if (jQuery('#popupFamille').is(':visible')) {
                jQuery('#popupFamille').html(data);
                initBoutonFamille();
            } else {
                afficherPopupFamille(data);
            }
        }
    });
}

function creerFamilleTalent() {
    var nom = jQuery('#nomFamille').val();
    var description = jQuery('#descriptionFamilleTalent').val();
    var idCategorie = jQuery('#idCategorie').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomFamille').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionFamilleTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerFamille";
        } else {
            url = "gameplay/creerFamille";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                idCategorie: idCategorie
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("La creation est effective.", "info");
                    setTimeout(function () {
                        afficherDetailFamille(data);
                        updateListeFamille(idCategorie);
                        cleanErreurFamille();
                    }, 10);
                }
            }
        });
    }
}

function boxModifierFamilleTalent() {
    ouvreMsgBox("Modifier cette famille de talents peut avoir un impact sur l'intégralité du jeu. Confirmez-vous vos modifications ?", "question", "ouinon", modifierFamilleTalent, "");
}

function modifierFamilleTalent() {
    var idFamille = jQuery('#idFamilleTalent').val();
    var nom = jQuery('#nomFamille').val();
    var description = jQuery('#descriptionFamilleTalent').val();
    var idCategorie = jQuery('#idCategorie').val();
    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomFamille').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCategorieTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierFamille";
        } else {
            url = "gameplay/modifierFamille";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                id: idFamille
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("Les modification a été prise en compte.", "info");
                    setTimeout(function () {
                        updateListeFamille(idCategorie);
                        cleanErreurFamille();
                    }, 10);
                }
            }
        });
    }
}

function editerFamilleTalent() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    var idFamille = jQuery('#idFamille').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailFamille";
    } else {
        url = "gameplay/detailFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idFamille,
            idCategorie: idCategorie
        },
        success: function (data) {
            if (jQuery('#popupFamille').is(':visible')) {
                jQuery('#popupFamille').html(data);
                initBoutonFamille();
            } else {
                afficherPopupFamille(data);
            }
        }
    });
}

function showlisteContraintesFamille() {
    var idFamille = jQuery('#idFamilleSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteContraintesFamille";
    } else {
        url = "gameplay/showlisteContraintesFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idFamille,
            type: 'familleTalent'
        },
        success: function (data) {
            jQuery('#informationTechniqueFamille').html(data);
            jQuery('#informationTechniqueFamille').show();
            jQuery('#showlisteContraintesFamille').hide();
            jQuery('#hidelisteContraintesFamille').show();
            initBoutonContrainte();
        }
    });
}

function hidelisteContraintesFamille() {
    jQuery('#informationTechniqueFamille').html("");
    jQuery('#informationTechniqueFamille').hide();
    jQuery('#showlisteContraintesFamille').show();
    jQuery('#hidelisteContraintesFamille').hide();
}

function cleanErreurFamille() {
    if (jQuery('#nomFamille').hasClass("erreur")) {
        jQuery('#nomFamille').removeClass("erreur");
    }
    if (jQuery('#descriptionCategorieTalent').hasClass("erreur")) {
        jQuery('#descriptionCategorieTalent').removeClass("erreur");
    }
}

function chargerFamille(idFamille) {
    if (window.location.href.includes('/gameplay/')) {
        url = "chargerFamille";
    } else {
        url = "gameplay/chargerFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idFamille,
            simulation: simulation
        },
        success: function (data) {
            jQuery('#divGeneralTalent').html(data);
            initBoutonFamille();
            getListeTalentAdmin();
        }
    });
}

function chargerModifierFamille(idFamille) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailFamille";
    } else {
        url = "gameplay/detailFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idFamille
        },
        success: function (data) {
            afficherPopupFamille(data);
        }
    });
}

function boxSupprimerFamille(idFamille) {
    jQuery('#idFamilleSelect').val(idFamille);
    ouvreMsgBox("Supprimer cette famille de talents peut avoir un impact sur l'intégralité du jeu. Les arbres et talents associés à cette dernière seront également supprimées. Confirmez-vous votre action ?", "question", "ouinon", supprimerFamille, "");
}

function supprimerFamille() {
    var idCategorie = jQuery('#idCategorieSelect').val();
    var idFamille = jQuery('#idFamilleSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerFamille";
    } else {
        url = "gameplay/supprimerFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idFamille
        },
        success: function (data) {
            ouvreMsgBox("La famille et toutes les informations en découlant a bien été supprimée.", "info");
            setTimeout(function () {
                chargeCategorie(idCategorie);
            }, 100);
        }
    });
}

function updateListeFamille(idCategorie) {
    var idFamille = jQuery('#idFamilleSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "chargerFamille";
    } else {
        url = "gameplay/chargerFamille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idCategorie: idCategorie,
            id: idFamille
        },
        success: function (data) {
            jQuery('#divGeneralTalent').html(data);
            getListeTalentAdmin();
        }
    });
}

function afficherDescriptionFamille(idFamille) {
    var pos = jQuery('#divFamilleLI_' + idFamille).position();
    jQuery('#divDescriptionFamille_' + idFamille).show();
    jQuery('#divDescriptionFamille_' + idFamille).css({top: pos.top + 5, left: pos.left + 80});
}

function hideDescriptionFamille(idFamille) {
    jQuery('#divDescriptionFamille_' + idFamille).hide();
}

//###################### Arbre ###########################//
function chargerNewImageArbreTalent() {
    var id = jQuery('#idArbre').val();
    var type = "arbre";
    var urlFile = jQuery('#newImageArbreTalent').val();

    // Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function creerArbreTalent() {
    var nom = jQuery('#nomArbre').val();
    var idFamille = jQuery('#idFamille').val();
    var description = jQuery('#descriptionArbreTalent').val();
    var image = jQuery('#listeImage').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomArbre').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionArbreTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerArbre";
        } else {
            url = "gameplay/creerArbre";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                idFamille: idFamille,
                image: image
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("La creation est effective.", "info");
                    afficherDetailArbre(data);
                    setTimeout(function () {
                        cleanErreurArbre();
                        chargerFamille(idFamille);
                    }, 50);
                }
            }
        });
    }
}

function boxModifierArbreTalent() {
    ouvreMsgBox("Modifier cette arbre de talents peut avoir un impact sur l'intégralité du jeu. Confirmez-vous vos modifications ?", "question", "ouinon", modifierArbreTalent, "");
}

function modifierArbreTalent() {
    var idArbre = jQuery('#idArbreTalent').val();
    var nom = jQuery('#nomArbre').val();
    var description = jQuery('#descriptionArbreTalent').val();
    var image = jQuery('#listeImage').val();
    var idFamille = jQuery('#idFamilleSelect').val();
    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomArbre').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionArbreTalent').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierArbre";
        } else {
            url = "gameplay/modifierArbre";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                id: idArbre
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("Les modifications ont bien été prise en compte.", "info");
                    setTimeout(function () {
                        chargerFamille(idFamille);
                        cleanErreurArbre();

                    }, 20);
                }
            }
        });
    }
}

function editerArbreTalent() {
    var idArbre = jQuery('#idArbreTalent').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailArbre";
    } else {
        url = "gameplay/detailArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idArbre
        },
        success: function (data) {
            if (jQuery('#popupArbre').is(':visible')) {
                jQuery('#popupArbre').html(data);
                initBoutonArbre();
            } else {
                afficherPopupArbre(data);
            }
        }
    });
}

function afficherDetailArbre(id) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailArbre";
    } else {
        url = "gameplay/detailArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: id
        },
        success: function (data) {
            if (jQuery('#popupArbre').is(':visible')) {
                jQuery('#popupArbre').html(data);
                initBoutonArbre();
            } else {
                afficherPopupArbre(data);
            }
        }
    });
}

function showlisteContraintesArbre() {
    var idArbre = jQuery('#idArbreTalent').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteContraintesArbre";
    } else {
        url = "gameplay/showlisteContraintesArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idArbre,
            type: 'arbreTalent'
        },
        success: function (data) {
            jQuery('#informationTechniqueArbre').html(data);
            jQuery('#informationTechniqueArbre').show();
            jQuery('#showlisteContraintesArbre').hide();
            jQuery('#hidelisteContraintesArbre').show();
            initBoutonContrainte();
        }
    });
}

function hidelisteContraintesArbre() {
    jQuery('#informationTechniqueArbre').html("");
    jQuery('#informationTechniqueArbre').hide();
    jQuery('#showlisteContraintesArbre').show();
    jQuery('#hidelisteContraintesArbre').hide();
}

function cleanErreurArbre() {
    if (jQuery('#nomArbre').hasClass("erreur")) {
        jQuery('#nomArbre').removeClass("erreur");
    }
    if (jQuery('#descriptionArbreTalent').hasClass("erreur")) {
        jQuery('#descriptionArbreTalent').removeClass("erreur");
    }
}

function ajouterNouvelArbre() {
    var idFamille = jQuery('#idFamilleSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailArbre";
    } else {
        url = "gameplay/detailArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null,
            idFamille: idFamille
        },
        success: function (data) {
            if (jQuery('#popupArbre').is(':visible')) {
                jQuery('#popupArbre').html(data);
                initBoutonArbre();
            } else {
                afficherPopupArbre(data);
            }
        }
    });
}

function chargerModifierArbre(idArbre) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailArbre";
    } else {
        url = "gameplay/detailArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idArbre
        },
        success: function (data) {
            if (jQuery('#popupArbre').is(':visible')) {
                jQuery('#popupArbre').html(data);
                initBoutonArbre();
            } else {
                afficherPopupArbre(data);
            }
        }
    });
}

function boxSupprimerArbre(idArbre) {
    jQuery('#idArbreSelect').val(idArbre);
    ouvreMsgBox("Supprimer cet arbre de talents peut avoir un impact sur l'intégralité du jeu. Lestalents associés à ce dernier seront également supprimées. Confirmez-vous votre action ?", "question", "ouinon", supprimerArbre, "");
}

function supprimerArbre() {
    var idFamille = jQuery('#idFamilleSelect').val();
    var idArbre = jQuery('#idArbreSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerArbre";
    } else {
        url = "gameplay/supprimerArbre";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idArbre
        },
        success: function (data) {
            ouvreMsgBox("L'arbre et toutes les informations en découlant a bien été supprimé.", "info");
            setTimeout(function () {
                chargerFamille(idFamille);
            }, 50);
        }
    });
}

function changerImageArbre() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageArbre").attr("src", src);
}

function updateListeArbre(idFamille) {
    chargerFamille(idFamille);
}

function afficherDescriptionArbre(idArbre) {
    var pos = jQuery('#blocTotalArbre_' + idArbre).position();
    jQuery('#divDescriptionArbre_' + idArbre).show();
    jQuery('#divDescriptionArbre_' + idArbre).css({left: pos.left + 180});
}

function hideDescriptionArbre(idArbre) {
    jQuery('#divDescriptionArbre_' + idArbre).hide();
}

//#################### Talents ##################//
function chargerNewImageTalent() {
    var id = jQuery('#idTalent').val();
    var type = "talent";
    var urlFile = jQuery('#newImageTalentTalent').val();

    // Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function maxTalent() {
    var nbMax = jQuery('#maxTalent').val();
    if (isNaN(nbMax)) {
        nbMax = 1;
        jQuery('#maxTalent').val(nbMax);
    }
    jQuery('#numberTalentGris').html("0/" + nbMax);
    jQuery('#numberTalentVert').html(nbMax + "/" + nbMax);
    jQuery('#numberTalentJaune').html("1/" + nbMax);
}

function creerTalent() {
    var nom = jQuery('#nomTalent').val();
    var description = jQuery('#descriptionTalent').val();
    var image = jQuery('#listeImage').val();
    var niveauMax = jQuery('#maxTalent').val();
    var idArbre = jQuery('#idArbre').val();
    var rang = jQuery('#rang').val();
    var position = jQuery('#position').val();
    var isActif = jQuery('#isActifTalent').is(":checked");

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomTalent').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionTalent').addClass("erreur");
    }

    var maxSansEspace = niveauMax.replace(/\s/g, "");
    if (maxSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nombre maximum de point attribuable. \n";
        jQuery('#maxTalent').addClass("erreur");
    } else if (isNaN(niveauMax)) {
        erreur = erreur + "Le nombre maximum de point doit être un entier. \n";
        jQuery('#maxTalent').addClass("erreur");
    }

    if (isActif == true) {
        isActif = 1;
    } else {
        isActif = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerTalent";
        } else {
            url = "gameplay/creerTalent";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                idArbre: idArbre,
                image: image,
                max: niveauMax,
                rang: rang,
                position: position,
                isActif: isActif
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else if (data == "erreurPosition") {
                    ouvreMsgBox("Il ne peut pas y avoir plus de 4 talents par rang.", "erreur");
                } else {
                    ouvreMsgBox("La creation est effective.", "info");
                    setTimeout(function () {
                        afficherDetailTalent(data);
                        var idFamille = jQuery('#idFamilleSelect').val();
                        chargerFamille(idFamille);
                        setTimeout(function () {
                            cleanErreurTalent();
                        }, 10);
                    }, 10);
                }
            }
        });
    }
}

function boxModifierTalent() {
    ouvreMsgBox("Modifier ce talent peut avoir un impact sur l'intégralité du jeu et entraîner une redistribution des points de talent chez les joueurs en ayant déjà investi dans ce dernier. Ce sera le cas si le nombre de point maximum attribuables a été diminué ou si un des effets/contraintes a été modifié.<br/> Confirmez-vous vos modifications ?", "question", "ouinon", modifierTalent, "");
}

function modifierTalent() {
    var idTalent = jQuery('#idTalent').val();
    var nom = jQuery('#nomTalent').val();
    var description = jQuery('#descriptionTalent').val();
    var image = jQuery('#listeImage').val();
    var niveauMax = jQuery('#maxTalent').val();
    var isActif = jQuery('#isActifTalent').is(":checked");

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomTalent').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionTalent').addClass("erreur");
    }

    var maxSansEspace = niveauMax.replace(/\s/g, "");
    if (maxSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nombre maximum de point attribuable. \n";
        jQuery('#maxTalent').addClass("erreur");
    } else if (isNaN(niveauMax)) {
        erreur = erreur + "Le nombre maximum de point doit être un entier. \n";
        jQuery('#maxTalent').addClass("erreur");
    }

    if (isActif == true) {
        isActif = 1;
    } else {
        isActif = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierTalent";
        } else {
            url = "gameplay/modifierTalent";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                max: niveauMax,
                isActif: isActif,
                id: idTalent
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("Les modifications ont bien été prise en compte.", "info");
                    jQuery("#grayBack").remove();
                    setTimeout(function () {
                        getListeTalentAdmin();
                        cleanErreurTalent();
                    }, 10);
                }
            }
        });
    }
}

function editerTalent() {
    jQuery('[name=hiddenMenuTalent').hide();
    var idTalent = jQuery('#idTalent').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTalent";
    } else {
        url = "gameplay/detailTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idTalent
        },
        success: function (data) {
            if (jQuery('#popupTalent').is(':visible')) {
                jQuery('#popupTalent').html(data);
                initBoutonTalent();
            } else {
                afficherPopupTalent(data);
            }
        }
    });
}

function showlisteEffetTalent() {
    var idTalent = jQuery('#idTalent').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEffetTalent";
    } else {
        url = "gameplay/showlisteEffetTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idTalent
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetTalent').hide();
            jQuery('#hidelisteEffetTalent').show();
            jQuery('#showlisteContraintesTalent').show();
            jQuery('#hidelisteContraintesTalent').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteEffetTalent() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetTalent').show();
    jQuery('#hidelisteEffetTalent').hide();
    jQuery('#showlisteContraintesTalent').show();
    jQuery('#hidelisteContraintesTalent').hide();
    getListeTalentAdmin();
}

function showlisteContraintesTalent() {
    var idTalent = jQuery('#idTalent').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteContraintesTalent";
    } else {
        url = "gameplay/showlisteContraintesTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idTalent
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetTalent').show();
            jQuery('#hidelisteEffetTalent').hide();
            jQuery('#showlisteContraintesTalent').hide();
            jQuery('#hidelisteContraintesTalent').show();
            initBoutonContrainte();
        }
    });
}

function hidelisteContraintesTalent() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetTalent').show();
    jQuery('#hidelisteEffetTalent').hide();
    jQuery('#showlisteContraintesTalent').show();
    jQuery('#hidelisteContraintesTalent').hide();
    getListeTalentAdmin();
}

function afficherDetailTalent(id) {
    var idArbre = jQuery('#idArbre').val();
    jQuery('[name=hiddenMenuTalent').hide();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTalent";
    } else {
        url = "gameplay/detailTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: id,
            idArbre: idArbre
        },
        success: function (data) {
            if (jQuery('#popupTalent').is(':visible')) {
                jQuery('#popupTalent').html(data);
                initBoutonTalent();
            } else {
                afficherPopupTalent(data);
            }
        }
    });
}

function cleanErreurTalent() {
    if (jQuery('#nomTalent').hasClass("erreur")) {
        jQuery('#nomTalent').removeClass("erreur");
    }
    if (jQuery('#descriptionTalent').hasClass("erreur")) {
        jQuery('#descriptionTalent').removeClass("erreur");
    }
    if (jQuery('#maxTalent').hasClass("erreur")) {
        jQuery('#maxTalent').removeClass("erreur");
    }
    if (jQuery('#rangTalent').hasClass("erreur")) {
        jQuery('#rangTalent').removeClass("erreur");
    }
    if (jQuery('#positionTalent').hasClass("erreur")) {
        jQuery('#positionTalent').removeClass("erreur");
    }
}

function afficherCreationTalent(rang, position, idArbre) {
    jQuery('[name=hiddenMenuTalent').hide();
    if (window.location.href.includes('/gameplay/')) {
        url = "afficherCreationTalent";
    } else {
        url = "gameplay/afficherCreationTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null,
            idArbre: idArbre,
            rang: rang,
            position: position
        },
        success: function (data) {
            if (jQuery('#popupTalent').is(':visible')) {
                jQuery('#popupTalent').html(data);
                init = true;
                initBoutonTalents();
            } else {
                afficherPopupTalent(data);
            }
        }
    });
}

function changerImageTalent() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }

    url = "url(" + src + ")";
    jQuery('#imgTalentGris').css("background-image", url);
    jQuery('#imgTalentVert').css("background-image", url);
    jQuery('#imgTalentJaune').css("background-image", url);
}

function supprimerGenealogie(idFils, idPere) {
    var idArbre = jQuery('#idArbreTalent').html();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerGenealogie";
    } else {
        url = "gameplay/supprimerGenealogie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idPere: idPere,
            idFils: idFils
        },
        success: function (data) {
            jQuery('#divGenealogieTalent').html(data);
            setTimeout(function () {
                var idFamille = jQuery('#idFamilleSelect').val();
                chargerFamille(idFamille);
            }, 100);
        }
    });
}

function ajouterGenealogie() {
    var idFils = jQuery('#idTalent').val();
    var idArbre = jQuery('#idArbre').html();
    var idPere = jQuery('#selectTalentPere').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "ajouterGenealogie";
    } else {
        url = "gameplay/ajouterGenealogie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idPere: idPere,
            idFils: idFils
        },
        success: function (data) {
            if (data == "error") {
                ouvreMsgBox('Cette liaison existe déjà.', "error");
            } else {
                jQuery('#divGenealogieTalent').html(data);
                setTimeout(function () {
                    var idFamille = jQuery('#idFamilleSelect').val();
                    chargerFamille(idFamille);
                }, 10);
            }
        }
    });
}

function displayOptionTalent(idTalent, idArbre) {
    jQuery('[name=hiddenMenuTalent').hide();
    var pos = jQuery('#talent_' + idTalent).position();
    var posArbre = jQuery('#arbre_' + idArbre).position();
    jQuery('#augmenterTalent_' + idArbre).unbind("click");
    jQuery('#modifierTalent_' + idArbre).unbind("click");
    jQuery('#retirerTalent_' + idArbre).unbind("click");
    jQuery('#augmenterTalent_' + idArbre).click(function () {
        augmenterTalent(idTalent, idArbre);
    });
    jQuery('#modifierTalent_' + idArbre).click(function () {
        accesEditerTalent(idTalent);
    });
    jQuery('#retirerTalent_' + idArbre).click(function () {
        boxRetirerTalent(idTalent);
    });
    jQuery('#hiddenMenuTalent_' + idArbre).css({top: pos.top, left: pos.left - 10 + posArbre.left});
    if (parseInt(listeTalentsAdmin[idTalent].max) == parseInt(listeTalentsAdmin[idTalent].actuel) || listeTalentsAdmin[idTalent].couleur == "gris") {
        jQuery('#augmenterTalent_' + idArbre).hide();
    } else {
        jQuery('#augmenterTalent_' + idArbre).show();
    }

    jQuery('#hiddenMenuTalent_' + idArbre).show();
}

function augmenterTalent(idTalent, idArbre) {
    var max = parseInt(jQuery('#talent_max_' + idTalent).val());
    var actuel = parseInt(jQuery('#talent_actu_' + idTalent).val());
    var newActuel = parseInt(actuel + 1);

    if (newActuel >= max) {
        newActuel = max;
    }

    if (window.location.href.includes('/gameplay/')) {
        url = "augmenterTalent";
    } else {
        url = "gameplay/augmenterTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idTalent: idTalent,
            newActuel: newActuel
        },
        success: function (data) {
            getListeTalentAdmin();
            var compteur = jQuery('#compteurArbre_' + idArbre).val();
            showDescriptionTalent = true;
            afficherDescriptionTalent(idTalent, compteur);
            //jQuery('#descriptionTalentRang').html("Rang " + newActuel + "/" + max);
            if (newActuel == max) {
                jQuery('#augmenterTalent_' + idArbre).hide();
            }
        }
    });
}

function accesEditerTalent(idTalent) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTalent";
    } else {
        url = "gameplay/detailTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idTalent
        },
        success: function (data) {
            jQuery('[name=hiddenMenuTalent').hide();
            if (jQuery('#popupTalent').is(':visible')) {
                jQuery('#popupTalent').html(data);
                initBoutonTalent();
            } else {
                afficherPopupTalent(data);
            }
        }
    });
}

function boxRetirerTalent(idTalent) {
    jQuery('#idTalentSelectDelet').val(idTalent);
    ouvreMsgBox("Supprimer ce talent peut avoir un impact sur l'intégralité du jeu et entraîner une redistribution des points de talent chez les joueurs en ayant déjà investi dans ce dernier.<br/> Confirmez-vous la suppression ?", "question", "ouinon", supprimerTalent, "");
}

function supprimerTalent() {
    var idTalent = jQuery('#idTalentSelectDelet').val();
    jQuery('[name=hiddenMenuTalent').hide();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerTalent";
    } else {
        url = "gameplay/supprimerTalent";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idTalent
        },
        success: function (data) {
            ouvreMsgBox("La suppression du talent est effective.", "info");
            jQuery("#grayBack").remove();
            setTimeout(function () {
                var idFamille = jQuery('#idFamilleSelect').val();
                chargerFamille(idFamille);
                cleanErreurTalent();
            }, 10);

        }
    });
}

function initListeTalentsAdmin() {
    //On récupère la famille
    var idFamille = jQuery('#idFamilleSelect').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "initListeTalentsAdmin";
    } else {
        url = "gameplay/initListeTalentsAdmin";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idFamille: idFamille
        },
        success: function (data) {
            listeTalentsAdmin = jQuery.parseJSON(data);
            updateTalent();
        }
    });
}

function updateTalent() {
    var listeArbre = [];
    var listeFamille = [];
    if (listeTalentsAdmin != null) {
        jQuery.each(listeTalentsAdmin, function (idTalent, talent) {
            if (listeArbre[talent.idArbre] === undefined) {
                listeArbre[talent.idArbre] = parseInt(0) + parseInt(talent.actuel);
            } else {
                listeArbre[talent.idArbre] = parseInt(listeArbre[talent.idArbre]) + parseInt(talent.actuel);
            }
            if (listeFamille[talent.idFamille] === undefined) {
                listeFamille[talent.idFamille] = parseInt(0) + parseInt(talent.actuel);
            } else {
                listeFamille[talent.idFamille] = parseInt(listeFamille[talent.idFamille]) + parseInt(talent.actuel);
            }
            if (talent.isActif == false) {
                jQuery('#imageNonActifTalent_' + idTalent).addClass("nonActifTalentImage");
            } else {
                jQuery('#imageNonActifTalent_' + idTalent).removeClass("nonActifTalentImage");
                if (talent.couleur == "gris") {
                    jQuery('#talent_' + idTalent).addClass("imgTalentGrise");
                    if (simulation == true) {
                        jQuery('#talent_' + idTalent).unbind('click');
                    }
                } else {
                    jQuery('#talent_' + idTalent).removeClass("imgTalentGrise");
                    if (simulation == true) {
                        jQuery('#talent_' + idTalent).click(function () {
                            augmenterTalent(idTalent, talent.idArbre);
                        });
                    }
                }
            }
            var ratio = talent.actuel + "/" + talent.max;
            if (talent.couleur == "gris") {
                jQuery('#talent_actu_' + idTalent).val(talent.actuel);
                jQuery('#talent_max_' + idTalent).val(talent.max);
                jQuery('#number_' + idTalent).css("color", "rgb(176,176,176)");
                jQuery('#number_' + idTalent).html("");
                jQuery('#spacer_' + idTalent).css('background-position', '0px 0px');
            } else if (talent.couleur == "vert") {
                jQuery('#talent_actu_' + idTalent).val(talent.actuel);
                jQuery('#talent_max_' + idTalent).val(talent.max);
                jQuery('#number_' + idTalent).css("color", "rgb(46, 255, 0)");
                jQuery('#number_' + idTalent).html(ratio);
                jQuery('#spacer_' + idTalent).css('background-position', '-56px 0px');
            } else if (talent.couleur == "jaune") {
                jQuery('#talent_actu_' + idTalent).val(talent.actuel);
                jQuery('#talent_max_' + idTalent).val(talent.max);
                jQuery('#number_' + idTalent).css("color", "rgb(255, 209, 0)");
                jQuery('#number_' + idTalent).html(ratio);
                jQuery('#spacer_' + idTalent).css('background-position', '-224px 0px');
            }
            if (!simulation) {
                jQuery('#talent_' + idTalent).draggable({
                    containment: '#arbre_' + talent.idArbre,
                    revert: 'invalid',
                    drag: function (event) {
                        idTalentDrag = event.target.id;
                    }
                });
            }
        });

        jQuery.each(listeArbre, function (idArbre, nbPoint) {
            jQuery("#nombreDePointDepense_" + idArbre).html(parseInt(nbPoint));
        });
        jQuery.each(listeFamille, function (idFamille, nbPoint) {
            jQuery("#nombreDePointDepenseFamille_" + idFamille).html("(" + parseInt(nbPoint) + ")");
        });

    }
    if (simulation == false) {
        $('.imgTalentMiniature').droppable({
            drop: function (event) {
                switchTalent(idTalentDrag, event.target.id);
                jQuery('[name=hiddenMenuTalent').hide();
            }
        });
    }
}

function getListeTalentAdmin() {
    if (window.location.href.includes('/gameplay/')) {
        url = "getListeTalentAdmin";
    } else {
        url = "gameplay/getListeTalentAdmin";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            listeTalentsAdmin = jQuery.parseJSON(data);
            setTimeout(function () {
                updateTalent();
                getGenealogieAdmin();
            }, 30);
        }
    });
}

function getGenealogieAdmin() {
    if (window.location.href.includes('/gameplay/')) {
        url = "getGenealogieAdmin";
    } else {
        url = "gameplay/getGenealogieAdmin";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            listeGenealogieAdmin = jQuery.parseJSON(data);
            setTimeout(function () {
                updateGenealogie();
            }, 100);
        }
    });
}

function updateGenealogie() {
    if (listeGenealogieAdmin != null) {
        jQuery.each(listeGenealogieAdmin, function (clef, element) {
            var posFils = jQuery('#talent_' + element.idTalent).position();
            var posPere = jQuery('#talent_' + element.idPere).position();
            var posArbre = jQuery('#arbre_' + element.idArbre).position();

            if (jQuery('#canvas_' + element.idArbre).get(0) !== undefined && jQuery('#canvas_' + element.idArbre)) {
                var canvas = jQuery('#canvas_' + element.idArbre).get(0);
                var context = canvas.getContext('2d');
                var startX = posPere.left;
                var startY = posPere.top * 150 / 440;
                var destX = posFils.left;
                var destY = posFils.top * 150 / 440;
                if (element.couleur == "jaune") {
                    var color = "#FFD100";
                } else if (element.couleur == "vert") {
                    var color = "#2EFF00";
                } else if (element.couleur == "gris") {
                    var color = "#B0B0B0";
                }
                draw_line(context, startX, startY, destX, destY, color);
            }
        });
    }
}

function switchTalent(idOrigine, idCible) {
    if (idOrigine == null) {
        ouvreMsgBox("Oups ! Une erreur s'est produite.", "error");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "switchTalent";
        } else {
            url = "gameplay/switchTalent";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idOrigine: idOrigine,
                idCible: idCible
            },
            success: function (data) {
                chargerFamille(data);
            }
        });
    }
}

function draw_line(context, startX, startY, destX, destY, color) {
    context.beginPath();
    context.moveTo(startX + 35, startY - 7);
    if (simulation) {
        context.lineTo(destX + 35, destY);
    } else {
        context.lineTo(destX + 35, destY - 20);
    }
    context.strokeStyle = color;
    context.lineWidth = 5;
    context.stroke();
}

function simulerArbre(type) {
    var idFamille = jQuery('#idFamilleSelect').val();
    if (type == "modifier") {
        simulation = false;
    } else {
        simulation = true;
    }
    chargerFamille(idFamille);
}

function afficherDescriptionTalent(idTalent, numArbre) {
    if (showDescriptionTalent) {
        var pos = jQuery('#talent_' + idTalent).position();
        if (window.location.href.includes('/gameplay/')) {
            url = "afficherDescriptionTalent";
        } else {
            url = "gameplay/afficherDescriptionTalent";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idTalent: idTalent
            },
            success: function (data) {
                showDescriptionTalent = false;
                var left = parseInt(pos.left) + parseInt(numArbre) * 300 + 60;
                jQuery('#divDescriptionTalent').html(data);
                jQuery('#divDescriptionTalent').show();
                jQuery('#divDescriptionTalent').css({top: pos.top + 15, left: parseInt(left)});
            }
        });
    }
}

function hideDescriptionTalent(idTalent) {
    showDescriptionTalent = true;
    jQuery('#divDescriptionTalent').hide();
}