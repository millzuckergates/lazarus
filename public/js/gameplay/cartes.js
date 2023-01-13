function initBoutonCarte() {
    jQuery('#ajouterCarte').click(function () {
        ajouterCarte();
    });
    jQuery('#lancerRechercheSaisonCarte').click(function () {
        filtrerCarte();
    });
    jQuery('#showListeCarteSaisonToutes').click(function () {
        showListeCarteSaisonToutes();
    });
    jQuery('#hideListeCarteSaisonToutes').click(function () {
        hideListeCarteSaisonToutes();
    });
    jQuery('#hideListeCarteSaisonToutes').hide();
    jQuery('#showListeCarteSaisonHiver').click(function () {
        showListeCarteSaisonHiver();
    });
    jQuery('#hideListeCarteSaisonHiver').click(function () {
        hideListeCarteSaisonHiver();
    });
    jQuery('#hideListeCarteSaisonHiver').hide();
    jQuery('#showListeCarteSaisonPrintemps').click(function () {
        showListeCarteSaisonPrintemps();
    });
    jQuery('#hideListeCarteSaisonPrintemps').click(function () {
        hideListeCarteSaisonPrintemps();
    });
    jQuery('#hideListeCarteSaisonPrintemps').hide();
    jQuery('#showListeCarteSaisonEte').click(function () {
        showListeCarteSaisonEte();
    });
    jQuery('#hideListeCarteSaisonEte').click(function () {
        hideListeCarteSaisonEte();
    });
    jQuery('#hideListeCarteSaisonEte').hide();
    jQuery('#showListeCarteSaisonAutomne').click(function () {
        showListeCarteSaisonAutomne();
    });
    jQuery('#hideListeCarteSaisonAutomne').click(function () {
        hideListeCarteSaisonAutomne();
    });
    jQuery('#hideListeCarteSaisonAutomne').hide();
    jQuery('#editerCarte').click(function () {
        editerCarte();
    });
    jQuery('#retourListeCarte').click(function () {
        retourListeCarte();
    });
    jQuery('#annulerEditerCarte').click(function () {
        annulerEditerCarte();
    });
    jQuery('#chargerNewImageCarte').click(function () {
        chargerNewImageCarte();
    });
    jQuery('#chargerNewImageCartePJ').click(function () {
        chargerNewImageCartePJ();
    });
    jQuery('#showlisteEffetsCartes').click(function () {
        showlisteEffetsCartes();
    });
    jQuery('#hidelisteEffetsCartes').click(function () {
        hidelisteEffetsCartes();
    });
    jQuery('#hidelisteEffetsCartes').hide();
    jQuery('#showlisteStatistiqueCartes').click(function () {
        showlisteStatistiqueCartes();
    });
    jQuery('#hidelisteStatistiqueCartes').click(function () {
        hidelisteStatistiqueCartes();
    });
    jQuery('#hidelisteStatistiqueCartes').hide();
    jQuery('#modifierCarte').click(function () {
        modifierCarte();
    });
    jQuery('#boutonChargerCarte').click(function () {
        boutonChargerCarte('first', 0);
    });
    jQuery('#creerCarte').click(function () {
        creerCarte();
    });
}

function ajouterCarte() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarte";
    } else {
        url = "gameplay/detailCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarte();
        }
    });
}

function editerCarte() {
    var idCarte = jQuery('#idCarte').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarte";
    } else {
        url = "gameplay/detailCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCarte
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarte();
        }
    });
}

function retourListeCarte() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarte";
    } else {
        url = "gameplay/detailCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarte();
        }
    });
}

function afficherDetailCarte(idCarte) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarte";
    } else {
        url = "gameplay/detailCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idCarte
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonCarte();
        }
    });
}

function annulerEditerCarte() {
    var idCarte = jQuery('#idCarte').val();
    afficherDetailCarte(idCarte);
}

function filtrerCarte() {
    var nom = jQuery('#searchNom').val();
    var saison = jQuery('#selectSearchSaison').val();
    var type = jQuery('#selectSearchType').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "filtrerCarte";
    } else {
        url = "gameplay/filtrerCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            nom: nom,
            saison: saison,
            type: type
        },
        success: function (data) {
            var identifiant = data.substring(0, 2);
            if (identifiant == "id") {
                data.replace('id', '');
                afficherDetailCarte(data);
            } else {
                jQuery('#divListeCartes').html(data);
            }
        }
    });
}


