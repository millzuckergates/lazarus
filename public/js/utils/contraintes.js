function initBoutonContrainte() {
    jQuery('.ajouterContrainte').click(function () {
        ajouterContrainte();
    });
    jQuery('.annulerAjoutContrainte').click(function () {
        annulerAjoutContrainte();
    });
    jQuery('.editerContrainte').click(function () {
        editerContrainteFromConsult();
    });
    jQuery('.validerContrainte').click(function () {
        validerContrainte();
    });
    jQuery('.modifierContrainte').click(function () {
        modifierContrainte();
    });
}

function openPopupContrainte(mode, idContrainte, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Contraintes/afficherFormulaireContrainte",
        data: {
            mode: mode,
            idContrainte: idContrainte,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupContrainte').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupContrainte").before('<div id="graybackContrainte"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupContrainte").height();
            var popupW = jQuery("#popupContrainte").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupContrainte").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupContrainte").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#graybackContrainte").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupContrainte").fadeIn(500);
            });
            initBoutonContrainte();
        }
    });
}

function ajouterContrainte() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var position = "new";
    var idContrainte = "new";
    var mode = "creation";
    openPopupContrainte(mode, idContrainte, type, idType, position);
}

function annulerAjoutContrainte() {
    var type = jQuery('#type').val();
    // on fait disparaître le gris de fond rapidement
    jQuery("#graybackContrainte").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupContrainte").hide();

    if (type == "sort") {
        showlisteContraintesSorts();
    } else if (type == "familleTalent") {
        showlisteContraintesFamille();
    } else if (type == "arbreTalent") {
        showlisteContraintesArbre();
    } else if (type == "talent") {
        showlisteContraintesTalent();
    } else if (type == "competence") {
        showlisteContraintesCompetence();
    }
}

function editerContrainteFromConsult() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idContrainte = jQuery('#idContrainte').val();
    var position = jQuery('#position').val();

    jQuery.ajax({
        type: "POST",
        url: "Contraintes/afficherFormulaireContrainte",
        data: {
            mode: "edition",
            idContrainte: idContrainte,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupContrainte').html(data);
            initBoutonContrainte();
        }
    });
}

function validerContrainte() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idContrainte = jQuery('#selectContrainte').val();

    if (idContrainte == "0") {
        ouvreMsgBox("Vous devez sélectionner une contrainte !", "erreur");
    } else {
        //Récupérer les paramètres
        var nbParam = jQuery('#nbParam').val();
        var listeParam = "";
        if (nbParam != 0) {
            for (var i = 1; i <= nbParam; i++) {
                if (jQuery('#param' + i).val()) {
                    var param = jQuery('#param' + i).val();
                } else {
                    var param = jQuery('#param' + i).html();
                }
                var paramMin = null;
                var paramMax = null;
                if (jQuery('#param' + i + 'Min')) {
                    paramMin = jQuery('#param' + i + 'Min').val();
                }
                if (jQuery('#param' + i + 'Max')) {
                    paramMax = jQuery('#param' + i + 'Max').val();
                }
                listeParam = listeParam + "|" + param + "|" + paramMin + "|" + paramMax;
            }
        }

        jQuery.ajax({
            type: "POST",
            url: "Contraintes/validerContrainte",
            data: {
                type: type,
                idType: idType,
                listeParam: listeParam,
                idContrainte: idContrainte
            },
            success: function (data) {
                if (data == "success") {
                    //Dire que c'est OK
                    ouvreMsgBox("La contrainte a été correctement ajoutée.", "info");
                    setTimeout(function () {
                        annulerAjoutContrainte()
                    }, 300);
                } else {
                    ouvreMsgBox(data, "erreur");
                }
            }
        });
    }
}

function modifierContrainte() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idContrainte = jQuery('#idContrainte').val();
    var position = jQuery('#position').val();

    //Récupérer les paramètres
    var nbParam = jQuery('#nbParam').val();
    var listeParam = "";
    if (nbParam != 0) {
        for (var i = 1; i <= nbParam; i++) {
            if (jQuery('#param' + i).val()) {
                var param = jQuery('#param' + i).val();
            } else {
                var param = jQuery('#param' + i).html();
            }
            var paramMin = null;
            var paramMax = null;
            if (jQuery('#param' + i + 'Min')) {
                paramMin = jQuery('#param' + i + 'Min').val();
            }
            if (jQuery('#param' + i + 'Max')) {
                paramMax = jQuery('#param' + i + 'Max').val();
            }
            listeParam = listeParam + "|" + param + "|" + paramMin + "|" + paramMax;
        }
    }

    jQuery.ajax({
        type: "POST",
        url: "Contraintes/modifierContrainte",
        data: {
            type: type,
            idType: idType,
            idContrainte: idContrainte,
            position: position,
            listeParam: listeParam
        },
        success: function (data) {
            if (data == "success") {
                //Dire que c'est OK
                ouvreMsgBox("Les modifications ont été prises en compte.", "info");
                setTimeout(function () {
                    annulerAjoutContrainte()
                }, 300);
            } else {
                ouvreMsgBox(data, "erreur");
            }
        }
    });
}

function boxRetirerContrainte(idContrainte, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Contraintes/retirerContrainte",
        data: {
            type: type,
            idType: idType,
            idContrainte: idContrainte,
            position: position
        },
        success: function (data) {
            if (type == "sort") {
                showlisteContraintesSorts();
            } else if (type == "familleTalent") {
                showlisteContraintesFamille();
            } else if (type == "arbreTalent") {
                showlisteContraintesArbre();
            } else if (type == "talent") {
                showlisteContraintesTalent();
            }
        }
    });
}

function afficherFormulaireContrainte(type, idType, idContrainte, position) {
    openPopupContrainte("consultation", idContrainte, type, idType, position);
}

function editerContrainte(idContrainte, type, idType, position) {
    openPopupContrainte("edition", idContrainte, type, idType, position);
}

function chargerContrainteByType() {
    var type = jQuery('#selectTypeContrainte').val();
    jQuery.ajax({
        type: "POST",
        url: "Contraintes/chargerContrainteByType",
        data: {
            type: type
        },
        success: function (data) {
            jQuery('#spanSelectContrainte').html(data);
            jQuery('#detailContrainte').html("");
            jQuery('#detailContrainte').hide();
        }
    });
}

function chargerDetailContrainte() {
    var idContrainte = jQuery('#selectContrainte').val();
    if (idContrainte == "0") {
        jQuery('#detailContrainte').html("");
        jQuery('#detailContrainte').hide();
    } else {
        jQuery.ajax({
            type: "POST",
            url: "Contraintes/chargerDetailContrainte",
            data: {
                idContrainte: idContrainte
            },
            success: function (data) {
                jQuery('#detailContrainte').html(data);
                jQuery('#detailContrainte').show();
            }
        });
    }
}