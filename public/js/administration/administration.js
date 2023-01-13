//Init
jQuery(document).ready(function init() {
    //Redirection si pas de droits
    var nbOnglet = jQuery('#nombreOngletAdministration').val();
    if (nbOnglet == 0) {
        redirectAccueil();
    } else {
        jQuery('#blocAdministration').show();
    }
    initBouton();

});

function initBouton() {
    initBoutonDroit();
    initBoutonLogs();
    initBoutonReferentiels();
    initBoutonProfilTest();
    initBoutonImage();
}

function openOngletAdministration(conteneur) {
    if (conteneur != "") {
        if (window.location.href.includes('/administration/')) {
            url = "afficherAdministration";
        } else {
            url = "administration/afficherAdministration";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                conteneur: conteneur
            },
            success: function (data) {
                jQuery('#blocAdministration').html(data);

                var listeNode = document.getElementsByName("ongletAdministration");
                for (var i = 0; i < listeNode.length; i++) {
                    var element = listeNode[i];
                    element.classList.remove("ongletAdministrationSelect");
                    element.classList.remove("ongletAdministration");
                    if (element.id != conteneur) {
                        element.classList.add("ongletAdministration");
                    }
                }
                jQuery('#' + conteneur).addClass("ongletAdministrationSelect");
                initBouton();
            },
            error: function (errorThrown) {

            }
        });
    }
}

function openOngletAdministrationQuestionnaire(conteneur, type, idType) {
    if (conteneur != "") {
        if (window.location.href.includes('/administration/')) {
            url = "afficherAdministration";
        } else {
            url = "administration/afficherAdministration";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                conteneur: conteneur
            },
            success: function (data) {
                jQuery('#blocAdministration').html(data);
                var listeNode = document.getElementsByName("ongletAdministration");
                for (var i = 0; i < listeNode.length; i++) {
                    var element = listeNode[i];
                    element.classList.remove("ongletAdministrationSelect");
                    element.classList.remove("ongletAdministration");
                    if (element.id != conteneur) {
                        element.classList.add("ongletAdministration");
                    }
                }
                jQuery('#' + conteneur).addClass("ongletAdministrationSelect");
                initBouton();

                if (type == "royaumes") {
                    afficherDetailRoyaume(idType);
                } else if (type == "races") {
                    afficherDetailRace(idType);
                } else if (type == "religions") {
                    afficherDetailReligion(idType);
                } else if (type == "divinites") {
                    afficherDetailDieu(idType);
                }
            },
            error: function (errorThrown) {

            }
        });
    }
}

//#### Gestion des Droits #####//
function initBoutonDroit() {
    jQuery('#boutonModifierDroit').click(function () {
        boxModifierDroit();
    });
    jQuery('#boutonAjouterDroit').click(function () {
        boxAjouterDroit();
    });
    jQuery('#boutonSupprimerDroit').click(function () {
        boxSupprimerDroit();
    });
}

function chargeListeAutorisations() {
    var idProfil = jQuery('#listeProfils').val();
    if (window.location.href.includes('/administration/')) {
        url = "chargeListeAutorisations";
    } else {
        url = "administration/chargeListeAutorisations";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idProfil: idProfil
        },
        success: function (data) {
            jQuery('#listeAutorisationsDroit').html(data);
        }
    });
}

function boxAjouterDroit() {
    ouvreMsgBox("Entrez un libellé pour le nouveau profil", "question", "saisie", ajouterDroit, "");
}

function ajouterDroit() {
    var libelle = recupSaisie();
    var libelleSansEspace = libelle.replace(/\s/g, "");
    if (libelleSansEspace == "") {
        ouvreMsgBox("Vous devez entrer un libellé.", "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterDroit";
        } else {
            url = "administration/ajouterDroit";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                libelle: libelle
            },
            success: function (data) {
                if (data == "errorExistant") {
                    ouvreMsgBox("Ce profil existe déjà.", "erreur");
                } else {
                    var result = data.split('@');
                    jQuery('#listeProfils').html(result[0]);
                    var idProfil = "droit" + result[1];
                    document.getElementById(idProfil).selected = "selected";
                    chargeListeAutorisations();
                    initBoutonDroit();
                    ouvreMsgBox("Le profil a été correctement crée.", "info");
                }
            }
        });
    }
}

function boxSupprimerDroit() {
    ouvreMsgBox("Supprimer ce droit l'effacera pour tous les personnages l'ayant (s'ils n'en ont pas d'autres, ils passeront simplement 'joueur'). Confirmez vous la suppression de ce profil ?", "question", "ouinon", supprimerDroit, "");
}

function supprimerDroit() {
    var idProfil = jQuery('#listeProfils').val();
    if (idProfil == "0") {
        ouvreMsgBox("Vous devez choisir un profil.", "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "supprimerDroit";
        } else {
            url = "administration/supprimerDroit";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idProfil: idProfil
            },
            success: function (data) {
                jQuery('#blocAdministration').html(data);
                ouvreMsgBox("Le profil a été correctement supprimé.", "info");
                initBoutonDroit();
            }
        });
    }
}

function boxModifierDroit() {
    ouvreMsgBox("Confirmez les modifications ?", "question", "ouinon", modifierDroit, "");
}

function modifierDroit() {
    var idProfil = jQuery('#listeProfils').val();
    //Récupération des checkbox
    var listeAutorisation = document.getElementsByName('autorisations');
    var idAutorisations = "";
    for (var i = 0; i < listeAutorisation.length; i++) {
        var element = listeAutorisation[i];
        if (element.checked === true) {
            if (i == 0) {
                idAutorisations = element.value;
            } else {
                idAutorisations = idAutorisations + "," + element.value;
            }
        }
    }
    if (idProfil == "0") {
        ouvreMsgBox("Vous devez choisir un profil.", "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierDroit";
        } else {
            url = "administration/modifierDroit";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idProfil: idProfil,
                idAutorisations: idAutorisations
            },
            success: function (data) {
                var result = data.split('@');
                jQuery('#listeProfils').html(result[0]);
                var idProfil = "droit" + result[1];
                document.getElementById(idProfil).selected = "selected";
                chargeListeAutorisations();
                initBoutonDroit();
                ouvreMsgBox("Le profil a été correctement modifié.", "info");
            }
        });
    }
}

//#### Gestion des logs #####//
function initBoutonLogs() {
    jQuery('#boutonLogMJ').click(function () {
        chargerLog('MJ');
    });
    jQuery('#boutonLogADMIN').click(function () {
        chargerLog('ADMIN');
    });
    jQuery('#boutonLogDEV').click(function () {
        chargerLog('DEV');
    });
    jQuery('#boutonLogArchive').click(function () {
        chargerLog('Archive');
    });
    jQuery('#logsFiltre').click(function () {
        filtreLog();
    });
    jQuery('#exporterLogs').click(function () {
        boxExporterLogs();
    });
}

