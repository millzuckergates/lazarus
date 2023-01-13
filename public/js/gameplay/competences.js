function initBoutonCompetences() {
    jQuery('#boutonAjouterCompetence').click(function () {
        afficherAjouterCompetence();
    });
    initBoutonRang();
}

//############ Fonctions liées au Rang ############//
function initBoutonRang() {
    jQuery('#ajouterRangCompetence').click(function () {
        ajouterRangCompetence();
    });
}

function afficherPopupRangCompetence(data) {
    jQuery('#popupRangCompetence').html(data);
    // Ici on insère dans notre page html notre div gris
    jQuery("#popupRangCompetence").before('<div id="graybackRangCompetence"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupRangCompetence").height();
    var popupW = jQuery("#popupRangCompetence").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupRangCompetence").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupRangCompetence").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackRangCompetence").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupRangCompetence").fadeIn(500);
    });
    jQuery("#graybackRangCompetence").click(function () {
        fermerPopupRangCompetence();
    });
    cleanErreurRangCompetence();
    setTimeout(function () {
        initBoutonPopupRangCompetence();
    }, 10);
}

function fermerPopupRangCompetence() {
    jQuery("#graybackRangCompetence").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupRangCompetence").hide();
}

function initBoutonPopupRangCompetence() {
    jQuery('#editerRangCompetence').click(function () {
        editerRangCompetence();
    });
    jQuery('#creerRangCompetence').click(function () {
        creerRangCompetence();
    });
    jQuery('#modifierRangCompetence').click(function () {
        boxModifierRangCompetence();
    });
    jQuery('#supprimerRangCompetence').click(function () {
        boxSupprimerRang();
    });
    jQuery('#fermerPopupRangCompetence').click(function () {
        fermerPopupRangCompetence();
    });
    jQuery('#graybackRangCompetence').click(function () {
        fermerPopupRangCompetence();
    });
}

function ajouterRangCompetence() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailRangCompetence";
    } else {
        url = "gameplay/detailRangCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            if (jQuery('#popupRangCompetence').is(':visible')) {
                jQuery('#popupRangCompetence').html(data);
                initBoutonPopupRangCompetence();
            } else {
                afficherPopupRangCompetence(data);
            }
        }
    });
}

function ouvreEditionRang(idRang) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailRangCompetence";
    } else {
        url = "gameplay/detailRangCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idRang
        },
        success: function (data) {
            if (jQuery('#popupRangCompetence').is(':visible')) {
                jQuery('#popupRangCompetence').html(data);
                initBoutonPopupRangCompetence();
            } else {
                afficherPopupRangCompetence(data);
            }
        }
    });
}

function editerRangCompetence() {
    var idRang = jQuery('#idRangCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailRangCompetence";
    } else {
        url = "gameplay/detailRangCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idRang
        },
        success: function (data) {
            if (jQuery('#popupRangCompetence').is(':visible')) {
                jQuery('#popupRangCompetence').html(data);
                initBoutonPopupRangCompetence();
            } else {
                afficherPopupRangCompetence(data);
            }
        }
    });
}

function creerRangCompetence() {
    var nom = jQuery('#nomRangCompetence').val();
    var description = jQuery('#descriptionRangCompetence').val();
    var point = jQuery('#pointRangCompetence').val();
    var niveau = jQuery('#niveauRangCompetence').val();
    var pointCompare = jQuery('#pointReference').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nom. \n";
        jQuery('#nomRangCompetence').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionRangCompetence').addClass("erreur");
    }
    var pointSansEspace = point.replace(/\s/g, "");
    if (pointSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une nombre de point à atteindre. \n";
        jQuery('#pointRangCompetence').addClass("erreur");
    } else if (isNaN(point)) {
        erreur = erreur + "Le nombre de point à atteindre doit être un entier. \n";
        jQuery('#pointRangCompetence').addClass("erreur");
    } else if (parseInt(point) <= parseInt(pointCompare)) {
        erreur = erreur + "Le nombre de point à atteindre doit être supérieur à celui du rang précédent. \n";
        jQuery('#pointRangCompetence').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerRangCompetence";
        } else {
            url = "gameplay/creerRangCompetence";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                point: point,
                niveau: niveau
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    afficherPopupRang(data);
                    refreshListeRang();
                }
            }
        });
    }
}

