//variable global
var bouton_courant = false;
var lien_courant = false;

// Gestion d'un Message Box
function fermeMsgBox() {
    // on fait disparaître le gris de fond rapidement
    jQuery("#grayBack").remove();//fadeOut('fast', function () { jQuery(this).remove() });

    // on fait disparaître le popup à la même vitesse
    jQuery("#msgBox").hide();
}

function ouvreMsgBox(msg, img, type, action, msgSaisie, titre, special) {
    delier();
    // ici on insère dans notre page html notre div gris
    jQuery("#msgBox").before('<div id="grayBack"></div>');
    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#msgBox").height();
    var popupW = jQuery("#msgBox").width();

    // ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#msgBox").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#msgBox").css("margin-left", "-" + popupW / 2 + "px");

    form = document.forms["msgBox_form"];

    if (!msgSaisie) {
        msgSaisie = "";
    }
    if (!img) {
        img = 'info';
    }
    if (!type) {
        type = 'ok';
    }
    if (!titre) {
        titre = 'Info';
    }
    if (special) {
        document.imgMsgBox.src = img;
    } else {
        //TODO Image
        //document.imgMsgBox.src = repSite + "msgbox/" + img + ".png";
    }

    divMsgBox = document.getElementById("msgBoxMsg");
    setContent("msgBox_titre", titre);
    divMsgBox.innerHTML = nl2br(msg);

    // Initialisation affichage #msgBox_infoMsgRestant
    document.getElementById("msgBox_infoMsgRestant").style.display = "none";

    // Initialisation affichage des boutons de #msgBox
    form.boxOk.style.display = "none";
    form.annuler.style.display = "none";
    form.oui.style.display = "none";
    form.non.style.display = "none";
    form.saisie.style.display = "none";
    form.aide.style.display = "none";
    form.liste.style.display = "none";
    form.ok.style.display = "none";
    divMsgBox.style.height = "93px";
    // Modif affichage des boutons de #msgBox
    switch (type) {
        case "ok":
            form.boxOk.style.display = "inline";
            break;
        case "saisieaide":
            form.aide.style.display = "inline";
        case "saisie":
            divMsgBox.style.height = "45px";
            form.saisie.value = msgSaisie;
            form.saisie.style.display = "inline";
            form.ok.style.display = "inline";
            form.annuler.style.display = "inline";
            break;
        case 'liste':
            divMsgBox.style.height = "60px";
            form.liste.value = "";
            form.liste.options.length = 0;
            form.liste.style.display = "inline";
            form.boxOk.style.display = "inline";
            form.annuler.style.display = "inline";
            setListe(special, 0);
            break;
        case "okannuler":
            form.ok.style.display = "inline";
            form.annuler.style.display = "inline";
            break;
        case "ouinonannuler":
            form.annuler.style.display = "inline";
        case "ouinon":
            form.oui.style.display = "inline";
            form.non.style.display = "inline";
            break;
        case "ouinonaide":
            form.aide.style.display = "inline";
            form.oui.style.display = "inline";
            form.non.style.display = "inline";
            break;
    }
    //Gestion des boutons
    if (type == "saisie" && action != "") {
        lier('ok', action);
        form.saisie.focus();
    } else if (type == "ouinon" && action != "") {
        lier('oui', action);
    } else if (type == "liste" && action != "") {
        lier('ok', action);
    }

    // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
    // son apparition terminée, on fait apparaître en fondu notre popup
    jQuery("#grayBack").css('opacity', 0).fadeTo(300, 0.5, function () {
        jQuery("#msgBox").fadeIn(500);
    });
}

// Récupére la saisie de la boite de message
function recupSaisie() {
    return document.forms["msgBox_form"].saisie.value;
}

// Met un texte de saisie dans la boite de message
function setSaisie(str) {
    document.forms["msgBox_form"].saisie.value = str;
}

// Récupére la saisie de la boite de message
function recupListe() {
    return valueList(document.forms["msgBox_form"].liste);
}

// Met un texte de saisie dans la boite de message
function setListe(data, selIndex) {
    document.forms["msgBox_form"].liste.options.length = 0;
    i = 0;
    var group = false;
    for (value in data) {
        if (data[value] == "group") {
            group = document.createElement("optgroup");
            group.label = value;
            document.forms["msgBox_form"].liste.appendChild(group);
        } else {
            var option = document.createElement("option");
            option.text = data[value];
            option.innerText = data[value];
            option.value = data[value];
            if (group) {
                group.appendChild(option);
            } else {
                document.forms["msgBox_form"].liste.appendChild(option);
            }
        }
        i++;
    }
    if (selIndex) {
        document.forms["msgBox_form"].liste.options.selectedIndex = selIndex;
    }
}

function nl2br(string) {
    var retour = string;
    retour = retour.replace(/\r\n/gi, '<br />');
    retour = retour.replace(/\r/gi, '<br />');
    retour = retour.replace(/\n/gi, '<br />');

    return retour;
}

function setContent(objet, content) {
    document.getElementById(objet).innerHTML = content;
}

function lier(bouton, action) {
    bouton_courant = bouton;
    lien_courant = action;
    if (document.all) {
        document.forms["msgBox_form"][bouton_courant].attachEvent("onclick", action);
    } else {
        document.forms["msgBox_form"][bouton_courant].addEventListener("click", action, false);
    }
}

function delier() {
    if (bouton_courant && lien_courant) {
        if (document.all) {
            document.forms["msgBox_form"][bouton_courant].detachEvent("onclick", lien_courant);
        } else {
            document.forms["msgBox_form"][bouton_courant].removeEventListener("click", lien_courant, false);
        }
    }
}

function valueList(liste) {
    var id = liste.options.selectedIndex;
    if (id >= 0) {
        return liste.options[id].value;
    } else {
        return false;
    }
}

function deconnectProfilTest() {
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {
            action: "deconnectProfilTest"
        },
        success: function (data) {
            window.location.reload();
            ouvreMsgBox("Vous vous êtes déconnecté du profil de test.", "info");
        },
        error: function (errorThrown) {
            alert("An error occured" + errorThrown.error);
        }
    });
}