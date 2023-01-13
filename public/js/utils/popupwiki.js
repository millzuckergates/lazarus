function openPopUpWiki(idArticle) {
    //On remplit la pop-up de l'article :
    jQuery.ajax({
        type: "POST",
        url: "Utils/openPopupWiki",
        data: {
            idArticle: idArticle
        },
        success: function (data) {
            jQuery('#popupWiki').html(data);

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupWiki").before('<div id="grayBack"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupWiki").height();
            var popupW = jQuery("#popupWiki").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupWiki").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#grayBack").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupWiki").fadeIn(500);
            });
        }
    });
}

function closePopupWiki() {
    // on fait disparaître le gris de fond rapidement
    jQuery("#grayBack").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupWiki").hide();
}