function boxModifierRangCompetence() {
    ouvreMsgBox("Modifier ce rang peut avoir un impact sur l'intégralité du jeu. Notemment sur les personnages et les compétences. Confirmez votre action ?", "question", "ouinon", modifierRangCompetence, "");
}

function modifierRangCompetence() {
    var nom = jQuery('#nomRangCompetence').val();
    var description = jQuery('#descriptionRangCompetence').val();
    var point = jQuery('#pointRangCompetence').val();
    var idRang = jQuery('#idRangCompetence').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nom. \n";
        jQuery('#nomRangCompetence').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionRangCompetence').addClass("erreur");
    }
    var pointSansEspace = point.replace(/\s/g, "");
    if (pointSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une nombre de point à atteindre. \n";
        jQuery('#pointRangCompetence').addClass("erreur");
    } else if (isNaN(point)) {
        erreur = erreur + "Le nombre de point à atteindre doit être un entier. \n";
        jQuery('#pointRangCompetence').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierRangCompetence";
        } else {
            url = "gameplay/modifierRangCompetence";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                point: point,
                id: idRang
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else if (data == "errorPointAAtteindre") {
                    ouvreMsgBox("Le nombre de point à atteindre ne correspond pas à la position donnée. Il doit se trouver entre celui du rang supérieur et celui du rang inférieur.", "erreur");
                } else {
                    ouvreMsgBox("Les modifications ont bien été prises en compte." + data, "info");
                    refreshListeRang();
                }
            }
        });
    }
}

function refreshListeRang() {
    if (window.location.href.includes('/gameplay/')) {
        url = "refreshListeRang";
    } else {
        url = "gameplay/refreshListeRang";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            jQuery('#divListeRangCompetence').html(data);
            initBoutonRang();
        }
    });
}

function afficherPopupRang(idRang) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailRangCompetence";
    } else {
        url = "gameplay/detailRangCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idRang
        },
        success: function (data) {
            if (jQuery('#popupRangCompetence').is(':visible')) {
                jQuery('#popupRangCompetence').html(data);
                initBoutonPopupRangCompetence();
            } else {
                afficherPopupRangCompetence(data);
            }
        }
    });
}

function boxSupprimerRang() {
    ouvreMsgBox("Supprimer ce rang peut avoir un impact sur l'intégralité du jeu. Notemment sur les personnages et les compétences. Confirmez votre action ?", "question", "ouinon", supprimerRangCompetence, "");
}

function supprimerRangCompetence() {
    var idRang = jQuery('#idRangCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerRangCompetence";
    } else {
        url = "gameplay/supprimerRangCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idRang
        },
        success: function (data) {
            if (data == "errorUse") {
                ouvreMsgBox("Impossible de supprimer ce rang, il est utilisé.", "erreur");
            } else {
                fermerPopupRangCompetence();
                ouvreMsgBox("Le rang a correctement été supprimé.", "info");
                refreshListeRang();
            }
        }
    });
}

function cleanErreurRangCompetence() {
    if (jQuery('#nomRangCompetence').hasClass("erreur")) {
        jQuery('#nomRangCompetence').removeClass("erreur");
    }
    if (jQuery('#descriptionRangCompetence').hasClass("erreur")) {
        jQuery('#descriptionRangCompetence').removeClass("erreur");
    }
    if (jQuery('#pointRangCompetence').hasClass("erreur")) {
        jQuery('#pointRangCompetence').removeClass("erreur");
    }
}

//############ Fonctions liées aux Competences ############//
function initBoutonPopupCompetence() {
    jQuery('#chargerNewImageCompetence').click(function () {
        chargerNewImageCompetence();
    });
    jQuery('#creerCompetence').click(function () {
        creerCompetence();
    });
    jQuery('#editerCompetence').click(function () {
        editerCompetence();
    });
    jQuery('#graybackCompetence').click(function () {
        fermerPopupCompetence();
    });
    jQuery('#fermerPopupCompetence').click(function () {
        fermerPopupCompetence();
    });
    jQuery('#modifierCompetence').click(function () {
        modifierCompetence();
    });
    jQuery('#showlisteEvolutionCompetence').click(function () {
        showlisteEvolutionCompetence();
    });
    jQuery('#hidelisteEvolutionCompetence').click(function () {
        hidelisteEvolutionCompetence();
    });
    jQuery('#hidelisteEvolutionCompetence').hide();
    jQuery('#showlisteEffetCompetence').click(function () {
        showlisteEffetCompetence();
    });
    jQuery('#hidelisteEffetCompetence').click(function () {
        hidelisteEffetCompetence();
    });
    jQuery('#hidelisteEffetCompetence').hide();
    jQuery('#showlisteContraintesCompetence').click(function () {
        showlisteContraintesCompetence();
    });
    jQuery('#hidelisteContraintesCompetence').click(function () {
        hidelisteContraintesCompetence();
    });
    jQuery('#hidelisteContraintesCompetence').hide();
}