function chargerLog(type) {
    var nomAuteur = jQuery('#logAuteur').val();
    var nomCible = jQuery('#logCible').val();
    var typeRecherche = jQuery('#selectLogType').val();
    if (window.location.href.includes('/administration/')) {
        url = "chargerLog";
    } else {
        url = "administration/chargerLog";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type,
            nomAuteur: nomAuteur,
            nomCible: nomCible,
            typeRecherche: typeRecherche
        },
        success: function (data) {
            jQuery('#blocAdministration').html(data);
            initBoutonLogs();
        }
    });
}

function chargerType() {
    var nomAuteur = jQuery('#logAuteur').val();
    var nomCible = jQuery('#logCible').val();
    var typeRecherche = jQuery('#selectLogType').val();
    var logSelect = jQuery('#logSelect').val();
    if (window.location.href.includes('/administration/')) {
        url = "filtreLog";
    } else {
        url = "administration/filtreLog";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            nomAuteur: nomAuteur,
            nomCible: nomCible,
            typeRecherche: typeRecherche,
            logSelect: logSelect
        },
        success: function (data) {
            jQuery('#divResultatLogs').html(data);
        }
    });
}

function changeLogNbEnregistrementParPage() {
    var nbElementParPage = jQuery('#enregistrementPageLog').val();
    if (isNaN(nbElementParPage) || nbElementParPage < 1) {
        ouvreMsgBox('Vous devez entrer un chiffre.', "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "changeNbElementParPage";
        } else {
            url = "administration/changeNbElementParPage";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nbElementParPage: nbElementParPage
            },
            success: function (data) {
                filtreLog();
            }
        });
    }
}

function filtreLog() {
    var nomAuteur = jQuery('#logAuteur').val();
    var nomCible = jQuery('#logCible').val();
    var typeRecherche = jQuery('#selectLogType').val();
    var logSelect = jQuery('#logSelect').val();
    if (window.location.href.includes('/administration/')) {
        url = "filtreLog";
    } else {
        url = "administration/filtreLog";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            nomAuteur: nomAuteur,
            nomCible: nomCible,
            typeRecherche: typeRecherche,
            logSelect: logSelect
        },
        success: function (data) {
            jQuery('#divResultatLogs').html(data);
        }
    });
}

function loadLogPage(number) {
    if (window.location.href.includes('/administration/')) {
        url = "changerPageLogs";
    } else {
        url = "administration/changerPageLogs";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            page: number
        },
        success: function (data) {
            filtreLog();
        }
    });
}

function trieDate() {
    if (window.location.href.includes('/administration/')) {
        url = "trieDateLogs";
    } else {
        url = "administration/trieDateLogs";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (data) {
            filtreLog();
        }
    });
}

function boxExporterLogs() {
    ouvreMsgBox("Les logs de plus de 30 jours seront exportés dans un fichier et supprimer de la base. Voulez-vous continuer ?", "question", "ouinon", exporterLogs, "");
}

function exporterLogs() {
    var type = jQuery('#logSelect').val();
    if (window.location.href.includes('/administration/')) {
        url = "exporterLogs";
    } else {
        url = "administration/exporterLogs";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type
        },
        success: function (data) {
            if (data == "empty") {
                ouvreMsgBox("Il n'y a pas de logs de plus de 30 jours.", "info");
            } else if (data == "error") {
                ouvreMsgBox("Une erreur s'est produite.", "erreur");
            } else if (data == "sucess") {
                ouvreMsgBox("Les logs ont été archivé avec succès");
                filtreLog();
            }
        }
    });
}

function chargerTypeArchive() {
    var type = jQuery('#selectLogType').val();
    if (window.location.href.includes('/administration/')) {
        url = "chargerTypeArchive";
    } else {
        url = "administration/chargerTypeArchive";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            type: type
        },
        success: function (data) {
            jQuery('#divResultatLogs').html(data);
        }
    });
}

function downloadFiles(fichier) {
    if (fichier != "" && fichier != null) {
        if (window.location.href.includes('/administration/')) {
            url = "downloadFiles";
        } else {
            url = "administration/downloadFiles";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                fichier: encodeURIComponent(fichier)
            },
            success: function (data) {
                window.location = data;
            }
        });
    }
}

//#### Gestion des Référentiels ####//
//Général
function initBoutonReferentiels() {
    //Général
    jQuery("#s").keyup(function () {
        chargeSuggestionArticle();
    });
    jQuery('#boutonRefRoyaume').click(function () {
        retourListeRoyaume();
    });
    jQuery('#boutonRefRace').click(function () {
        retourListeRace();
    });
    jQuery('#boutonRefReligion').click(function () {
        retourListeReligion();
    });
    jQuery('#boutonRefDivinite').click(function () {
        retourListeDieu();
    });
    jQuery('#boutonRefVille').click(function () {
        retourListeVille();
    });

    //Royaumes
    jQuery('#boutonAjouterRoyaume').click(function () {
        accesCreationRoyaume();
    });
    jQuery('#editerRoyaume').click(function () {
        afficherDetailModificationRoyaume();
    });
    jQuery('#retourListeRoyaume').click(function () {
        retourListeRoyaume();
    });
    jQuery('#annulerEditerRoyaume').click(function () {
        afficherDetailRoyaume(jQuery('#idRoyaume').val());
    });
    jQuery('#modifierRoyaume').click(function () {
        boxModifierRoyaume();
    });
    jQuery('#couleurRoyaumeEdition').change(function () {
        chargerCouleurFormRoyaume();
    });
    jQuery('#couleurRoyaumeCreation').change(function () {
        chargerCouleurFormRoyaume();
    });
    jQuery('#ajouterReligionJouable').click(function () {
        ajouterReligionJouable();
    });
    jQuery('#ajouterRaceJouable').click(function () {
        ajouterRaceJouable();
    }),
      jQuery('#chargerNewImageRoyaume').click(function () {
          chargerNewImageRoyaume();
      });
    jQuery('#consulterQuestionnaireInscriptionRoyaume').click(function () {
        consulterQuestionnaire('royaume', jQuery('#idRoyaume').val());
    });
    jQuery('#creerRoyaume').click(function () {
        creerRoyaume();
    });
    jQuery('#showlisteBonusRoyaume').click(function () {
        showlisteBonusRoyaume();
    });
    jQuery('#hidelisteBonusRoyaume').click(function () {
        hidelisteBonusRoyaume();
    });
    jQuery('#hidelisteBonusRoyaume').hide();

    //Races
    jQuery('#boutonAjouterRace').click(function () {
        accesCreationRace();
    });
    jQuery('#editerRace').click(function () {
        afficherDetailModificationRace();
    });
    jQuery('#retourListeRace').click(function () {
        retourListeRace();
    });
    jQuery('#annulerEditerRace').click(function () {
        afficherDetailRace(jQuery('#idRace').val());
    });
    jQuery('#modifierRace').click(function () {
        boxModifierRace();
    });
    jQuery('#ajouterReligionJouableRace').click(function () {
        ajouterReligionJouableRace();
    });
    jQuery('#chargerNewImageRace').click(function () {
        chargerNewImageRace();
    });
    jQuery('#consulterQuestionnaireInscriptionRace').click(function () {
        consulterQuestionnaire('race', jQuery('#idRace').val());
    });
    jQuery('#creerRace').click(function () {
        creerRace();
    });
    jQuery('#showlisteBonusRace').click(function () {
        showlisteBonusRace();
    });
    jQuery('#hidelisteBonusRace').click(function () {
        hidelisteBonusRace();
    });
    jQuery('#hidelisteBonusRace').hide();

    //Religion
    jQuery('#boutonAjouterReligion').click(function () {
        accesCreationReligion();
    });
    jQuery('#editerReligion').click(function () {
        afficherDetailModificationReligion();
    });
    jQuery('#retourListeReligion').click(function () {
        retourListeReligion();
    });
    jQuery('#annulerEditerReligion').click(function () {
        afficherDetailReligion(jQuery('#idReligion').val());
    });
    jQuery('#modifierReligion').click(function () {
        boxModifierReligion();
    });
    jQuery('#ajouterDiviniteReligion').click(function () {
        ajouterDiviniteReligion();
    });
    jQuery('#chargerNewImageReligion').click(function () {
        chargerNewImageReligion();
    });
    jQuery('#consulterQuestionnaireInscriptionReligion').click(function () {
        consulterQuestionnaire('religion', jQuery('#idReligion').val());
    });
    jQuery('#creerReligion').click(function () {
        creerReligion();
    });
    jQuery('#showlisteBonusReligion').click(function () {
        showlisteBonusReligion();
    });
    jQuery('#hidelisteBonusReligion').click(function () {
        hidelisteBonusReligion();
    });
    jQuery('#hidelisteBonusReligion').hide();

    //Dieu
    jQuery('#boutonAjouterDieu').click(function () {
        accesCreationDieu();
    });
    jQuery('#editerDieu').click(function () {
        afficherDetailModificationDieu();
    });
    jQuery('#retourListeDieu').click(function () {
        retourListeDieu();
    });
    jQuery('#annulerEditerDieu').click(function () {
        afficherDetailDieu(jQuery('#idDieu').val());
    });
    jQuery('#modifierDieu').click(function () {
        boxModifierDieu();
    });
    jQuery('#chargerNewImageDieu').click(function () {
        chargerNewImageDieu();
    });
    jQuery('#creerDieu').click(function () {
        creerDieu();
    });
    jQuery('#couleurDieuEdition').change(function () {
        chargerCouleurFormDieu();
    });
    jQuery('#couleurDieuCreation').change(function () {
        chargerCouleurFormDieu();
    });
    jQuery('#consulterQuestionnaireInscriptionDivinite').click(function () {
        consulterQuestionnaire('divinites', jQuery('#idDieu').val());
    });

    //Villes
    initBoutonsVille();
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

function getMultiCheckbox(nameCheckbox) {
    var checkboxes = document.getElementsByName(nameCheckbox);
    var result = "";
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            result = result + checkboxes[i].value + ",";
        }
    }
    if (result != "") {
        return result.substring(0, result.length - 1);
    } else {
        return result;
    }
}

