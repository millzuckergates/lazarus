//Init
jQuery(document).ready(function init() {
    //Redirection si pas de droits
    var nbOnglet = jQuery('#nombreOngletGameplay').val();
    if (nbOnglet == 0) {
        redirectAccueil();
    } else {
        jQuery('#blocGameplay').show();
    }
    initBoutonGameplay();
});

function initBoutonGameplay() {
    initBoutonCarte();
    initBoutonMagie();
    initBoutonCarac();
    initBoutonTerrain();
    initBoutonTexture();
    initBoutonTalents(true);
    initBoutonCompetences();
}

function openOngletGameplay(conteneur) {
    if (conteneur != "") {
        if (window.location.href.includes('/gameplay/')) {
            url = "afficherGameplay";
        } else {
            url = "gameplay/afficherGameplay";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                conteneur: conteneur
            },
            success: function (data) {
                jQuery('#blocGameplay').html(data);

                var listeNode = document.getElementsByName("ongletGameplay");
                for (var i = 0; i < listeNode.length; i++) {
                    var element = listeNode[i];
                    element.classList.remove("ongletGameplaySelect");
                    element.classList.remove("ongletGameplay");
                    if (element.id != conteneur) {
                        element.classList.add("ongletGameplay");
                    }
                }
                jQuery('#' + conteneur).addClass("ongletGameplaySelect");
                initBoutonGameplay();
            },
            error: function (errorThrown) {

            }
        });
    }
}

// ##########  Gestion de la magie  ############ //
function initBoutonMagie() {
    //Global
    jQuery("#s").keyup(function () {
        chargeSuggestionArticle();
    });
    jQuery('#boutonTypesMagie').click(function () {
        retourListeTypesMagie();
    });
    jQuery('#boutonEcolesMagie').click(function () {
        retourListeEcolesMagie();
    });
    jQuery('#boutonSortsMagie').click(function () {
        retourListeSortsMagie();
    });

    //Type de magie
    jQuery('#boutonAjouterTypeMagie').click(function () {
        ajouterTypeMagie();
    });
    jQuery('#retourListeTypesMagie').click(function () {
        retourListeTypesMagie();
    });
    jQuery('#retourListeTypeMagie').click(function () {
        retourListeTypesMagie();
    });
    jQuery('#editerTypeMagie').click(function () {
        editerTypeMagie();
    });
    jQuery('#ajouterEcoleNatureMagie').click(function () {
        ajouterEcoleNatureMagie();
    });
    jQuery('#modifierTypeMagie').click(function () {
        boxModifierTypeMagie();
    });
    jQuery('#annulerEditerTypeMagie').click(function () {
        annulerEditerTypeMagie();
    });
    jQuery('#chargerNewImageTypeMagie').click(function () {
        chargerNewImageTypeMagie();
    });
    jQuery('#couleurNatureMagieEdition').change(function () {
        changeCouleurNatureMagieEdition();
    });
    jQuery('#couleurNatureMagieCreation').change(function () {
        changeCouleurNatureMagieEdition();
    });
    jQuery('#creerTypeMagie').click(function () {
        creerTypeMagie();
    });

    //Ecole de magie
    jQuery('#boutonAjouterEcoleMagie').click(function () {
        ajouterEcoleMagie();
    });
    jQuery('#editerEcoleMagie').click(function () {
        editerEcoleMagie();
    });
    jQuery('#retourListeEcolesMagie').click(function () {
        retourListeEcolesMagie();
    });
    jQuery('#annulerEditerEcoleMagie').click(function () {
        annulerEditerEcoleMagie();
    });
    jQuery('#chargerNewImageEcoleMagie').click(function () {
        chargerNewImageEcoleMagie();
    });
    jQuery('#modifierEcoleMagie').click(function () {
        boxModifierEcoleMagie();
    });
    jQuery('#creerEcoleMagie').click(function () {
        creerEcoleMagie();
    });
    jQuery('#couleurEcoleMagieEdition').change(function () {
        changeCouleurEcoleMagieEdition();
    });
    jQuery('#couleurEcoleMagieCreation').change(function () {
        changeCouleurEcoleMagieEdition();
    });
    jQuery('#ajouterSortEcoleMagie').click(function () {
        ajouterSortEcoleMagie();
    });

    //Sorts
    jQuery('#boutonAjouterSort').click(function () {
        ajouterSort();
    });
    jQuery('#editerSort').click(function () {
        editerSort();
    });
    jQuery('#annulerEditerSort').click(function () {
        annulerEditerSort();
    });
    jQuery('#retourListeSortsMagie').click(function () {
        retourListeSortsMagie();
    });
    jQuery('#retourListeSort').click(function () {
        retourListeSortsMagie();
    });
    jQuery('#chargerNewImageSort').click(function () {
        chargerNewImageSort();
    });
    jQuery('#accesTableCiblage').click(function () {
        accesTableCiblage();
    });
    jQuery('#showlisteEffetsSorts').click(function () {
        showlisteEffetsSorts();
    });
    jQuery('#hidelisteEffetsSorts').click(function () {
        hidelisteEffetsSorts();
    });
    jQuery('#hidelisteEffetsSorts').hide();
    jQuery('#showlisteContraintesSorts').click(function () {
        showlisteContraintesSorts();
    });
    jQuery('#hidelisteContraintesSorts').click(function () {
        hidelisteContraintesSorts();
    });
    jQuery('#hidelisteContraintesSorts').hide();
    jQuery('#showlisteEvolutionsSorts').click(function () {
        showlisteEvolutionsSorts();
    });
    jQuery('#hidelisteEvolutionsSorts').click(function () {
        hidelisteEvolutionsSorts();
    });
    jQuery('#hidelisteEvolutionsSorts').hide();
    jQuery('#creerSort').click(function () {
        creerSort();
    });
    jQuery('#modifierSort').click(function () {
        modifierSort();
    });

}

function chargeSuggestionArticle() {
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
                            result += '<li class="suggest" onclick="remplacerTitre(' + pourClick + ');">' + title['titre'] + '</li>';
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

function remplacerTitre(titre) {
    jQuery('#s').val(titre);
    jQuery("#suggestions").html("");
}

//Nature Magie
function retourListeTypesMagie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTypesMagie";
    } else {
        url = "gameplay/detailTypesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').addClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function afficherDetailNatureMagie(idNatureMagie) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTypesMagie";
    } else {
        url = "gameplay/detailTypesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idNatureMagie
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').addClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function editerTypeMagie() {
    var idNatureMagie = jQuery('#idNatureMagie').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTypesMagie";
    } else {
        url = "gameplay/detailTypesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            id: idNatureMagie
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').addClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function ajouterTypeMagie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTypesMagie";
    } else {
        url = "gameplay/detailTypesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').addClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function ajouterEcoleNatureMagie() {
    var id = jQuery('#idNatureMagie').val();
    var idEcoleSelect = jQuery('#selectEcoleDisponible').val();
    if (idEcoleSelect == 0) {
        ouvreMsgBox("Veuillez sélectionner une école de magie.", "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "ajouterEcoleNatureMagie";
        } else {
            url = "gameplay/ajouterEcoleNatureMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idEcole: idEcoleSelect,
                idNatureMagie: id
            },
            success: function (data) {
                if (data == "errorEcole") {
                    ouvreMsgBox("L'école choisie n'a pas été trouvée.", "error");
                } else {
                    jQuery('#listeEcolesMagieAssociee').html(data);
                    setTimeout(function () {
                        majListeEcoleDisponible(id);
                    }, 250);
                }
            }
        });
    }
}

function majListeEcoleDisponible(idNatureMagie) {
    if (window.location.href.includes('/gameplay/')) {
        url = "majListeEcoleDisponible";
    } else {
        url = "gameplay/majListeEcoleDisponible";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idNatureMagie: idNatureMagie
        },
        success: function (data) {
            jQuery('#divSelectEcoleMagie').html(data);
            initBoutonMagie();
        }
    });
}

function supprimerEcoleNatureMagie(idEcole) {
    var id = jQuery('#idNatureMagie').val();
    if (idEcole == 0) {
        ouvreMsgBox("Veuillez sélectionner une école de magie.", "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "supprimerEcoleNatureMagie";
        } else {
            url = "gameplay/supprimerEcoleNatureMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idEcole: idEcole,
                idNatureMagie: id
            },
            success: function (data) {
                if (data == "errorEcole") {
                    ouvreMsgBox("L'école choisie n'a pas été trouvée.", "error");
                } else {
                    jQuery('#listeEcolesMagieAssociee').html(data);
                    setTimeout(function () {
                        majListeEcoleDisponible(id);
                    }, 250);
                }
            }
        });
    }
}