function afficherPopupCompetence(data) {
    jQuery('#popupCompetence').html(data);
    // Ici on insère dans notre page html notre div gris
    jQuery("#popupCompetence").before('<div id="graybackCompetence"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupCompetence").height();
    var popupW = jQuery("#popupCompetence").width();

    // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupCompetence").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupCompetence").css("margin-left", "-" + popupW / 2 + "px");

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#graybackCompetence").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#popupCompetence").fadeIn(500);
    });
    jQuery("#graybackCompetence").click(function () {
        fermerPopupCompetence();
    });
    cleanErreurCompetence();
    setTimeout(function () {
        initBoutonPopupCompetence();
    }, 10);
}

function fermerPopupCompetence() {
    jQuery("#graybackCompetence").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupCompetence").hide();
}

function afficherAjouterCompetence() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCompetence";
    } else {
        url = "gameplay/detailCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            if (jQuery('#popupCompetence').is(':visible')) {
                jQuery('#popupCompetence').html(data);
                initBoutonPopupCompetence();
            } else {
                afficherPopupCompetence(data);
            }
        }
    });
}

function chargerPopupCompetence(idCompetence) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCompetence";
    } else {
        url = "gameplay/detailCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idCompetence
        },
        success: function (data) {
            if (jQuery('#popupCompetence').is(':visible')) {
                jQuery('#popupCompetence').html(data);
                initBoutonPopupCompetence();
            } else {
                afficherPopupCompetence(data);
            }
        }
    });
}

function editerCompetence() {
    var id = jQuery('#idCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCompetence";
    } else {
        url = "gameplay/detailCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: id
        },
        success: function (data) {
            if (jQuery('#popupCompetence').is(':visible')) {
                jQuery('#popupCompetence').html(data);
                initBoutonPopupCompetence();
            } else {
                afficherPopupCompetence(data);
            }
        }
    });
}

function chargerNewImageCompetence() {
    var id = jQuery('#idCompetence').val();
    var type = "competence";
    var urlFile = jQuery('#newImageCompetence').val();

    //Verification de l'url
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

function gererBlocActif() {
    var isActif = jQuery('#isCompetenceActivable').is(':checked');
    if (isActif == true) {
        jQuery('#divBlocActif').show();
    } else {
        jQuery('#divBlocActif').hide();
    }
}

function creerCompetence() {
    var nom = jQuery('#nomCompetence').val();
    var description = jQuery('#descriptionCompetence').val();
    var image = jQuery('#listeImage').val();
    var type = jQuery('#typeCompetence').val();
    var isDispo = jQuery('#isDispoCompetence').is(':checked');
    var isActivable = jQuery('#isCompetenceActivable').is(':checked');
    var messageRP = jQuery('#messageRPCompetence').val();
    var eventLanceur = jQuery('#eventLanceurCompetence').val();
    var eventGlobal = jQuery('#eventGlobalCompetence').val();
    var coutPA = jQuery('#coutPACompetence').val();
    var isEntrainable = jQuery('#isEntrainableCompetence').is(':checked');
    var isEnseignable = jQuery('#isEnseignableCompetence').is(':checked');
    var idRangAutonomie = jQuery('#rangAutonomie').val();
    var idRangBloque = jQuery('#rangBloque').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nom. \n";
        jQuery('#nomCompetence').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCompetence').addClass("erreur");
    }

    if (isActivable == true) {
        isActivable = 1;
        var messageRPSansEspace = messageRP.replace(/\s/g, "");
        if (messageRPSansEspace == "") {
            erreur = erreur + "Vous devez renseigner un message qui s'affichera lors du lancement de la compétence par le joueur. \n";
            jQuery('#messageRPCompetence').addClass("erreur");
        }
        var eventLanceurSansEspace = eventLanceur.replace(/\s/g, "");
        if (eventLanceurSansEspace == "") {
            erreur = erreur + "Vous devez renseigner l'évenement que le joueur verra. \n";
            jQuery('#eventLanceurCompetence').addClass("erreur");
        }
        var eventGlobalSansEspace = eventGlobal.replace(/\s/g, "");
        if (eventGlobalSansEspace == "") {
            erreur = erreur + "Vous devez renseigner l'évenement que les observateurs verront. \n";
            jQuery('#eventGlobalCompetence').addClass("erreur");
        }
        var coutPASansEspace = coutPA.replace(/\s/g, "");
        if (coutPASansEspace == "") {
            erreur = erreur + "Vous devez renseigner un nombre de PA à dépenser pour utiliser cette compétence. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        } else if (isNaN(coutPA)) {
            erreur = erreur + "Le cout en PA doit être un entier. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        } else if (parseInt(coutPA) <= 0) {
            erreur = erreur + "Le cout en PA doit être un entier position. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        }

        if (isEntrainable == true) {
            isEntrainable = 1;
        } else {
            isEntrainable = 0;
        }
    } else {
        isActivable = 0;
        isEntrainable = 0;
        messageRP = null;
        eventLanceur = null;
        eventGlobal = null;
        coutPA = null;
    }

    if (isDispo == true) {
        isDispo = 1;
    } else {
        isDispo = 0;
    }

    if (isEnseignable == true) {
        isEnseignable = 1;
    } else {
        isEnseignable = 0;
    }

    if (parseInt(idRangAutonomie) == 0) {
        idRangAutonomie = null;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerCompetence";
        } else {
            url = "gameplay/creerCompetence";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                type: type,
                isActif: isDispo,
                isActivable: isActivable,
                messageRP: messageRP,
                eventLanceur: eventLanceur,
                eventGlobal: eventGlobal,
                coutPA: coutPA,
                isEntrainable: isEntrainable,
                isEnseignable: isEnseignable,
                idRangAutonomie: idRangAutonomie,
                idRangBloque: idRangBloque
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("La création a été correctement réalisée.", "info");
                    chargerPopupCompetence(data);
                    cleanErreurCompetence();
                    refreshListeCompetence();
                }
            }
        });
    }
}

