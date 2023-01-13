function initBoutonsVille() {
    jQuery('#boutonAjouterVille').click(function () {
        accesCreationVille();
    });
    jQuery('#accesEditerVille').click(function () {
        afficherDetailModificationVille();
    });
    jQuery('#retourListeVille').click(function () {
        retourListeVille();
    });
    jQuery('#annulerEditerVille').click(function () {
        afficherVille(jQuery('#idVille').val());
    });
    jQuery('#modifierVille').click(function () {
        modifierVille();
    });
    jQuery('#creerVille').click(function () {
        creerVille();
    });
    jQuery('#chargerNewImageVille').click(function () {
        chargerNewImageVille();
    });
    jQuery('#showlisteGestionVille').click(function () {
        showlisteGestionVille();
    });
    jQuery('#hidelisteGestionVille').click(function () {
        hidelisteAnnexeVille();
    });
    jQuery('#hidelisteGestionVille').hide();
    jQuery('#showlisteFinanceVille').click(function () {
        showlisteFinanceVille();
    });
    jQuery('#hidelisteFinanceVille').click(function () {
        hidelisteAnnexeVille();
    });
    jQuery('#hidelisteFinanceVille').hide();
    jQuery('#showlisteDiplomatieVille').click(function () {
        showlisteDiplomatieVille();
    });
    jQuery('#hidelisteDiplomatieVille').click(function () {
        hidelisteAnnexeVille();
    });
    jQuery('#hidelisteDiplomatieVille').hide();
    jQuery('#showlisteMiliceVille').click(function () {
        showlisteMiliceVille();
    });
    jQuery('#hidelisteMiliceVille').click(function () {
        hidelisteAnnexeVille();
    });
    jQuery('#hidelisteMiliceVille').hide();
    jQuery('#showlisteQuartierVille').click(function () {
        showlisteQuartierVille();
    });
    jQuery('#hidelisteQuartierVille').click(function () {
        hidelisteAnnexeVille();
    });
    jQuery('#hidelisteQuartierVille').hide();
    jQuery('#imageVille').click(function () {
        displayImageVilleReelle();
    });
}

function initBoutonGestionVille() {
}

function initBoutonFinanceVille() {
}

function initBoutonMiliceVille() {
}

function initBoutonDiplomatieVille() {
}

function initBoutonQuartierVille() {
}

function accesCreationVille() {
    if (window.location.href.includes('/administration/')) {
        url = "detailVille";
    } else {
        url = "administration/detailVille";
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
            initBoutonReferentiels();
        }
    });
}

