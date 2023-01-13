//Init
jQuery(document).ready(function init() {
    //Initialisation des boutons
    jQuery("#showSearch").click(function () {
        displayBlocRecherche();
    });
    jQuery("#showSearch").show();
    jQuery("#hideSearch").click(function () {
        hideBlocRecherche();
    });
    jQuery("#hideSearch").hide();
    jQuery("#boutonAideWiki").click(function () {
        afficherAideRechercheWiki();
    });
    jQuery("#lancerRechercheWiki").click(function () {
        lancerRechercheWiki();
    });
    jQuery("#s").keyup(function () {
        chargeSuggestionPere();
    });
    jQuery("#ajouterPereArticle").click(function () {
        ajouterPereArticle();
    })
    jQuery("#changerImageWiki").click(function () {
        changerImageWiki();
    })
    jQuery('#enregistrerWiki').click(function () {
        enregistrerWiki();
    });
    jQuery('#annulerPropositionWiki').click(function () {
        annulerPropositionWiki();
    });
    jQuery('#demanderRevisionArticleWiki').click(function () {
        boxDemanderRevisionArticle();
    });
    jQuery('#demanderValiderArticleWiki').click(function () {
        boxDemanderValidationArticle();
    });
    jQuery('#reviserArticleWiki').click(function () {
        boxReviserArticleWiki();
    });
    jQuery('#restaurerArticleWiki').click(function () {
        boxRestaurerArticleWiki();
    });
    jQuery('#annulerRevisionArticleWiki').click(function () {
        boxAnnulerReviserArticleWiki();
    });
    jQuery('#archiverArticleWiki').click(function () {
        boxArchiverArticleWiki();
    });
    jQuery('#supprimerArticleWiki').click(function () {
        boxSupprimerArticleWiki();
    });
    jQuery('#boutonSupprimerEnProgression').click(function () {
        boxPurgerArticles();
    });

    jQuery("#showlisteArticlesEnCours").click(function () {
        displayBloc("listeArticlesEnCours");
    });
    jQuery("#showlisteArticlesEnCours").show();
    jQuery("#hidelisteArticlesEnCours").click(function () {
        hideBloc("listeArticlesEnCours");
    });
    jQuery("#hidelisteArticlesEnCours").hide();
    jQuery("#showlisteArticlesEnValidation").click(function () {
        displayBloc("listeArticlesEnValidation");
    });
    jQuery("#showlisteArticlesEnValidation").show();
    jQuery("#hidelisteArticlesEnValidation").click(function () {
        hideBloc("listeArticlesEnValidation");
    });
    jQuery("#hidelisteArticlesEnValidation").hide();
    jQuery("#showlisteArticlesEnRevision").click(function () {
        displayBloc("listeArticlesEnRevision");
    });
    jQuery("#showlisteArticlesEnRevision").show();
    jQuery("#hidelisteArticlesEnRevision").click(function () {
        hideBloc("listeArticlesEnRevision");
    });
    jQuery("#hidelisteArticlesEnRevision").hide();

    jQuery('#envoyerNoteArticle').click(function () {
        envoyerNoteArticle();
    });
    jQuery("#showlisteContenuNotes").click(function () {
        displayBloc("listeContenuNotes");
    });
    jQuery("#showlisteContenuNotes").show();
    jQuery("#hidelisteContenuNotes").click(function () {
        hideBloc("listeContenuNotes");
    });
    jQuery("#hidelisteContenuNotes").hide();

    jQuery('#historiqueArticle').click(function () {
        ouvrePopupHistorique();
    });
    jQuery('#historiqueArticle').attr('href', '#');
    jQuery('#fermerHistoriqueArticle').click(function () {
        fermerPopupHistorique();
    });

    var compteurTitre = jQuery('#compteurTitre').val();
    if (compteurTitre != 0) {
        for (var i = 1; i <= compteurTitre; i++) {
            var name = "categorie" + i;
            jQuery("#show" + name).show();
            jQuery("#hide" + name).hide();
        }
    }
});


//Fonction du bloc de recherche
function displayBlocRecherche() {
    jQuery("#showSearch").hide();
    jQuery("#hideSearch").show();
    jQuery("#contenuBlocRechercheWiki").show();
}