function modifierCompetence() {
    var idCompetence = jQuery('#idCompetence').val();
    var nom = jQuery('#nomCompetence').val();
    var description = jQuery('#descriptionCompetence').val();
    var image = jQuery('#listeImage').val();
    var type = jQuery('#typeCompetence').val();
    var isDispo = jQuery('#isDispoCompetence').is(':checked');
    var isActivable = jQuery('#isCompetenceActivable').is(':checked');
    var messageRP = jQuery('#messageRPCompetence').val();
    var eventLanceur = jQuery('#eventLanceurCompetence').val();
    var eventGlobal = jQuery('#eventGlobalCompetence').val();
    var coutPA = jQuery('#coutPACompetence').val();
    var isEntrainable = jQuery('#isEntrainableCompetence').is(':checked');
    var isEnseignable = jQuery('#isEnseignableCompetence').is(':checked');
    var idRangAutonomie = jQuery('#rangAutonomie').val();
    var idRangBloque = jQuery('#rangBloque').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = erreur + "Vous devez renseigner un nom. \n";
        jQuery('#nomCompetence').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCompetence').addClass("erreur");
    }

    if (isActivable == true) {
        isActivable = 1;
        var messageRPSansEspace = messageRP.replace(/\s/g, "");
        if (messageRPSansEspace == "") {
            erreur = erreur + "Vous devez renseigner un message qui s'affichera lors du lancement de la compétence par le joueur. \n";
            jQuery('#messageRPCompetence').addClass("erreur");
        }
        var eventLanceurSansEspace = eventLanceur.replace(/\s/g, "");
        if (eventLanceurSansEspace == "") {
            erreur = erreur + "Vous devez renseigner l'évenement que le joueur verra. \n";
            jQuery('#eventLanceurCompetence').addClass("erreur");
        }
        var eventGlobalSansEspace = eventGlobal.replace(/\s/g, "");
        if (eventGlobalSansEspace == "") {
            erreur = erreur + "Vous devez renseigner l'évenement que les observateurs verront. \n";
            jQuery('#eventGlobalCompetence').addClass("erreur");
        }
        var coutPASansEspace = coutPA.replace(/\s/g, "");
        if (coutPASansEspace == "") {
            erreur = erreur + "Vous devez renseigner un nombre de PA à dépenser pour utiliser cette compétence. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        } else if (isNaN(coutPA)) {
            erreur = erreur + "Le cout en PA doit être un entier. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        } else if (parseInt(coutPA) <= 0) {
            erreur = erreur + "Le cout en PA doit être un entier position. \n";
            jQuery('#coutPACompetence').addClass("erreur");
        }

        if (isEntrainable == true) {
            isEntrainable = 1;
        } else {
            isEntrainable = 0;
        }
    } else {
        isActivable = 0;
        isEntrainable = 0;
        messageRP = null;
        eventLanceur = null;
        eventGlobal = null;
        coutPA = null;
    }

    if (isDispo == true) {
        isDispo = 1;
    } else {
        isDispo = 0;
    }

    if (isEnseignable == true) {
        isEnseignable = 1;
    } else {
        isEnseignable = 0;
    }

    if (parseInt(idRangAutonomie) == 0) {
        idRangAutonomie = null;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierCompetence";
        } else {
            url = "gameplay/modifierCompetence";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                type: type,
                isActif: isDispo,
                isActivable: isActivable,
                messageRP: messageRP,
                eventLanceur: eventLanceur,
                eventGlobal: eventGlobal,
                coutPA: coutPA,
                isEntrainable: isEntrainable,
                isEnseignable: isEnseignable,
                idRangAutonomie: idRangAutonomie,
                idCompetence: idCompetence,
                idRangBloque: idRangBloque
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else {
                    ouvreMsgBox("La modification a été correctement réalisée." + data, "info");
                    refreshListeCompetence();
                    cleanErreurCompetence();
                }
            }
        });
    }
}