function editerVille(idVille) {
    if (window.location.href.includes('/administration/')) {
        url = "detailVille";
    } else {
        url = "administration/detailVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            id: idVille
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherDetailModificationVille() {
    var id = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "detailVille";
    } else {
        url = "administration/detailVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            id: id
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherVille(idVille) {
    if (window.location.href.includes('/administration/')) {
        url = "detailVille";
    } else {
        url = "administration/detailVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idVille
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function retourListeVille() {
    if (window.location.href.includes('/administration/')) {
        url = "detailVille";
    } else {
        url = "administration/detailVille";
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
            jQuery('#boutonRefRoyaume').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefVille').addClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function changerImageVille() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageVille").attr("src", src);
}

function modifierVille() {
    var idVille = jQuery('#idVille').val();
    var nom = jQuery('#nomVille').val();
    var description = jQuery('#descriptionVille').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var idRoyaumeOrigine = jQuery('#royaumeOrigine').val();
    var idRoyaumeActuel = jQuery('#royaumeActuel').val();
    var messageAccueil = jQuery('#messageAccueilVille').val();
    var isNaissance = jQuery('#isNaissanceVille').is(':checked');
    var hasCarte = jQuery('#hasCarte').val();

    var erreur = "";
    if (hasCarte == "true") {
        var positionXMinVille = jQuery('#positionXMinVille').html();
        var positionXMaxVille = jQuery('#positionXMaxVille').html();
        var positionYMinVille = jQuery('#positionYMinVille').html();
        var positionYMaxVille = jQuery('#positionYMaxVille').html();
    } else {
        var positionXMinVille = jQuery('#positionXMinVille').val();
        var positionXMaxVille = jQuery('#positionXMaxVille').val();
        var positionYMinVille = jQuery('#positionYMinVille').val();
        var positionYMaxVille = jQuery('#positionYMaxVille').val();

        //Contrôle des champs
        var positionXMinVilleSansEspace = positionXMinVille.replace(/\s/g, "");
        if (positionXMinVilleSansEspace == "") {
            positionXMinVille = 0;
        } else if (isNaN(positionXMinVille)) {
            erreur += "L'abscisse minimum doit être un chiffre.\n";
            jQuery('#positionXMinVille').addClass("erreur");
        }

        var positionXMaxVilleVilleSansEspace = positionXMaxVille.replace(/\s/g, "");
        if (positionXMaxVilleVilleSansEspace == "") {
            positionXMaxVille = 0;
        } else if (isNaN(positionXMaxVille)) {
            erreur += "L'abscisse maximum doit être un chiffre.\n";
            jQuery('#positionXMaxVille').addClass("erreur");
        }

        if (erreur == "") {
            if (parseInt(positionXMaxVille) < parseInt(positionXMinVille)) {
                erreur += "L'abscisse minimum doit être inférieure à l'abscisse maximum.\n";
            }
        }

        var positionYMinVilleSansEspace = positionYMinVille.replace(/\s/g, "");
        if (positionYMinVilleSansEspace == "") {
            positionYMinVille = 0;
        } else if (isNaN(positionYMinVille)) {
            erreur += "L'ordonnée minimum doit être un chiffre.\n";
            jQuery('#positionYMinVille').addClass("erreur");
        }

        var positionYMaxVilleVilleSansEspace = positionYMaxVille.replace(/\s/g, "");
        if (positionYMaxVilleVilleSansEspace == "") {
            positionYMaxVille = 0;
        } else if (isNaN(positionYMaxVille)) {
            erreur += "L'ordonnée maximum doit être un chiffre.\n";
            jQuery('#positionYMaxVille').addClass("erreur");
        }

        if (erreur == "") {
            if (parseInt(positionYMaxVille) < parseInt(positionYMinVille)) {
                erreur += "L'ordonnée minimum doit être inférieure à l'ordonnée maximum.\n";
            }
        }
    }

    //Contrôle des champs
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#nomVille').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionVille').addClass("erreur");
    }
    var messageAccueilSansEspace = messageAccueil.replace(/\s/g, "");
    if (messageAccueilSansEspace == "") {
        erreur += "Un message d'accueil est obligatoire.\n";
        jQuery('#messageAccueil').addClass("erreur");
    }

    if (isNaissance == true) {
        isNaissance = 1;
    } else {
        isNaissance = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierVille";
        } else {
            url = "administration/modifierVille";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idVille: idVille,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isNaissance: isNaissance,
                idRoyaumeOrigine: idRoyaumeOrigine,
                idRoyaumeActuel: idRoyaumeActuel,
                messageAccueil: messageAccueil,
                xmin: positionXMinVille,
                xmax: positionXMaxVille,
                ymin: positionYMinVille,
                ymax: positionYMaxVille,
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                    cleanErreurVille();
                }
            }
        });
    }
}

function creerVille() {
    var nom = jQuery('#nomVille').val();
    var description = jQuery('#descriptionVille').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var idRoyaumeOrigine = jQuery('#royaumeOrigine').val();
    var idRoyaumeActuel = jQuery('#royaumeActuel').val();
    var messageAccueil = jQuery('#messageAccueilVille').val();
    var isNaissance = jQuery('#isNaissanceVille').is(':checked');
    var positionXMinVille = jQuery('#positionXMinVille').val();
    var positionXMaxVille = jQuery('#positionXMaxVille').val();
    var positionYMinVille = jQuery('#positionYMinVille').val();
    var positionYMaxVille = jQuery('#positionYMaxVille').val();

    //Contrôle des champs
    var erreur = "";
    var positionXMinVilleSansEspace = positionXMinVille.replace(/\s/g, "");
    if (positionXMinVilleSansEspace == "") {
        positionXMinVille = 0;
    } else if (isNaN(positionXMinVille)) {
        erreur += "L'abscisse minimum doit être un chiffre.\n";
        jQuery('#positionXMinVille').addClass("erreur");
    }

    var positionXMaxVilleVilleSansEspace = positionXMaxVille.replace(/\s/g, "");
    if (positionXMaxVilleVilleSansEspace == "") {
        positionXMaxVille = 0;
    } else if (isNaN(positionXMaxVille)) {
        erreur += "L'abscisse maximum doit être un chiffre.\n";
        jQuery('#positionXMaxVille').addClass("erreur");
    }

    if (erreur == "") {
        if (parseInt(positionXMaxVille) < parseInt(positionXMinVille)) {
            erreur += "L'abscisse minimum doit être inférieure à l'abscisse maximum.\n";
        }
    }

    var positionYMinVilleSansEspace = positionYMinVille.replace(/\s/g, "");
    if (positionYMinVilleSansEspace == "") {
        positionYMinVille = 0;
    } else if (isNaN(positionYMinVille)) {
        erreur += "L'ordonnée minimum doit être un chiffre.\n";
        jQuery('#positionYMinVille').addClass("erreur");
    }

    var positionYMaxVilleVilleSansEspace = positionYMaxVille.replace(/\s/g, "");
    if (positionYMaxVilleVilleSansEspace == "") {
        positionYMaxVille = 0;
    } else if (isNaN(positionYMaxVille)) {
        erreur += "L'ordonnée maximum doit être un chiffre.\n";
        jQuery('#positionYMaxVille').addClass("erreur");
    }

    if (erreur == "") {
        if (parseInt(positionYMaxVille) < parseInt(positionYMinVille)) {
            erreur += "L'ordonnée minimum doit être inférieure à l'ordonnée maximum.\n";
        }
    }


    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#nomVille').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionVille').addClass("erreur");
    }
    var messageAccueilSansEspace = messageAccueil.replace(/\s/g, "");
    if (messageAccueilSansEspace == "") {
        erreur += "Un message d'accueil est obligatoire.\n";
        jQuery('#messageAccueil').addClass("erreur");
    }


    if (isNaissance == true) {
        isNaissance = 1;
    } else {
        isNaissance = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "creerVille";
        } else {
            url = "administration/creerVille";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isNaissance: isNaissance,
                idRoyaumeOrigine: idRoyaumeOrigine,
                idRoyaumeActuel: idRoyaumeActuel,
                messageAccueil: messageAccueil,
                xmin: positionXMinVille,
                xmax: positionXMaxVille,
                ymin: positionYMinVille,
                ymax: positionYMaxVille,
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else if (data == "error") {
                    ouvreMsgBox("Une erreur s'est produite.", "error");
                } else {
                    ouvreMsgBox("La ville a été correctement créée.", "info");
                    cleanErreurVille();
                    afficherVille(data);
                }
            }
        });
    }
}