function boxModifierTypeMagie() {
    ouvreMsgBox("Les modifications seront directement appliqués en jeu. Cela peut avoir des impacts important sur les joueurs. Etes-vous sûr de vos modificaions ? ", "question", "ouinon", modifierTypeMagie, "");
}

function modifierTypeMagie() {
    var idNatureMagie = jQuery('#idNatureMagie').val();
    var isDispoInscription = jQuery('#isDispoInscriptionNatureMagieEdition').is(':checked');
    var isBloque = jQuery('#isBloqueNatureMagieEdition').is(':checked');
    var nom = jQuery('#titreFormNatureMagie').val();
    var description = jQuery('#descriptionNatureMagieEdition').val();
    var couleur = jQuery('#couleurNatureMagieEdition').val();
    var type = jQuery('#selectTypeNatureMagie').val();
    var fichier = jQuery('#selectFichierNatureMagie').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormNatureMagie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionNatureMagieEdition').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurNatureMagieEdition').addClass("erreur");
    }
    if (fichier == "0") {
        erreur += "Il faut associer un script à la nature de magie.\n";
        jQuery('#selectFichierNatureMagie').addClass("erreur");
    }
    if (type == "0") {
        erreur += "Il faut associer un type à la nature de magie.\n";
        jQuery('#selectTypeNatureMagie').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierTypeMagie";
        } else {
            url = "gameplay/modifierTypeMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idNatureMagie: idNatureMagie,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isBloque: isBloque,
                couleur: couleur,
                type: type,
                fichier: fichier,
                isDispoInscription: isDispoInscription
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                    cleanErrorFormNatureMagieEdition();
                }
            }
        });
    }
}

function cleanErrorFormNatureMagieEdition() {
    if (jQuery("#titreFormNatureMagie").hasClass("erreur")) {
        jQuery('#titreFormNatureMagie').removeClass("erreur");
    }
    if (jQuery("#descriptionNatureMagieEdition").hasClass("erreur")) {
        jQuery('#descriptionNatureMagieEdition').removeClass("erreur");
    }
    if (jQuery("#couleurNatureMagieEdition").hasClass("erreur")) {
        jQuery('#couleurNatureMagieEdition').removeClass("erreur");
    }
    if (jQuery("#selectFichierNatureMagie").hasClass("erreur")) {
        jQuery('#selectFichierNatureMagie').removeClass("erreur");
    }
    if (jQuery("#selectTypeNatureMagie").hasClass("erreur")) {
        jQuery('#selectTypeNatureMagie').removeClass("erreur");
    }
}

function cleanErrorFormNatureMagieCreation() {
    if (jQuery("#titreFormNatureMagie").hasClass("erreur")) {
        jQuery('#titreFormNatureMagie').removeClass("erreur");
    }
    if (jQuery("#descriptionNatureMagieCreation").hasClass("erreur")) {
        jQuery('#descriptionNatureMagieCreation').removeClass("erreur");
    }
    if (jQuery("#couleurNatureMagieCreation").hasClass("erreur")) {
        jQuery('#couleurNatureMagieCreation').removeClass("erreur");
    }
    if (jQuery("#selectFichierNatureMagie").hasClass("erreur")) {
        jQuery('#selectFichierNatureMagie').removeClass("erreur");
    }
    if (jQuery("#selectTypeNatureMagie").hasClass("erreur")) {
        jQuery('#selectTypeNatureMagie').removeClass("erreur");
    }
}

function annulerEditerTypeMagie() {
    var id = jQuery('#idNatureMagie').val();
    afficherDetailNatureMagie(id);
}

function chargerNewImageTypeMagie() {
    var id = jQuery('#idNatureMagie').val();
    var type = "NatureMagie";
    var urlFile = jQuery('#newImageTypeMagie').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function changeCouleurNatureMagieEdition() {
    if (jQuery('#couleurNatureMagieEdition').length) {
        var couleur = jQuery('#couleurNatureMagieEdition').val();
    } else if (jQuery('#couleurNatureMagieCreation').length) {
        var couleur = jQuery('#couleurNatureMagieCreation').val();
    }
    jQuery('#carreCouleurNatureMagie').css('background-color', couleur);
}

function creerTypeMagie() {
    var isDispoInscription = jQuery('#isDispoInscriptionNatureMagieCreation').is(':checked');
    var isBloque = jQuery('#isBloqueNatureMagieCreation').is(':checked');
    var nom = jQuery('#titreFormNatureMagie').val();
    var description = jQuery('#descriptionNatureMagieCreation').val();
    var couleur = jQuery('#couleurNatureMagieCreation').val();
    var type = jQuery('#selectTypeNatureMagie').val();
    var fichier = jQuery('#selectFichierNatureMagie').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormNatureMagie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionNatureMagieCreation').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurNatureMagieCreation').addClass("erreur");
    }
    if (fichier == "0") {
        erreur += "Il faut associer un script à la nature de magie.\n";
        jQuery('#selectFichierNatureMagie').addClass("erreur");
    }
    if (type == "0") {
        erreur += "Il faut associer un type à la nature de magie.\n";
        jQuery('#selectTypeNatureMagie').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerTypeMagie";
        } else {
            url = "gameplay/creerTypeMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isBloque: isBloque,
                couleur: couleur,
                type: type,
                fichier: fichier,
                isDispoInscription: isDispoInscription
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("La création est effective.", "info");
                    cleanErrorFormNatureMagieCreation();
                    afficherDetailNatureMagie(data);
                }
            }
        });
    }
}

function changerImageNatureMagie() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageNatureMagie").attr("src", src);
}

//Ecoles de magie
function retourListeEcolesMagie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailEcolesMagie";
    } else {
        url = "gameplay/detailEcolesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function afficherDetailEcole(idEcole) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailEcolesMagie";
    } else {
        url = "gameplay/detailEcolesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idEcole
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function ajouterEcoleMagie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailEcolesMagie";
    } else {
        url = "gameplay/detailEcolesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function editerEcoleMagie() {
    var idEcole = jQuery('#idEcoleMagie').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailEcolesMagie";
    } else {
        url = "gameplay/detailEcolesMagie";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            id: idEcole
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function annulerEditerEcoleMagie() {
    var id = jQuery('#idEcoleMagie').val();
    afficherDetailEcole(id);
}

function chargerNewImageEcoleMagie() {
    var id = jQuery('#idEcoleMagie').val();
    var type = "EcoleMagie";
    var urlFile = jQuery('#newImageEcoleMagie').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function boxModifierEcoleMagie() {
    ouvreMsgBox("Les modifications seront directement appliqués en jeu. Cela peut avoir des impacts important sur les joueurs. Etes-vous sûr de vos modificaions ? ", "question", "ouinon", modifierEcoleMagie, "");
}

function modifierEcoleMagie() {
    var idEcoleMagie = jQuery('#idEcoleMagie').val();
    var nom = jQuery('#titreFormEcoleMagie').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionEcoleMagieEdition').val();
    var titreArticle = jQuery('#s').val();
    var couleur = jQuery('#couleurEcoleMagieEdition').val();
    var idNatureMagie = jQuery('#selectTypeNatureMagieEcole').val();
    var fichier = jQuery('#selectScriptEcole').val();
    var idCompetence = jQuery('#selectCompetenceAssocieeEcole').val();
    var isDispoInscription = jQuery('#isDispoInscriptionEcoleMagieEdition').is(':checked');
    var isBloque = jQuery('#isBloqueEcoleMagieEdition').is(':checked');

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormNatureMagie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionNatureMagieEdition').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurNatureMagieEdition').addClass("erreur");
    }

    if (idCompetence == "0") {
        erreur += "Il faut associer une compétence à l'école de magie.\n";
        jQuery('#selectCompetenceAssocieeEcole').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierEcoleMagie";
        } else {
            url = "gameplay/modifierEcoleMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idEcoleMagie: idEcoleMagie,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isBloque: isBloque,
                couleur: couleur,
                idNatureMagie: idNatureMagie,
                fichier: fichier,
                isDispoInscription: isDispoInscription,
                idCompetence: idCompetence
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                    cleanErrorFormEcoleMagieEdition();
                }
            }
        });
    }
}

