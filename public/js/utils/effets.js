function initBoutonEffet() {
    jQuery('#ajouterEffet').click(function () {
        ajouterEffet();
    });
    jQuery('#annulerAjoutEffet').click(function () {
        annulerAjoutEffet();
    });
    jQuery('#editerEffet').click(function () {
        editerEffetFromConsult();
    });
    jQuery('#validerEffet').click(function () {
        validerEffet();
    });
    jQuery('#modifierEffet').click(function () {
        modifierEffet();
    });
}

function openPopupEffet(mode, idEffet, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Effets/afficherFormulaireEffet",
        data: {
            mode: mode,
            idEffet: idEffet,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupEffet').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupEffet").before('<div id="graybackEffet"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupEffet").height();
            var popupW = jQuery("#popupEffet").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupEffet").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupEffet").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#graybackEffet").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupEffet").fadeIn(500);
            });
            initBoutonEffet();
        }
    });
}

function ajouterEffet() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var position = "new";
    var idEffet = "new";
    var mode = "creation";
    openPopupEffet(mode, idEffet, type, idType, position);
}

function annulerAjoutEffet() {
    var type = jQuery('#type').val();
    // on fait disparaître le gris de fond rapidement
    jQuery("#graybackEffet").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupEffet").hide();

    if (type == "sort") {
        showlisteEffetsSorts();
    } else if (type == "terrain") {
        showlisteEffetsTerrains();
    } else if (type == "carte") {
        showlisteEffetsCartes();
    } else if (type == "talent") {
        showlisteEffetTalent();
    }
}

function editerEffetFromConsult() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idEffet = jQuery('#idEffet').val();
    var position = jQuery('#position').val();

    jQuery.ajax({
        type: "POST",
        url: "Effets/afficherFormulaireEffet",
        data: {
            mode: "edition",
            idEffet: idEffet,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupEffet').html(data);
            initBoutonEffet();
        }
    });
}

function validerEffet() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idEffet = jQuery('#selectEffet').val();
    var action = null;
    if (jQuery('#selectActionEffet')) {
        action = jQuery('#selectActionEffet').val();
    }

    if (idEffet == "0") {
        ouvreMsgBox("Vous devez sélectionner un effet !", "erreur");
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
            url: "Effets/validerEffet",
            data: {
                type: type,
                idType: idType,
                listeParam: listeParam,
                idEffet: idEffet,
                action: action
            },
            success: function (data) {
                if (data == "success") {
                    //Dire que c'est OK
                    ouvreMsgBox("L'effet a été correctement ajouté.", "info");
                    setTimeout(function () {
                        annulerAjoutEffet()
                    }, 300);
                } else {
                    ouvreMsgBox(data, "erreur");
                }
            }
        });
    }
}

function modifierEffet() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idEffet = jQuery('#idEffet').val();
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
        url: "Effets/modifierEffet",
        data: {
            type: type,
            idType: idType,
            idEffet: idEffet,
            position: position,
            listeParam: listeParam
        },
        success: function (data) {
            if (data == "success") {
                //Dire que c'est OK
                ouvreMsgBox("Les modifications ont été prises en compte.", "info");
                setTimeout(function () {
                    annulerAjoutEffet()
                }, 300);
            } else {
                ouvreMsgBox(data, "erreur");
            }
        }
    });
}

function boxRetirerEffet(idEffet, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Effets/retirerEffet",
        data: {
            type: type,
            idType: idType,
            idEffet: idEffet,
            position: position
        },
        success: function (data) {
            if (type == "sort") {
                showlisteEffetsSorts();
            } else if (type == "terrain") {
                showlisteEffetsTerrains();
            }
        }
    });
}

function afficherFormulaireEffet(type, idType, idEffet, position) {
    openPopupEffet("consultation", idEffet, type, idType, position);
}

function editerEffet(idEffet, type, idType, position) {
    openPopupEffet("edition", idEffet, type, idType, position);
}

function chargerEffetByType() {
    var type = jQuery('#selectTypeEffet').val();
    jQuery.ajax({
        type: "POST",
        url: "Effets/chargerEffetByType",
        data: {
            type: type
        },
        success: function (data) {
            jQuery('#spanSelectEffet').html(data);
            jQuery('#detailEffet').html("");
            jQuery('#detailEffet').hide();
        }
    });
}

function chargerDetailEffet() {
    var idEffet = jQuery('#selectEffet').val();
    if (idEffet == "0") {
        jQuery('#detailEffet').html("");
        jQuery('#detailEffet').hide();
    } else {
        jQuery.ajax({
            type: "POST",
            url: "Effets/chargerDetailEffet",
            data: {
                idEffet: idEffet
            },
            success: function (data) {
                jQuery('#detailEffet').html(data);
                jQuery('#detailEffet').show();
            }
        });
    }
}