function cleanErreurVille() {
    if (jQuery('#positionXMinVille').hasClass("erreur")) {
        jQuery('#positionXMinVille').removeClass("erreur");
    }
    if (jQuery('#positionXMaxVille').hasClass("erreur")) {
        jQuery('#positionXMaxVille').removeClass("erreur");
    }
    if (jQuery('#positionYMinVille').hasClass("erreur")) {
        jQuery('#positionYMinVille').removeClass("erreur");
    }
    if (jQuery('#positionYMaxVille').hasClass("erreur")) {
        jQuery('#positionYMaxVille').removeClass("erreur");
    }
    if (jQuery('#nomVille').hasClass("erreur")) {
        jQuery('#nomVille').removeClass("erreur");
    }
    if (jQuery('#descriptionVille').hasClass("erreur")) {
        jQuery('#descriptionVille').removeClass("erreur");
    }
    if (jQuery('#descriptionVille').hasClass("erreur")) {
        jQuery('#messageAccueil').removeClass("erreur");
    }
}

function chargerNewImageVille() {
    var id = jQuery('#idVille').val();
    var type = "Ville";
    var urlFile = jQuery('#newImageVille').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "uploadImageUrl";
        } else {
            url = "administration/uploadImageUrl";
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

function showlisteGestionVille() {
    var idVille = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteGestionVille";
    } else {
        url = "administration/showlisteGestionVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idVille
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteGestionVille').hide();
            jQuery('#hidelisteGestionVille').show();
            jQuery('#showlisteFinanceVille').show();
            jQuery('#hidelisteFinanceVille').hide();
            jQuery('#showlisteDiplomatieVille').show();
            jQuery('#hidelisteDiplomatieVille').hide();
            jQuery('#showlisteMiliceVille').show();
            jQuery('#hidelisteMiliceVille').hide();
            jQuery('#showlisteQuartierVille').show();
            jQuery('#hidelisteQuartierVille').hide();
            initBoutonGestionVille();
        }
    });
}