function cleanErrorFormEcoleMagieEdition() {
    if (jQuery("#titreFormEcoleMagie").hasClass("erreur")) {
        jQuery('#titreFormEcoleMagie').removeClass("erreur");
    }
    if (jQuery("#descriptionEcoleMagieEdition").hasClass("erreur")) {
        jQuery('#descriptionEcoleMagieEdition').removeClass("erreur");
    }
    if (jQuery("#couleurNatureMagieEdition").hasClass("erreur")) {
        jQuery('#couleurNatureMagieEdition').removeClass("erreur");
    }
    if (jQuery("#selectCompetenceAssocieeEcole").hasClass("erreur")) {
        jQuery('#selectCompetenceAssocieeEcole').removeClass("erreur");
    }
}

function creerEcoleMagie() {
    var nom = jQuery('#titreFormEcoleMagie').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionEcoleMagieCreation').val();
    var titreArticle = jQuery('#s').val();
    var couleur = jQuery('#couleurEcoleMagieCreation').val();
    var idNatureMagie = jQuery('#selectTypeNatureMagieEcole').val();
    var fichier = jQuery('#selectScriptEcole').val();
    var idCompetence = jQuery('#selectCompetenceAssocieeEcole').val();
    var isDispoInscription = jQuery('#isDispoInscriptionEcoleMagieCreation').is(':checked');
    var isBloque = jQuery('#isBloqueEcoleMagieCreation').is(':checked');

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormNatureMagie').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionNatureMagieCreation').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurNatureMagieCreation').addClass("erreur");
    }

    if (idCompetence == "0") {
        erreur += "Il faut associer une compétence à l'école de magie.\n";
        jQuery('#selectCompetenceAssocieeEcole').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerEcoleMagie";
        } else {
            url = "gameplay/creerEcoleMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isBloque: isBloque,
                couleur: couleur,
                idNatureMagie: idNatureMagie,
                fichier: fichier,
                isDispoInscription: isDispoInscription,
                idCompetence: idCompetence
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("La création est effective.", "info");
                    cleanErrorFormEcoleMagieCreation();
                    afficherDetailEcole(data);
                }
            }
        });
    }
}

function cleanErrorFormEcoleMagieCreation() {
    if (jQuery("#titreFormEcoleMagie").hasClass("erreur")) {
        jQuery('#titreFormEcoleMagie').removeClass("erreur");
    }
    if (jQuery("#descriptionEcoleMagieCreation").hasClass("erreur")) {
        jQuery('#descriptionEcoleMagieCreation').removeClass("erreur");
    }
    if (jQuery("#couleurNatureMagieCreation").hasClass("erreur")) {
        jQuery('#couleurNatureMagieCreation').removeClass("erreur");
    }
    if (jQuery("#selectCompetenceAssocieeEcole").hasClass("erreur")) {
        jQuery('#selectCompetenceAssocieeEcole').removeClass("erreur");
    }
}

function boxRetirerSort(idSort) {
    jQuery('#idSortRetirer').val(idSort);
    ouvreMsgBox("Les modifications seront directement appliquées en jeu. Retirer ce sort de cette école de magie le rendra inacessible pour les joueurs. Êtes-vous sûr de votre choix  ? ", "question", "ouinon", retirerSort, "");
}

function retirerSort() {
    idEcole = jQuery('#idEcoleMagie').val();
    idSort = jQuery('#idSortRetirer').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "retirerSort";
    } else {
        url = "gameplay/retirerSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idEcole: idEcole,
            idSort: idSort
        },
        success: function (data) {
            jQuery('#listeSortsAssocies').html(data);
            setTimeout(function () {
                majListeSortDisponible(idEcole);
            }, 250);
        }
    });
}

function majListeSortDisponible(idEcoleMagie) {
    if (window.location.href.includes('/gameplay/')) {
        url = "majListeSortDisponible";
    } else {
        url = "gameplay/majListeSortDisponible";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idEcole: idEcoleMagie
        },
        success: function (data) {
            jQuery('#divSelectSort').html(data);
        }
    });
}

function ajouterSortEcoleMagie() {
    var idEcole = jQuery('#idEcoleMagie').val();
    var idSort = jQuery('#selectSortDisponible').val();

    if (idSort == 0) {
        ouvreMsgBox("Vous devez sélectionner un sort.", "error");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "ajouterSortEcoleMagie";
        } else {
            url = "gameplay/ajouterSortEcoleMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idEcole: idEcole,
                idSort: idSort
            },
            success: function (data) {
                jQuery('#listeSortsAssocies').html(data);
                setTimeout(function () {
                    majListeSortDisponible(idEcole);
                }, 250);
            }
        });
    }
}

function changerImageEcoleMagie() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageEcoleMagie").attr("src", src);
}

function changeCouleurEcoleMagieEdition() {
    if (jQuery('#couleurEcoleMagieEdition').length) {
        var couleur = jQuery('#couleurEcoleMagieEdition').val();
    } else if (jQuery('#couleurEcoleMagieCreation').length) {
        var couleur = jQuery('#couleurEcoleMagieCreation').val();
    }
    jQuery('#carreCouleurEcoleMagie').css('background-color', couleur);
}

//Sorts
function retourListeSortsMagie() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailSort";
    } else {
        url = "gameplay/detailSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function ajouterSort() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailSort";
    } else {
        url = "gameplay/detailSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function afficherDetailSort(idSort) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailSort";
    } else {
        url = "gameplay/detailSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idSort
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            jQuery('#boutonSortsMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });
}

function changerImageSort() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageSort").attr("src", src);
}

function editerSort() {
    idSort = jQuery('#idSort').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailSort";
    } else {
        url = "gameplay/detailSort";
    }

    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            id: idSort
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
        }
    });

}

function annulerEditerSort() {
    var id = jQuery('#idSort').val();
    afficherDetailSort(id);
}

function chargerNewImageSort() {
    var id = jQuery('#idSort').val();
    var type = "Sort";
    var urlFile = jQuery('#newImageSort').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function showlisteEffetsSorts() {
    var idSort = jQuery('#idSort').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showListeEffets";
    } else {
        url = "gameplay/showListeEffets";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idSort,
            type: "sort"
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsSorts').hide();
            jQuery('#hidelisteEffetsSorts').show();
            jQuery('#showlisteContraintesSorts').show();
            jQuery('#hidelisteContraintesSorts').hide();
            jQuery('#showlisteEvolutionsSorts').show();
            jQuery('#hidelisteEvolutionsSorts').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteEffetsSorts() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsSorts').show();
    jQuery('#hidelisteEffetsSorts').hide();
    jQuery('#showlisteContraintesSorts').show();
    jQuery('#hidelisteContraintesSorts').hide();
    jQuery('#showlisteEvolutionsSorts').show();
    jQuery('#hidelisteEvolutionsSorts').hide();
}

function showlisteContraintesSorts() {
    var idSort = jQuery('#idSort').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteContraintes";
    } else {
        url = "gameplay/showlisteContraintes";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idSort
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsSorts').show();
            jQuery('#hidelisteEffetsSorts').hide();
            jQuery('#showlisteContraintesSorts').hide();
            jQuery('#hidelisteContraintesSorts').show();
            jQuery('#showlisteEvolutionsSorts').show();
            jQuery('#hidelisteEvolutionsSorts').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteContraintesSorts() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsSorts').show();
    jQuery('#hidelisteEffetsSorts').hide();
    jQuery('#showlisteContraintesSorts').show();
    jQuery('#hidelisteContraintesSorts').hide();
    jQuery('#showlisteEvolutionsSorts').show();
    jQuery('#hidelisteEvolutionsSorts').hide();
}

function showlisteEvolutionsSorts() {
    var idSort = jQuery('#idSort').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEvolutions";
    } else {
        url = "gameplay/showlisteEvolutions";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idSort
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsSorts').show();
            jQuery('#hidelisteEffetsSorts').hide();
            jQuery('#showlisteContraintesSorts').hide();
            jQuery('#hidelisteContraintesSorts').show();
            jQuery('#showlisteEvolutionsSorts').hide();
            jQuery('#hidelisteEvolutionsSorts').show();
            initBoutonEffet();
        }
    });
}