//Royaume
function afficherDetailRoyaume(idRoyaume) {
    if (window.location.href.includes('/administration/')) {
        url = "detailRoyaume";
    } else {
        url = "administration/detailRoyaume";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            idRoyaume: idRoyaume
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonReferentiels();
        }
    });
}

function accesCreationRoyaume() {
    if (window.location.href.includes('/administration/')) {
        url = "detailRoyaume";
    } else {
        url = "administration/detailRoyaume";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            idRoyaume: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherDetailModificationRoyaume() {
    var idRoyaume = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "detailRoyaume";
    } else {
        url = "administration/detailRoyaume";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            idRoyaume: idRoyaume
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function retourListeRoyaume() {
    if (window.location.href.includes('/administration/')) {
        url = "detailRoyaume";
    } else {
        url = "administration/detailRoyaume";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            idRoyaume: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonRefRoyaume').addClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefVille').removeClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function changerImageRoyaume() {
    var src = jQuery('#listeEtendard').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#etendardRoyaume").attr("src", src);
}

function boxModifierRoyaume() {
    ouvreMsgBox("Modifier les informations sur le royaume impactera l'ensemble du personnage et des joueurs. Voulez-vous continuer ?", "question", "ouinon", modifierRoyaume, "");
}

function modifierRoyaume() {
    var idRoyaume = jQuery('#idRoyaume').val();
    var nom = jQuery('#titreRoyaumeEdition').val();
    var description = jQuery('#descriptionRoyaumeEdition').val();
    var etendard = jQuery('#listeEtendard').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionRoyaumeEdition').is(':checked');
    var couleur = jQuery('#couleurRoyaumeEdition').val();

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRoyaumeEdition').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRoyaumeEdition').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurRoyaumeEdition').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierRoyaume";
        } else {
            url = "administration/modifierRoyaume";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idRoyaume: idRoyaume,
                nom: nom,
                description: description,
                etendard: etendard,
                titre: titreArticle,
                isDispoInscription: isDispoInscription,
                couleur: couleur
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                }
            }
        });
    }
}

function chargerCouleurFormRoyaume() {
    if (jQuery('#couleurRoyaumeEdition').length) {
        var couleur = jQuery('#couleurRoyaumeEdition').val();
    } else if (jQuery('#couleurRoyaumeCreation').length) {
        var couleur = jQuery('#couleurRoyaumeCreation').val();
    }
    jQuery('#carreCouleurRoyaume').css('background-color', couleur);
}

function ajouterReligionJouable() {
    var idReligion = jQuery('#selectReligionAutorisable').val();
    var idRoyaume = jQuery('#idRoyaume').val();
    if (idReligion == 0) {
        ouvreMsgBox('Vous devez sélectionner une religion.', 'info');
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterReligionJouable";
        } else {
            url = "administration/ajouterReligionJouable";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idRoyaume: idRoyaume,
                idReligion: idReligion
            },
            success: function (data) {
                jQuery('#religionAutoriseeRoyaumeEdition').html(data);
                rechargerSelectReligion();
            }
        });
    }
}

function rechargerSelectReligion() {
    var idRoyaume = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "rechargerSelectReligion";
    } else {
        url = "administration/rechargerSelectReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRoyaume: idRoyaume
        },
        success: function (data) {
            jQuery('#divSelectReligionsAutoriseesRoyaume').html(data);
            initBoutonReferentiels();
        }
    });
}

function ajouterRaceJouable() {
    var idRace = jQuery('#selectRaceAutorisable').val();
    var idRoyaume = jQuery('#idRoyaume').val();
    if (idRace == 0) {
        ouvreMsgBox('Vous devez sélectionner une race.', 'info');
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterRaceJouable";
        } else {
            url = "administration/ajouterRaceJouable";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idRoyaume: idRoyaume,
                idRace: idRace
            },
            success: function (data) {
                jQuery('#raceAutoriseeRoyaumeEdition').html(data);
                rechargerSelectRace();
            }
        });
    }
}

function rechargerSelectRace() {
    var idRoyaume = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "rechargerSelectRace";
    } else {
        url = "administration/rechargerSelectRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRoyaume: idRoyaume
        },
        success: function (data) {
            jQuery('#divSelectsRaceAutoriseesRoyaume').html(data);
            initBoutonReferentiels();
        }
    });
}