function hideBlocRecherche() {
    jQuery("#showSearch").show();
    jQuery("#hideSearch").hide();
    jQuery("#contenuBlocRechercheWiki").hide();
}

function afficherAideRechercheWiki() {
    alert("TODO");
}

function lancerRechercheWiki() {
    //Récupération des variables pour la recherche
    var titre = jQuery("#searchTitle").val();
    var contenu = jQuery("#searchContent").val();
    var motClef = jQuery("#searchKeyWord").val();
    if (jQuery("#statusArticle")) {
        var status = jQuery("#statusArticle").val();
    } else {
        status = "";
    }
    //Vérification des champs
    var titreSansEspace = titre;
    var contenuSansEspace = contenu;
    var motClefSansEspace = motClef;
    if (titreSansEspace.replace(/\s/g, "") == "" && contenuSansEspace.replace(/\s/g, "") == "" && motClefSansEspace.replace(/\s/g, "") == "" && status == "") {
        ouvreMsgBox("Vous devez renseigner au moins un critère de recherche.", "error");
    } else {
        //Gestion de l'url
        var url = "";
        if (window.location.href.includes('/wiki/')) {
            url = "recherche";
        } else {
            url = "Wiki/recherche";
        }
        //Appel jquery pour lancer la recherche
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                titre: titre,
                contenu: contenu,
                motClef: motClef,
                status: status
            },
            success: function (data) {
                if (jQuery('#divPageArticle')) {
                    jQuery('#divPageArticle').hide();
                }
                if (jQuery('#listeArticleIndex')) {
                    jQuery('#listeArticleIndex').hide();
                }
                jQuery('#blocNoteWiki').hide();
                jQuery('#blocHistorique').hide();
                jQuery('#informationsArticle').hide();
                jQuery('#resultatRecherche').html(data);
                jQuery('#resultatRecherche').show();
                jQuery('html, #pageArticle').animate({scrollTop: 150}, 'slow');
            },
            error: function (errorThrown) {

            }
        });
    }
}