function hidelisteEvolutionsSorts() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsSorts').show();
    jQuery('#hidelisteEffetsSorts').hide();
    jQuery('#showlisteContraintesSorts').show();
    jQuery('#hidelisteContraintesSorts').hide();
    jQuery('#showlisteEvolutionsSorts').show();
    jQuery('#hidelisteEvolutionsSorts').hide();
}

function creerSort() {
    var nom = jQuery('#titreFormSort').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionSortCreation').val();
    var messageRP = jQuery('#messageRPSortCreation').val();
    var titreArticle = jQuery('#s').val();
    var idEcole = jQuery('#selectEcoleSort').val();
    var arcane = jQuery('#arcaneSortCreation').val();
    var mana = jQuery('#manaSort').html();
    var portee = jQuery('#porteeSort').html();
    var pa = jQuery('#paSort').html();
    var duree = jQuery('#dureeSort').html();
    var dureeMax = jQuery('#dureeMaxSort').html();
    var cumulQuantite = jQuery('#cumulQuantiteSort').html();
    var isBloque = jQuery('#isBloqueSortCreation').is(':checked');
    var enseignable = jQuery('#isEsquivableSortSortCreation').is(':checked');
    var retranscriptible = jQuery('#isRetranscriptibleSortSortCreation').is(':checked');
    var isJS = jQuery('#isJSSortCreation').is(':checked');
    var isJSEV = jQuery('#isJSEVSortCreation').is(':checked');
    var eventLanceur = jQuery('#eventLanceur').val();
    var eventGlobal = jQuery('#eventGlobal').val();
    var esquivable = jQuery('#isEsquivableSortSortCreation').is(':checked');

    //TODO voir pour enregistrer les éléments particuliers lié au type de magie

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormSort').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionSortCreation').addClass("erreur");
    }
    var eventLanceurSansEspace = eventLanceur.replace(/\s/g, "");
    if (eventLanceurSansEspace == "") {
        erreur += "Il faut définir l'évenement que verra le lanceur de sort.\n";
        jQuery('#eventLanceur').addClass("erreur");
    }
    var eventGlobalSansEspace = eventGlobal.replace(/\s/g, "");
    if (eventGlobalSansEspace == "") {
        erreur += "Il faut définir l'évenement que verront les observateurs du sort.\n";
        jQuery('#eventGlobal').addClass("erreur");
    }
    var arcaneSansEspace = arcane.replace(/\s/g, "");
    if (arcaneSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ arcane.\n";
        jQuery('#arcaneSortCreation').addClass("erreur");
    }
    var manaSansEspace = mana.replace(/\s/g, "");
    if (manaSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ mana.\n";
        jQuery('#manaSort').addClass('erreur');
    }
    var porteeSansEspace = portee.replace(/\s/g, "");
    if (porteeSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ portée.\n";
        jQuery('#porteeSort').addClass('erreur');
    }
    var coutPASansEspace = pa.replace(/\s/g, "");
    if (coutPASansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ cout PA.\n";
        jQuery('#paSort').addClass('erreur');
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (enseignable == true) {
        enseignable = 1;
    } else {
        enseignable = 0;
    }

    if (esquivable == true) {
        esquivable = 1;
    } else {
        esquivable = 0;
    }

    if (retranscriptible == true) {
        retranscriptible = 1;
    } else {
        retranscriptible = 0;
    }

    if (isJS == true) {
        isJS = 1;
    } else {
        isJS = 0;
    }

    if (isJSEV == true) {
        isJSEV = 1;
    } else {
        isJSEV = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerSort";
        } else {
            url = "gameplay/creerSort";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                image: image,
                description: description,
                messageRP: messageRP,
                titre: titreArticle,
                idEcole: idEcole,
                arcane: arcane,
                mana: mana,
                portee: portee,
                pa: pa,
                duree: duree,
                dureeMax: dureeMax,
                cumulQuantite: cumulQuantite,
                isBloque: isBloque,
                enseignable: enseignable,
                retranscriptible: retranscriptible,
                isJS: isJS,
                isJSEV: isJSEV,
                eventLanceur: eventLanceur,
                esquivable: esquivable,
                eventGlobal: eventGlobal
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    cleanErrorFormSortCreation();
                    editerSortTableCiblage(data);
                }
            }
        });
    }
}

function editerSortTableCiblage(idSort) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailSort";
    } else {
        url = "gameplay/detailSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idSort
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonSortsMagie').addClass("boutonGestionMagieSelect");
            jQuery('#boutonEcolesMagie').removeClass("boutonGestionMagieSelect");
            jQuery('#boutonTypesMagie').removeClass("boutonGestionMagieSelect");
            initBoutonMagie();
            setTimeout(function () {
                accesTableCiblage();
            }, 750);
        }
    });
}

function cleanErrorFormSortCreation() {
    if (jQuery("#titreFormSort").hasClass("erreur")) {
        jQuery('#titreFormSort').removeClass("erreur");
    }
    if (jQuery("#descriptionSortCreation").hasClass("erreur")) {
        jQuery('#descriptionSortCreation').removeClass("erreur");
    }
    if (jQuery("#arcaneSortCreation").hasClass("erreur")) {
        jQuery('#arcaneSortCreation').removeClass("erreur");
    }
    if (jQuery("#manaSort").hasClass("erreur")) {
        jQuery('#manaSort').removeClass("erreur");
    }
    if (jQuery("#porteeSort").hasClass("erreur")) {
        jQuery('#porteeSort').removeClass("erreur");
    }
    if (jQuery("#paSort").hasClass("erreur")) {
        jQuery('#paSort').removeClass("erreur");
    }
    if (jQuery("#eventLanceur").hasClass("erreur")) {
        jQuery('#eventLanceur').removeClass("erreur");
    }
    if (jQuery("#eventGlobal").hasClass("erreur")) {
        jQuery('#eventGlobal').removeClass("erreur");
    }
}

function modifierSort() {
    var idSort = jQuery('#idSort').val();
    var nom = jQuery('#titreFormSort').val();
    var image = jQuery('#listeImage').val();
    var description = jQuery('#descriptionSortEdition').val();
    var messageRP = jQuery('#messageRPSortEdition').val();
    var titreArticle = jQuery('#s').val();
    var idEcole = jQuery('#selectEcoleSort').val();
    var arcane = jQuery('#arcaneSortEdition').val();
    var mana = jQuery('#manaSort').html();
    var portee = jQuery('#porteeSort').html();
    var pa = jQuery('#paSort').html();
    var duree = jQuery('#dureeSort').html();
    var dureeMax = jQuery('#dureeMaxSort').html();
    var cumulQuantite = jQuery('#cumulQuantiteSort').html();
    var isBloque = jQuery('#isBloqueSortEdition').is(':checked');
    var enseignable = jQuery('#isEnseignableSortSortEdition').is(':checked');
    var retranscriptible = jQuery('#isRetranscriptibleSortSortEdition').is(':checked');
    var isJS = jQuery('#isJSSort').is(':checked');
    var isJSEV = jQuery('#isJSEVSort').is(':checked');
    var eventLanceur = jQuery('#eventLanceur').val();
    var eventGlobal = jQuery('#eventGlobal').val();
    var esquivable = jQuery('#isEsquivableSortSortEdition').is(':checked');

    //TODO voir pour enregistrer les éléments particuliers lié au type de magie

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormSort').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionSortEdition').addClass("erreur");
    }
    var eventLanceurSansEspace = eventLanceur.replace(/\s/g, "");
    if (eventLanceurSansEspace == "") {
        erreur += "Il faut définir l'évenement que verra le lanceur de sort.\n";
        jQuery('#eventLanceur').addClass("erreur");
    }
    var eventGlobalSansEspace = eventGlobal.replace(/\s/g, "");
    if (eventGlobalSansEspace == "") {
        erreur += "Il faut définir l'évenement que verront les observateurs du sort.\n";
        jQuery('#eventGlobal').addClass("erreur");
    }
    var arcaneSansEspace = arcane.replace(/\s/g, "");
    if (arcaneSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ arcane.\n";
        jQuery('#arcaneSortEdition').addClass("erreur");
    }
    var manaSansEspace = mana.replace(/\s/g, "");
    if (manaSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ mana.\n";
        jQuery('#manaSort').addClass('erreur');
    }
    var porteeSansEspace = portee.replace(/\s/g, "");
    if (porteeSansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ portée.\n";
        jQuery('#porteeSort').addClass('erreur');
    }
    var coutPASansEspace = pa.replace(/\s/g, "");
    if (coutPASansEspace == "") {
        erreur += "Il faut renseigner une valeur pour le champ cout PA.\n";
        jQuery('#paSort').addClass('erreur');
    }

    if (isBloque == true) {
        isBloque = 1;
    } else {
        isBloque = 0;
    }

    if (enseignable == true) {
        enseignable = 1;
    } else {
        enseignable = 0;
    }

    if (esquivable == true) {
        esquivable = 1;
    } else {
        esquivable = 0;
    }

    if (retranscriptible == true) {
        retranscriptible = 1;
    } else {
        retranscriptible = 0;
    }

    if (isJS == true) {
        isJS = 1;
    } else {
        isJS = 0;
    }

    if (isJSEV == true) {
        isJSEV = 1;
    } else {
        isJSEV = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierSort";
        } else {
            url = "gameplay/modifierSort";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: idSort,
                nom: nom,
                image: image,
                description: description,
                messageRP: messageRP,
                titre: titreArticle,
                idEcole: idEcole,
                arcane: arcane,
                mana: mana,
                portee: portee,
                pa: pa,
                duree: duree,
                dureeMax: dureeMax,
                cumulQuantite: cumulQuantite,
                isBloque: isBloque,
                enseignable: enseignable,
                retranscriptible: retranscriptible,
                isJS: isJS,
                isJSEV: isJSEV,
                eventLanceur: eventLanceur,
                eventGlobal: eventGlobal,
                esquivable: esquivable
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else if (data == "errorProduite") {
                    ouvreMsgBox("Une erreur s'est produite.", "error");
                } else {
                    cleanErrorFormSortEdition();
                    ouvreMsgBox("Les modifications ont été prises en compte.", "info");
                }
            }
        });
    }
}