function showlisteFinanceVille() {
    var idVille = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteFinanceVille";
    } else {
        url = "administration/showlisteFinanceVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idVille
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteGestionVille').show();
            jQuery('#hidelisteGestionVille').hide();
            jQuery('#showlisteFinanceVille').hide();
            jQuery('#hidelisteFinanceVille').show();
            jQuery('#showlisteDiplomatieVille').show();
            jQuery('#hidelisteDiplomatieVille').hide();
            jQuery('#showlisteMiliceVille').show();
            jQuery('#hidelisteMiliceVille').hide();
            jQuery('#showlisteQuartierVille').show();
            jQuery('#hidelisteQuartierVille').hide();
            initBoutonFinanceVille();
        }
    });
}

function showlisteDiplomatieVille() {
    var idVille = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteDiplomatieVille";
    } else {
        url = "administration/showlisteDiplomatieVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idVille
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteGestionVille').show();
            jQuery('#hidelisteGestionVille').hide();
            jQuery('#showlisteFinanceVille').show();
            jQuery('#hidelisteFinanceVille').hide();
            jQuery('#showlisteDiplomatieVille').hide();
            jQuery('#hidelisteDiplomatieVille').show();
            jQuery('#showlisteMiliceVille').show();
            jQuery('#hidelisteMiliceVille').hide();
            jQuery('#showlisteQuartierVille').show();
            jQuery('#hidelisteQuartierVille').hide();
            initBoutonDiplomatieVille();
        }
    });
}

function showlisteMiliceVille() {
    var idVille = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteMiliceVille";
    } else {
        url = "administration/showlisteMiliceVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idVille
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteGestionVille').show();
            jQuery('#hidelisteGestionVille').hide();
            jQuery('#showlisteFinanceVille').show();
            jQuery('#hidelisteFinanceVille').hide();
            jQuery('#showlisteDiplomatieVille').show();
            jQuery('#hidelisteDiplomatieVille').hide();
            jQuery('#showlisteMiliceVille').hide();
            jQuery('#hidelisteMiliceVille').show();
            jQuery('#showlisteQuartierVille').show();
            jQuery('#hidelisteQuartierVille').hide();
            initBoutonMiliceVille();
        }
    });
}

function showlisteQuartierVille() {
    var idVille = jQuery('#idVille').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteQuartierVille";
    } else {
        url = "administration/showlisteQuartierVille";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idVille
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteGestionVille').show();
            jQuery('#hidelisteGestionVille').hide();
            jQuery('#showlisteFinanceVille').show();
            jQuery('#hidelisteFinanceVille').hide();
            jQuery('#showlisteDiplomatieVille').show();
            jQuery('#hidelisteDiplomatieVille').hide();
            jQuery('#showlisteMiliceVille').show();
            jQuery('#hidelisteMiliceVille').hide();
            jQuery('#showlisteQuartierVille').hide();
            jQuery('#hidelisteQuartierVille').show();
            initBoutonQuartierVille();
        }
    });
}

function hidelisteAnnexeVille() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteGestionVille').show();
    jQuery('#hidelisteGestionVille').hide();
    jQuery('#showlisteFinanceVille').show();
    jQuery('#hidelisteFinanceVille').hide();
    jQuery('#showlisteDiplomatieVille').show();
    jQuery('#hidelisteDiplomatieVille').hide();
    jQuery('#showlisteMiliceVille').show();
    jQuery('#hidelisteMiliceVille').hide();
    jQuery('#showlisteQuartierVille').show();
    jQuery('#hidelisteQuartierVille').hide();
}

function displayImageVilleReelle() {
    var src = jQuery('#imageVille').attr('src');
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageVilleGrande").attr("src", src);
    jQuery('#divImageVilleGrande').show();
}

function hideImageVilleReelle() {
    jQuery('#divImageVilleGrande').hide();
}