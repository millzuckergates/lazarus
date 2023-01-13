function initBoutonBonus() {
    jQuery('#ajouterBonus').click(function () {
        ajouterBonus();
    });

}

function initBoutonPopupBonus() {
    jQuery('#annulerAjoutBonus').click(function () {
        annulerAjoutBonus();
    });
    jQuery('#editerBonus').click(function () {
        editerBonusFromConsult();
    });
    jQuery('#validerBonus').click(function () {
        validerBonus();
    });
    jQuery('#modifierBonus').click(function () {
        modifierBonus();
    });
}

function openPopupBonus(mode, idBonus, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Bonus/afficherFormulaireBonus",
        data: {
            mode: mode,
            idBonus: idBonus,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupBonus').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupBonus").before('<div id="graybackBonus"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupBonus").height();
            var popupW = jQuery("#popupBonus").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupBonus").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupBonus").css("margin-left", "-" + popupW / 2 + "px");

            jQuery("#graybackBonus").click(function () {
                annulerAjoutBonus();
            });

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#graybackBonus").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupBonus").fadeIn(500);
            });
            initBoutonPopupBonus();
        }
    });
}

function ajouterBonus() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var position = "new";
    var idBonus = "new";
    var mode = "creation";
    openPopupBonus(mode, idBonus, type, idType, position);
}

function annulerAjoutBonus() {
    var type = jQuery('#type').val();
    // on fait disparaître le gris de fond rapidement
    jQuery("#graybackBonus").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupBonus").hide();

    if (type == "royaume") {
        showlisteBonusRoyaume();
    } else if (type == "race") {
        showlisteBonusRace();
    } else if (type == "religion") {
        showlisteBonusReligion();
    }
}

function editerBonusFromConsult() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idBonus = jQuery('#idBonus').val();
    var position = jQuery('#position').val();

    jQuery.ajax({
        type: "POST",
        url: "Bonus/afficherFormulaireBonus",
        data: {
            mode: "edition",
            idBonus: idBonus,
            type: type,
            idType: idType,
            position: position
        },
        success: function (data) {
            jQuery('#popupBonus').html(data);
            initBoutonPopupBonus();
        }
    });
}

function validerBonus() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idBonus = jQuery('#selectBonus').val();
    if (idBonus == "0") {
        ouvreMsgBox("Vous devez sélectionner un bonus !", "erreur");
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
                listeParam = listeParam + "|" + param;
            }
        }

        jQuery.ajax({
            type: "POST",
            url: "Bonus/validerBonus",
            data: {
                type: type,
                idType: idType,
                listeParam: listeParam,
                idBonus: idBonus
            },
            success: function (data) {
                if (data == "success") {
                    //Dire que c'est OK
                    ouvreMsgBox("Le bonus a été correctement ajouté.", "info");
                    setTimeout(function () {
                        annulerAjoutBonus();
                    }, 100);
                } else {
                    ouvreMsgBox(data, "erreur");
                }
            }
        });
    }
}

function modifierBonus() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var idBonus = jQuery('#idBonus').val();
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
            listeParam = listeParam + "|" + param;
        }
    }

    jQuery.ajax({
        type: "POST",
        url: "Bonus/modifierBonus",
        data: {
            type: type,
            idType: idType,
            idBonus: idBonus,
            position: position,
            listeParam: listeParam
        },
        success: function (data) {
            if (data == "success") {
                //Dire que c'est OK
                ouvreMsgBox("Les modifications ont été prises en compte.", "info");
                setTimeout(function () {
                    annulerAjoutBonus();
                }, 300);
            } else {
                ouvreMsgBox(data, "erreur");
            }
        }
    });
}

function boxRetirerBonus(idBonus, type, idType, position) {
    jQuery.ajax({
        type: "POST",
        url: "Bonus/retirerBonus",
        data: {
            type: type,
            idType: idType,
            idBonus: idBonus,
            position: position
        },
        success: function (data) {
            if (type == "royaume") {
                showlisteBonusRoyaume();
            } else if (type == "race") {
                showlisteBonusRace();
            } else if (type == "religion") {
                showlisteBonusReligion();
            }
        }
    });
}

function afficherFormulaireBonus(type, idType, idBonus, position) {
    openPopupBonus("consultation", idBonus, type, idType, position);
}

function editerBonus(idBonus, type, idType, position) {
    openPopupBonus("edition", idBonus, type, idType, position);
}

function chargerDetailBonus() {
    var idBonus = jQuery('#selectBonus').val();
    if (idBonus == "0") {
        jQuery('#detailBonus').html("");
        jQuery('#detailBonus').hide();
    } else {
        jQuery.ajax({
            type: "POST",
            url: "Bonus/chargerDetailBonus",
            data: {
                idBonus: idBonus
            },
            success: function (data) {
                jQuery('#detailBonus').html(data);
                jQuery('#detailBonus').show();
            }
        });
    }
}

function enleverElementBonus(idElement) {
    var idBonus = jQuery('#selectBonus').val();
    if (jQuery('#idBonus').val() !== undefined) {
        idBonus = jQuery('#idBonus').val();
    }
    var param1 = jQuery('#param1').val();
    var param2 = jQuery('#param2').val();
    jQuery.ajax({
        type: "POST",
        url: "Bonus/enleverChoixMultiple",
        data: {
            idBonus: idBonus,
            id: idElement,
            param1: param1,
            param2: param2
        },
        success: function (data) {
            if (data == "errorProduit") {
                ouvreMsgBox("Une erreur s'est produite.", "error");
            } else {
                jQuery('#templateBonus').html(data);
            }
        }
    });
}

function ajouterElementBonus() {
    var idElement = jQuery('#listeSelectElement').val();
    if (idElement == "0" || idElement == 0) {
        ouvreMsgBox("Vous devez sélectionner un élément.", "error");
    } else {
        var idBonus = jQuery('#selectBonus').val();
        if (jQuery('#idBonus').val() !== undefined) {
            idBonus = jQuery('#idBonus').val();
        }
        var param1 = jQuery('#param1').val();
        var param2 = jQuery('#param2').val();
        jQuery.ajax({
            type: "POST",
            url: "Bonus/ajouterChoixMultiple",
            data: {
                idBonus: idBonus,
                id: idElement,
                param1: param1,
                param2: param2
            },
            success: function (data) {
                if (data == "errorProduit") {
                    ouvreMsgBox("Une erreur s'est produite.", "error");
                } else {
                    jQuery('#templateBonus').html(data);
                }
            }
        });
    }
}