function cleanErrorFormSortEdition() {
    if (jQuery("#titreFormSort").hasClass("erreur")) {
        jQuery('#titreFormSort').removeClass("erreur");
    }
    if (jQuery("#descriptionSortEdition").hasClass("erreur")) {
        jQuery('#descriptionSortEdition').removeClass("erreur");
    }
    if (jQuery("#arcaneSortEdition").hasClass("erreur")) {
        jQuery('#arcaneSortEdition').removeClass("erreur");
    }
    if (jQuery("#manaSort").hasClass("erreur")) {
        jQuery('#manaSort').removeClass("erreur");
    }
    if (jQuery("#porteeSort").hasClass("erreur")) {
        jQuery('#porteeSort').removeClass("erreur");
    }
    if (jQuery("#paSort").hasClass("erreur")) {
        jQuery('#paSort').removeClass("erreur");
    }
    if (jQuery("#eventLanceur").hasClass("erreur")) {
        jQuery('#eventLanceur').removeClass("erreur");
    }
    if (jQuery("#eventGlobal").hasClass("erreur")) {
        jQuery('#eventGlobal').removeClass("erreur");
    }
}

function retirerSortFromListeSort(idSort) {
    if (window.location.href.includes('/gameplay/')) {
        url = "retirerSort";
    } else {
        url = "gameplay/retirerSort";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idSort
        },
        success: function (data) {
            retourListeSortsMagie();
        }
    });
}

function chargeDivParticulariteTypeMagie() {
    var idEcole = jQuery('#selectEcoleSort').val();
    var idSort = jQuery('#idSort').val();
    if (idEcole != 0) {
        if (window.location.href.includes('/gameplay/')) {
            url = "chargeDivParticulariteTypeMagie";
        } else {
            url = "gameplay/chargeDivParticulariteTypeMagie";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idEcole: idEcole,
                idSort: idSort
            },
            success: function (data) {
                jQuery('#blocParticulierNatureMagie').html(data);
            }
        });
    } else {
        jQuery('#blocParticulierNatureMagie').html("");
    }
}

//Gestion des caracs
function initBoutonCarac() {
    jQuery('#ajouterCarac').click(function () {
        ajouterCarac();
    });
    jQuery('#showCaracPrimaire').click(function () {
        showCaracPrimaire();
    });
    jQuery('#showCaracPrimaire').hide();
    jQuery('#hideCaracPrimaire').click(function () {
        hideCaracPrimaire();
    });
    jQuery('#showCaracSecondaire').click(function () {
        showCaracSecondaire();
    });
    jQuery('#showCaracSecondaire').hide();
    jQuery('#hideCaracSecondaire').click(function () {
        hideCaracSecondaire();
    });
    jQuery('#editerCarac').click(function () {
        editerCarac();
    });
    jQuery('#retourListeCarac').click(function () {
        retourListeCarac();
    });
    jQuery('#annulerEditerCarac').click(function () {
        annulerEditerCarac();
    });
    jQuery('#chargerNewImageCarac').click(function () {
        chargerNewImageCarac();
    });
    jQuery('#modifierCarac').click(function () {
        modifierCarac();
    });
    jQuery('#creerCarac').click(function () {
        creerCarac();
    });
}

function ajouterCarac() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarac";
    } else {
        url = "gameplay/detailCarac";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarac();
        }
    });
}

function showCaracPrimaire() {
    jQuery('#showCaracPrimaire').hide();
    jQuery('#hideCaracPrimaire').show();
    jQuery('#divTableCaracPrimaire').slideToggle();
}

function hideCaracPrimaire() {
    jQuery('#showCaracPrimaire').show();
    jQuery('#hideCaracPrimaire').hide();
    jQuery('#divTableCaracPrimaire').slideToggle();
}

function showCaracSecondaire() {
    jQuery('#showCaracSecondaire').hide();
    jQuery('#hideCaracSecondaire').show();
    jQuery('#divTableCaracSecondaire').slideToggle();
}

function hideCaracSecondaire() {
    jQuery('#showCaracSecondaire').show();
    jQuery('#hideCaracSecondaire').hide();
    jQuery('#divTableCaracSecondaire').slideToggle();
}

function editerCarac() {
    var idCarac = jQuery('#idCarac').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarac";
    } else {
        url = "gameplay/detailCarac";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idCarac
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarac();
        }
    });
}

function afficherDetailCarac(idCarac) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarac";
    } else {
        url = "gameplay/detailCarac";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idCarac
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonCarac();
        }
    });
}

function retourListeCarac() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailCarac";
    } else {
        url = "gameplay/detailCarac";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonCarac();
        }
    });
}

function annulerEditerCarac() {
    var idCarac = jQuery('#idCarac').val();
    afficherDetailCarac(idCarac);
}

function chargerNewImageCarac() {
    var id = jQuery('#idCarac').val();
    var type = "caracs";
    var urlFile = jQuery('#newImageCarac').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrl";
        } else {
            url = "gameplay/uploadImageUrl";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    jQuery('#listeImage').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function modifierCarac() {
    var idCarac = jQuery('#idCarac').val();
    var nom = jQuery('#titreFormCarac').val();
    var trigramme = jQuery('#trigrammeCarac').val();
    var description = jQuery('#descriptionCarac').val();
    var image = jQuery('#listeImage').val();
    var isModifiable = jQuery('#isModifiable').is(':checked');
    var type = jQuery('#selectListeTypeCarac').val();
    var valMin = jQuery('#valMinCarac').val();
    var valMax = jQuery('#valMaxCarac').val();
    var formule = jQuery('#formuleCarac').html();
    var genre = jQuery('#selectGenreCarac').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#titreFormCarac').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCarac').addClass("erreur");
    }
    var trigrammeSansEspace = trigramme.replace(/\s/g, "");
    if (trigrammeSansEspace == "" || trigrammeSansEspace.length != 3) {
        erreur = erreur + "Le trigramme doit faire trois caractères. \n";
        jQuery('#trigrammeCarac').addClass("erreur");
    }
    if (isNaN(valMin)) {
        valMin = "";
    }
    if (isNaN(valMax)) {
        valMax = "";
    }
    if (valMin != "" && valMax != "" && valMin > valMax) {
        erreur = erreur + "La valeur minimum doit être inférieure à la valeur maximum. \n";
    }
    if (type == "0") {
        erreur = erreur + "Vous devez choisir un type pour cette caractéristique. \n";
        jQuery('#selectListeTypeCarac').addClass("erreur");
    }


    if (isModifiable == true) {
        isModifiable = 1;
    } else {
        isModifiable = 0;
    }
    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierCarac";
        } else {
            url = "gameplay/modifierCarac";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: idCarac,
                nom: nom,
                description: description,
                image: image,
                trigramme: trigramme,
                isModifiable: isModifiable,
                type: type,
                valMin: valMin,
                valMax: valMax,
                formule: formule,
                genre: genre
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else if (data == "errorTrigramme") {
                    ouvreMsgBox("Ce trigramme a déjà été choisi. Veuillez en choisir un autre.", "error");
                } else if (data == "errorChangementType") {
                    ouvreMsgBox("Il n'est pas possible de passer d'un type primaire à un type secondaire lorsque la caractéristique a déjà été initialisée pour les compétences.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                    cleanClassErrorCarac();
                }
            }
        });
    }
}