function showListeCarteSaisonToutes() {
    jQuery('#showListeCarteSaisonToutes').hide();
    jQuery('#hideListeCarteSaisonToutes').show();
    jQuery('#divTableCartesSaisonToutes').slideToggle();
}

function hideListeCarteSaisonToutes() {
    jQuery('#showListeCarteSaisonToutes').show();
    jQuery('#hideListeCarteSaisonToutes').hide();
    jQuery('#divTableCartesSaisonToutes').slideToggle();
}

function showListeCarteSaisonHiver() {
    jQuery('#showListeCarteSaisonHiver').hide();
    jQuery('#hideListeCarteSaisonHiver').show();
    jQuery('#divTableCartesSaisonHiver').slideToggle();
}

function hideListeCarteSaisonHiver() {
    jQuery('#showListeCarteSaisonHiver').show();
    jQuery('#hideListeCarteSaisonHiver').hide();
    jQuery('#divTableCartesSaisonHiver').slideToggle();
}

function showListeCarteSaisonPrintemps() {
    jQuery('#showListeCarteSaisonPrintemps').hide();
    jQuery('#hideListeCarteSaisonPrintemps').show();
    jQuery('#divTableCartesSaisonPrintemps').slideToggle();
}

function hideListeCarteSaisonPrintemps() {
    jQuery('#showListeCarteSaisonPrintemps').show();
    jQuery('#hideListeCarteSaisonPrintemps').hide();
    jQuery('#divTableCartesSaisonPrintemps').slideToggle();
}

function showListeCarteSaisonEte() {
    jQuery('#showListeCarteSaisonEte').hide();
    jQuery('#hideListeCarteSaisonEte').show();
    jQuery('#divTableCartesSaisonEte').slideToggle();
}

function hideListeCarteSaisonEte() {
    jQuery('#showListeCarteSaisonEte').show();
    jQuery('#hideListeCarteSaisonEte').hide();
    jQuery('#divTableCartesSaisonEte').slideToggle();
}

function showListeCarteSaisonAutomne() {
    jQuery('#showListeCarteSaisonAutomne').hide();
    jQuery('#hideListeCarteSaisonAutomne').show();
    jQuery('#divTableCartesSaisonAutomne').slideToggle();
}

function hideListeCarteSaisonAutomne() {
    jQuery('#showListeCarteSaisonAutomne').show();
    jQuery('#hideListeCarteSaisonAutomne').hide();
    jQuery('#divTableCartesSaisonAutomne').slideToggle();
}

function chargerNewImageCarte() {
    var id = jQuery('#idCarte').val();
    var type = "carte";
    var urlFile = jQuery('#newImageCarte').val();

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

function chargerNewImageCartePJ() {
    var id = jQuery('#idCarte').val();
    var type = "cartePJ";
    var urlFile = jQuery('#newImageCartePJ').val();

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
                    jQuery('#listeImagePJ').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function showlisteEffetsCartes() {
    var idCarte = jQuery('#idCarte').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEffetsCartes";
    } else {
        url = "gameplay/showlisteEffetsCartes";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCarte
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsCartes').hide();
            jQuery('#hidelisteEffetsCartes').show();
            jQuery('#showlisteStatistiqueCartes').show();
            jQuery('#hidelisteStatistiqueCartes').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteEffetsCartes() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsCartes').show();
    jQuery('#hidelisteEffetsCartes').hide();
    jQuery('#showlisteStatistiqueCartes').show();
    jQuery('#hidelisteStatistiqueCartes').hide();
}

function showlisteStatistiqueCartes() {
    var idCarte = jQuery('#idCarte').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteStatistiqueCartes";
    } else {
        url = "gameplay/showlisteStatistiqueCartes";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idCarte
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsCartes').show();
            jQuery('#hidelisteEffetsCartes').hide();
            jQuery('#showlisteStatistiqueCartes').hide();
            jQuery('#hidelisteStatistiqueCartes').show();
            initBoutonEffet();
        }
    });
}

function hidelisteStatistiqueCartes() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsCartes').show();
    jQuery('#hidelisteEffetsCartes').hide();
    jQuery('#showlisteStatistiqueCartes').show();
    jQuery('#hidelisteStatistiqueCartes').hide();
}

