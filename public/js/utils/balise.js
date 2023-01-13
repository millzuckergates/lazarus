//Init
jQuery(document).ready(function init() {
    initBoutonsBalise();
});

function initBoutonsBalise() {
    jQuery("#baliseTitre").click(function () {
        balise('titre');
    });
    jQuery("#baliseGras").click(function () {
        balise('g');
    });
    jQuery("#baliseItalique").click(function () {
        balise('i');
    });
    jQuery("#baliseSouligner").click(function () {
        balise('s');
    });
    jQuery("#baliseLien").click(function () {
        balise('lien');
    });
    jQuery("#baliseImage").click(function () {
        balise('img');
    });
    jQuery("#baliseMJ").click(function () {
        balise('mj');
    });
    jQuery("#baliseDEV").click(function () {
        balise('dev');
    });
    jQuery("#baliseLienWiki").click(function () {
        balise('wiki');
    });
    jQuery("#baliseProfil").click(function () {
        balise('profil');
    });
    jQuery("#baliseCiter").click(function () {
        balise('citer');
    });

    jQuery("#baliseTaille").click(function () {
        font('taille');
    });
    jQuery("#baliseCouleur").click(function () {
        font('couleur');
    });
    jQuery("#baliseRoyaume").click(function () {
        font('royaume');
    });
    jQuery("#baliseRace").click(function () {
        font('race');
    });
    jQuery("#baliseReligion").click(function () {
        font('religion');
    });
}

function balise(balise) {
    // obtain the object reference for the <textarea>
    var txtarea = document.getElementById("contenuArticle");
    if (txtarea == null) {
        txtarea = document.getElementById("texteNews");
    }
    // obtain the index of the first selected character
    var start = txtarea.selectionStart;
    // obtain the index of the last selected character
    var finish = txtarea.selectionEnd;
    // obtain the selected text
    var sel = txtarea.value.substring(start, finish);
    //En fonction de la balise

    if (balise == "g") {
        replaceSelText("[g]" + sel + "[/g]", start, finish);
    } else if (balise == "titre") {
        replaceSelText("[Titre]" + sel + "[/Titre]", start, finish);
    } else if (balise == "i") {
        replaceSelText("[i]" + sel + "[/i]", start, finish);
    } else if (balise == "s") {
        replaceSelText("[s]" + sel + "[/s]", start, finish);
    } else if (balise == "lien") {
        if (sel != "") {
            jQuery('#start').val(start);
            jQuery('#end').val(finish);
            jQuery('#texte').val(sel);
            ouvreMsgBox("Renseignez le lien", "question", "saisie", addLien, "http://");
        } else {
            jQuery('#start').val(start);
            jQuery('#end').val(finish);
            ouvreMsgBox("Renseignez le texte", "question", "saisie", addTexte, "");
        }
    } else if (balise == "mj") {
        replaceSelText("[MJ]" + sel + "[/MJ]", start, finish);
    } else if (balise == "dev") {
        replaceSelText("[MJ_BG]" + sel + "[/MJ_BG]", start, finish);
    } else if (balise == "profil") {
        replaceSelText("[profil]" + sel + "[/profil]", start, finish);
    } else if (balise == "citer") {
        replaceSelText("[citer]" + sel + "[/citer]", start, finish);
    } else if (balise == "wiki") {
        var link = sel.replace(/'/g, "_");
        replaceSelText("[[" + link + "|" + sel + "]]", start, finish);
    } else if (balise == "royaume") {
        var value = document.getElementById('outils_' + balise).value;
        replaceSelText("[royaume=" + value + "]" + sel + "[/royaume]", start, finish);
    } else if (balise == "race") {
        var value = document.getElementById('outils_' + balise).value;
        replaceSelText("[race=" + value + "]" + sel + "[/race]", start, finish);
    } else if (balise == "religion") {
        var value = document.getElementById('outils_' + balise).value;
        replaceSelText("[religion=" + value + "]" + sel + "[/religion]", start, finish);
    } else if (balise == "img") {
        replaceSelText("[img]" + sel + "[/img]", start, finish);
    }
}

function font(balise) {
    // obtain the object reference for the <textarea>
    var txtarea = document.getElementById("contenuArticle");
    if (txtarea == null) {
        txtarea = document.getElementById("texteNews");
    }
    // obtain the index of the first selected character
    var start = txtarea.selectionStart;
    // obtain the index of the last selected character
    var finish = txtarea.selectionEnd;
    // obtain the selected text
    var sel = txtarea.value.substring(start, finish);

    var value = jQuery('#outils_' + balise).val();
    replaceSelText("[" + balise + "=" + value + "]" + sel + "[/" + balise + "]", start, finish);
}


function replaceSelText(newText, selStart, selEnd) {
    var textArea = document.getElementById("contenuArticle");
    if (textArea != null) {
        if (selStart && selEnd) {
            jQuery('#contenuArticle').val(jQuery("#contenuArticle").val().substring(0, selStart) + newText + jQuery("#contenuArticle").val().substring(selEnd, parseInt(jQuery("#contenuArticle").val().length)));
        } else if (selStart) {
            jQuery("#contenuArticle").val((jQuery("#contenuArticle").val()).substring(0, selStart) + newText);
        } else if (selEnd) {
            jQuery("#contenuArticle").val(newText + (jQuery("#contenuArticle").val()).substring(selEnd, parseInt(jQuery("#contenuArticle").val().length)));
        } else {
            var selText = newText;
        }
    } else {
        if (selStart && selEnd) {
            jQuery('#texteNews').val(jQuery("#texteNews").val().substring(0, selStart) + newText + jQuery("#texteNews").val().substring(selEnd, parseInt(jQuery("#texteNews").val().length)));
        } else if (selStart) {
            jQuery("#texteNews").val((jQuery("#texteNews").val()).substring(0, selStart) + newText);
        } else if (selEnd) {
            jQuery("#texteNews").val(newText + (jQuery("#texteNews").val()).substring(selEnd, parseInt(jQuery("#texteNews").val().length)));
        } else {
            var selText = newText;
        }
    }
}

function addLien() {
    var saisie = recupSaisie();
    if (saisie != "") {
        var start = jQuery('#start').val();
        var finish = jQuery('#end').val();
        var texte = jQuery('#texte').val();
        replaceSelText("[lien=" + saisie + "]" + texte + "[/lien]", start, finish);
    } else {
        ouvreMsgBox("Vous devez renseigner un lien.", "error");
    }
}

function addTexte() {
    var saisie = recupSaisie();
    if (saisie != "") {
        jQuery('texte').val(saisie);
        ouvreMsgBox("Renseignez le lien", "question", "saisie", addLien, "http://");
    } else {
        ouvreMsgBox("Vous devez renseigner un texte.", "error");
    }
}