function cleanClassErrorCarac() {
    if (jQuery("#titreFormCarac").hasClass("erreur")) {
        jQuery('#titreFormCarac').removeClass("erreur");
    }
    if (jQuery("#descriptionCarac").hasClass("erreur")) {
        jQuery('#descriptionCarac').removeClass("erreur");
    }
    if (jQuery("#trigrammeCarac").hasClass("erreur")) {
        jQuery('#trigrammeCarac').removeClass("erreur");
    }
    if (jQuery("#selectListeTypeCarac").hasClass("erreur")) {
        jQuery('#selectListeTypeCarac').removeClass("erreur");
    }
}

function creerCarac() {
    var nom = jQuery('#titreFormCarac').val();
    var trigramme = jQuery('#trigrammeCarac').val();
    var description = jQuery('#descriptionCarac').val();
    var image = jQuery('#listeImage').val();
    var isModifiable = jQuery('#isModifiable').is(':checked');
    var type = jQuery('#selectListeTypeCarac').val();
    var valMin = jQuery('#valMinCarac').val();
    var valMax = jQuery('#valMaxCarac').val();
    var formule = jQuery('#formuleCarac').html();
    var genre = jQuery('#selectGenreCarac').val();

    //Test des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur = "Vous devez renseigner un nom. \n";
        jQuery('#titreFormCarac').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur = erreur + "Vous devez renseigner une description. \n";
        jQuery('#descriptionCarac').addClass("erreur");
    }
    var trigrammeSansEspace = trigramme.replace(/\s/g, "");
    if (trigrammeSansEspace == "" || trigrammeSansEspace.length != 3) {
        erreur = erreur + "Le trigramme doit faire trois caractères. \n";
        jQuery('#trigrammeCarac').addClass("erreur");
    }
    if (isNaN(valMin)) {
        valMin = "";
    }
    if (isNaN(valMax)) {
        valMax = "";
    }
    if (valMin != "" && valMax != "" && valMin > valMax) {
        erreur = erreur + "La valeur minimum doit être inférieure à la valeur maximum. \n";
    }
    if (type == "0") {
        erreur = erreur + "Vous devez choisir un type pour cette caractéristique. \n";
        jQuery('#selectListeTypeCarac').addClass("erreur");
    }


    if (isModifiable == true) {
        isModifiable = 1;
    } else {
        isModifiable = 0;
    }
    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerCarac";
        } else {
            url = "gameplay/creerCarac";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                trigramme: trigramme,
                isModifiable: isModifiable,
                type: type,
                valMin: valMin,
                valMax: valMax,
                formule: formule,
                genre: genre
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("La création est effective.", "info");
                    cleanClassErrorCarac();
                    setTimeout(function () {
                        afficherDetailCarac(data);
                    }, 500);
                }
            }
        });
    }
}

function changerImageCarac() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageCarac").attr("src", src);
}

function afficherBlocFormule() {
    var type = jQuery('#selectListeTypeCarac').val();
    if (type == "0" || type == "Primaire") {
        jQuery('#divFormuleCarac').hide();
    } else {
        jQuery('#divFormuleCarac').show();
    }
}

//Gestion des terrains
function initBoutonTerrain() {
    jQuery('#ajouterTerrain').click(function () {
        ajouterTerrain();
    });
    jQuery('#showListeTerrainSaisonToutes').click(function () {
        showListeTerrainSaisonToutes();
    });
    jQuery('#hideListeTerrainSaisonToutes').click(function () {
        hideListeTerrainSaisonToutes();
    }).hide();
    jQuery('#showListeTerrainSaisonHiver').click(function () {
        showListeTerrainSaisonHiver();
    });
    jQuery('#hideListeTerrainSaisonHiver').click(function () {
        hideListeTerrainSaisonHiver();
    }).hide();
    jQuery('#showListeTerrainSaisonPrintemps').click(function () {
        showListeTerrainSaisonPrintemps();
    });
    jQuery('#hideListeTerrainSaisonPrintemps').click(function () {
        hideListeTerrainSaisonPrintemps();
    }).hide();
    jQuery('#showListeTerrainSaisonEte').click(function () {
        showListeTerrainSaisonEte();
    });
    jQuery('#hideListeTerrainSaisonEte').click(function () {
        hideListeTerrainSaisonEte();
    }).hide();
    jQuery('#showListeTerrainSaisonAutomne').click(function () {
        showListeTerrainSaisonAutomne();
    });
    jQuery('#hideListeTerrainSaisonAutomne').click(function () {
        hideListeTerrainSaisonAutomne();
    }).hide();
    jQuery('#editerTerrain').click(function () {
        editerTerrain();
    });
    jQuery('#annulerEditerTerrain').click(function () {
        annulerEditerTerrain();
    });
    jQuery('#retourListeTerrain').click(function () {
        retourListeType();
    });
    jQuery('#chargerNewImageTerrain').click(function () {
        chargerNewImageTerrain();
    });
    jQuery('#showlisteEffetsTerrains').click(function () {
        showlisteEffetsTerrains();
    });
    jQuery('#hidelisteEffetsTerrains').click(function () {
        hidelisteEffetsTerrains();
    }).hide();
    jQuery('#showlisteArtisanatTerrain').click(function () {
        showlisteArtisanatTerrain();
    });
    jQuery('#hidelisteArtisanatTerrain').click(function () {
        hidelisteArtisanatTerrain();
    }).hide();
    jQuery('#lancerRechercheSaison').click(function () {
        lancerRechercheSaison();
    });
    jQuery('#modifierTerrain').click(function () {
        modifierTerrain();
    });
    jQuery('#creerTerrain').click(function () {
        creerTerrain();
    });
    jQuery('#couleurTerrain').change(function () {
        changeCouleurTerrain();
    });
}

function initBoutonTexture() {
    jQuery('#ajouterTexture').click(function () {
        ajouterTexture();
    });
    jQuery('#retourListeTexture').click(function () {
        retourListeType("Texture");
    });
    jQuery('#modifierTexture').click(function () {
        modifierTexture();
    });
    jQuery('#supprimerTexture').click(function () {
        ouvreMsgBox("Voulez-vous vraiment supprimer cette texture ?", "info", "ouinon", supprimerTexture);
    });
}

function ajouterTerrain() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTerrain";
    } else {
        url = "gameplay/detailTerrain";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonTerrain();
        }
    });
}

function ajouterTexture() {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTexture";
    } else {
        url = "gameplay/detailTexture";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonTexture();
        }
    });
}

function editerTerrain() {
    var idTerrain = jQuery('#idTerrain').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTerrain";
    } else {
        url = "gameplay/detailTerrain";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idTerrain
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonTerrain();
        }
    });
}

function afficherDetailTerrain(idTerrain) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTerrain";
    } else {
        url = "gameplay/detailTerrain";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            id: idTerrain
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonTerrain();
        }
    });
}

function afficherDetailTexture(idTexture) {
    if (window.location.href.includes('/gameplay/')) {
        url = "detailTexture";
    } else {
        url = "gameplay/detailTexture";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "edition",
            id: idTexture
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonTexture();
        }
    });
}

function modifierTexture() {
    const idTexture = jQuery('#idTexture').val();
    const nom = jQuery('#titreFormTexture').val();
    if (nom === "") {
        ouvreMsgBox("Veuillez entrer un nom pour la texture", "erreur");
        return;
    }
    const file = jQuery('#imageTexture')[0].files[0];
    if (idTexture === undefined && file === undefined) {
        ouvreMsgBox("Veuillez uploader une image pour la texture", "erreur");
        return;
    }
    if (file !== undefined && !validMIMEtypes.includes(file.type)) {
        ouvreMsgBox("Le fichier uploadé n'est pas une image valide", "erreur");
        return;
    }
    const upload = new Upload(file);

    if (window.location.href.includes('/gameplay/')) {
        url = "modifierTexture";
    } else {
        url = "gameplay/modifierTexture";
    }
    upload.doUpload(
      url,
      {
          nom: nom,
          id: idTexture
      }
    );
}