//Actions relatives à l'article
function chargeSuggestionPere() {
    var value = jQuery('#s').val();
    if (value != "") {
        var isLoading = false;
        if (isLoading == false) {
            isLoading = true;
            jQuery.ajax({
                type: "GET",
                url: "../Utils/chargerSuggestionPere",
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
                            result += '<li class="suggest" onclick="remplacerPere(' + pourClick + ');">' + title['titre'] + '</li>';
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

function ajouterPereArticle() {
    var titrePere = jQuery('#s').val();
    jQuery.ajax({
        type: "POST",
        url: "ajouterPere",
        data: {
            idArticle: jQuery('#idArticle').val(),
            titrePere: titrePere
        },
        success: function (data) {
            if (data == "errorArticle") {
                ouvreMsgBox("L'article n'a pas été trouvé.", "erreur");
                jQuery("#suggestions").hmtl("");
            } else if (data == "errorDroit") {
                ouvreMsgBox("Vous n'avez pas les droits pour inscrire cet article à la racine.", "erreur");
                jQuery("#suggestions").html("");
            } else {
                jQuery('#filArianneComplet').html(data);
                jQuery('#s').val(titrePere);
                jQuery("#suggestions").html("");
                ouvreMsgBox("Modification effectuée.", "info");
            }
        },
        error: function (errorThrown) {

        }
    });
}

function remplacerPere(titre) {
    jQuery('#s').val(titre);
    jQuery("#suggestions").html("");
}

function changerImageWiki() {
    var src = jQuery('#img').val();
    if (src.startsWith('public/img/wiki/')) {
        src = "/" + src;
    }
    jQuery("#imageArticle").attr("src", src);
}

function enregistrerWiki() {
    var contenu = jQuery('#contenuArticle').val();
    var img = jQuery('#img').val();
    var titre = jQuery('#titreEdition').val();
    var idArticle = jQuery('#idArticle').val();

    //Verification des data
    var contenuSansEspace = contenu.replace(/\s/g, "");
    var imgSansEspace = img.replace(/\s/g, "");
    var titreSansEspace = titre.replace(/\s/g, "");

    var erreur = "";
    if (contenuSansEspace == "") {
        erreur += "Un contenu est obligatoire pour l'article.\n";
        jQuery('#contenu').addClass("erreur");
    }
    if (imgSansEspace == "") {
        erreur += "Une image est obligatoire pour l'article.\n";
        jQuery('#addImage').addClass("erreur");
    }
    if (titreSansEspace == "") {
        erreur += "Un titre est obligatoire pour l'article.\n";
        jQuery('#titre').addClass("erreur");
    }

    if (erreur == "") {
        jQuery.ajax({
            type: "POST",
            url: "saveArticle",
            data: {
                idArticle: idArticle,
                contenu: contenu,
                img: img,
                titre: titre
            },
            success: function (data) {
                if (data == "errorTitre") {
                    jQuery('html, body').animate({scrollTop: 0}, 'slow');
                    ouvreMsgBox("Ce titre est déjà pris.", "error");
                } else {
                    ouvreMsgBox("L'article a été modifié avec succès !", "info");
                }
            }
        });
    } else {
        ouvreMsgBox(erreur, "error");
    }
}

function cleanClassErrorArticle() {
    if (jQuery("#contenu").hasClass("erreur")) {
        jQuery('#contenu').removeClass("erreur");
    }
    if (jQuery("#titre").hasClass("erreur")) {
        jQuery('#titre').removeClass("erreur");
    }
    if (jQuery("#addImage").hasClass("erreur")) {
        jQuery('#addImage').removeClass("erreur");
    }
}

function annulerPropositionWiki() {
    ouvreMsgBox("Voulez-vous annuler votre proposition d'article ? Cela supprimera toutes les informations liées à cette proposition.", "question", "ouinon", supprimerArticle, "");

}

function supprimerArticle() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "supprimerArticle",
            data: {
                idArticle: idArticle
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Vous n'avez pas le droit de supprimer cet article ! Sinon, ça casse tout !", "error");
                } else {
                    window.location.href = data;
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        jQuery('html, body').animate({scrollTop: 0}, 'slow');
        ouvreMsgBox("Une erreur est survenue. L'article est incorrectement renseigné.", "erreur");
    }
}

function boxDemanderRevisionArticle() {
    ouvreMsgBox("Entrez un commentaire pour justifier votre demande de révision de l'article :", "question", "saisie", demandeRevisionArticle, "Votre commentaire");
}

function demandeRevisionArticle() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null) {
        var commentaire = recupSaisie();
        var commentaireSansEspace = commentaire.replace(/\s/g, "");
        if (commentaire == "Votre commentaire" || commentaireSansEspace == "") {
            ouvreMsgBox("Vous devez entrer un commentaire", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "demandeRevisionArticle",
                data: {
                    idArticle: idArticle,
                    commentaire: commentaire
                },
                success: function (data) {
                    ouvreMsgBox("Votre demande a bien été prise en compte !", "info");
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                },
                error: function (errorThrown) {
                    alert("An error occured");
                }
            });
        }
    }
}

function boxDemanderValidationArticle() {
    ouvreMsgBox("Effectuer une demande de validation enverra une notification aux responsables du wiki pour valider l'article et le rendra inaccessible pendant ce temps. Validez-vous votre choix ?", "question", "ouinon", demanderValidationArticle);
}

function demanderValidationArticle() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "demanderValidationArticle",
            data: {
                idArticle: idArticle
            },
            success: function (data) {
                if (data == "errorStatus") {
                    ouvreMsgBox("Le status de l'article ne permet pas une demande de validation.", "error");
                } else {
                    jQuery('html, body').animate({scrollTop: 0}, 'slow');
                    ouvreMsgBox("Votre demande a bien été prise en compte !", "info");
                    window.location.href = data;
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox("Une erreur est survenue. L'article est incorrectement renseignés.", "erreur");
    }
}

function boxReviserArticleWiki() {
    ouvreMsgBox("Confirmez vous la révision de l'article ?", "question", "ouinon", reviserArticleWiki);
}

function reviserArticleWiki() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "reviserArticle",
            data: {
                idArticle: idArticle
            },
            success: function (data) {
                //Retour à l'index + notification ok
                ouvreMsgBox("L'article est désormais en révision.", "info");
                setTimeout(function () {
                    location.reload();
                }, 500);
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox("Une erreur est survenue. L'article et/ou le personnage sont incorrectement renseignés.", "erreur");
    }
}

