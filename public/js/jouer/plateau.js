jQuery(document).ready(function init() {
    setBackground();
    chargerPlateauJeu();
});

function setBackground() {
    if (window.location.href.includes('/jouer/')) {
        url = "setBackground";
    } else {
        url = "jouer/setBackground";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            jQuery('body').css('background-image', data);
        }
    });
}

function chargerPlateauJeu() {
    if (window.location.href.includes('/jouer/')) {
        url = "chargerPlateauJeu";
    } else {
        url = "jouer/chargerPlateauJeu";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            chargerCSSTerrains(data);
        }
    });
}

function chargerCSSTerrains(plateauHtml) {
    if (window.location.href.includes('/jouer/')) {
        url = "chargerCSSTerrains";
    } else {
        url = "jouer/chargerCSSTerrains";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            eval(data);
            afficherPlateauHTML(plateauHtml);
        }
    });
}

function afficherPlateauHTML(plateau) {
    jQuery('#plateauJeu').html(plateau);
}

function createCSSClass(selector, style) {
    var css = document.createElement('style');
    css.type = 'text/css';
    css.innerHTML = selector + "{" + style + "}";
    document.getElementsByTagName('head')[0].appendChild(css);
}