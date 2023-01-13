function initBoutonFormule() {
    jQuery('#modifierFormuleConstante').click(function () {
        modifierFormuleConstante();
    });
    jQuery('#validerFormule').click(function () {
        validerFormule();
    });
    jQuery('#resetFormule').click(function () {
        resetFormule();
    });
    jQuery('#fermerFormulaireFormule').click(function () {
        fermerFormulaireFormule();
    });
}

function afficherFormulaireFormule(champ) {
    //On remplit la pop-up de l'article :
    jQuery.ajax({
        type: "POST",
        url: "Utils/afficherFormulaireFormule",
        data: {},
        success: function (data) {
            jQuery('#popupFormule').html(data);
            jQuery('#idDuChamp').val(champ);
            jQuery('#valeurFormule').val(jQuery('#' + champ).html());

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupFormule").before('<div id="graybackFormule"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupFormule").height();
            var popupW = jQuery("#popupFormule").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupFormule").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupFormule").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#graybackFormule").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupFormule").fadeIn(500);
            });
            initBoutonFormule();
        }
    });
}

function modifierFormule(valeur) {
    jQuery('#valeurFormule').val(jQuery('#valeurFormule').val() + valeur);
}

function resetFormule() {
    jQuery('#valeurFormule').val("0");
}

function chargerListeConstante() {
    var type = jQuery('#selectListeTypesConstante').val();
    jQuery.ajax({
        type: "POST",
        url: "Utils/chargerListeConstante",
        data: {
            type: type
        },
        success: function (data) {
            jQuery('#spanSelectListeConstantes').html(data);
            jQuery('#divDescriptionConstante').hide();
        }
    });
}

function chargerDescriptionConstante() {
    var idConstante = jQuery('#selectListeConstantes').val();
    jQuery.ajax({
        type: "POST",
        url: "Utils/chargerDescriptionConstante",
        data: {
            idConstante: idConstante
        },
        success: function (data) {
            jQuery('#divDescriptionConstante').show();
            jQuery('#descriptionCst').html(data);
        }
    });
}

function fermerFormulaireFormule() {
    // on fait disparaître le gris de fond rapidement
    jQuery("#graybackFormule").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupFormule").hide();
}

function validerFormule() {
    var champ = jQuery('#idDuChamp').val();
    var formule = jQuery('#valeurFormule').val();
    jQuery('#' + champ).html(formule);
    fermerFormulaireFormule();
}

function modifierFormuleConstante() {
    var texte = "@" + jQuery('#selectListeConstantes option:selected').text() + "_";
    jQuery('#valeurFormule').val(jQuery('#valeurFormule').val() + texte);
}