function boxRestaurerArticleWiki() {
    ouvreMsgBox("Confirmez vous la restauration de l'article ?", "question", "ouinon", restaurerArticleWiki);
}

function restaurerArticleWiki() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "restaurerArticle",
            data: {
                idArticle: idArticle
            },
            success: function (data) {
                //Retour à l'index + notification ok
                ouvreMsgBox("L'article est désormais réstauré.", "info");
                setTimeout(function () {
                    location.reload();
                }, 500);
            },
            error: function (errorThrown) {

            }
        });
    } else {
        jQuery('html, body').animate({scrollTop: 0}, 'slow');
        ouvreMsgBox("Une erreur est survenue. L'article et/ou le personnage sont incorrectement renseignés.", "erreur");
    }
}

function boxAnnulerReviserArticleWiki() {
    ouvreMsgBox("Entrez un commentaire pour justifier votre demande d'annulation de l'article", "question", "saisie", annulerReviserArticleWiki, "Annulation de la demande de révision.");
}

function annulerReviserArticleWiki() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        var commentaire = recupSaisie();
        var commentaireSansEspace = commentaire.replace(/\s/g, "");
        if (commentaireSansEspace == "") {
            ouvreMsgBox("Vous devez entrer un commentaire.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "annulerReviserArticle",
                data: {
                    idArticle: idArticle,
                    commentaire: commentaire
                },
                success: function (data) {
                    ouvreMsgBox("L'article est désormais réstauré.", "info");
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                },
                error: function (errorThrown) {

                }
            });
        }
    } else {
        jQuery('html, body').animate({scrollTop: 0}, 'slow');
        ouvreMsgBox("Une erreur est survenue. L'article et/ou le personnage sont incorrectement renseignés.", "erreur");
    }
}

function boxArchiverArticleWiki() {
    ouvreMsgBox("Archiver l'article le rendra inaccessible. Entrez un commentaire pour justifier votre demande d'archivage de l'article", "question", "saisie", archiverArticleWiki, "Archivage de l'article.");
}

function archiverArticleWiki() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        var commentaire = recupSaisie();
        var commentaireSansEspace = commentaire.replace(/\s/g, "");
        if (commentaireSansEspace == "") {
            ouvreMsgBox("Vous devez entrer un commentaire.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "archiverArticle",
                data: {
                    idArticle: idArticle,
                    commentaire: commentaire
                },
                success: function (data) {
                    jQuery('html, body').animate({scrollTop: 0}, 'slow');
                    ouvreMsgBox("L'article a été correctement archivé.", "info");
                    window.location.href = data;
                },
                error: function (errorThrown) {

                }
            });
        }
    } else {
        ouvreMsgBox("Une erreur est survenue. L'article et/ou le personnage sont incorrectement renseignés.", "erreur");
    }
}

function boxSupprimerArticleWiki() {
    ouvreMsgBox("Supprimer l'article rendra cela définitif. Il n'apparaîtra plus dans la base de données, son historique sera effacé et il n'y aura plus aucun moyen de le récupérer. Etes-vous sûr(e) de votre choix ?", "question", "ouinon", supprimerArticle);
}

function boxRetirerFils(idFils) {
    jQuery('#idFils').val(idFils);
    ouvreMsgBox("Retirer ce lient de parenté revient à placer l'article fils dans la section 'Autres'. Coninuer ?", "question", "ouinon", retirerFils);
}

function retirerFils() {
    var idArticle = jQuery('#idArticle').val();
    var idFils = jQuery('#idFils').val();
    if (idArticle != null && idFils != null) {
        jQuery.ajax({
            type: "POST",
            url: "retirerFils",
            data: {
                idArticle: idArticle,
                idFils: idFils,
            },
            success: function (data) {
                jQuery('#listeDesFilsEdition').html(data);
            },
            error: function (errorThrown) {

            }
        });
    }
}

//Action administratives du Wiki
function boxPurgerArticles() {
    ouvreMsgBox("Cette action va purger les articles qui sont encore au status 'En proposition' depuis au moins trois jours. Continuez ?", "question", "ouinon", purgerArticles);
}