function cleanErreurCompetence() {
    if (jQuery('#nomCompetence').hasClass("erreur")) {
        jQuery('#nomCompetence').removeClass("erreur");
    }
    if (jQuery('#descriptionCompetence').hasClass("erreur")) {
        jQuery('#descriptionCompetence').removeClass("erreur");
    }
    if (jQuery('#messageRPCompetence').hasClass("erreur")) {
        jQuery('#messageRPCompetence').removeClass("erreur");
    }
    if (jQuery('#eventLanceurCompetence').hasClass("erreur")) {
        jQuery('#eventLanceurCompetence').removeClass("erreur");
    }
    if (jQuery('#eventGlobalCompetence').hasClass("erreur")) {
        jQuery('#eventGlobalCompetence').removeClass("erreur");
    }
    if (jQuery('#coutPACompetence').hasClass("erreur")) {
        jQuery('#coutPACompetence').removeClass("erreur");
    }
}

function refreshListeCompetence() {
    if (window.location.href.includes('/gameplay/')) {
        url = "refreshListeCompetence";
    } else {
        url = "gameplay/refreshListeCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            jQuery('#divBlocListeCompetence').html(data);
        }
    });
}

function showlisteEvolutionCompetence() {
    var idCompetence = jQuery('#idCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEvolutionCompetence";
    } else {
        url = "gameplay/showlisteEvolutionCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCompetence,
            type: 'competence'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEvolutionCompetence').hide();
            jQuery('#hidelisteEvolutionCompetence').show();
            jQuery('#showlisteEffetCompetence').show();
            jQuery('#hidelisteEffetCompetence').hide();
            jQuery('#showlisteContraintesCompetence').show();
            jQuery('#hidelisteContraintesCompetence').hide();
            initBoutonEvolution();
        }
    });
}

function hidelisteEvolutionCompetence() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEvolutionCompetence').show();
    jQuery('#hidelisteEvolutionCompetence').hide();
    jQuery('#showlisteEffetCompetence').show();
    jQuery('#hidelisteEffetCompetence').hide();
    jQuery('#showlisteContraintesCompetence').show();
    jQuery('#hidelisteContraintesCompetence').hide();
}