function formCalculCoordoonneeCarteX(coordonneeModifiee) {
    var valeur = jQuery('#' + coordonneeModifiee).val();
    if (valeur == "") {
        valeur = 0;
        jQuery('#' + coordonneeModifiee).val(0);
    }
    // On verifie qu'il s'agit bien d'un entier
    if (isNaN(valeur)) {
        ouvreMsgBox("Entrez une valeur numérique.", "erreur");
    } else {
        var largeur = parseFloat(jQuery("#largeurCarte").val());
        if (coordonneeModifiee == "xRef") {
            // Recalcule du xMin et du xMax
            if (largeur % 2 == 1) {
                var varianteMin = (largeur + 1) / 2;
                var varianteMax = (largeur - 1) / 2;
                var xmin = parseFloat(valeur) - parseFloat(varianteMin) + 1;
                var xmax = parseFloat(valeur) + parseFloat(varianteMax);
                jQuery('#xMin').val(xmin);
                jQuery('#xMax').val(xmax);
            } else {
                var variante = largeur / 2;
                var xmin = parseFloat(valeur) - parseFloat(variante);
                var xmax = parseFloat(valeur) + parseFloat(variante) - 1;
                jQuery('#xMin').val(xmin);
                jQuery('#xMax').val(xmax)
            }
        } else if (coordonneeModifiee == "xMin") {
            // Recalcule du xRef et du xMax
            if (largeur % 2 == 1) {
                var varianteRef = (largeur - 1) / 2;
                var xref = parseFloat(valeur) + parseFloat(varianteRef);
                var xmax = parseFloat(valeur) + parseFloat(largeur) - 1;
                jQuery('#xRef').val(xref);
                jQuery('#xMax').val(xmax);
            } else {
                var variante = largeur / 2;
                var xref = parseFloat(valeur) + parseFloat(variante);
                var xmax = parseFloat(valeur) + parseFloat(largeur) - 1;
                jQuery('#xMax').val(xmax);
                jQuery('#xRef').val(xref);
            }
        } else if (coordonneeModifiee == "xMax") {
            // Recalcule du xRef et du xMin
            if (largeur % 2 == 1) {
                var varianteRef = (largeur - 1) / 2;
                var xref = parseFloat(valeur) - parseFloat(varianteRef);
                var xmin = parseFloat(valeur) - parseFloat(largeur) + 1;
                jQuery('#xRef').val(xref);
                jQuery('#xMin').val(xmin);
            } else {
                var variante = largeur / 2;
                var xref = parseFloat(valeur) - parseFloat(variante) + 1;
                var xmax = parseFloat(valeur) - parseFloat(largeur) + 1;
                jQuery('#xMin').val(xmin);
                jQuery('#xRef').val(xref);
            }
        }
    }
}

function formCalculCoordoonneeCarteY(coordonneeModifiee) {
    var valeur = jQuery('#' + coordonneeModifiee).val();
    if (valeur == "") {
        valeur = 0;
        jQuery('#' + coordonneeModifiee).val(0);
    }
    // On verifie qu'il s'agit bien d'un entier
    if (isNaN(valeur)) {
        ouvreMsgBox("Entrez une valeur numérique.", erreur);
    } else {
        var hauteur = parseFloat(jQuery('#hauteurCarte').val());
        if (coordonneeModifiee == "yRef") {
            // Recalcule du yMin et du yMax
            if (hauteur % 2 == 1) {
                var variante = (hauteur - 1) / 2;
                var ymin = parseFloat(valeur) - parseFloat(variante);
                var ymax = parseFloat(valeur) + parseFloat(variante);
                jQuery('#yMin').val(ymin);
                jQuery('#yMax').val(ymax);
            } else {
                var variante = hauteur / 2;
                var ymin = parseFloat(valeur) - parseFloat(variante) + 1;
                var ymax = parseFloat(valeur) + parseFloat(variante);
                jQuery('#yMin').val(ymin);
                jQuery('#yMax').val(ymax);
            }
        } else if (coordonneeModifiee == "yMin") {
            // Recalcule du yRef et du yMax
            if (hauteur % 2 == 1) {
                var varianteRef = (hauteur - 1) / 2;
                var yref = parseFloat(valeur) + parseFloat(varianteRef);
                var ymax = parseFloat(valeur) + parseFloat(hauteur) - 1;
                jQuery('#yRef').val(yref);
                jQuery('#yMax').val(ymax);
            } else {
                var variante = hauteur / 2;
                var yref = parseFloat(valeur) + parseFloat(variante) - 1;
                var ymax = parseFloat(valeur) + parseFloat(hauteur) - 1;
                jQuery('#yMax').val(ymax);
                jQuery('#yRef').val(yref);
            }
        } else if (coordonneeModifiee == "yMax") {
            // Recalcule du yRef et du yMin
            if (hauteur % 2 == 1) {
                var varianteRef = (hauteur - 1) / 2;
                var yref = parseFloat(valeur) - parseFloat(varianteRef);
                var ymin = parseFloat(valeur) - parseFloat(hauteur) + 1;
                jQuery('#yRef').val(yref);
                jQuery('#yMin').val(ymin);
            } else {
                var variante = hauteur / 2;
                var yref = parseFloat(valeur) - parseFloat(variante);
                var ymax = parseFloat(valeur) - parseFloat(hauteur) + 1;
                jQuery('#yMin').val(ymin);
                jQuery('#yRef').val(yref);
            }
        }
    }
}

