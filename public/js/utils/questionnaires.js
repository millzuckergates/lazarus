function initBoutonQuestionnaire() {
    jQuery('#boutonAjouterQuestion').click(function () {
        afficherFormulaireQuestionnaire('new');
    });
    jQuery('#boutonRetourFormulaire').click(function () {
        retourFormulaire();
    });
}

function consulterQuestionnaire(type, idType) {
    if (window.location.href.includes('/administration/')) {
        url = "consulterQuestionnaire";
    } else {
        url = "administration/consulterQuestionnaire";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            idType: idType
        },
        success: function (data) {
            jQuery('#blocAdministration').html(data);
            initBoutonQuestionnaire();
        }
    });
}

function desactiverQuestionnaire(idQuestionnaire) {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();

    if (window.location.href.includes('/administration/')) {
        url = "desactiverQuestionnaire";
    } else {
        url = "administration/desactiverQuestionnaire";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            idType: idType,
            idQuestionnaire: idQuestionnaire
        },
        success: function (data) {
            jQuery('#blocAdministration').html(data);
            initBoutonQuestionnaire();
        }
    });
}

function activerQuestionnaire(idQuestionnaire) {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();

    if (window.location.href.includes('/administration/')) {
        url = "activerQuestionnaire";
    } else {
        url = "administration/activerQuestionnaire";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            idType: idType,
            idQuestionnaire: idQuestionnaire
        },
        success: function (data) {
            jQuery('#blocAdministration').html(data);
            initBoutonQuestionnaire();
        }
    });
}

function boxSupprimerQuestionnaire(idQuestionnaire) {
    jQuery('#idDeleteQuestionnaire').val(idQuestionnaire);
    ouvreMsgBox("Supprimer cette question la supprimera définitivement. Voulez-vous continuer ?", "question", "ouinon", supprimerQuestionnaire, "");
}

function supprimerQuestionnaire() {
    var idQuestionnaire = jQuery('#idDeleteQuestionnaire').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();

    if (window.location.href.includes('/administration/')) {
        url = "supprimerQuestionnaire";
    } else {
        url = "administration/supprimerQuestionnaire";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            idType: idType,
            idQuestionnaire: idQuestionnaire
        },
        success: function (data) {
            jQuery('#blocAdministration').html(data);
            jQuery('#idDeleteQuestionnaire').val("");
            initBoutonQuestionnaire();
        }
    });
}

function afficherFormulaireQuestionnaire(idQuestionnaire) {
    var mode = "modification";
    if (idQuestionnaire == "new") {
        mode = "creation";
    }
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    //On remplit la pop-up du questionnaire :
    if (window.location.href.includes('/administration/')) {
        url = "afficherFormulaireQuestionnaire";
    } else {
        url = "administration/afficherFormulaireQuestionnaire";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            idType: idType,
            idQuestionnaire: idQuestionnaire,
            mode: mode
        },
        success: function (data) {
            jQuery('#popupQuestionnaire').html(data);
            if (idQuestionnaire == null) {
                jQuery('#idQuestionnaire').val("");
            } else {
                jQuery('#idQuestionnaire').val(idQuestionnaire);
            }

            // Ici on insère dans notre page html notre div gris
            jQuery("#popupQuestionnaire").before('<div id="grayBack"></div>');
            // maintenant, on récupère la largeur et la hauteur de notre popup
            var popupH = jQuery("#popupQuestionnaire").height();
            var popupW = jQuery("#popupQuestionnaire").width();

            // Ensuite, on crée des marges négatives pour notre popup, chacune faisant
            // la moitié de la largeur/hauteur du popup
            jQuery("#popupQuestionnaire").css("margin-top", "-" + popupH / 2 + "px");
            jQuery("#popupQuestionnaire").css("margin-left", "-" + popupW / 2 + "px");

            // enfin, on fait apparaître en 300 ms notre div gris de fond, et une fois
            // son apparition terminée, on fait apparaître en fondu notre popup
            jQuery("#grayBack").css('opacity', 0).fadeTo(300, 0.5, function () {
                jQuery("#popupQuestionnaire").fadeIn(500);
            });
        }
    });
}

function retourFormulaire() {
    closePopupQuestionnaire();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    openOngletAdministrationFromQuestionnaire('referentiels', type, idType);
}

function closePopupQuestionnaire() {
    jQuery('#idQuestionnaire').val("");
    // on fait disparaître le gris de fond rapidement
    jQuery("#grayBack").remove();//fadeOut('fast', function () { jQuery(this).remove() });
    // on fait disparaître le popup à la même vitesse
    jQuery("#popupQuestionnaire").hide();
}