function supprimerTexture() {
    const idTexture = jQuery('#idTexture').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "modifierTexture";
    } else {
        url = "gameplay/modifierTexture";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "suppression",
            id: idTexture
        },
        success: function (data) {
            retourListeType("Texture");
            ouvreMsgBox("Suppression effectuée.", "info");
        }
    });
}

function annulerEditerTerrain() {
    var idTerrain = jQuery('#idTerrain').val();
    afficherDetailTerrain(idTerrain);
}

function retourListeType(type = "Terrain") {
    if (window.location.href.includes('/gameplay/')) {
        url = "detail" + type;
    } else {
        url = "gameplay/detail" + type;
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            id: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonTerrain();
            initBoutonTexture();
        }
    });
}

function showListeTerrainSaisonToutes() {
    jQuery('#showListeTerrainSaisonToutes').hide();
    jQuery('#hideListeTerrainSaisonToutes').show();
    jQuery('#divTableTerrainsSaisonToutes').slideToggle();
}

function hideListeTerrainSaisonToutes() {
    jQuery('#showListeTerrainSaisonToutes').show();
    jQuery('#hideListeTerrainSaisonToutes').hide();
    jQuery('#divTableTerrainsSaisonToutes').slideToggle();
}

function showListeTerrainSaisonHiver() {
    jQuery('#showListeTerrainSaisonHiver').hide();
    jQuery('#hideListeTerrainSaisonHiver').show();
    jQuery('#divTableTerrainsSaisonHiver').slideToggle();
}

function hideListeTerrainSaisonHiver() {
    jQuery('#showListeTerrainSaisonHiver').show();
    jQuery('#hideListeTerrainSaisonHiver').hide();
    jQuery('#divTableTerrainsSaisonHiver').slideToggle();
}

function showListeTerrainSaisonPrintemps() {
    jQuery('#showListeTerrainSaisonPrintemps').hide();
    jQuery('#hideListeTerrainSaisonPrintemps').show();
    jQuery('#divTableTerrainsSaisonPrintemps').slideToggle();
}

function hideListeTerrainSaisonPrintemps() {
    jQuery('#showListeTerrainSaisonPrintemps').show();
    jQuery('#hideListeTerrainSaisonPrintemps').hide();
    jQuery('#divTableTerrainsSaisonPrintemps').slideToggle();
}

function showListeTerrainSaisonEte() {
    jQuery('#showListeTerrainSaisonEte').hide();
    jQuery('#hideListeTerrainSaisonEte').show();
    jQuery('#divTableTerrainsSaisonEte').slideToggle();
}

function hideListeTerrainSaisonEte() {
    jQuery('#showListeTerrainSaisonEte').show();
    jQuery('#hideListeTerrainSaisonEte').hide();
    jQuery('#divTableTerrainsSaisonEte').slideToggle();
}

function showListeTerrainSaisonAutomne() {
    jQuery('#showListeTerrainSaisonAutomne').hide();
    jQuery('#hideListeTerrainSaisonAutomne').show();
    jQuery('#divTableTerrainsSaisonAutomne').slideToggle();
}

function hideListeTerrainSaisonAutomne() {
    jQuery('#showListeTerrainSaisonAutomne').show();
    jQuery('#hideListeTerrainSaisonAutomne').hide();
    jQuery('#divTableTerrainsSaisonAutomne').slideToggle();
}

function chargerNewImageTerrain() {
    var id = jQuery('#idTerrain').val();
    var type = jQuery('#selectTypeJournee').val();
    var urlFile = jQuery('#newImageTerrain').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "uploadImageUrlTerrain";
        } else {
            url = "gameplay/uploadImageUrlTerrain";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type,
                urlFile: urlFile
            },
            success: function (data) {
                if (data == "errorType") {
                    ouvreMsgBox("Le type du fichier que vous souhaitez ajouter est incorrect (jpg, gif et png autorisés).", "erreur");
                } else if (data == "errorUpload") {
                    ouvreMsgBox("Une erreur est survenur au cours de l'upload.", "erreur");
                } else {
                    if (type == "jour") {
                        jQuery('#listeImageTerrainJour').html(data);
                    } else if (type == "nuit") {
                        jQuery('#listeImageTerrainNuit').html(data);
                    } else if (type == "jourBrouillard") {
                        jQuery('#listeImageTerrainJourBrouillard').html(data);
                    } else if (type == "nuitBrouillard") {
                        jQuery('#listeImageTerrainNuitBrouillard').html(data);
                    }
                }
            }
        });
    }
}

function showlisteEffetsTerrains() {
    var idTerrain = jQuery('#idTerrain').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteEffetsTerrains";
    } else {
        url = "gameplay/showlisteEffetsTerrains";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idTerrain,
            type: "terrain"
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsTerrains').hide();
            jQuery('#hidelisteEffetsTerrains').show();
            jQuery('#showlisteArtisanatTerrain').show();
            jQuery('#hidelisteArtisanatTerrain').hide();
            initBoutonEffet();
        }
    });
}

function hidelisteEffetsTerrains() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsTerrains').show();
    jQuery('#hidelisteEffetsTerrains').hide();
    jQuery('#showlisteArtisanatTerrain').show();
    jQuery('#hidelisteArtisanatTerrain').hide();
}

function showlisteArtisanatTerrain() {
    var idTerrain = jQuery('#idTerrain').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "showlisteArtisanatTerrain";
    } else {
        url = "gameplay/showlisteArtisanatTerrain";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: idTerrain
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteEffetsTerrains').show();
            jQuery('#hidelisteEffetsTerrains').hide();
            jQuery('#showlisteArtisanatTerrain').hide();
            jQuery('#hidelisteArtisanatTerrain').show();
            initBoutonArtisanat();
        }
    });
}

function hidelisteArtisanatTerrain() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteEffetsTerrains').show();
    jQuery('#hidelisteEffetsTerrains').hide();
    jQuery('#showlisteArtisanatTerrain').show();
    jQuery('#hidelisteArtisanatTerrain').hide();
}

function lancerRechercheSaison() {
    var nom = jQuery('#searchNom').val();
    var saison = jQuery('#selectSearchSaison').val();

    if (window.location.href.includes('/gameplay/')) {
        url = "lancerRechercheSaison";
    } else {
        url = "gameplay/lancerRechercheSaison";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            nom: nom,
            saison: saison
        },
        success: function (data) {
            var identifiant = data.substring(0, 2);
            if (identifiant == "id") {
                data.replace('id', '');
                afficherDetailTerrain(data);
            } else {
                jQuery('#divListeTerrains').html(data);
                initBoutonTerrain();
            }
        }
    });
}