function purgerArticles() {
    var url = "";
    if (jQuery('#idArticle').val() === undefined) {
        url = "Wiki/purgerArticles";
    } else {
        url = "purgerArticles";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            if (data == "") {
                data = 0;
            }
            jQuery('html, body').animate({scrollTop: 0}, 'slow');
            ouvreMsgBox("La purge a supprimé " + data + " article(s).", "info");
        },
        error: function (errorThrown) {

        }
    });
}

//Action pour les notifications
function displayBloc(type) {
    jQuery("#show" + type).hide();
    jQuery("#hide" + type).show();
    jQuery("#" + type).show();
}

function hideBloc(type) {
    jQuery("#show" + type).show();
    jQuery("#hide" + type).hide();
    jQuery("#" + type).hide();
}

function displayBlocCategorie(name) {
    var idHide = "hide" + name;
    var idShow = "show" + name;
    jQuery('#' + idShow).hide();
    jQuery('#' + idHide).show();
    jQuery('#' + name).show();
}

function hideBlocCategorie(name) {
    var idHide = "hide" + name;
    var idShow = "show" + name;
    document.getElementById(idShow).style.display = "inline-block";
    document.getElementById(idHide).style.display = "none";
    document.getElementById(name).style.display = "none";
}

//Actions relatives aux notes
function boxDeleteNote(idNote) {
    jQuery('#idNote').val(idNote);
    ouvreMsgBox("Supprimer la note ?", "question", "ouinon", deleteNoteArticle);

}

function deleteNoteArticle() {
    var idNote = jQuery('#idNote').val();
    if (idNote != null || idNote != "") {
        jQuery.ajax({
            type: "POST",
            url: "deleteNoteArticle",
            data: {
                idNote: idNote
            },
            success: function (data) {
                jQuery('#listeContenuNotes').html(data);
                ouvreMsgBox("La note a été correctement supprimée.", "info");
                jQuery('#envoyerNoteArticle').click(function () {
                    envoyerNoteArticle();
                });
            },
            error: function (errorThrown) {

            }
        });
    }
}

function envoyerNoteArticle() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        var contenu = jQuery("#newNote").val();
        var contenuSansEspace = contenu;
        if (contenuSansEspace.replace(/\s/g, "") == "") {
            ouvreMsgBox("Vous devez renseigner un contenu pour la note.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "ajouterNoteArticle",
                data: {
                    idArticle: idArticle,
                    contenu: contenu
                },
                success: function (data) {
                    jQuery('#listeContenuNotes').html(data);
                    ouvreMsgBox("La note a été correctement ajoutée.", "info");
                    jQuery('#envoyerNoteArticle').click(function () {
                        envoyerNoteArticle();
                    });
                },
                error: function (errorThrown) {

                }
            });
        }
    }
}

//Actions relatives à l'historique
function ouvrePopupHistorique() {
    jQuery('#popupHistoriqueArticle').before('<div id="grayBack"></div>');

    // maintenant, on récupère la largeur et la hauteur de notre popup
    var popupH = jQuery("#popupHistoriqueArticle").height();
    var popupW = jQuery("#popupHistoriqueArticle").width();

    // ensuite, on crée des marges négatives pour notre popup, chacune faisant
    // la moitié de la largeur/hauteur du popup
    jQuery("#popupHistoriqueArticle").css("margin-top", "-" + popupH / 2 + "px");
    jQuery("#popupHistoriqueArticle").css("margin-left", "-" + popupW / 2 + "px");

    jQuery('#popupHistoriqueArticle').show();
}

function fermerPopupHistorique() {
    // on fait disparaître le gris de fond rapidement
    jQuery("#grayBack").remove();

    // on fait disparaître le popup à la même vitesse
    jQuery("#popupHistoriqueArticle").hide();
}

function lireSuite(id) {
    var element = "texte_" + id;
    if (jQuery('#' + element).is(':visible')) {
        jQuery('#' + element).hide();
    } else {
        jQuery('#' + element).show();
    }
}

//Actions relatives aux mots clefs
function boxAjouterMotClef() {
    ouvreMsgBox("Entrez un mot-clef à ajouter", "question", "saisie", ajouterMotClef, "");
}