function showlisteEffetCompetence() {
    var idCompetence = jQuery('#idCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEffetCompetence";
    } else {
        url = "gameplay/showlisteEffetCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCompetence,
            type: 'competence'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEvolutionCompetence').show();
            jQuery('#hidelisteEvolutionCompetence').hide();
            jQuery('#showlisteEffetCompetence').hide();
            jQuery('#hidelisteEffetCompetence').show();
            jQuery('#showlisteContraintesCompetence').show();
            jQuery('#hidelisteContraintesCompetence').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteEffetCompetence() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEvolutionCompetence').show();
    jQuery('#hidelisteEvolutionCompetence').hide();
    jQuery('#showlisteEffetCompetence').show();
    jQuery('#hidelisteEffetCompetence').hide();
    jQuery('#showlisteContraintesCompetence').show();
    jQuery('#hidelisteContraintesCompetence').hide();
}

function showlisteContraintesCompetence() {
    var idCompetence = jQuery('#idCompetence').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteContraintesCompetence";
    } else {
        url = "gameplay/showlisteContraintesCompetence";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCompetence,
            type: 'competence'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEvolutionCompetence').show();
            jQuery('#hidelisteEvolutionCompetence').hide();
            jQuery('#showlisteEffetCompetence').show();
            jQuery('#hidelisteEffetCompetence').hide();
            jQuery('#showlisteContraintesCompetence').hide();
            jQuery('#hidelisteContraintesCompetence').show();
            initBoutonContrainte();
        }
    });
}

function hidelisteContraintesCompetence() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEvolutionCompetence').show();
    jQuery('#hidelisteEvolutionCompetence').hide();
    jQuery('#showlisteEffetCompetence').show();
    jQuery('#hidelisteEffetCompetence').hide();
    jQuery('#showlisteContraintesCompetence').show();
    jQuery('#hidelisteContraintesCompetence').hide();
}

function afficherSpecificiteCompetence(texte, idCompetence) {
    var pos = jQuery('#competence_' + idCompetence).position();
    jQuery('#divSpecificiteCompetence').html(texte);
    jQuery('#divSpecificiteCompetence').show();
    jQuery('#divSpecificiteCompetence').css({top: pos.top + 5, left: pos.left + 80});
}

function hideSpecificiteCompetence() {
    jQuery('#divSpecificiteCompetence').hide();
}

function changerImageCompetence() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageCompetence").attr("src", src);
}

function initBoutonEvolution() {
    jQuery('#modifierEvolutionCompetence').click(function () {
        modifierEvolutionCompetence();
    });
}

function modifierEvolutionCompetence() {
    var idComp = jQuery('#idCompetence').val();
    var idRangBloque = jQuery('#rangBloque').val();
    var listeIdRang = jQuery('#listeIdRang').val();
    var listeIdCarac = jQuery('#listeIdCarac').val();

    var tabIdRang = listeIdRang.split(",");
    var tabIdCarac = listeIdCarac.split(",");

    var error = "";
    var listeRang = "";
    for (var i = 0; i < tabIdRang.length - 1; i++) {
        var idRang = tabIdRang[i];
        var value = jQuery('#point_' + idRang).val();
        if (isNaN(value)) {
            error = error + "Une des valeurs pour les rangs est incorrect. Il doit s'agir d'une valeur entière positive.\n";
        }
        listeRang = listeRang + idRang + "_" + value + "@";
    }

    var listeCarac = "";
    for (var i = 0; i < tabIdCarac.length - 1; i++) {
        var idCarac = tabIdCarac[i];
        var value = jQuery('#carac_' + idCarac).val();
        if (isNaN(value)) {
            error = error + "Une des valeurs pour les caractéristiques est incorrect. Il doit s'agir d'une valeur numérique.\n";
        }
        var influence = jQuery('#influenceur_' + idCarac).is(':checked');
        if (influence == true) {
            influence = 1;
        } else {
            influence = 0;
        }
        listeCarac = listeCarac + idCarac + "_" + value + "_" + influence + "@";
    }

    if (error != "") {
        ouvreMsgBox(error, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierEvolutionCompetence";
        } else {
            url = "gameplay/modifierEvolutionCompetence";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: idComp,
                idRangBloque: idRangBloque,
                listeRang: listeRang,
                listeCarac: listeCarac
            },
            success: function (data) {
                if (data == "errorZero") {
                    ouvreMsgBox('Le premier rang doit avoir une valeur de zéro.', 'erreur');
                } else if (data == "errorHierarchie") {
                    ouvreMsgBox("Les rangs inférieurs ne doivent pas avoir une valeur plus importante qu'un rang supérieur.", "erreur");
                } else if (data == "erreurCalcul") {
                    ouvreMsgBox("La somme des modificateurs pour les caractéristiques doit êre égale à zéro.", "erreur");
                } else if (data == "errorVide") {
                    ouvreMsgBox("Les rangs et caractéristiques ne peuvent pas avoir de valeur vide.", "erreur");
                } else if (data == "error") {
                    ouvreMsgBox("Une erreur s'est produite.", "erreur");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en compte.");
                    refreshListeCompetence();
                }
            }
        });
    }

}