function modifierQuestionnaire() {
    var idQuestionnaire = jQuery('#idQuestionnaire').val();
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var question = jQuery('#champQuestion').val();
    var choixA = jQuery('#champChoixA').val();
    var choixB = jQuery('#champChoixB').val();
    var choixC = jQuery('#champChoixC').val();
    var reponse = jQuery('#selectQuestionnaireReponse').val();
    var titreArticle = jQuery('#s').val();
    var paragraphe = jQuery('#selectQuestionnaireParagraphe').val();

    //Contrôle des champs
    var erreur = "";
    var questionSansEspace = question.replace(/\s/g, "");
    if (questionSansEspace == "") {
        erreur += "Il faut renseigner une question.\n";
        jQuery('#champQuestion').addClass("erreur");
    }

    var choixASansEspace = choixA.replace(/\s/g, "");
    if (choixASansEspace == "") {
        erreur += "Il faut renseigner un choix A.\n";
        jQuery('#champChoixA').addClass("erreur");
    }

    var choixBSansEspace = choixB.replace(/\s/g, "");
    if (choixBSansEspace == "") {
        erreur += "Il faut renseigner un choix B.\n";
        jQuery('#champChoixB').addClass("erreur");
    }

    var choixCSansEspace = choixC.replace(/\s/g, "");
    if (choixCSansEspace == "") {
        erreur += "Il faut renseigner un choix C.\n";
        jQuery('#champChoixC').addClass("erreur");
    }

    if (reponse == "0" || reponse == 0) {
        erreur += "Une réponse est obligatoire.\n";
        jQuery('#selectQuestionnaireReponse').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierQuestionnaire";
        } else {
            url = "administration/modifierQuestionnaire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                type: type,
                idType: idType,
                idQuestionnaire: idQuestionnaire,
                question: question,
                choixA: choixA,
                choixB: choixB,
                choixC: choixC,
                reponse: reponse,
                titreArticle: titreArticle,
                paragraphe: paragraphe
            },
            success: function (data) {
                jQuery('#idQuestionnaire').val();
                cleanErrorQuestionnaire();
                closePopupQuestionnaire();
                jQuery('#blocAdministration').html(data);
                ouvreMsgBox("La question a été correctement modifiée", "info");
                initBoutonQuestionnaire();
            }
        });
    }
}

function cleanErrorQuestionnaire() {
    if (jQuery("#champQuestion").hasClass("erreur")) {
        jQuery('#champQuestion').removeClass("erreur");
    }
    if (jQuery("#selectQuestionnaireReponse").hasClass("erreur")) {
        jQuery('#selectQuestionnaireReponse').removeClass("erreur");
    }
}

function ajouterQuestionnaire() {
    var type = jQuery('#type').val();
    var idType = jQuery('#idType').val();
    var question = jQuery('#champQuestion').val();
    var choixA = jQuery('#champChoixA').val();
    var choixB = jQuery('#champChoixB').val();
    var choixC = jQuery('#champChoixC').val();
    var reponse = jQuery('#selectQuestionnaireReponse').val();
    var titreArticle = jQuery('#s').val();
    var paragraphe = jQuery('#selectQuestionnaireParagraphe').val();

    //Contrôle des champs
    var erreur = "";
    var questionSansEspace = question.replace(/\s/g, "");
    if (questionSansEspace == "") {
        erreur += "Il faut renseigner une question.\n";
        jQuery('#champQuestion').addClass("erreur");
    }

    var choixASansEspace = choixA.replace(/\s/g, "");
    if (choixASansEspace == "") {
        erreur += "Il faut renseigner un choix A.\n";
        jQuery('#champChoixA').addClass("erreur");
    }

    var choixBSansEspace = choixB.replace(/\s/g, "");
    if (choixBSansEspace == "") {
        erreur += "Il faut renseigner un choix B.\n";
        jQuery('#champChoixB').addClass("erreur");
    }

    var choixCSansEspace = choixC.replace(/\s/g, "");
    if (choixCSansEspace == "") {
        erreur += "Il faut renseigner un choix C.\n";
        jQuery('#champChoixC').addClass("erreur");
    }

    if (reponse == "0" || reponse == 0) {
        erreur += "Une réponse est obligatoire.\n";
        jQuery('#selectQuestionnaireReponse').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterQuestionnaire";
        } else {
            url = "administration/ajouterQuestionnaire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                type: type,
                idType: idType,
                question: question,
                choixA: choixA,
                choixB: choixB,
                choixC: choixC,
                reponse: reponse,
                titreArticle: titreArticle,
                paragraphe: paragraphe
            },
            success: function (data) {
                cleanErrorQuestionnaire();
                closePopupQuestionnaire();
                ouvreMsgBox("La question a été correctement ajoutée", "info");
                jQuery('#blocAdministration').html(data);
                initBoutonQuestionnaire();
            }
        });
    }
}

function chargerParagraphe(titre) {
    if (window.location.href.includes('/administration/')) {
        url = "chargerParagraphe";
    } else {
        url = "administration/chargerParagraphe";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            titre: titre
        },
        success: function (data) {
            jQuery('#questionnaireParagraphe').html(data);
        }
    });
}

function chargeSuggestionArticleQuestionnaire() {
    var value = jQuery('#s').val();
    if (value != "") {
        var isLoading = false;
        if (isLoading == false) {
            isLoading = true;
            jQuery.ajax({
                type: "GET",
                url: "Utils/chargerSuggestionArticle",
                data: {
                    recherche: value,
                    idArticle: jQuery('#idArticle').val()
                },
                success: function (res) {
                    var result = '';
                    var obj = jQuery.parseJSON(res);
                    if (obj.length > 0) {
                        result += '<ul class="listeDesSuggestions">';
                        for (var i = 0; i < obj.length; i++) {
                            var title = obj[i];
                            var pourClick = "'" + title['titre'] + "'";
                            result += '<li class="suggest" onclick="remplacerTitreQuestionnaire(' + pourClick + ');">' + title['titre'] + '</li>';
                        }
                        result += '</ul>';
                        jQuery("#suggestions").html(result);
                    }
                }
            }).done(function () {
                isLoading = false;
            });
        }
    } else {
        jQuery("#suggestions").html("");
    }
}

function remplacerTitreQuestionnaire(titre) {
    jQuery('#s').val(titre);
    jQuery("#suggestions").html("");
    chargerParagraphe(titre);
}