function modifierTerrain() {
    var idTerrain = jQuery('#idTerrain').val();
    var nom = jQuery('#titreFormTerrain').val();
    var description = jQuery('#descriptionTerrain').val();
    var genre = jQuery('#selectGenreTerrain').val();
    var saison = jQuery('#selectSaisonTerrain').val();
    var typeAcces = jQuery('#selectTypeAccesTerrain').val();
    var idCompetence = jQuery('#selectCompetenceTerrain').val();
    var couleur = jQuery('#couleurTerrain').val();
    var mvt = jQuery('#mvtTerrain').val();
    var vision = jQuery('#visionTerrain').val();
    var zindex = jQuery('#zindexTerrain').val();
    var bloquemvt = jQuery('#isBloqueMvtTerrain').is(':checked');
    var bloquevue = jQuery('#isBloqueVueTerrain').is(':checked');
    var repartition = jQuery('#repartitionTerrain').val();

    if (bloquemvt == true) {
        bloquemvt = 1;
    } else {
        bloquemvt = 0;
    }

    if (bloquevue == true) {
        bloquevue = 1;
    } else {
        bloquevue = 1;
    }

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormTerrain').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionTerrain').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurTerrain').addClass("erreur");
    }

    var mvtSansEspace = mvt.replace(/\s/g, "");
    if (mvtSansEspace == "") {
        erreur += "Une valeur pour la base de mouvement est obligatoire.\n";
        jQuery('#mvtTerrain').addClass("erreur");
    } else if (isNaN(mvt)) {
        erreur += "La base de mouvement doit être un entier.\n";
        jQuery('#mvtTerrain').addClass("erreur");
    }

    var visionSansEspace = vision.replace(/\s/g, "");
    if (visionSansEspace == "") {
        erreur += "Une valeur pour le modificateur de vision est obligatoire.\n";
        jQuery('#visionTerrain').addClass("erreur");
    } else if (isNaN(vision)) {
        erreur += "Le modificateur de vision doit être un entier.\n";
        jQuery('#visionTerrain').addClass("erreur");
    }

    var zindexSansEspace = zindex.replace(/\s/g, "");
    if (zindexSansEspace == "") {
        erreur += "Une valeur z-index est obligatoire (Même si on sait pas ce que c'est !).\n";
        jQuery('#zindexTerrain').addClass("erreur");
    } else if (isNaN(vision)) {
        erreur += "La valeur z-index doit être un entier.\n";
        jQuery('#zindexTerrain').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "modifierTerrain";
        } else {
            url = "gameplay/modifierTerrain";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                id: idTerrain,
                nom: nom,
                description: description,
                genre: genre,
                saison: saison,
                typeAcces: typeAcces,
                idCompetence: idCompetence,
                couleur: couleur,
                mvt: mvt,
                vision: vision,
                zindex: zindex,
                bloquemvt: bloquemvt,
                bloquevue: bloquevue,
                repartition: repartition
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi pour cette saison. Veuillez en donner un autre.", "error");
                } else if (data == "errorColor") {
                    ouvreMsgBox("Cette couleur a déjà été choisie. Veuillez en choisir un autre.", "error");
                } else if (data == "errorRepartition") {
                    ouvreMsgBox("La répartition des terrains est incorrect. La somme doit impérativement faire 100.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                    cleanClassErrorTerrain();
                }
            }
        });
    }
}

function cleanClassErrorTerrain() {
    if (jQuery("#titreFormTerrain").hasClass("erreur")) {
        jQuery('#titreFormTerrain').removeClass("erreur");
    }
    if (jQuery("#descriptionTerrain").hasClass("erreur")) {
        jQuery('#descriptionTerrain').removeClass("erreur");
    }
    if (jQuery("#couleurTerrain").hasClass("erreur")) {
        jQuery('#couleurTerrain').removeClass("erreur");
    }
    if (jQuery("#mvtTerrain").hasClass("erreur")) {
        jQuery('#mvtTerrain').removeClass("erreur");
    }
    if (jQuery("#visionTerrain").hasClass("erreur")) {
        jQuery('#visionTerrain').removeClass("erreur");
    }
    if (jQuery("#zindexTerrain").hasClass("erreur")) {
        jQuery('#zindexTerrain').removeClass("erreur");
    }
}

function creerTerrain() {
    var nom = jQuery('#titreFormTerrain').val();
    var description = jQuery('#descriptionTerrain').val();
    var genre = jQuery('#genreTerrain').val();
    var saison = jQuery('#selectSaisonTerrain').val();
    var typeAcces = jQuery('#selectTypeAccesTerrain').val();
    var idCompetence = jQuery('#selectCompetenceTerrain').val();
    var couleur = jQuery('#couleurTerrain').val();
    var mvt = jQuery('#mvtTerrain').val();
    var vision = jQuery('#visionTerrain').val();
    var zindex = jQuery('#zindexTerrain').val();
    var bloquemvt = jQuery('#isBloqueMvtTerrain').is(':checked');
    var bloquevue = jQuery('#isBloqueVueTerrain').is(':checked');

    if (bloquemvt == true) {
        bloquemvt = 1;
    } else {
        bloquemvt = 0;
    }

    if (bloquevue == true) {
        bloquevue = 1;
    } else {
        bloquevue = 1;
    }

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreFormTerrain').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionTerrain').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurTerrain').addClass("erreur");
    }

    var mvtSansEspace = mvt.replace(/\s/g, "");
    if (mvtSansEspace == "") {
        erreur += "Une valeur pour la base de mouvement est obligatoire.\n";
        jQuery('#mvtTerrain').addClass("erreur");
    } else if (isNaN(mvt)) {
        erreur += "La base de mouvement doit être un entier.\n";
        jQuery('#mvtTerrain').addClass("erreur");
    }

    var visionSansEspace = vision.replace(/\s/g, "");
    if (visionSansEspace == "") {
        erreur += "Une valeur pour le modificateur de vision est obligatoire.\n";
        jQuery('#visionTerrain').addClass("erreur");
    } else if (isNaN(vision)) {
        erreur += "Le modificateur de vision doit être un entier.\n";
        jQuery('#visionTerrain').addClass("erreur");
    }

    var zindexSansEspace = zindex.replace(/\s/g, "");
    if (zindexSansEspace == "") {
        erreur += "Une valeur z-index est obligatoire (Même si on sait pas ce que c'est !).\n";
        jQuery('#zindexTerrain').addClass("erreur");
    } else if (isNaN(vision)) {
        erreur += "La valeur z-index doit être un entier.\n";
        jQuery('#zindexTerrain').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/gameplay/')) {
            url = "creerTerrain";
        } else {
            url = "gameplay/creerTerrain";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                genre: genre,
                saison: saison,
                typeAcces: typeAcces,
                idCompetence: idCompetence,
                couleur: couleur,
                mvt: mvt,
                vision: vision,
                zindex: zindex,
                bloquemvt: bloquemvt,
                bloquevue: bloquevue
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi pour cette saison. Veuillez en donner un autre.", "error");
                } else if (data == "errorColor") {
                    ouvreMsgBox("Cette couleur a déjà été choisie. Veuillez en choisir un autre.", "error");
                } else {
                    jQuery('#divListeResume').hide();
                    jQuery('#divListeResume').html("");
                    jQuery('#divDetailEditionReferentiel').show();
                    jQuery('#divDetailEditionReferentiel').html(data);
                    jQuery('#divDetailConsultationReferentiel').hide();
                    jQuery('#divDetailConsultationReferentiel').html("");
                    initBoutonTerrain();
                    ouvreMsgBox("La création est effective. Veuillez maintenant ajouter des images au terrain.", "info");
                }
            }
        });
    }
}

function deleteImageTerrain(fichier, type) {
    var idTerrain = jQuery('#idTerrain').val();
    if (window.location.href.includes('/gameplay/')) {
        url = "deleteImageTerrain";
    } else {
        url = "gameplay/deleteImageTerrain";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idTerrain: idTerrain,
            image: fichier,
            type: type
        },
        success: function (data) {
            if (type == "jour") {
                type = "Jour";
            } else if (type == "nuit") {
                type = "Nuit";
            } else if (type == "jourBrouillard") {
                type = "JourBrouillard";
            } else if (type == "nuitBrouillard") {
                type = "NuitBrouillard";
            }
            var element = "listeImageTerrain" + type;

            jQuery('#' + element).html(data);
        }
    });
}

function changeCouleurTerrain() {
    var couleur = jQuery('#couleurTerrain').val();
    jQuery('#carreCouleurTerrain').css('background-color', couleur);
}

var Upload = function (file) {
    this.file = file;
};

Upload.prototype.getType = function () {
    return this.file.type;
};
Upload.prototype.getSize = function () {
    return this.file.size;
};
Upload.prototype.getName = function () {
    return this.file.name;
};
Upload.prototype.doUpload = function (url, fields) {
    let formData = new FormData();

    // add assoc key values, this will be posts values
    if (this.file !== undefined) {
        formData.append("file", this.file, this.getName());
        formData.append("upload_file", true);
    }
    for (let key in fields) {
        formData.append(key, fields[key])
    }

    $.ajax({
        type: "POST",
        url: url,
        success: function (data) {
            if (data === "success") {
                retourListeType("Texture");
                ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
            } else if (data === "imageExist") {
                ouvreMsgBox("Une image avec le même nom est déjà présente sur le serveur.", "error");
            } else {
                ouvreMsgBox("Une erreur inconnue s'est produite pendant l'enregistrement.", "error");
            }
        },
        error: function (error) {
            ouvreMsgBox("Une erreur s'est produite pendant l'upload.", "error");
        },
        async: true,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        timeout: 60000
    });
};

const validMIMEtypes = [
    "image/gif",
    "image/jpeg",
    "image/png"
]