function supprimerReligionJouable(idReligion) {
    var idRoyaume = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "supprimerReligionJouable";
    } else {
        url = "administration/supprimerReligionJouable";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRoyaume: idRoyaume,
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#religionAutoriseeRoyaumeEdition').html(data);
            rechargerSelectReligion();
        }
    });
}

function supprimerRaceJouable(idRace) {
    var idRoyaume = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "supprimerRaceJouable";
    } else {
        url = "administration/supprimerRaceJouable";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRoyaume: idRoyaume,
            idRace: idRace
        },
        success: function (data) {
            jQuery('#raceAutoriseeRoyaumeEdition').html(data);
            rechargerSelectRace();
        }
    });
}

function chargerNewImageRoyaume() {
    var id = jQuery('#idRoyaume').val();
    var type = "Royaume";
    var urlFile = jQuery('#newEtendardRoyaume').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "uploadImageUrl";
        } else {
            url = "administration/uploadImageUrl";
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
                    jQuery('#listeEtendardRoyaume').html(data);
                    ouvreMsgBox("Fichier correctement uploadé.", "info");
                }
            }
        });
    }
}

function creerRoyaume() {
    var nom = jQuery('#titreRoyaumeCreation').val();
    var description = jQuery('#descriptionRoyaumeCreation').val();
    var etendard = jQuery('#listeEtendard').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionRoyaumeCreation').is(':checked');
    var couleur = jQuery('#couleurRoyaumeCreation').val();

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }
    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRoyaumeCreation').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRoyaumeCreation').addClass("erreur");
    }
    var couleurSansEspace = description.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurRoyaumeCreation').addClass("erreur");
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "creerRoyaume";
        } else {
            url = "administration/creerRoyaume";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                etendard: etendard,
                titre: titreArticle,
                isDispoInscription: isDispoInscription,
                couleur: couleur
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    afficherDetailRoyaume(data);
                }
            }
        });
    }
}

function showlisteBonusRoyaume() {
    var id = jQuery('#idRoyaume').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteBonus";
    } else {
        url = "administration/showlisteBonus";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: id,
            type: 'royaume'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteBonusRoyaume').hide();
            jQuery('#hidelisteBonusRoyaume').show();
            initBoutonBonus();
        }
    });
}

function hidelisteBonusRoyaume() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteBonusRoyaume').show();
    jQuery('#hidelisteBonusRoyaume').hide();
}

//Race
function afficherDetailRace(idRace) {
    if (window.location.href.includes('/administration/')) {
        url = "detailRace";
    } else {
        url = "administration/detailRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            idRace: idRace
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonReferentiels();
        }
    });
}

function accesCreationRace() {
    if (window.location.href.includes('/administration/')) {
        url = "detailRace";
    } else {
        url = "administration/detailRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            idRace: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherDetailModificationRace() {
    var idRace = jQuery('#idRace').val();
    if (window.location.href.includes('/administration/')) {
        url = "detailRace";
    } else {
        url = "administration/detailRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            idRace: idRace
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function retourListeRace() {
    if (window.location.href.includes('/administration/')) {
        url = "detailRace";
    } else {
        url = "administration/detailRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            idRace: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonRefRoyaume').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').addClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefVille').removeClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function changerImageRace() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageRace").attr("src", src);
}

function boxModifierRace() {
    ouvreMsgBox("Modifier les informations sur la race impactera l'ensemble du personnage et des joueurs. Voulez-vous continuer ?", "question", "ouinon", modifierRace, "");
}

function modifierRace() {
    var idRace = jQuery('#idRace').val();
    var nom = jQuery('#titreRaceEdition').val();
    var description = jQuery('#descriptionRaceEdition').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionRaceEdition').is(':checked');
    var tailleMin = jQuery('#raceTailleMinEdition').val();
    var tailleMax = jQuery('#raceTailleMaxEdition').val();
    var poidsMin = jQuery('#racePoidsMinEdition').val();
    var poidsMax = jQuery('#racePoidsMaxEdition').val();
    var ageMin = jQuery('#raceAgeMinEdition').val();
    var ageMax = jQuery('#raceAgeMaxEdition').val();
    var listeCouleurYeuxAutorisees = getMultiCheckbox("couleurYeux");
    var listeCouleurCheveuxAutorisees = getMultiCheckbox("couleurCheveux");

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRaceEdition').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRaceEdition').addClass("erreur");
    }
    //Contrôle sur les paramètres physiques
    if (isNaN(tailleMin)) {
        erreur += "La taille minimale doit être un nombre.\n";
        jQuery('#raceTailleMinEdition').addClass("erreur");
        tailleMin = 0;
    }
    if (isNaN(tailleMax)) {
        erreur += "La taille maximale doit être un nombre.\n";
        jQuery('#raceTailleMaxEdition').addClass("erreur");
        tailleMax = 0;
    }
    if (parseInt(tailleMin) > parseInt(tailleMax)) {
        erreur += "La taille minimale doit être inférieure à la taille maximale.\n";
    }
    if (isNaN(poidsMin)) {
        erreur += "Le poids minimal doit être un nombre.\n";
        jQuery('#racePoidsMinEdition').addClass("erreur");
        poidsMin = 0;
    }
    if (isNaN(poidsMax)) {
        erreur += "Le poids maximal doit être un nombre.\n";
        jQuery('#racePoidsMaxEdition').addClass("erreur");
        poidsMax = 0;
    }
    if (parseInt(poidsMin) > parseInt(poidsMax)) {
        erreur += "Le poids minimal doit être inférieur au poids maximal.\n";
    }
    if (isNaN(ageMin)) {
        erreur += "L'âge minimal doit être un nombre.\n";
        jQuery('#raceAgeMinEdition').addClass("erreur");
        ageMin = 0;
    }
    if (isNaN(ageMax)) {
        erreur += "L'âge maximal doit être un nombre.\n";
        jQuery('#raceAgeMaxEdition').addClass("erreur");
        ageMax = 0;
    }
    if (parseInt(ageMin) > parseInt(ageMax)) {
        erreur += "L'âge minimal doit être inférieur à l'âge maximal.\n";
    }

    if (listeCouleurYeuxAutorisees == "") {
        erreur += "Au moins une couleur doit être autorisée pour les yeux.\n";
    }
    if (listeCouleurCheveuxAutorisees == "") {
        erreur += "Au moins une couleur doit être autorisée pour les cheveux.\n";
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierRace";
        } else {
            url = "administration/modifierRace";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idRace: idRace,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                tailleMin: tailleMin,
                tailleMax: tailleMax,
                poidsMin: poidsMin,
                poidsMax: poidsMax,
                ageMin: ageMin,
                ageMax: ageMax,
                listeCouleurYeuxAutorisees: listeCouleurYeuxAutorisees,
                listeCouleurCheveuxAutorisees: listeCouleurCheveuxAutorisees
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                }
            }
        });
    }
}