function ajouterMotClef() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != null && idArticle != "") {
        var motClef = recupSaisie();
        var newmotClef = motClef;
        if (newmotClef.replace(/\s/g, "") == "") {
            ouvreMsgBox("Vous devez renseigner un mot clef.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "ajouterMotClef",
                data: {
                    motClef: motClef,
                    idArticle: idArticle
                },
                success: function (data) {
                    if (data == "error") {
                        ouvreMsgBox("Cette association existe déjà.", "erreur");
                    } else {
                        //On met à jour la liste des mots clefs
                        jQuery('#listeMotClef').html(data);
                    }
                },
                error: function (errorThrown) {

                }
            });
        }
    }
}

function boxRetirerMotClef(idMotClef) {
    jQuery('#idMotclefDelete').val(idMotClef);
    ouvreMsgBox("Supprimez ce mot clef ?", "question", "ouinon", supprimerMotClef, "");
}

function supprimerMotClef() {
    var idArticle = jQuery('#idArticle').val();
    var idMotClef = jQuery('#idMotclefDelete').val();
    if (idArticle != null && idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "supprimerMotClef",
            data: {
                idMotClef: idMotClef,
                idArticle: idArticle
            },
            success: function (data) {
                //On met à jour la liste des mots clefs
                jQuery('#listeMotClef').html(data);
            },
            error: function (errorThrown) {

            }
        });
    }
}

