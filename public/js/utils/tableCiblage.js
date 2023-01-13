function initBoutonTableCiblage() {
    jQuery('#editerTableCiblage').click(function () {
        editerTableCiblage();
    });
    jQuery('#fermerTableCiblage').click(function () {
        closePopupTableCiblage();
    });
    jQuery('#annulerEditionTableCiblage').click(function () {
        annulerEditionTableCiblage();
    });
}

function accesTableCiblage() {
    var idSort = jQuery('#idSort').val();

    //On remplit la pop-up de la table de ciblage :
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/openPopupTableCiblage",
        data: {
            idSort: idSort
        },
        success: function (data) {
            jQuery('#popupTableCiblage').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupTableCiblage").before('<div id="grayBack"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupTableCiblage").height();
            var popupW = jQuery("#popupTableCiblage").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupTableCiblage").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#grayBack").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupTableCiblage").fadeIn(500);
            });
            initBoutonTableCiblage();
        }
    });
}

function chargerBloc(bloc) {
    if (bloc == "creatures") {
        var check = jQuery('#isCreatureCiblable').is(':checked');
        if (check == 0) {
            jQuery('#divDetailCiblageCreature').hide();
            deleteCreatures();
            resetCreatures();
        } else {
            jQuery('#divDetailCiblageCreature').show();
        }
    } else if (bloc == "environnement") {
        var check = jQuery('#isEnvironnementCiblable').is(':checked');
        if (check == 0) {
            jQuery('#divDetailEnvironnement').hide();
            deleteEnvironnement();
            resetEnvironnement();
        } else {
            jQuery('#divDetailEnvironnement').show();
            autoriseEnvironnement();
        }
    } else if (bloc == "batiments") {
        //TODO
    } else if (bloc == "personnages") {
        var check = jQuery('#isPersonnageCiblable').is(':checked');
        if (check == 0) {
            interdirePersonnage();
        } else {
            autoriserPersonnage();
        }
    }
    initBoutonTableCiblage();
}

function deleteCreatures() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/deleteCreatures",
        data: {
            id: idTableCiblage,
            type: type,
            idType: idType
        },
        success: function (data) {
            initBoutonTableCiblage();
        }
    });
}

function resetCreatures() {
    //TODO
}

function deleteEnvironnement() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/deleteEnvironnement",
        data: {
            id: idTableCiblage,
            type: type,
            idType: idType
        },
        success: function (data) {
            initBoutonTableCiblage();
        }
    });
}

function resetEnvironnement() {
    jQuery('#spanResumeEnvironnementCiblable').html("");
    genererListeTerrain();
}

function genererListeTerrain() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/genererListeTerrainCiblable",
        data: {
            id: idTableCiblage
        },
        success: function (data) {
            jQuery('#spanSelectEnvironnementCiblable').html(data);
            initBoutonTableCiblage();
        }
    });
}

function retirerEnvironnementCiblable(idTerrain) {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/retirerEnvironnementCiblable",
        data: {
            idTableCiblage: idTableCiblage,
            idTerrain: idTerrain,
            type: type,
            idType: idType
        },
        success: function (data) {
            jQuery('#spanResumeEnvironnementCiblable').html(data);
            genererListeTerrain();
        }
    });
}

function ajouterTerrainCiblable() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    var idTerrain = jQuery('#listeTerrainCiblable').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/ajouterTerrainCiblable",
        data: {
            idTableCiblage: idTableCiblage,
            idTerrain: idTerrain,
            type: type,
            idType: idType
        },
        success: function (data) {
            jQuery('#spanResumeEnvironnementCiblable').html(data);
            genererListeTerrain();
        }
    });
}

function editerTableCiblage() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idTableCiblage = jQuery('#idTableCiblage').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/editerTableCiblage",
        data: {
            type: type,
            idType: idType,
            id: idTableCiblage
        },
        success: function (data) {
            jQuery('#popupTableCiblage').html(data);
            initBoutonTableCiblage();
        }
    });
}

function annulerEditionTableCiblage() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idTableCiblage = jQuery('#idTableCiblage').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/annulerEditionTableCiblage",
        data: {
            type: type,
            idType: idType,
            id: idTableCiblage
        },
        success: function (data) {
            jQuery('#popupTableCiblage').html(data);
            initBoutonTableCiblage();
        }
    });
}

function autoriseEnvironnement() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/autoriseEnvironnement",
        data: {
            id: idTableCiblage,
            type: type,
            idType: idType
        },
        success: function (data) {
            initBoutonTableCiblage();
        }
    });
}

function autoriserPersonnage() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/autoriserPersonnage",
        data: {
            id: idTableCiblage
        },
        success: function (data) {
            initBoutonTableCiblage();
        }
    });
}

function interdirePersonnage() {
    var idTableCiblage = jQuery('#idTableCiblage').val();
    jQuery.ajax({
        type: "POST",
        url: "Tableciblage/interdirePersonnage",
        data: {
            id: idTableCiblage
        },
        success: function (data) {
            initBoutonTableCiblage();
        }
    });
}

function closePopupTableCiblage() {
    // on fait disparaître le gris de fond rapidement
    jQuery("#grayBack").remove();
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupTableCiblage").hide();
}  