function ajouterReligionJouableRace() {
    var idReligion = jQuery('#selectReligionAutorisable').val();
    var idRace = jQuery('#idRace').val();
    if (idReligion == 0) {
        ouvreMsgBox('Vous devez sélectionner une religion.', 'info');
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterReligionJouableRace";
        } else {
            url = "administration/ajouterReligionJouableRace";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idRace: idRace,
                idReligion: idReligion
            },
            success: function (data) {
                jQuery('#religionAutoriseeRaceEdition').html(data);
                rechargerSelectReligionRace();
            }
        });
    }
}

function rechargerSelectReligionRace() {
    var idRace = jQuery('#idRace').val();
    if (window.location.href.includes('/administration/')) {
        url = "rechargerSelectReligionRace";
    } else {
        url = "administration/rechargerSelectReligionRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRace: idRace
        },
        success: function (data) {
            jQuery('#divSelectReligionsAutoriseesRace').html(data);
            initBoutonReferentiels();
        }
    });
}

function supprimerReligionJouableRace(idReligion) {
    var idRace = jQuery('#idRace').val();
    if (window.location.href.includes('/administration/')) {
        url = "supprimerReligionJouableRace";
    } else {
        url = "administration/supprimerReligionJouableRace";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idRace: idRace,
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#religionAutoriseeRaceEdition').html(data);
            rechargerSelectReligionRace();
        }
    });
}

function chargerNewImageRace() {
    var id = jQuery('#idRace').val();
    var type = "Race";
    var urlFile = jQuery('#newImageRace').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "uploadImageUrl";
        } else {
            url = "administration/uploadImageUrl";
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

function creerRace() {
    var nom = jQuery('#titreRaceCreation').val();
    var description = jQuery('#descriptionRaceCreation').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionRaceCreation').is(':checked');
    var tailleMin = jQuery('#raceTailleMinCreation').val();
    var tailleMax = jQuery('#raceTailleMaxCreation').val();
    var poidsMin = jQuery('#racePoidsMinCreation').val();
    var poidsMax = jQuery('#racePoidsMaxCreation').val();
    var ageMin = jQuery('#raceAgeMinCreation').val();
    var ageMax = jQuery('#raceAgeMaxCreation').val();
    var listeCouleurYeuxAutorisees = getMultiCheckbox("couleurYeux");
    var listeCouleurCheveuxAutorisees = getMultiCheckbox("couleurCheveux");

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRaceCreation').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRaceCreation').addClass("erreur");
    }
    //Contrôle sur les paramètres physiques
    if (isNaN(tailleMin)) {
        erreur += "La taille minimale doit être un nombre.\n";
        jQuery('#raceTailleMinCreation').addClass("erreur");
        tailleMin = 0;
    }
    if (isNaN(tailleMax)) {
        erreur += "La taille maximale doit être un nombre.\n";
        jQuery('#raceTailleMaxCreation').addClass("erreur");
        tailleMax = 0;
    }
    if (parseInt(tailleMin) > parseInt(tailleMax)) {
        erreur += "La taille minimale doit être inférieure à la taille maximale.\n";
    }
    if (isNaN(poidsMin)) {
        erreur += "Le poids minimal doit être un nombre.\n";
        jQuery('#racePoidsMinCreation').addClass("erreur");
        poidsMin = 0;
    }
    if (isNaN(poidsMax)) {
        erreur += "Le poids maximal doit être un nombre.\n";
        jQuery('#racePoidsMaxCreation').addClass("erreur");
        poidsMax = 0;
    }
    if (parseInt(poidsMin) > parseInt(poidsMax)) {
        erreur += "Le poids minimal doit être inférieur au poids maximal.\n";
    }
    if (isNaN(ageMin)) {
        erreur += "L'âge minimal doit être un nombre.\n";
        jQuery('#raceAgeMinCreation').addClass("erreur");
        ageMin = 0;
    }
    if (isNaN(ageMax)) {
        erreur += "L'âge maximal doit être un nombre.\n";
        jQuery('#raceAgeMaxCreation').addClass("erreur");
        ageMax = 0;
    }
    if (parseInt(ageMin) > parseInt(ageMax)) {
        erreur += "L'âge minimal doit être inférieur à l'âge maximal.\n";
    }

    if (listeCouleurYeuxAutorisees == "") {
        erreur += "Au moins une couleur doit être autorisée pour les yeux.\n";
    }
    if (listeCouleurCheveuxAutorisees == "") {
        erreur += "Au moins une couleur doit être autorisée pour les cheveux.\n";
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "creerRace";
        } else {
            url = "administration/creerRace";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                tailleMin: tailleMin,
                tailleMax: tailleMax,
                poidsMin: poidsMin,
                poidsMax: poidsMax,
                ageMin: ageMin,
                ageMax: ageMax,
                listeCouleurYeuxAutorisees: listeCouleurYeuxAutorisees,
                listeCouleurCheveuxAutorisees: listeCouleurCheveuxAutorisees
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    afficherDetailRace(data);
                }
            }
        });
    }
}

function showlisteBonusRace() {
    var id = jQuery('#idRace').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteBonus";
    } else {
        url = "administration/showlisteBonus";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: id,
            type: 'race'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteBonusRace').hide();
            jQuery('#hidelisteBonusRace').show();
            initBoutonBonus();
        }
    });
}

function hidelisteBonusRace() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteBonusRace').show();
    jQuery('#hidelisteBonusRace').hide();
}

//Religion
function afficherDetailReligion(idReligion) {
    if (window.location.href.includes('/administration/')) {
        url = "detailReligion";
    } else {
        url = "administration/detailReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            initBoutonReferentiels();
        }
    });
}

function accesCreationReligion() {
    if (window.location.href.includes('/administration/')) {
        url = "detailReligion";
    } else {
        url = "administration/detailReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            idReligion: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherDetailModificationReligion() {
    var idReligion = jQuery('#idReligion').val();
    if (window.location.href.includes('/administration/')) {
        url = "detailReligion";
    } else {
        url = "administration/detailReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function retourListeReligion() {
    if (window.location.href.includes('/administration/')) {
        url = "detailReligion";
    } else {
        url = "administration/detailReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            idReligion: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonRefRoyaume').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').addClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefVille').removeClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function changerImageReligion() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageReligion").attr("src", src);
}

function boxModifierReligion() {
    ouvreMsgBox("Modifier les informations sur la religion impactera l'ensemble du personnage et des joueurs. Voulez-vous continuer ?", "question", "ouinon", modifierReligion, "");
}

function modifierReligion() {
    var idReligion = jQuery('#idReligion').val();
    var nom = jQuery('#titreReligionEdition').val();
    var description = jQuery('#descriptionReligionEdition').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionReligionEdition').is(':checked');
    var idNatureMagie = jQuery('#selectNatureMagieReligion').val();


    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreReligionEdition').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionReligionEdition').addClass("erreur");
    }
    if (idNatureMagie == "0") {
        erreur += "Il faut sélectionner un type de magie.\n";
        jQuery('#selectNatureMagieReligion').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierReligion";
        } else {
            url = "administration/modifierReligion";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idReligion: idReligion,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                idNatureMagie: idNatureMagie
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                }
            }
        });
    }
}