function modifierCarte() {
    ouvreMsgBox("Attention, si vous avez modifié les coordonnées de la carte, l'existante sera effacée pour être recrée. Les personnages présent sur la carte seront mis en téléportation, les créatures et bâtiments effacés. Etes vous sûr de vouloir continuer ?", "question", "ouinon", modifierCarteOK, "");
}

function modifierCarteOK() {
    var nom = jQuery('#nomCarte').val();
    var description = jQuery('#descriptionCarte').val();
    var saison = jQuery('#selectSaisonCarte').val();
    var typeCarte = jQuery('#selectTypeCarte').val();
    var idCarteCreature = null;
    var idVille = null;
    var idReligion = null;
    var xref = jQuery('#xRef').val();
    var yref = jQuery('#yRef').val();
    var xmin = jQuery('#xMin').val();
    var xmax = jQuery('#xMax').val();
    var ymin = jQuery('#yMin').val();
    var ymax = jQuery('#yMax').val();
    var imageCarte = jQuery('#listeImage').val();
    var decouverte = jQuery('#isDecouverteCarte').is(':checked');
    var imageCartePJ = jQuery('#listeImagePJ').val();
    var idCarte = jQuery('#idCarte').val();


    if (jQuery('#selectCarteCreature')) {
        idCarteCreature = jQuery('#selectCarteCreature').val();
    }
    if (jQuery('#selectCarteVille')) {
        idVille = jQuery('#selectCarteVille').val();
    }
    if (jQuery('#selectCarteReligion')) {
        idReligion = jQuery('#selectCarteReligion').val();
    }
    if (decouverte == "checked") {
        decouverte = 1;
    } else {
        decouverte = 0;
    }

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomCarte').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCarte').addClass("erreur");
    }
    if (isNaN(xref)) {
        erreur = erreur + "L'abscisse de référence doit être numérique.\n";
        jQuery('#xRef').addClass("erreur");
    }
    if (isNaN(xmin)) {
        erreur = erreur + "L'abscisse minimum doit être numérique.\n";
        jQuery('#xMin').addClass("erreur");
    }
    if (isNaN(xmax)) {
        erreur = erreur + "L'abscisse maximum doit être numérique.\n";
        jQuery('#xMax').addClass("erreur");
    }
    if (isNaN(yref)) {
        erreur = erreur + "L'ordonnée de référence doit être numérique.\n";
        jQuery('#yRef').addClass("erreur");
    }
    if (isNaN(ymin)) {
        erreur = erreur + "L'ordonnée minimum doit être numérique.\n";
        jQuery('#yMin').addClass("erreur");
    }
    if (isNaN(ymax)) {
        erreur = erreur + "L'ordonnée maximum doit être numérique.\n";
        jQuery('#yMax').addClass("erreur");
    }
    var imageSansEspace = imageCarte.replace(/\s/g, "");
    if (imageSansEspace == "" || imageCarte == "imagedefaut.jpg") {
        erreur = erreur + "Vous devez renseigner une image pour la carte.\n";
        jQuery('#listeImage').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierCarte";
        } else {
            url = "gameplay/modifierCarte";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                saison: saison,
                typeCarte: typeCarte,
                idCarteCreature: idCarteCreature,
                idVille: idVille,
                idReligion: idReligion,
                xref: xref,
                yref: yref,
                xmin: xmin,
                xmax: xmax,
                ymin: ymin,
                ymax: ymax,
                image: imageCarte,
                decouverte: decouverte,
                imageCartePJ: imageCartePJ,
                idCarte: idCarte
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else if (data == "errorLocalisation") {
                    ouvreMsgBox("La nouvelle localisation est déjà prise.", "erreur");
                } else if (data == "recharge") {
                    ouvreMsgBox("La carte a du être rechargée dans une matrice et réinitialisée.", "info");
                } else {
                    ouvreMsgBox("Les modifications ont été prise en compte.", "info");
                }
            }
        });
    }
}