function rechercheMotClef(idMotClef) {
    if (idMotClef != null && idMotClef != "") {
        jQuery.ajax({
            type: "POST",
            url: "rechercheMotClef",
            data: {
                idMotClef: idMotClef
            },
            success: function (data) {
                jQuery('#resultatRecherche').html(data);
                jQuery('#resultatRecherche').show();
                if (jQuery('#divPageArticle')) {
                    jQuery('#divPageArticle').hide();
                }
                if (jQuery('#listeArticleIndex')) {
                    jQuery('#listeArticleIndex').hide();
                }
                jQuery('#informationsArticle').hide();
                jQuery('#blocNoteWiki').hide();
                jQuery('#blocHistorique').hide();
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox("Une erreur est survenue.", "erreur");
    }
}

//Actions relatives aux restrictions
function chargerListeRestriction() {
    var type = jQuery('#typeRestriction').val();
    jQuery.ajax({
        type: "POST",
        url: "chargerRestrictions",
        data: {
            type: type
        },
        success: function (data) {
            jQuery('#choixRestriction').html(data);
        },
        error: function (errorThrown) {

        }

    });
}

function ajouterAutorisation() {
    var id = jQuery('#autorisation').val();
    var idArticle = jQuery('#idArticle').val();
    if (id != 0) {
        jQuery.ajax({
            type: "POST",
            url: "ajouterRestriction",
            data: {
                type: "Autorisation",
                idArticle: idArticle,
                id: id
            },
            success: function (data) {
                if (data == "errorexistant") {
                    ouvreMsgBox("Cette restriction est déjà présente.", "error");
                } else {
                    jQuery('#listeRestrictions').html(data);
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox('Sélectionnez une autorisation.', 'error');
    }
}

function retirerRestriction(idRestriction) {
    var idArticle = jQuery('#idArticle').val();
    jQuery.ajax({
        type: "POST",
        url: "retirerRestriction",
        data: {
            idRestriction: idRestriction,
            idArticle: idArticle
        },
        success: function (data) {
            jQuery('#listeRestrictions').html(data);
        },
        error: function (errorThrown) {

        }
    });
}

function ajouterRoyaume() {
    var id = jQuery('#royaume').val();
    var idArticle = jQuery('#idArticle').val();
    if (id != 0) {
        jQuery.ajax({
            type: "POST",
            url: "ajouterRestriction",
            data: {
                type: "Royaume",
                idArticle: idArticle,
                id: id
            },
            success: function (data) {
                if (data == "errorexistant") {
                    ouvreMsgBox("Cette restriction est déjà présente.", "error");
                } else {
                    jQuery('#listeRestrictions').html(data);
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox('Sélectionnez un royaume.', 'error');
    }
}

function ajouterReligion() {
    var id = jQuery('#religion').val();
    var idArticle = jQuery('#idArticle').val();
    if (id != 0) {
        jQuery.ajax({
            type: "POST",
            url: "ajouterRestriction",
            data: {
                type: "Religion",
                idArticle: idArticle,
                id: id
            },
            success: function (data) {
                if (data == "errorexistant") {
                    ouvreMsgBox("Cette restriction est déjà présente.", "error");
                } else {
                    jQuery('#listeRestrictions').html(data);
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox('Sélectionnez un royaume.', 'error');
    }
}

function ajouterGrade() {
    var id = jQuery('#grade').val();
    var idArticle = jQuery('#idArticle').val();
    if (id != 0) {
        jQuery.ajax({
            type: "POST",
            url: "ajouterRestriction",
            data: {
                type: "Grade",
                idArticle: idArticle,
                id: id
            },
            success: function (data) {
                if (data == "errorexistant") {
                    ouvreMsgBox("Cette restriction est déjà présente.", "error");
                } else {
                    jQuery('#listeRestrictions').html(data);
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox('Sélectionnez un royaume.', 'error');
    }
}

function ajouterRace() {
    var id = jQuery('#race').val();
    var idArticle = jQuery('#idArticle').val();
    if (id != 0) {
        jQuery.ajax({
            type: "POST",
            url: "ajouterRestriction",
            data: {
                type: "Race",
                idArticle: idArticle,
                id: id
            },
            success: function (data) {
                if (data == "errorexistant") {
                    ouvreMsgBox("Cette restriction est déjà présente.", "error");
                } else {
                    jQuery('#listeRestrictions').html(data);
                }
            },
            error: function (errorThrown) {

            }
        });
    } else {
        ouvreMsgBox('Sélectionnez un royaume.', 'error');
    }
}

//Actions relatives aux contributeurs
function boxAjouterContributeur() {
    ouvreMsgBox("Entrez le nom du contributeur", "question", "saisie", ajouterContributeur, "");
}

function ajouterContributeur() {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != "") {
        var nom = recupSaisie();
        var newnom = nom;
        if (newnom.replace(/\s/g, "") == "") {
            ouvreMsgBox("Vous devez renseigner un nom.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "ajouterContributeur",
                data: {
                    idArticle: idArticle,
                    nom: nom
                },
                success: function (data) {
                    if (data == "false") {
                        ouvreMsgBox("Vous devez renseigner un personnage existant.", "erreur");
                    } else if (data == "errorContrib") {
                        ouvreMsgBox("Ce joueur est déjà contributeur sur l'article !", "erreur");
                    } else {
                        jQuery('#listeContributeurs').html(data);
                    }
                },
                error: function (errorThrown) {

                }
            });
        }
    }
}

function retirerContributeur(idContributeur) {
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != "") {
        jQuery.ajax({
            type: "POST",
            url: "retirerContributeur",
            data: {
                idArticle: idArticle,
                idContributeur: idContributeur
            },
            success: function (data) {
                jQuery('#listeContributeurs').html(data);
            },
            error: function (errorThrown) {

            }
        });
    }
}

//Actions relatives à l'auteur
function boxChangerAuteur() {
    ouvreMsgBox("Entrez le nom de l'auteur.", "question", "saisie", changerAuteur, "");
}

function changerAuteur() {
    //Auteur
    var idArticle = jQuery('#idArticle').val();
    if (idArticle != "") {
        var nom = recupSaisie();
        var newnom = nom;
        if (newnom.replace(/\s/g, "") == "") {
            ouvreMsgBox("Vous devez renseigner un nom.", "erreur");
        } else {
            jQuery.ajax({
                type: "POST",
                url: "changerAuteur",
                data: {
                    idArticle: idArticle,
                    nom: nom
                },
                success: function (data) {
                    if (data == "false") {
                        ouvreMsgBox("Vous devez renseigner un personnage existant.", "erreur");
                    } else {
                        jQuery('#spanProfil').html(data);
                    }
                },
                error: function (errorThrown) {

                }
            });
        }
    }
}

//###############################################//

function afficheResultUpload() {
    if (document.getElementById('messageUpload')) {
        var message = document.getElementById('messageUpload').value;
        if (message != "") {
            if (message == "error") {
                ouvreMsgBox("Un problème est survenu au cours de l'upload.", "erreur");
            } else if (message == "vide") {
                ouvreMsgBox("Il faut renseigner un fichier.", "erreur");
            } else {
                ouvreMsgBox("Votre image a été chargée à l'adresse suivante : " + message, "info");
            }
            document.getElementById('messageUpload').value = "";
        }
    }
}