function ajouterDiviniteReligion() {
    var idDieu = jQuery('#selectDivinite').val();
    var idReligion = jQuery('#idReligion').val();
    if (idDieu == 0) {
        ouvreMsgBox('Vous devez sélectionner une divinité.', 'info');
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "ajouterDiviniteReligion";
        } else {
            url = "administration/ajouterDiviniteReligion";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idDieu: idDieu,
                idReligion: idReligion
            },
            success: function (data) {
                jQuery('#divDivinitesPresentes').html(data);
                rechargerSelectDiviniteReligion();
            }
        });
    }
}

function rechargerSelectDiviniteReligion() {
    var idReligion = jQuery('#idReligion').val();
    if (window.location.href.includes('/administration/')) {
        url = "rechargerSelectDiviniteReligion";
    } else {
        url = "administration/rechargerSelectDiviniteReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#diviniteDisponibleReligionEdition').html(data);
            initBoutonReferentiels();
        }
    });
}

function supprimerDiviniteReligion(idDieu) {
    var idReligion = jQuery('#idReligion').val();
    if (window.location.href.includes('/administration/')) {
        url = "supprimerDiviniteReligion";
    } else {
        url = "administration/supprimerDiviniteReligion";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idDieu: idDieu,
            idReligion: idReligion
        },
        success: function (data) {
            jQuery('#divDivinitesPresentes').html(data);
            rechargerSelectDiviniteReligion();
        }
    });
}

function chargerNewImageReligion() {
    var id = jQuery('#idReligion').val();
    var type = "Religion";
    var urlFile = jQuery('#newImageReligion').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "uploadImageUrl";
        } else {
            url = "administration/uploadImageUrl";
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

function creerReligion() {
    var nom = jQuery('#titreReligionCreation').val();
    var description = jQuery('#descriptionReligionCreation').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionReligionCreation').is(':checked');
    var idNatureMagie = jQuery('#selectNatureMagieReligion').val();

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRaceCreation').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRaceCreation').addClass("erreur");
    }
    if (idNatureMagie == "0") {
        erreur += "Il faut sélectionner un type de magie.\n";
        jQuery('#selectNatureMagieReligion').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "creerReligion";
        } else {
            url = "administration/creerReligion";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                idNatureMagie: idNatureMagie
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    afficherDetailReligion(data);
                }
            }
        });
    }
}

function showlisteBonusReligion() {
    var id = jQuery('#idReligion').val();
    if (window.location.href.includes('/administration/')) {
        url = "showlisteBonus";
    } else {
        url = "administration/showlisteBonus";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            id: id,
            type: 'religion'
        },
        success: function (data) {
            jQuery('#informationTechnique').html(data);
            jQuery('#informationTechnique').show();
            jQuery('#showlisteBonusReligion').hide();
            jQuery('#hidelisteBonusReligion').show();
            initBoutonBonus();
        }
    });
}

function hidelisteBonusReligion() {
    jQuery('#informationTechnique').html("");
    jQuery('#informationTechnique').hide();
    jQuery('#showlisteBonusReligion').show();
    jQuery('#hidelisteBonusReligion').hide();
}

//Dieu
function afficherDetailDieu(idDieu) {
    if (window.location.href.includes('/administration/')) {
        url = "detailDieu";
    } else {
        url = "administration/detailDieu";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "consultation",
            idDieu: idDieu
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').show();
            jQuery('#divDetailConsultationReferentiel').html(data);
            jQuery('#boutonRefRoyaume').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').addClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function accesCreationDieu() {
    if (window.location.href.includes('/administration/')) {
        url = "detailDieu";
    } else {
        url = "administration/detailDieu";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "creation",
            idDieu: null
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function afficherDetailModificationDieu() {
    var idDieu = jQuery('#idDieu').val();
    if (window.location.href.includes('/administration/')) {
        url = "detailDieu";
    } else {
        url = "administration/detailDieu";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "modification",
            idDieu: idDieu
        },
        success: function (data) {
            jQuery('#divListeResume').hide();
            jQuery('#divListeResume').html("");
            jQuery('#divDetailEditionReferentiel').show();
            jQuery('#divDetailEditionReferentiel').html(data);
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            initBoutonReferentiels();
        }
    });
}

function retourListeDieu() {
    if (window.location.href.includes('/administration/')) {
        url = "detailDieu";
    } else {
        url = "administration/detailDieu";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            mode: "liste",
            idDieu: null
        },
        success: function (data) {
            jQuery('#divListeResume').show();
            jQuery('#divListeResume').html(data);
            jQuery('#divDetailEditionReferentiel').hide();
            jQuery('#divDetailEditionReferentiel').html("");
            jQuery('#divDetailConsultationReferentiel').hide();
            jQuery('#divDetailConsultationReferentiel').html("");
            jQuery('#boutonRefRoyaume').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefRace').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefReligion').removeClass("boutonReferentielSelect");
            jQuery('#boutonRefDivinite').addClass("boutonReferentielSelect");
            jQuery('#boutonRefVille').removeClass("boutonReferentielSelect");
            initBoutonReferentiels();
        }
    });
}

function changerImageDieu() {
    var src = jQuery('#listeImage').val();
    if (src.startsWith('public/img/')) {
        src = "/" + src;
    }
    jQuery("#imageDieu").attr("src", src);
}

function boxModifierDieu() {
    ouvreMsgBox("Modifier les informations sur le dieu impactera l'ensemble du personnage et des joueurs. Voulez-vous continuer ?", "question", "ouinon", modifierDieu, "");
}

function modifierDieu() {
    var idDieu = jQuery('#idDieu').val();
    var nom = jQuery('#titreDieuEdition').val();
    var description = jQuery('#descriptionDieuEdition').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionDieuEdition').is(':checked');
    var idRace = jQuery('#raceDieuEdition').val();
    var couleur = jQuery('#couleurDieuEdition').val();

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreDieuEdition').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionDieuEdition').addClass("erreur");
    }
    if (idRace == "0") {
        erreur += "Il faut sélectionner une race.\n";
        jQuery('#raceDieuEdition').addClass("erreur");
    }
    var couleurSansEspace = couleur.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Il faut renseigner une couleur.\n";
        jQuery('#couleurDieuEdition').addClass("erreur");
    }

    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "modifierDieu";
        } else {
            url = "administration/modifierDieu";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                idDieu: idDieu,
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                idRace: idRace,
                couleur: couleur
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    ouvreMsgBox("Les modifications ont été prises en comptes.", "info");
                }
            }
        });
    }
}