function creerCarte() {
    var nom = jQuery('#nomCarte').val();
    var description = jQuery('#descriptionCarte').val();
    var saison = jQuery('#selectSaisonCarte').val();
    var typeCarte = jQuery('#selectTypeCarte').val();
    var idCarteCreature = null;
    var idVille = null;
    var idReligion = null;
    var xref = jQuery('#xRef').val();
    var yref = jQuery('#yRef').val();
    var xmin = jQuery('#xMin').val();
    var xmax = jQuery('#xMax').val();
    var ymin = jQuery('#yMin').val();
    var ymax = jQuery('#yMax').val();
    var imageCarte = jQuery('#listeImage').val();
    var decouverte = jQuery('#isDecouverteCarte').is(':checked');
    var isCharge = jQuery('#isChargeCarte').is(':checked');
    var imageCartePJ = jQuery('#listeImagePJ').val();


    if (jQuery('#selectCarteCreature')) {
        idCarteCreature = jQuery('#selectCarteCreature').val();
    }
    if (jQuery('#selectCarteVille')) {
        idVille = jQuery('#selectCarteVille').val();
    }
    if (jQuery('#selectCarteReligion')) {
        idReligion = jQuery('#selectCarteReligion').val();
    }
    if (decouverte == "checked") {
        decouverte = 1;
    } else {
        decouverte = 0;
    }

    if (isCharge == "checked") {
        isCharge = 1;
    } else {
        isCharge = 0;
    }

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#nomCarte').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCarte').addClass("erreur");
    }
    if (isNaN(xref)) {
        erreur = erreur + "L'abscisse de référence doit être numérique.\n";
        jQuery('#xRef').addClass("erreur");
    }
    if (isNaN(xmin)) {
        erreur = erreur + "L'abscisse minimum doit être numérique.\n";
        jQuery('#xMin').addClass("erreur");
    }
    if (isNaN(xmax)) {
        erreur = erreur + "L'abscisse maximum doit être numérique.\n";
        jQuery('#xMax').addClass("erreur");
    }
    if (isNaN(yref)) {
        erreur = erreur + "L'ordonnée de référence doit être numérique.\n";
        jQuery('#yRef').addClass("erreur");
    }
    if (isNaN(ymin)) {
        erreur = erreur + "L'ordonnée minimum doit être numérique.\n";
        jQuery('#yMin').addClass("erreur");
    }
    if (isNaN(ymax)) {
        erreur = erreur + "L'ordonnée maximum doit être numérique.\n";
        jQuery('#yMax').addClass("erreur");
    }
    var imageSansEspace = imageCarte.replace(/\s/g, "");
    if (imageSansEspace == "" || imageCarte == "imagedefaut.jpg") {
        erreur = erreur + "Vous devez renseigner une image pour la carte.\n";
        jQuery('#listeImage').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerCarte";
        } else {
            url = "gameplay/creerCarte";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                saison: saison,
                typeCarte: typeCarte,
                idCarteCreature: idCarteCreature,
                idVille: idVille,
                idReligion: idReligion,
                xref: xref,
                yref: yref,
                xmin: xmin,
                xmax: xmax,
                ymin: ymin,
                ymax: ymax,
                image: imageCarte,
                decouverte: decouverte,
                imageCartePJ: imageCartePJ,
                isCharge: isCharge
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom est déjà pris.", "erreur");
                } else if (data == "errorLocalisation") {
                    ouvreMsgBox("L'espace choisi est déjà occupé par une autre carte.", "erreur");
                } else {
                    ouvreMsgBox("La creation est effective.", "info");
                    setTimeout(function () {
                        afficherDetailCarte(data);
                    }, 500);
                }
            }
        });
    }
}