function chargerNewImageDieu() {
    var id = jQuery('#idDieu').val();
    var type = "Dieu";
    var urlFile = jQuery('#newImageDieu').val();

    //Verification de l'url
    var http = urlFile.substring(0, 4);
    var urlFileSansEspace = urlFile.replace(/\s/g, "");

    if (urlFileSansEspace == "") {
        ouvreMsgBox("Il faut renseigner une url.", "erreur");
    } else if (http != "http") {
        ouvreMsgBox("Erreur de format pour l'url");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "uploadImageUrl";
        } else {
            url = "administration/uploadImageUrl";
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

function creerDieu() {
    var nom = jQuery('#titreDieuCreation').val();
    var description = jQuery('#descriptionDieuCreation').val();
    var image = jQuery('#listeImage').val();
    var titreArticle = jQuery('#s').val();
    var isDispoInscription = jQuery('#isDispoInscriptionDieuCreation').is(':checked');
    var idRace = jQuery('#raceDieuCreation').val();
    var couleur = jQuery('#couleurDieuCreation').val();

    //Contrôle des champs
    var erreur = "";
    var nomSansEspace = nom.replace(/\s/g, "");
    if (nomSansEspace == "") {
        erreur += "Il faut renseigner un nom.\n";
        jQuery('#titreRaceCreation').addClass("erreur");
    }
    var descriptionSansEspace = description.replace(/\s/g, "");
    if (descriptionSansEspace == "") {
        erreur += "Une description est obligatoire.\n";
        jQuery('#descriptionRaceCreation').addClass("erreur");
    }
    if (idRace == "0") {
        erreur += "Il faut sélectionner une race.\n";
        jQuery('#raceDieuCreation').addClass("erreur");
    }
    var couleurSansEspace = couleur.replace(/\s/g, "");
    if (couleurSansEspace == "") {
        erreur += "Une couleur est obligatoire.\n";
        jQuery('#couleurDieuCreation').addClass("erreur");
    }


    if (isDispoInscription == true) {
        isDispoInscription = 1;
    } else {
        isDispoInscription = 0;
    }

    if (erreur != "") {
        ouvreMsgBox(erreur, "erreur");
    } else {
        if (window.location.href.includes('/administration/')) {
            url = "creerDieu";
        } else {
            url = "administration/creerDieu";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                nom: nom,
                description: description,
                image: image,
                titreArticle: titreArticle,
                isDispoInscription: isDispoInscription,
                idRace: idRace,
                couleur: couleur
            },
            success: function (data) {
                if (data == "errorNom") {
                    ouvreMsgBox("Ce nom a déjà été choisi. Veuillez en donner un autre.", "error");
                } else {
                    afficherDetailDieu(data);
                }
            }
        });
    }
}

function chargerCouleurFormDieu() {
    if (jQuery('#couleurDieuEdition').length) {
        var couleur = jQuery('#couleurDieuEdition').val();
    } else if (jQuery('#couleurDieuCreation').length) {
        var couleur = jQuery('#couleurDieuCreation').val();
    }
    jQuery('#carreCouleurDieu').css('background-color', couleur);
}

//########## Profil de Test ############//
function initBoutonProfilTest() {
    jQuery('#buttonDecoProfilTest').click(function () {
        deconnectProfilTest();
    });
    jQuery('#pt_connexion').click(function () {
        connectProfilTest();
    });
}

function connectProfilTest() {
    var idDroit = jQuery('#profilTestProfil').val();
    var idRoyaume = jQuery('#profilTestRoyaume').val();
    var idRace = jQuery('#profilTestRace').val();
    var idReligion = jQuery('#profilTestReligion').val();

    if (window.location.href.includes('/administration/')) {
        url = "connectProfilTest";
    } else {
        url = "administration/connectProfilTest";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            idDroit: idDroit,
            idRoyaume: idRoyaume,
            idRace: idRace,
            idReligion: idReligion
        },
        success: function (data) {
            window.location.href = data;
        }
    });
}

//########## Gestion des images ##########//
function initBoutonImage() {
    jQuery('#modifierRepertoireImage').click(function () {
        modifierRepertoireImage();
    });
    jQuery('#ajouterRepertoireImage').click(function () {
        ajouterRepertoireImage();
    });
    jQuery('#supprimerRepertoireImage').click(function () {
        boxSupprimerRepertoireImage();
    });
}

function chargerRepertoire(repertoire) {
    if (window.location.href.includes('/administration/')) {
        url = "chargerRepertoire";
    } else {
        url = "administration/chargerRepertoire";
    }
    if (repertoire != null && repertoire != "") {
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                repertoire: repertoire
            },
            success: function (data) {
                jQuery('#listeImages').html(data);
                jQuery('#repertoireEnCours').val(repertoire);
                jQuery('#oldRep').val(repertoire);
                chargerImage(null);
            }
        });
    } else if (repertoire == null) {
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                repertoire: null
            },
            success: function (data) {
                jQuery('#listeImages').html(data);
                jQuery('#repertoireEnCours').val("");
                jQuery('#oldRep').val("");
                chargerImage(null);
            }
        });
    }
}

function deplier(repertoire) {
    var listeNode = document.getElementsByName(repertoire);
    for (var i = 0; i < listeNode.length; i++) {
        var element = listeNode[i];
        element.style.display = "block";
    }
    document.getElementById(repertoire).onclick = function () {
        plier(repertoire);
    };
    document.getElementById(repertoire).src = "/public/img/site/utils/folder_opened.gif";

}

function plier(repertoire) {
    if (repertoire != "") {
        var listeNode = document.getElementsByName(repertoire);
        for (var i = 0; i < listeNode.length; i++) {
            var element = listeNode[i];
            element.style.display = "none";
            var image = element.getElementsByTagName('img');
            plier(image[0].id);
        }
        document.getElementById(repertoire).onclick = function () {
            deplier(repertoire);
        };
        document.getElementById(repertoire).src = "/public/img/site/utils/folder.png";
    }
}

function modifierRepertoireImage() {
    var newRep = jQuery('#repertoireEnCours').val();
    var oldRep = jQuery('#oldRep').val();
    if (newRep != "" && newRep.charAt(0) == "/" && newRep != oldRep && oldRep != "") {
        if (window.location.href.includes('/administration/')) {
            url = "modifierRepertoire";
        } else {
            url = "administration/modifierRepertoire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                oldRepertoire: oldRep,
                newRep: newRep
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else {
                    jQuery('#tree').html(data);
                    ouvreMsgBox("La modification a été prise en compte.", "info");
                    chargerRepertoire(null);
                }
            }
        });
    } else {
        ouvreMsgBox("Le répertoire est vide ou le format est incorrect (il doit commencer par un '/').", "erreur");
    }
}

function ajouterRepertoireImage() {
    var newRep = jQuery('#repertoireEnCours').val();
    if (newRep != "" && newRep.charAt(0) == "/") {
        if (window.location.href.includes('/administration/')) {
            url = "creerRepertoire";
        } else {
            url = "administration/creerRepertoire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                newRep: newRep
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else {
                    jQuery('#tree').html(data);
                    ouvreMsgBox("La modification a été prise en compte.", "info");
                    chargerRepertoire(null);
                }
            }
        });
    } else {
        ouvreMsgBox("Le répertoire est vide ou le format est incorrect (il doit commencer par un '/').", "erreur");
    }
}

function boxSupprimerRepertoireImage() {
    ouvreMsgBox("Confirmez la suppression du répertoire ? Cela supprimera toutes les images du répertoire.", "question", "ouinon", supprimerRepertoire, "");
}

function supprimerRepertoire() {
    var repertoire = jQuery('#repertoireEnCours').val();
    if (repertoire != "" && repertoire.charAt(0) == "/" && repertoire.charAt(1) != ".") {
        if (window.location.href.includes('/administration/')) {
            url = "supprimerRepertoire";
        } else {
            url = "administration/supprimerRepertoire";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                repertoire: repertoire
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else if (data == "errorFils") {
                    ouvreMsgBox("Veuillez supprimer les répertoires dépendant avant le répertoire père.", "erreur");
                } else {
                    jQuery('#tree').html(data);
                    ouvreMsgBox("La suppression a été prise en compte.", "info");
                    chargerRepertoire(null);
                }
            }
        });
    } else {
        ouvreMsgBox("Le répertoire est vide ou le format est incorrect (il doit commencer par un '/').", "erreur");
    }
}

function chargerImage(image) {
    if (window.location.href.includes('/administration/')) {
        url = "chargerImageGestionFichier";
    } else {
        url = "administration/chargerImageGestionFichier";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            image: image
        },
        success: function (data) {
            jQuery('#detailImage').html(data);
            if (image != null) {
                jQuery('#detailImage').show();
            } else {
                jQuery('#detailImage').hide();
            }
        }
    });
}

function modifierImg(image) {
    var newImage = jQuery('#imageEnCours').val();
    var repertoire = jQuery('#repertoireEnCours').val();
    if (newImage != "" && newImage.charAt(0) == "/" && newImage.charAt(1) != ".") {
        if (window.location.href.includes('/administration/')) {
            url = "modifierImageGestionFichier";
        } else {
            url = "administration/modifierImageGestionFichier";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                oldImage: image,
                newImage: newImage
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else {
                    jQuery('#detailImage').html(data);
                    ouvreMsgBox("La modification a été prise en compte.", "info");
                    chargerRepertoire(repertoire);
                }
            }
        });
    }
}

function supprimerImg(image) {
    var repertoire = jQuery('#repertoireEnCours').val();
    if (image != "" && image.charAt(0) == "/" && image.charAt(1) != ".") {
        if (window.location.href.includes('/administration/')) {
            url = "supprimerImageGestionFichier";
        } else {
            url = "administration/supprimerImageGestionFichier";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                image: image
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else if (data == "errorFils") {
                    ouvreMsgBox("Supprimer les répertoires dépendant avant le répertoire père.", "erreur");
                } else {
                    jQuery('#detailImage').html(data);
                    ouvreMsgBox("L'image a été supprimée.", "info");
                    chargerRepertoire(repertoire);
                }
            }
        });
    }
}

function uploadFileGestionImage() {
    var file = jQuery('#selectedfile').val();
    var fileSansEspace = file.replace(/\s/g, "");
    if (file == "Choisissez un fichier" || fileSansEspace == "") {
        ouvreMsgBox('Selectionnez un fichier', "info");
    } else {
        var repertoire = jQuery('#repertoireEnCours').val();
        var file_data = $("#hiddenfile").prop("files")[0];
        var form_data = new FormData();
        form_data.append("file", file_data);
        form_data.append("repertoire", repertoire);

        if (window.location.href.includes('/administration/')) {
            url = "uploadFileGestionImage";
        } else {
            url = "administration/uploadFileGestionImage";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            processData: false,
            contentType: false,
            data: form_data,
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox('Le fichier a été correctement ajouté.', "info");
                } else {
                    jQuery('#listeImages').html(data);
                    jQuery('#repertoireEnCours').val(repertoire);
                    jQuery('#oldRep').val(repertoire);
                    chargerImage(null);
                    ouvreMsgBox('Le fichier a été correctement mis ajouté.', "info");
                }
            }
        });
    }
}

//########## Gestion des gifs ############//
function chargerRepertoireGif(repertoire) {
    if (window.location.href.includes('/administration/')) {
        url = "chargerRepertoireGif";
    } else {
        url = "administration/chargerRepertoireGif";
    }
    if (repertoire != null && repertoire != "") {
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                repertoire: repertoire
            },
            success: function (data) {
                jQuery('#listeGifs').html(data);
                jQuery('#repertoireEnCours').val(repertoire);
                chargerGif(null);
            }
        });
    } else if (repertoire == null) {
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                repertoire: null
            },
            success: function (data) {
                jQuery('#listeGifs').html(data);
                jQuery('#repertoireEnCours').val("");
                chargerGif(null);
            }
        });
    }
}

function chargerGif(gif) {
    if (window.location.href.includes('/administration/')) {
        url = "chargerGifGestionFichier";
    } else {
        url = "administration/chargerGifGestionFichier";
    }
    jQuery.ajax({
        type: "POST",
        url: url,
        data: {
            gif: gif
        },
        success: function (data) {
            jQuery('#detailGif').html(data);
            if (gif != null) {
                jQuery('#detailGif').show();
            } else {
                jQuery('#detailGif').hide();
            }
        }
    });
}

function modifierGif(gif) {
    var newGif = jQuery('#gifEnCours').val();
    var repertoire = jQuery('#repertoireEnCours').val();
    if (newGif != "" && newGif.charAt(0) == "/" && newGif.charAt(1) != ".") {
        if (window.location.href.includes('/administration/')) {
            url = "modifierGifGestionFichier";
        } else {
            url = "administration/modifierGifGestionFichier";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                oldGif: gif,
                newGif: newGif
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else {
                    jQuery('#detailGif').html(data);
                    ouvreMsgBox("La modification a été prise en compte.", "info");
                    chargerRepertoireGif(repertoire);
                }
            }
        });
    }
}

function deleteGif(gif) {
    var repertoire = jQuery('#repertoireEnCours').val();
    if (gif != "" && gif.charAt(0) == "/" && gif.charAt(1) != ".") {
        if (window.location.href.includes('/administration/')) {
            url = "supprimerGifGestionFichier";
        } else {
            url = "administration/supprimerGifGestionFichier";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                gif: gif
            },
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox("Une erreur est survenue au cours du traitement.", "erreur");
                } else {
                    jQuery('#detailGif').html(data);
                    ouvreMsgBox("Le gif a été supprimé.", "info");
                    chargerRepertoireGif(repertoire);
                }
            }
        });
    }
}

function uploadFileGestionGif() {
    var file = jQuery('#selectedfile').val();
    var fileSansEspace = file.replace(/\s/g, "");
    if (file == "Choisissez un fichier" || fileSansEspace == "") {
        ouvreMsgBox('Selectionnez un fichier', "info");
    } else {
        var repertoire = jQuery('#repertoireEnCours').val();
        var file_data = $("#hiddenfile").prop("files")[0];
        var form_data = new FormData();
        form_data.append("file", file_data);
        form_data.append("repertoire", repertoire);

        if (window.location.href.includes('/administration/')) {
            url = "uploadFileGestionGif";
        } else {
            url = "administration/uploadFileGestionGif";
        }
        jQuery.ajax({
            type: "POST",
            url: url,
            processData: false,
            contentType: false,
            data: form_data,
            success: function (data) {
                if (data == "error") {
                    ouvreMsgBox('Le gif a été correctement ajouté.', "info");
                } else {
                    jQuery('#listeGifs').html(data);
                    jQuery('#repertoireEnCours').val(repertoire);
                    chargerGif(null);
                    ouvreMsgBox('Le fichier a été correctement mis ajouté.', "info");
                }
            }
        });
    }
}