function cleanFormulaireCarte() {
    if (jQuery("#nomCarte").hasClass("erreur")) {
        jQuery('#nomCarte').removeClass("erreur");
    }
    if (jQuery("#descriptionCarte").hasClass("erreur")) {
        jQuery('#descriptionNatureMagieEdition').removeClass("erreur");
    }
    if (jQuery("#xRef").hasClass("erreur")) {
        jQuery("#xRef").removeClass("erreur");
    }
    if (jQuery("#xMin").hasClass("erreur")) {
        jQuery('#xMin').removeClass("erreur");
    }
    if (jQuery("#xMax").hasClass("erreur")) {
        jQuery('#xMax').removeClass("erreur");
    }
    if (jQuery("#yRef").hasClass("erreur")) {
        jQuery("#yRef").removeClass("erreur");
    }
    if (jQuery("#yMin").hasClass("erreur")) {
        jQuery('#yMin').removeClass("erreur");
    }
    if (jQuery("#yMax").hasClass("erreur")) {
        jQuery('#yMax').removeClass("erreur");
    }
    if (jQuery("#listeImage").hasClass("erreur")) {
        jQuery('#listeImage').removeClass("erreur");
    }
}

function boutonChargerCarte(first, nbDone) {
    var id = jQuery('#idCarte').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "chargerCarte";
    } else {
        url = "gameplay/chargerCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: id,
            nbDone: nbDone,
            first: first
        },
        success: function (data) {
            if (data === "done") {
                ouvreMsgBox("La carte a été correctement chargée dans une matrice.", "info");
            } else if(data === "success") {
                boutonChargerCarte('notFirst', nbDone+1000);
            } else {
                ouvreMsgBox("Une erreur s'est produite pendant le processus de chargement.", "erreur");
            }
        }
    });
}

function editerMap(idCarte) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarte";
    } else {
        url = "gameplay/detailCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCarte
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarte();
        }
    });
}

function afficherDivTypeCarte() {
    var type = jQuery('#selectTypeCarte').val();
    var mode = jQuery('#mode').val();
    var id = jQuery('#idCarte').val();
    if (type != "Aucun") {
        if (window.location.href.includes('/gameplay/')) {
            url = "chargerDivTypeCarte";
        } else {
            url = "gameplay/chargerDivTypeCarte";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                mode: mode,
                id: id,
                type: type
            },
            success: function (data) {
                jQuery('#divSpecificiteTypeCarte').html(data);
                jQuery('#divSpecificiteTypeCarte').show();
            }
        });
    } else {
        jQuery('#divSpecificiteTypeCarte').html("");
        jQuery('#divSpecificiteTypeCarte').hide();
    }
}

function boxDeleteMap(idCarte) {
    jQuery('#idCarte').val(idCarte);
    ouvreMsgBox('Attention, supprimer la map supprimera également toutes les créatures et placera tous les personnages présent sur cette derniere en téléportation. Etes vous sûr de vouloir continuer ?', "question", "ouinon", supprimerCarte, "");
}

function supprimerCarte() {
    var id = jQuery('#idCarte').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "supprimerCarte";
    } else {
        url = "gameplay/supprimerCarte";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: id
        },
        success: function (data) {
            ouvreMsgBox("La carte a été correctement supprimée.", "info");
            setTimeout(function () {
                retourListeCarte();
            }, 500);
        }
    });
}

function afficherCarteCreature() {
    var idMap = jQuery('#selectCarteCreature').val();
    if (idMap == "0") {
        jQuery("#imageCarteCreature").html("");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "afficherCarteCreature";
        } else {
            url = "gameplay/afficherCarteCreature";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: idMap
            },
            success: function (data) {
                jQuery("#imageCarteCreature").html(data);
            }
        });
    }
}

function chargerImageCarte() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }

    jQuery("#imageCarte").attr("src", src).on("load", function () {
        var width = this.width;
        var height = this.height;
        jQuery('#largeurCarte').val(width);
        jQuery('#hauteurCarte').val(height);
    });
    jQuery("#champImageCarte").attr("src", src);
    setTimeout(function () {
        formCalculCoordoonneeCarteX('xRef');
        formCalculCoordoonneeCarteY('yRef');
    }, 100);
}

function chargerImageCartePJ() {
    var src = jQuery('#listeImagePJ').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageCartePJ").attr("src", src);
}
