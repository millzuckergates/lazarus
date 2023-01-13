<?php

use Phalcon\Mvc\View;
use Phalcon\Http\Response;

class AdministrationController extends \Phalcon\Mvc\Controller {

    public function indexAction() {
        //Calcul de la liste des articles de l'index
        $auth = $this->session->get("auth");

        //Construction de la liste des onglets et placement en session
        $this->session->set("listeOngletAdministration", $this->buildListeOnglet($auth));
        $this->view->auth = $auth;
        $this->pageview();
    }

    /**
     * Permet d'afficher l'onglet choisi
     */
    public function afficherAdministrationAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $conteneur = $this->request->get("conteneur");

                if ($conteneur == "droit") {
                    $this->view->pick("administration/droits");
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                } else {
                    if ($conteneur == "logs") {
                        if ($this->initGestionLog($auth)) {
                            $this->view->pick("administration/logs");
                            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                        } else {
                            return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                        }
                    } else {
                        if ($conteneur == "referentiels") {
                            if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_CONSULTATION, $auth['autorisations'])
                              || Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_GERER, $auth['autorisations'])) {
                                $this->view->pick("administration/referentiels");
                                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                            } else {
                                return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                            }
                        } else {
                            if ($conteneur == "statistiques") {
                                if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_STATISTIQUES, $auth['autorisations'])) {
                                    $this->view->pick("administration/statistiques");
                                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                                } else {
                                    return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                                }
                            } else {
                                if ($conteneur == "test") {
                                    if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_PROFIL_TEST, $auth['autorisations'])) {
                                        if (!isset($auth['profilTest'])) {
                                            $profilTest = null;
                                        } else {
                                            $profilTest = $auth['profilTest'];
                                        }
                                        $this->view->profilTest = $profilTest;
                                        $this->view->pick("administration/profilTest");
                                        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                                    } else {
                                        return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                                    }
                                } else {
                                    if ($conteneur == "image") {
                                        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_GESTION_IMAGE, $auth['autorisations'])) {
                                            $this->view->pick("administration/gestionImages");
                                            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                                        } else {
                                            return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                                        }
                                    } else {
                                        if ($conteneur == "gif") {
                                            if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_GESTION_GIF, $auth['autorisations'])) {
                                                $this->view->pick("administration/gestionGifs");
                                                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                                            } else {
                                                return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->view->auth = $auth;
                $this->pageview();
            }
        }
    }

    /**
     * Permet d'enregistrer une image sur le référentiel
     * à partir de son url
     * @return string
     */
    public function uploadImageUrlAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $url = $this->request->get('urlFile');
                $id = $this->request->get('id');

                $name = basename($url);
                list($txt, $ext) = explode(".", $name);
                //check if the files are only image / document
                if ($ext != "jpg" && $ext != "png" && $ext != "gif") {
                    return "errorType";
                } else {
                    if ($type == "Royaume") {
                        $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/royaumes/' . $name;
                        $resultUpload = copy($url, $destination);
                        if ($id != null && $id != false) {
                            $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                            $retour = $royaume->genererListeEtendardsRoyaume();
                        } else {
                            $retour = Royaumes::genererListeEtendardsRoyaumeVide($this->getDI()->get('config')->application->imgDir);
                        }
                    } else {
                        if ($type == "Race") {
                            $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/races/' . $name;
                            $resultUpload = copy($url, $destination);
                            if ($id != null && $id != false) {
                                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                $retour = $race->genererListeImagesRace();
                            } else {
                                $retour = Races::genererListeImagesRaceVide($this->getDI()->get('config')->application->imgDir);
                            }
                        } else {
                            if ($type == "Religion") {
                                $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/religions/' . $name;
                                $resultUpload = copy($url, $destination);
                                if ($id != null && $id != false) {
                                    $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                    $retour = $religion->genererListeImagesReligion();
                                } else {
                                    $retour = Religions::genererListeImagesReligionVide($this->getDI()->get('config')->application->imgDir);
                                }
                            } else {
                                if ($type == "Dieu") {
                                    $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/divinites/' . $name;
                                    $resultUpload = copy($url, $destination);
                                    if ($id != null && $id != false) {
                                        $dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                        $retour = $dieu->genererListeImagesDieu();
                                    } else {
                                        $retour = Dieux::genererListeImagesDieuVide($this->getDI()->get('config')->application->imgDir);
                                    }
                                } else {
                                    if ($type == "Ville") {
                                        $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/villes/' . $name;
                                        $resultUpload = copy($url, $destination);
                                        if ($id != null && $id != false) {
                                            $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                            $retour = $ville->genererListeImages();
                                        } else {
                                            $retour = Villes::genererListeImageVide($this->getDI()->get('config')->application->imgDir);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!$resultUpload) {
                        return "errorUpload";
                    } else {
                        return $retour;
                    }
                }
            }
        }
    }

    //############ Gestion des Droits ############//

    /**
     * Permet de charger la liste des autorisations
     * @return string
     */
    public function chargeListeAutorisationsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idDroit = $this->request->get("idProfil");
                $this->view->disable();
                return Droits::genererListeAutorisations($auth, $idDroit);
            }
        }
    }

    /**
     * Ajoute un droit
     * @return string
     */
    public function ajouterDroitAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $libelle = $this->request->get("libelle");
                $this->view->disable();

                //On vérifie que le libellé n'est pas déjà utilisé
                $verif = Droits::findFirst(['libelle = :libelle:', 'bind' => ['libelle' => $libelle]]);
                if (!$verif) {
                    $droit = Droits::creer($libelle);
                    //Logs de l'action
                    $logAdmin = new LogsADMIN();
                    $logAdmin->action = "Ajout du nouveau droit : " . $droit->libelle;
                    $logAdmin->idPersonnage = $auth['perso']->id;
                    $logAdmin->dateLog = time();
                    $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                    $logAdmin->save();
                    $retour = Droits::genererListeProfils($auth);
                    $retour .= "@" . $droit->id;
                    return $retour;
                } else {
                    return "errorExistant";
                }
            }
        }
    }

    /**
     * Supprime un droit
     */
    public function supprimerDroitAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idDroit = $this->request->get('idProfil');

                //Suppression des associations
                $condition = "DELETE FROM AssocPersonnageDroit WHERE idDroit =" . $idDroit;
                $query = $this->modelsManager->createQuery($condition);
                $articles = $query->execute();

                $droit = Droits::findFirst(['id = :id:', 'bind' => ['id' => $idDroit]]);

                //Logs de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Supression du droit : " . $droit->libelle;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                $logAdmin->save();

                //Suppression du Droit
                $droit->delete();

                //Redirection
                $this->view->pick("administration/droits");
                $this->view->auth = $auth;
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                $this->response->send();
            }
        }
    }

    /**
     * Supprime un droit
     */
    public function modifierDroitAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get("auth");
                $idDroit = $this->request->get('idProfil');

                //Suppression des associations
                $condition = "DELETE FROM AssocDroitAutorisation WHERE idDroit =" . $idDroit;
                $query = $this->modelsManager->createQuery($condition);
                $articles = $query->execute();

                $listeIdAutorisations = explode(",", $this->request->get('idAutorisations'));
                //On ajoute les nouvelles
                if (!empty($listeIdAutorisations)) {
                    for ($i = 0; $i < count($listeIdAutorisations); $i++) {
                        if ($listeIdAutorisations[$i] != "") {
                            $assoc = new AssocDroitAutorisation();
                            $assoc->idAutorisation = $listeIdAutorisations[$i];
                            $assoc->idDroit = $idDroit;
                            $assoc->save();
                        }
                    }
                }
                //On recharge le droit
                $droit = Droits::findFirst(['id = :id:', 'bind' => ['id' => $idDroit]]);
                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Modification du droit : " . $droit->libelle;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                $logAdmin->save();

                //Redirection
                $retour = Droits::genererListeProfils($auth);
                $retour .= "@" . $droit->id;
                return $retour;
            }
        }
    }

    //############# Gestion des Logs ############/

    /**
     * Permet de switcher entre les logs
     */
    public function chargerLogAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $administration = $this->session->get('administration');
                $administration['page'] = 1;
                $nomAuteur = $this->request->get('nomAuteur');
                $nomCible = $this->request->get('nomCible');
                $typRecherche = $this->request->get('typeRecherche');
                $type = $this->request->get('type');
                if ($type == "MJ") {
                    $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                    if ($auteur) {
                        $idAuteur = $auteur->id;
                    } else {
                        $idAuteur = null;
                    }
                    $cible = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomCible]]);
                    if ($cible) {
                        $idCible = $cible->id;
                    } else {
                        $idCible = null;
                    }
                    $this->view->listeAccesLogs = $this->boutonLogs($type, $auth);
                    $this->view->logSelect = "MJ";
                    $this->view->listeLogs = LogsMJ::getListeLogs($administration, $typRecherche, $idAuteur, $idCible, 1);
                    $total = LogsMJ::countListLog($idAuteur, $idCible, $typRecherche);
                    $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], 1);
                    $this->view->nbEnregistrementParPage = $administration['nbEnregistrementParPage'];
                    $this->view->administration = $this->session->get('administration');
                    $this->view->pick("administration/logs");
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                } else {
                    if ($type == "ADMIN") {
                        $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                        if ($auteur) {
                            $idAuteur = $auteur->id;
                        } else {
                            $idAuteur = null;
                        }
                        $cible = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomCible]]);
                        if ($cible) {
                            $idCible = $cible->id;
                        } else {
                            $idCible = null;
                        }
                        $this->view->listeAccesLogs = $this->boutonLogs($type, $auth);
                        $this->view->logSelect = "ADMIN";
                        $this->view->listeLogs = LogsADMIN::getListeLogs($administration, $typRecherche, $idAuteur, $idCible, 1);
                        $total = LogsADMIN::countListLog($idAuteur, $idCible, $typRecherche);
                        $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], 1);
                        $this->view->nbEnregistrementParPage = $administration['nbEnregistrementParPage'];
                        $this->view->administration = $this->session->get('administration');
                        $this->view->pick("administration/logs");
                        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    } else {
                        if ($type == "DEV") {
                            $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                            if ($auteur) {
                                $idAuteur = $auteur->id;
                            } else {
                                $idAuteur = null;
                            }
                            $this->view->listeAccesLogs = $this->boutonLogs($type, $auth);
                            $this->view->logSelect = "DEV";
                            $this->view->listeLogs = LogsDEV::getListeLogs($administration, $typRecherche, $idAuteur, 1);
                            $total = LogsDEV::countListLog($idAuteur, $typRecherche);
                            $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], 1);
                            $this->view->nbEnregistrementParPage = $administration['nbEnregistrementParPage'];
                            $this->view->administration = $this->session->get('administration');
                            $this->view->pick("administration/logs");
                            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                        } else {
                            if ($type == "Archive") {
                                $this->view->listeAccesLogs = $this->boutonLogs($type, $auth);
                                $this->view->logSelect = "Archive";
                                $this->view->listFiles = array();
                                $this->view->init = true;
                                $this->view->pick("administration/logs");
                                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                            }
                        }
                    }
                }

                $this->session->administration = $administration;
                $this->view->auth = $auth;
                $this->pageview();
            }
        }
    }

    /**
     * Méthode permettant de filtrer les logs
     * @return unknown
     */
    public function filtreLogAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $administration = $this->session->get('administration');
                $nomAuteur = $this->request->get('nomAuteur');
                $nomCible = $this->request->get('nomCible');
                $typRecherche = $this->request->get('typeRecherche');
                $type = $this->request->get('logSelect');
                if ($type == "MJ") {
                    $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                    if ($auteur) {
                        $idAuteur = $auteur->id;
                    } else {
                        $idAuteur = null;
                    }
                    $cible = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomCible]]);
                    if ($cible) {
                        $idCible = $cible->id;
                    } else {
                        $idCible = null;
                    }
                    $this->view->listeLogs = LogsMJ::getListeLogs($administration, $typRecherche, $idAuteur, $idCible, $administration['page']);
                    $total = LogsMJ::countListLog($idAuteur, $idCible, $typRecherche);
                    $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], $administration['page']);
                } else {
                    if ($type == "ADMIN") {
                        $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                        if ($auteur) {
                            $idAuteur = $auteur->id;
                        } else {
                            $idAuteur = null;
                        }
                        $cible = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomCible]]);
                        if ($cible) {
                            $idCible = $cible->id;
                        } else {
                            $idCible = null;
                        }
                        $this->view->listeLogs = LogsADMIN::getListeLogs($administration, $typRecherche, $idAuteur, $idCible, $administration['page']);
                        $total = LogsADMIN::countListLog($idAuteur, $idCible, $typRecherche);
                        $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], $administration['page']);
                    } else {
                        if ($type == "DEV") {
                            $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ['nom' => $nomAuteur]]);
                            if ($auteur) {
                                $idAuteur = $auteur->id;
                            } else {
                                $idAuteur = null;
                            }
                            $this->view->listeLogs = LogsDEV::getListeLogs($administration, $typRecherche, $idAuteur, $administration['page']);
                            $total = LogsDEV::countListLog($idAuteur, $typRecherche);
                            $this->view->detailPage = $this->genererDetailPage($total, $administration['nbEnregistrementParPage'], $administration['page']);
                        }
                    }
                }

                //Réinitialisation s'il n'y a pas de résultats
                if ($total == 0) {
                    $administration = $this->session->administration;
                    $administration['page'] = 1;
                    $this->session->administration = $administration;
                }

                $retour = $this->view->partial('administration/log/resultat' . $type);
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de changer le nombre d'élément par page
     */
    public function changeNbElementParPageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $administration = $this->session->administration;
                $administration['nbEnregistrementParPage'] = $this->request->get('nbElementParPage');
                $this->session->administration = $administration;
                $this->view->disable();
            }
        }
    }

    /**
     * Change la page consultée
     */
    public function changerPageLogsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $administration = $this->session->administration;
                $administration['page'] = $this->request->get('page');
                $this->session->administration = $administration;
                $this->view->disable();
            }
        }
    }

    /**
     * Change le tri par rapport à la date
     */
    public function trieDateLogsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $administration = $this->session->administration;
                if ($administration['triDate'] == "croissant") {
                    $administration['triDate'] = "decroissant";
                } else {
                    $administration['triDate'] = "croissant";
                }
                $this->session->administration = $administration;
                $this->view->disable();
            }
        }
    }

    /**
     * Permet d'effectuer l'export des logs sous forme de fichier csv
     * @return string
     */
    public function exporterLogsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $connection = $this->getDi()->get("db");
                $typeLog = $this->request->get('type');
                $dateStart = $date = mktime(0, 0, 0, date('m'), date('d') - 30, date('y'));

                if ($typeLog == "MJ") {
                    $listeLogs = LogsMJ::find(['dateLog < :dateStart:', 'bind' => ['dateStart' => $dateStart], 'order' => "dateLog ASC"]);
                    if (count($listeLogs) > 1) {
                        $nomFichier = $this->generateLogFileName($typeLog, $listeLogs, $dateStart);
                        $contenu = LogsMJ::genererContenuExport($listeLogs, $dateStart);
                        $retour = Files::createFileLog($typeLog, $contenu, $nomFichier, $this->getDi()->get('config')->application->logsDir);
                        if ($retour == "sucess") {
                            //Suppression des logs
                            $connection->execute("DELETE FROM logsmj WHERE dateLog < " . $dateStart);
                            //Log de l'action
                            $logAdmin = new LogsADMIN();
                            $logAdmin->action = "Logs MJ enregistrés dans le fichier : " . $nomFichier;
                            $logAdmin->idPersonnage = $auth['perso']->id;
                            $logAdmin->dateLog = time();
                            $logAdmin->typeLog = LogsADMIN::TYPE_ARCHIVAGE_LOGS;
                            $logAdmin->save();
                        }
                    } else {
                        $retour = "empty";
                    }
                } else {
                    if ($typeLog == "ADMIN") {
                        $listeLogs = LogsADMIN::find(['dateLog < :dateStart:', 'bind' => ['dateStart' => $dateStart], 'order' => "dateLog ASC"]);
                        if (count($listeLogs) > 1) {
                            $nomFichier = $this->generateLogFileName($typeLog, $listeLogs, $dateStart);
                            $contenu = LogsADMIN::genererContenuExport($listeLogs, $dateStart);
                            $retour = Files::createFileLog($typeLog, $contenu, $nomFichier, $this->getDi()->get('config')->application->logsDir);
                            if ($retour == "sucess") {
                                //Suppression des logs
                                $connection->execute("DELETE FROM logsadmin WHERE dateLog < " . $dateStart);
                                //Log de l'action
                                $logAdmin = new LogsADMIN();
                                $logAdmin->action = "Logs ADMIN enregistrés dans le fichier : " . $nomFichier;
                                $logAdmin->idPersonnage = $auth['perso']->id;
                                $logAdmin->dateLog = time();
                                $logAdmin->typeLog = LogsADMIN::TYPE_ARCHIVAGE_LOGS;
                                $logAdmin->save();
                            }
                        } else {
                            $retour = "empty";
                        }
                    } else {
                        if ($typeLog == "DEV") {
                            $listeLogs = LogsDEV::find(['dateLog < :dateStart:', 'bind' => ['dateStart' => $dateStart], 'order' => "dateLog ASC"]);
                            if (count($listeLogs) > 1) {
                                $nomFichier = $this->generateLogFileName($typeLog, $listeLogs, $dateStart);
                                $contenu = LogsDEV::genererContenuExport($listeLogs, $dateStart);
                                $retour = Files::createFileLog($typeLog, $contenu, $nomFichier, $this->getDi()->get('config')->application->logsDir);
                                if ($retour == "sucess") {
                                    //Suppression des logs
                                    $connection->execute("DELETE FROM logsdev WHERE dateLog < " . $dateStart);
                                    //Log de l'action
                                    $logAdmin = new LogsADMIN();
                                    $logAdmin->action = "Logs DEV enregistrés dans le fichier : " . $nomFichier;
                                    $logAdmin->idPersonnage = $auth['perso']->id;
                                    $logAdmin->dateLog = time();
                                    $logAdmin->typeLog = LogsADMIN::TYPE_ARCHIVAGE_LOGS;
                                    $logAdmin->save();
                                }
                            } else {
                                $retour = "empty";
                            }
                        }
                    }
                }
                return $retour;
            }
        }
    }

    /**
     * Permet d'afficher les différents types de fichiers de logs
     * @return unknown
     */
    public function chargerTypeArchiveAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $typeLog = $this->request->get('type');
                if ($typeLog == "MJ") {
                    $repo = $this->getDi()->get('config')->application->logsDir . "mj/";
                    $this->view->listFiles = Files::getFiles($repo);
                    $this->view->init = false;
                } else {
                    if ($typeLog == "ADMIN") {
                        $repo = $this->getDi()->get('config')->application->logsDir . "admin/";
                        $this->view->listFiles = Files::getFiles($repo);
                        $this->view->init = false;
                    } else {
                        if ($typeLog == "DEV") {
                            $repo = $this->getDi()->get('config')->application->logsDir . "dev/";
                            $this->view->listFiles = Files::getFiles($repo);
                            $this->view->init = false;
                        } else {
                            $repo = "";
                            $this->view->listFiles = array();
                            $this->view->init = true;
                        }
                    }
                }

                $this->view->repertoire = $repo;
                $retour = $this->view->partial('administration/log/resultatArchive');
                $this->view->disable();
                $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
                return $retour;
            }
        }
    }

    /**
     * Retourne l'url de téléchargement
     * @return string
     */
    public function downloadFilesAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                return $this->getDi()->get('config')->application->baseUri . "/utils/downloadFile?file=" . $this->request->get('fichier');
            }
        }
    }

    /**
     * Initialise la gestion des logs
     * @param unknown $auth
     * @return boolean
     */
    private function initGestionLog($auth) {
        //controle des autorisations de la page
        if (!Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_CONSULTATION_ARCHIVE_LOG, $auth['autorisations'])
          && !Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_MJ_CONSULTATION, $auth['autorisations'])
          && !Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_ADMIN_CONSULTATION, $auth['autorisations'])
          && !Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_DEV_CONSULTATION, $auth['autorisations'])) {
            return false;
        }

        //Gestion de la session
        if ($this->session->get('administration') != null) {
            if (isset($this->session->get('administration')['nbEnregistrementParPage'])) {
                $nbEnregistrementParPage = $this->session->get('administration')['nbEnregistrementParPage'];
            } else {
                $this->session->set("administration", ['nbEnregistrementParPage' => 25, 'triDate' => 'croissant', 'page' => 1]);
                $nbEnregistrementParPage = $this->session->get('administration')['nbEnregistrementParPage'];
            }

        } else {
            $this->session->set("administration", ['nbEnregistrementParPage' => 25, 'triDate' => 'croissant', 'page' => 1]);
            $nbEnregistrementParPage = $this->session->get('administration')['nbEnregistrementParPage'];
        }

        //Construction de la liste des accès aux logs
        $listeLogs = array();
        $elementFirst = true;
        $listeAccesLogs = "<div id='divBoutonAccesLogs'>";
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_MJ_CONSULTATION, $auth['autorisations'])) {
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            if ($elementFirst) {
                $logSelect = "MJ";
                $elementFirst = false;
                $class = 'boutonAccesLogSelect';
                $listeLogs = LogsMJ::getListeLogs($this->session->get('administration'), null, null, null, 1);
                $total = LogsMJ::countListLog(null, null, null);
            } else {
                $class = 'boutonAccesLog';
            }
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("MJ", 'id' => 'boutonLogMJ', 'class' => $class, 'title' => "Permet d'afficher les logs MJ."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_ADMIN_CONSULTATION, $auth['autorisations'])) {
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            if ($elementFirst) {
                $logSelect = "ADMIN";
                $elementFirst = false;
                $class = 'boutonAccesLogSelect';
                $listeLogs = LogsADMIN::getListeLogs($this->session->get('administration'), null, null, null, 1);
                $total = LogsADMIN::countListLog(null, null, null);
            } else {
                $class = 'boutonAccesLog';
            }
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("ADMIN", 'id' => 'boutonLogADMIN', 'class' => $class, 'title' => "Permet d'afficher les logs ADMIN."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_DEV_CONSULTATION, $auth['autorisations'])) {
            if ($elementFirst) {
                $logSelect = "DEV";
                $elementFirst = false;
                $class = 'boutonAccesLogSelect';
                $listeLogs = LogsDEV::getListeLogs($this->session->get('administration'), null, null, null, 1);
                $total = LogsDEV::countListLog(null, null, null);
            } else {
                $class = 'boutonAccesLog';
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("DEV", 'id' => 'boutonLogDEV', 'class' => $class, 'title' => "Permet d'afficher les logs DEV."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_CONSULTATION_ARCHIVE_LOG, $auth['autorisations'])) {
            if ($elementFirst) {
                $logSelect = "Archive";
                $elementFirst = false;
                $class = 'boutonAccesLogSelect';
            } else {
                $class = 'boutonAccesLog';
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("Archives", 'id' => 'boutonLogArchive', 'class' => $class, 'title' => "Permet d'afficher les logs archivés."));
            $listeAccesLogs .= "</span>";
        }
        $listeAccesLogs .= "</div>";


        $administration = $this->session->administration;
        $administration['page'] = 1;
        $this->session->administration = $administration;


        $this->view->listeAccesLogs = $listeAccesLogs;
        $this->view->logSelect = $logSelect;
        $this->view->listeLogs = $listeLogs;
        $this->view->detailPage = $this->genererDetailPage($total, $nbEnregistrementParPage, 1);

        $this->view->nbEnregistrementParPage = $nbEnregistrementParPage;
        $administration =
        $this->view->administration = $this->session->get('administration');


        return true;
    }

    /**
     * Générer la liste des pages
     * @param unknown $total
     * @param unknown $nbEnregistrement
     * @param unknown $pageEnCours
     * @return string
     */
    private function genererDetailPage($total, $nbEnregistrement, $pageEnCours) {
        $nbPage = ceil($total / $nbEnregistrement);
        $retour = "";
        for ($i = 1; $i <= $nbPage; $i++) {
            if ($i == $pageEnCours) {
                $retour .= "<span class='pageEnCours'>" . $i . "</span>";
            } else {
                $retour .= '<span onclick="loadLogPage(' . $i . ');" class="lienPage">' . $i . '</span>';
            }
        }
        return $retour;
    }

    /**
     * Permet de construire la liste des boutons
     * @param unknown $type
     * @param unknown $auth
     * @return string
     */
    private function boutonLogs($type, $auth) {
        $listeAccesLogs = "";
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_MJ_CONSULTATION, $auth['autorisations'])) {
            $class = "boutonAccesLog";
            if ($type == "MJ") {
                $class = "boutonAccesLogSelect";
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("MJ", 'id' => 'boutonLogMJ', 'class' => $class, 'title' => "Permet d'afficher les logs MJ."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_ADMIN_CONSULTATION, $auth['autorisations'])) {
            $class = "boutonAccesLog";
            if ($type == "ADMIN") {
                $class = "boutonAccesLogSelect";
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("ADMIN", 'id' => 'boutonLogADMIN', 'class' => $class, 'title' => "Permet d'afficher les logs ADMIN."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_DEV_CONSULTATION, $auth['autorisations'])) {
            $class = "boutonAccesLog";
            if ($type == "DEV") {
                $class = "boutonAccesLogSelect";
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("DEV", 'id' => 'boutonLogDEV', 'class' => $class, 'title' => "Permet d'afficher les logs DEV."));
            $listeAccesLogs .= "</span>";
        }
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_CONSULTATION_ARCHIVE_LOG, $auth['autorisations'])) {
            $class = "boutonAccesLog";
            if ($type == "Archive") {
                $class = "boutonAccesLogSelect";
            }
            $listeAccesLogs .= "<span class='spanBoutonAccesLogs'>";
            $listeAccesLogs .= Phalcon\Tag::SubmitButton(array("Archives", 'id' => 'boutonLogArchive', 'class' => $class, 'title' => "Permet d'afficher les logs archivés."));
            $listeAccesLogs .= "</span>";
        }
        return $listeAccesLogs;
    }

    /**
     * Génère le nom du fichier de log
     * @param unknown $typeLog
     * @param unknown $listeLogs
     * @param unknown $dateStart
     * @return string
     */
    private function generateLogFileName($typeLog, $listeLogs, $dateStart) {
        $fileName = "";
        $lastLog = $listeLogs[count($listeLogs) - 1];
        $dateEnd = date('d-m-Y', $lastLog->dateLog);
        $dateStartFormat = date('d-m-Y', $dateStart);
        if ($typeLog == "MJ") {
            $fileName = "LogsMJ_" . $dateStartFormat . "_" . $dateEnd . ".csv";
        } else {
            if ($typeLog == "ADMIN") {
                $fileName = "LogsADMIN_" . $dateStartFormat . "_" . $dateEnd . ".csv";
            } else {
                if ($typeLog == "DEV") {
                    $fileName = "LogsDEV_" . $dateStartFormat . "_" . $dateEnd . ".csv";
                }
            }
        }
        return $fileName;
    }

    //######## Référentiels #########//
    //Global
    /**
     * Permet d'afficher la liste des bonus
     * @return unknown
     */
    public function showlisteBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');

                if ($type == "royaume") {
                    $this->view->element = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                } else {
                    if ($type == "race") {
                        $this->view->element = Races::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                    } else {
                        if ($type == "religion") {
                            $this->view->element = Religions::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        }
                    }
                }
                $this->view->type = $type;
                $this->view->auth = $auth;
                $retour = $this->view->partial('utils/bonus/panneauBonus');
                $this->view->disable();
                return $retour;
            }
        }
    }

    //Royaumes

    /**
     * Permet d'afficher le détail d'un royaume
     * @return unknown
     */
    public function detailRoyaumeAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('administration/referentiel/creationRoyaume');
                } else {
                    if ($mode == "consultation") {
                        $this->view->royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRoyaume')]]);
                        $retour = $this->view->partial('administration/referentiel/detailRoyaume');
                    } else {
                        if ($mode == "modification") {
                            $this->view->royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRoyaume')]]);
                            $retour = $this->view->partial('administration/referentiel/editionRoyaume');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('administration/referentiel/listeRoyaumes');
                            }
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer un royaume
     * @return string
     */
    public function creerRoyaumeAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Royaumes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titre')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }


                //Création
                $royaume = new Royaumes();
                $royaume->nom = $nom;
                $royaume->idArticle = $idArticle;
                $royaume->description = $this->request->get('description');
                $royaume->etendard = $this->request->get('etendard');
                $royaume->couleur = $this->request->get('couleur');
                $royaume->isDispoInscription = $this->request->get('isDispoInscription');
                $royaume->save();

                //Création des répertoire pour les gifs
                mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/'));
                chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/'), 0777);
                mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/feminin/'));
                chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/feminin/'), 0777);
                mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/masculin/'));
                chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/masculin/'), 0777);
                $races = Races::find();
                foreach ($races as $race) {
                    mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/feminin/' . $race->nom . '/'));
                    chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/feminin/' . $race->nom . '/'), 0777);
                    mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/masculin/' . $race->nom . '/'));
                    chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom . '/masculin/' . $race->nom . '/'), 0777);
                }

                //Log de l'action
                $action = "Création du royaume : " . $royaume->nom;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return $royaume->id;
            }
        }
    }

    /**
     * Permet de modifier un royaume
     * @return string
     */
    public function modifierRoyaumeAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRoyaume')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $updateRepertoire = false;
                if ($royaume->nom != $nom) {
                    $updateRepertoire = true;
                    $verif = Royaumes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titre')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Initialisation de l'action de logs
                $ancienNom = $royaume->nom;
                $action = "Ancien Royaume :" . $royaume->toString();

                //Mise à jour
                $royaume->nom = $nom;
                $royaume->idArticle = $idArticle;
                $royaume->description = $this->request->get('description');
                $royaume->etendard = $this->request->get('etendard');
                $royaume->couleur = $this->request->get('couleur');
                $royaume->isDispoInscription = $this->request->get('isDispoInscription');
                $royaume->save();

                //Update du répertoire pour les gifs
                if ($updateRepertoire) {
                    $oldRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $ancienNom;
                    $newRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $nom;
                    rename(utf8_decode($oldRepertoire), utf8_decode($newRepertoire));
                }

                //Log de l'action
                $action = "Modification du royaume : " . $royaume->nom . " (" . $action . ", Nouveau Royaume : " . $royaume->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return "success";
            }
        }
    }

    /**
     * Permet d'ajouter une religion jouable pour un royaume à l'inscription
     * @return string
     */
    public function ajouterReligionJouableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRoyaume = $this->request->get('idRoyaume');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $assoc = new AssocRoyaumesReligionJouable();
                $assoc->idRoyaume = $idRoyaume;
                $assoc->idReligion = $idReligion;
                $assoc->save();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idRoyaume]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Autorisation de la religion : " . $religion->nom . " pour le royaume " . $royaume->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $royaume->genererReligionsAutorisees("modification");
            }
        }
    }

    /**
     * Méthode pour recharger la selection d'une relgion
     * @return string
     */
    public function rechargerSelectReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRoyaume')]]);

                //Retour du select mis à jour
                return $royaume->genererSelectListeReligionsAutorisees();
            }
        }
    }

    /**
     * Méthode pour ajouter une race jouable à l'inscription
     * pour ce royaume
     * @return string
     */
    public function ajouterRaceJouableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRoyaume = $this->request->get('idRoyaume');
                $idRace = $this->request->get('idRace');

                //On ajoute l'association
                $assoc = new AssocRoyaumesRaceJouable();
                $assoc->idRoyaume = $idRoyaume;
                $assoc->idRace = $idRace;
                $assoc->save();

                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idRace]]);
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idRoyaume]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Autorisation de la race : " . $race->nom . " pour le royaume " . $royaume->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $royaume->genererRacesAutorisees("modification");
            }
        }
    }

    /**
     * Méthode pour recharger la selection de la race
     * @return string
     */
    public function rechargerSelectRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRoyaume')]]);

                //Retour du select mis à jour
                return $royaume->genererSelectListeRacesAutorisees();
            }
        }
    }

    /**
     * Permet de supprimer une religion jouable pour un royaume
     * @return string
     */
    public function supprimerReligionJouableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRoyaume = $this->request->get('idRoyaume');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $assoc = AssocRoyaumesReligionJouable::findFirst(['idReligion = :idReligion: AND idRoyaume = :idRoyaume:', 'bind' => ['idReligion' => $idReligion, 'idRoyaume' => $idRoyaume]]);
                $assoc->delete();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idRoyaume]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Suppression de la religion jouable : " . $religion->nom . " pour le royaume " . $royaume->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $royaume->genererReligionsAutorisees("modification");
            }
        }
    }

    /**
     * Permet de supprimer une race jouable à l'inscription pour un royaume
     * @return string
     */
    public function supprimerRaceJouableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRoyaume = $this->request->get('idRoyaume');
                $idRace = $this->request->get('idRace');

                //On ajoute l'association
                $assoc = AssocRoyaumesRaceJouable::findFirst(['idRace = :idRace: AND idRoyaume = :idRoyaume:', 'bind' => ['idRace' => $idRace, 'idRoyaume' => $idRoyaume]]);
                $assoc->delete();

                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idRace]]);
                $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idRoyaume]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Suppression de la race jouable : " . $race->nom . " pour le royaume " . $royaume->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $royaume->genererRacesAutorisees("modification");
            }
        }
    }

    //Races

    /**
     * Permet d'afficher le détail d'une race
     * @return unknown
     */
    public function detailRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('administration/referentiel/creationRace');
                } else {
                    if ($mode == "consultation") {
                        $this->view->race = Races::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRace')]]);
                        $retour = $this->view->partial('administration/referentiel/detailRace');
                    } else {
                        if ($mode == "modification") {
                            $this->view->race = Races::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRace')]]);
                            $retour = $this->view->partial('administration/referentiel/editionRace');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('administration/referentiel/listeRaces');
                            }
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer une race
     * @return string
     */
    public function creerRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Races::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }


                //Création
                $race = new Races();
                $race->nom = $nom;
                $race->idArticle = $idArticle;
                $race->description = $this->request->get('description');
                $race->image = $this->request->get('image');
                $race->isDispoInscription = $this->request->get('isDispoInscription');
                $race->tailleMin = $this->request->get('tailleMin');
                $race->tailleMax = $this->request->get('tailleMax');
                $race->poidsMin = $this->request->get('poidsMin');
                $race->poidsMax = $this->request->get('poidsMax');
                $race->ageMin = $this->request->get('ageMin');
                $race->ageMax = $this->request->get('ageMax');
                $race->yeuxAutorise = $this->request->get('listeCouleurYeuxAutorisees');
                $race->cheveuxAutorise = $this->request->get('listeCouleurCheveuxAutorisees');
                $race->save();

                //Création des répertoire pour les gifs
                $royaumes = Royaumes::find();
                foreach ($royaumes as $royaume) {
                    mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/feminin/' . $race->nom . '/'));
                    chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/feminin/' . $race->nom . '/'), 0777);
                    mkdir(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/masculin/' . $race->nom . '/'));
                    chmod(utf8_decode($this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/masculin/' . $race->nom . '/'), 0777);
                }

                //Log de l'action
                $action = "Création de la race : " . $race->nom;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return $race->id;
            }
        }
    }

    /**
     * Permet de modifier une race
     * @return string
     */
    public function modifierRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRace')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $updateRepertoire = false;
                if ($race->nom != $nom) {
                    $updateRepertoire = true;
                    $verif = Races::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Initialisation de l'action de logs
                $ancienNom = $race->nom;
                $action = "Ancienne Race :" . $race->toString();

                //Mise à jour
                $race->nom = $nom;
                $race->idArticle = $idArticle;
                $race->description = $this->request->get('description');
                $race->image = $this->request->get('image');
                $race->isDispoInscription = $this->request->get('isDispoInscription');
                if ($this->request->get('tailleMin') == null || $this->request->get('tailleMin') == "") {
                    $race->tailleMin = 0;
                } else {
                    $race->tailleMin = $this->request->get('tailleMin');
                }
                if ($this->request->get('tailleMax') == null || $this->request->get('tailleMax') == "") {
                    $race->tailleMax = 0;
                } else {
                    $race->tailleMax = $this->request->get('tailleMax');
                }
                if ($this->request->get('poidsMin') == null || $this->request->get('poidsMin') == "") {
                    $race->poidsMin = 0;
                } else {
                    $race->poidsMin = $this->request->get('poidsMin');
                }
                if ($this->request->get('poidsMax') == null || $this->request->get('poidsMax') == "") {
                    $race->poidsMax = 0;
                } else {
                    $race->poidsMax = $this->request->get('poidsMax');
                }
                if ($this->request->get('ageMin') == null || $this->request->get('ageMin') == "") {
                    $race->ageMin = 0;
                } else {
                    $race->ageMin = $this->request->get('ageMin');
                }
                if ($this->request->get('ageMax') == null || $this->request->get('ageMax') == "") {
                    $race->ageMax = 0;
                } else {
                    $race->ageMax = $this->request->get('ageMax');
                }

                $race->yeuxAutorise = $this->request->get('listeCouleurYeuxAutorisees');
                $race->cheveuxAutorise = $this->request->get('listeCouleurCheveuxAutorisees');
                $race->save();

                //Update du répertoire pour les gifs
                if ($updateRepertoire) {
                    $royaumes = Royaumes::find();
                    foreach ($royaumes as $royaume) {
                        $oldRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/feminin/' . $ancienNom . '/';
                        $newRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/feminin/' . $race->nom . '/';
                        rename(utf8_decode($oldRepertoire), utf8_decode($newRepertoire));
                        $oldRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/masculin/' . $ancienNom . '/';
                        $newRepertoire = $this->getDI()->get('config')->application->gifDir . 'personnages/' . $royaume->nom . '/masculin/' . $race->nom . '/';
                        rename(utf8_decode($oldRepertoire), utf8_decode($newRepertoire));
                    }
                }

                //Log de l'action
                $action = "Modification de la race : " . $race->nom . " (" . $action . ", Nouvelle Race : " . $race->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return "success";
            }
        }
    }

    /**
     * Permet d'ajouter une religion jouable à une race à l'inscription
     * @return string
     */
    public function ajouterReligionJouableRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRace = $this->request->get('idRace');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $assoc = new AssocRacesReligionJouable();
                $assoc->idRace = $idRace;
                $assoc->idReligion = $idReligion;
                $assoc->save();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);
                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idRace]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Autorisation de la religion : " . $religion->nom . " pour la race " . $race->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $race->genererReligionsAutorisees("modification");
            }
        }
    }

    /**
     * Permet de recharger la selection d'une religion pour une race
     * @return string
     */
    public function rechargerSelectReligionRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRace')]]);

                //Retour du select mis à jour
                return $race->genererSelectListeReligionsAutorisees();
            }
        }
    }

    /**
     * Permet de supprimer une religion disponible pour une race à l'inscription
     * @return string
     */
    public function supprimerReligionJouableRaceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idRace = $this->request->get('idRace');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $assoc = AssocRacesReligionJouable::findFirst(['idReligion = :idReligion: AND idRace = :idRace:', 'bind' => ['idReligion' => $idReligion, 'idRace' => $idRace]]);
                $assoc->delete();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);
                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idRace]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Suppression de la religion jouable : " . $religion->nom . " pour la race " . $race->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $race->genererReligionsAutorisees("modification");
            }
        }
    }

    //Religions

    /**
     * Permet d'afficher le détail d'une religion
     * @return unknown
     */
    public function detailReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('administration/referentiel/creationReligion');
                } else {
                    if ($mode == "consultation") {
                        $this->view->religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idReligion')]]);
                        $retour = $this->view->partial('administration/referentiel/detailReligion');
                    } else {
                        if ($mode == "modification") {
                            $this->view->religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idReligion')]]);
                            $retour = $this->view->partial('administration/referentiel/editionReligion');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('administration/referentiel/listeReligions');
                            }
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer une religion
     * @return string
     */
    public function creerReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Religions::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }


                //Création
                $religion = new Religions();
                $religion->nom = $nom;
                $religion->idArticle = $idArticle;
                $religion->description = $this->request->get('description');
                $religion->img = $this->request->get('image');
                $religion->isDispoInscription = $this->request->get('isDispoInscription');
                $religion->idNatureMagie = $this->request->get('idNatureMagie');
                $religion->save();

                //Log de l'action
                $action = "Création de la religion : " . $religion->nom;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return $religion->id;
            }
        }
    }

    /**
     * Permet de modifier une religion
     * @return string
     */
    public function modifierReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idReligion')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                if ($religion->nom != $nom) {
                    $verif = Religions::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Initialisation de l'action de logs
                $ancienNom = $religion->nom;
                $action = "Ancienne Religion :" . $religion->toString();

                //Mise à jour
                $religion->nom = $nom;
                $religion->idArticle = $idArticle;
                $religion->description = $this->request->get('description');
                $religion->img = $this->request->get('image');
                $religion->isDispoInscription = $this->request->get('isDispoInscription');
                $religion->idNatureMagie = $this->request->get('idNatureMagie');
                $religion->save();

                //Log de l'action
                $action = "Modification de la religion : " . $religion->nom . " (" . $action . ", Nouvelle Religion : " . $religion->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return "success";
            }
        }
    }

    /**
     * Permet d'ajouter une divinite à la religion
     * @return string
     */
    public function ajouterDiviniteReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idDivinite = $this->request->get('idDieu');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $idDivinite]]);
                $dieu->idReligion = $idReligion;
                $dieu->save();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Ajout de la divinite : " . $dieu->nom . " à la reigion " . $religion->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $religion->genererListeDivinites("modification");
            }
        }
    }

    /**
     * Permet de recharger la selection de la liste des divinités pour
     * une religion
     * @return string
     */
    public function rechargerSelectDiviniteReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idReligion')]]);

                //Retour du select mis à jour
                return $religion->genererSelectListeDiviniteDisponible();
            }
        }
    }

    /**
     * Permet de supprimer une divinite d'un panthéon
     * @return string
     */
    public function supprimerDiviniteReligionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idDieu = $this->request->get('idDieu');
                $idReligion = $this->request->get('idReligion');

                //On ajoute l'association
                $dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $idDieu]]);
                $dieu->idReligion = '0';
                $dieu->save();

                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idReligion]]);

                //Log de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Suppression de la divinité " . $dieu->nom . " dans la religion " . $religion->nom;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                //Retour de la liste mise à jour
                return $religion->genererListeDivinites("modification");
            }
        }
    }

    //Dieux

    /**
     * Permet d'afficher le détail d'un dieu
     * @return unknown
     */
    public function detailDieuAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('administration/referentiel/creationDieu');
                } else {
                    if ($mode == "consultation") {
                        $this->view->dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idDieu')]]);
                        $retour = $this->view->partial('administration/referentiel/detailDieu');
                    } else {
                        if ($mode == "modification") {
                            $this->view->dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idDieu')]]);
                            $retour = $this->view->partial('administration/referentiel/editionDieu');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('administration/referentiel/listeDieux');
                            }
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer un dieu
     * @return string
     */
    public function creerDieuAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Dieux::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }


                //Création
                $dieu = new Dieux();
                $dieu->nom = $nom;
                $dieu->idArticle = $idArticle;
                $dieu->description = $this->request->get('description');
                $dieu->img = $this->request->get('image');
                $dieu->isDispoInscription = $this->request->get('isDispoInscription');
                $dieu->idRace = $this->request->get('idRace');
                $dieu->couleur = $this->request->get('couleur');
                $dieu->idReligion = '0';
                $dieu->save();

                //Log de l'action
                $action = "Création du dieu : " . $dieu->nom;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return $dieu->id;
            }
        }
    }

    /**
     * Permet de modifier un dieu
     * @return string
     */
    public function modifierDieuAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idDieu')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                if ($dieu->nom != $nom) {
                    $verif = Dieux::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Initialisation de l'action de logs
                $ancienNom = $dieu->nom;
                $action = "Ancienne Religion :" . $dieu->toString();

                //Mise à jour
                $dieu->nom = $nom;
                $dieu->idArticle = $idArticle;
                $dieu->description = $this->request->get('description');
                $dieu->img = $this->request->get('image');
                $dieu->isDispoInscription = $this->request->get('isDispoInscription');
                $dieu->couleur = $this->request->get('couleur');
                $dieu->save();
                $dieu = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idDieu')]]);

                $dieu->idRace = $this->request->get('idRace');
                $dieu->save();
                //Log de l'action
                $action = "Modification du dieu : " . $dieu->nom . " (" . $action . ", Nouveau Dieu : " . $dieu->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return "success";
            }
        }
    }

    //Villes
    public function detailVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('administration/referentiel/creationVille');
                } else {
                    if ($mode == "consultation") {
                        $this->view->ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('administration/referentiel/detailVille');
                    } else {
                        if ($mode == "modification") {
                            $this->view->ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('administration/referentiel/editionVille');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('administration/referentiel/listeVilles');
                            }
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function modifierVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idVille')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                if ($ville->nom != $nom) {
                    $updateRepertoire = true;
                    $verif = Villes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Initialisation de l'action de logs
                $action = "Ancienne Ville :" . $ville->toString();

                //Mise à jour
                $ville->nom = $nom;
                $ville->idArticle = $idArticle;
                $ville->description = $this->request->get('description');
                $ville->image = $this->request->get('image');
                $ville->isNaissance = $this->request->get('isNaissance');
                $ville->idRoyaumeOrigine = $this->request->get('idRoyaumeOrigine');
                $ville->idRoyaumeActuel = $this->request->get('idRoyaumeActuel');
                $ville->messageAccueil = $this->request->get('messageAccueil');
                $ville->xMin = $this->request->get('xmin');
                $ville->xMax = $this->request->get('xmax');
                $ville->yMin = $this->request->get('ymin');
                $ville->yMax = $this->request->get('ymax');
                $ville->save();

                //Log de l'action
                $action = "Modification de la ville : " . $ville->nom . " (" . $action . ", Nouvelle Ville : " . $ville->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return "success";
            }
        }
    }

    public function creerVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Villes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                //Création de l'élément
                $ville = new Villes();
                $ville->nom = $nom;
                $ville->idArticle = $idArticle;
                $ville->description = $this->request->get('description');
                $ville->image = $this->request->get('image');
                $ville->isNaissance = $this->request->get('isNaissance');
                $ville->idRoyaumeOrigine = $this->request->get('idRoyaumeOrigine');
                $ville->idRoyaumeActuel = $this->request->get('idRoyaumeActuel');
                $ville->messageAccueil = $this->request->get('messageAccueil');
                $ville->xMin = $this->request->get('xmin');
                $ville->xMax = $this->request->get('xmax');
                $ville->yMin = $this->request->get('ymin');
                $ville->yMax = $this->request->get('ymax');
                if ($ville->save() == false) {
                    return "error";
                }

                //Log de l'action
                $action = "Création de la ville : " . $ville->nom . " (" . $ville->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FORMULAIRE;
                $logAdmin->save();

                return $ville->id;
            }
        }
    }

    public function showlisteGestionVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->ville = $ville;
                $retour = $this->view->partial('administration/referentiel/villes/panneauGestion');
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function showlisteFinanceVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->ville = $ville;
                $retour = $this->view->partial('administration/referentiel/villes/panneauFinance');
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function showlisteDiplomatieVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->ville = $ville;
                $retour = $this->view->partial('administration/referentiel/villes/panneauDiplomatie');
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function showlisteMiliceVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->ville = $ville;
                $retour = $this->view->partial('administration/referentiel/villes/panneauMilice');
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function showlisteQuartierVilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->ville = $ville;
                $retour = $this->view->partial('administration/referentiel/villes/panneauQuartier');
                $this->view->disable();
                return $retour;
            }
        }
    }

    //########## Profil de test ########//

    /**
     * Méthode permettant de se déconnecter d'un profil de test
     * @return string
     */
    public function deconnectProfilTestAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idPerso = $auth['perso']->id;
                $perso = Personnages::findFirst(['id = :id:', 'bind' => ['id' => $idPerso]]);
                $auth['perso'] = $perso;
                $auth['profilTest'] = null;
                $auth['autorisations'] = $perso->genererAutorisation();
                $this->session->auth = $auth;
                return $this->getDi()->get("config")->application->baseUri . 'accueil';
            }
        }
    }

    /**
     * Méthode pour se connecter avec un profil de test
     * @return string
     */
    public function connectProfilTestAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $perso = $auth['perso'];

                $idDroit = $this->request->get('idDroit');
                $idRoyaume = $this->request->get('idRoyaume');
                $idRace = $this->request->get('idRace');
                $idReligion = $this->request->get('idReligion');

                if ($idDroit != "") {
                    if ($idDroit == 0) {
                        $perso->droits = array();
                        $auth['autorisations'] = array();
                    } else {
                        $droit = Droits::findFirst(['id = :id:', 'bind' => ['id' => $idDroit]]);
                        $perso->droits = array([$droit]);
                        $auth['autorisations'] = $droit->genererAutorisationTechnique();
                    }
                }

                if ($idRoyaume != "") {
                    $perso->idRoyaume = $idRoyaume;
                }

                if ($idRace != "") {
                    $perso->idRace = $idRace;
                }

                if ($idReligion != "") {
                    $perso->idReligion = $idReligion;
                }

                $auth['perso'] = $perso;
                $auth['profilTest'] = true;
                $this->session->auth = $auth;
                return $this->getDi()->get("config")->application->baseUri . 'accueil';
            }
        }
    }

    //######### Gestion des images ########//

    /**
     * Permet de charger les images d'un répertoire
     * @return string
     */
    public function chargerRepertoireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $repertoire = $this->request->get('repertoire');
                return Files::chargerImagesByRepertoire($repertoire, $this->getDI()->get('config')->application->imgDir);
            }
        }
    }

    /**
     * Méthode permettant de modifier le nom d'un répertoire
     * @return string
     */
    public function modifierRepertoireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $oldRepertoire = $this->getDI()->get('config')->application->imgDir . $this->request->get('oldRepertoire');
                $newRepertoire = $this->getDI()->get('config')->application->imgDir . $this->request->get('newRep');

                //Modification du répertoire
                rename(utf8_decode($oldRepertoire), utf8_decode($newRepertoire));

                //Log de l'action
                $action = "Modification du répertoire : " . $oldRepertoire . " -> " . $newRepertoire;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                $logAdmin->save();

                //Regénération de l'arbre des répertoires
                return Files::mkmapImage(null, $this->getDI()->get('config')->application->imgDir);
            }
        }
    }

    /**
     * Permet de créer un répertoire pour les images
     * @return string
     */
    public function creerRepertoireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $newRepertoire = $this->getDI()->get('config')->application->imgDir . $this->request->get('newRep');

                mkdir(utf8_decode($newRepertoire));
                chmod(utf8_decode($newRepertoire), 0777);
                //Log de l'action
                $action = "Création du répertoire : " . $newRepertoire;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                $logAdmin->save();

                //Regénération de l'arbre des répertoires
                return Files::mkmapImage(null, $this->getDI()->get('config')->application->imgDir);
            }
        }
    }

    /**
     * Méthode permettant de supprimer un répertoire
     * @return string
     */
    public function supprimerRepertoireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $repertoire = $this->getDI()->get('config')->application->imgDir . $this->request->get('repertoire');
                $repertoire = str_replace('//', '/', $repertoire);
                $repertoire = str_replace('\\', '/', $repertoire);
                $auth = $this->session->get('auth');

                //Suppression du répertoire
                $retour = Files::supprimerRepertoire($repertoire);
                if ($retour == "success") {
                    //Log de l'action
                    $action = "Suppression du répertoire : " . $repertoire;
                    $logAdmin = new LogsADMIN();
                    $logAdmin->action = $action;
                    $logAdmin->idPersonnage = $auth['perso']->id;
                    $logAdmin->dateLog = time();
                    $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                    $logAdmin->save();
                    //Regénération de l'arbre des répertoires
                    return Files::mkmapImage(null, $this->getDI()->get('config')->application->imgDir);
                } else {
                    return $retour;
                }
            }
        }
    }

    /**
     * Permet de charger le bloc d'action d'une image
     * @return string
     */
    public function chargerImageGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $image = $this->request->get('image');
                return Files::genererDetailImage($image, $this->getDI()->get('config')->application->imgDir);
            }
        }
    }

    /**
     * Permet de modifier le nom d'une image
     * @return boolean|string
     */
    public function modifierImageGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $oldImage = Files::formatFichier('image', $this->request->get('oldImage'), $this->getDI()->get('config')->application->imgDir);
                $newImage = Files::formatFichier('image', $this->request->get('newImage'), $this->getDI()->get('config')->application->imgDir);
                $auth = $this->session->get('auth');

                if ($newImage != "" && str_replace(" ", "", $newImage) != "") {
                    $retour = rename(utf8_decode($oldImage), utf8_decode($newImage));

                    if ($retour == "error") {
                        return $retour;
                    } else {
                        //Log de l'action
                        $action = "Modification de l'image : " . $oldImage . " -> " . $newImage;
                        $logAdmin = new LogsADMIN();
                        $logAdmin->action = $action;
                        $logAdmin->idPersonnage = $auth['perso']->id;
                        $logAdmin->dateLog = time();
                        $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                        $logAdmin->save();

                        return Files::genererDetailImage($newImage, $this->getDI()->get('config')->application->imgDir);
                    }
                }
            }
        }
    }

    /**
     * Permet de supprimer une image
     * @return boolean|string
     */
    public function supprimerImageGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $image = Files::formatFichier('image', $this->request->get('image'), $this->getDI()->get('config')->application->imgDir);

                //Suppression de l'image
                $retour = unlink($image);
                if ($retour == "error") {
                    return $retour;
                } else {
                    //Log de l'action
                    $action = "Suppresion de l'image : " . $image;
                    $logAdmin = new LogsADMIN();
                    $logAdmin->action = $action;
                    $logAdmin->idPersonnage = $auth['perso']->id;
                    $logAdmin->dateLog = time();
                    $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                    $logAdmin->save();
                    return Files::genererDetailImage(null, $image, $this->getDI()->get('config')->application->imgDir);
                }
            }
        }
    }

    /**
     * Méthode qui permet de charger un fichier depuis votre pc
     * sur le serveur
     * @return string
     */
    public function uploadFileGestionImageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $repertoire = $this->request->get('repertoire');
                $this->view->disable();
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        if ($file == null || empty($file)) {
                            return "error";
                        }
                        $directory = $this->getDI()->get('config')->application->imgDir . $repertoire . "/" . $file->getName();
                        $directory = str_replace('//', '/', $directory);
                        $directory = str_replace('\\', '/', $directory);

                        $upload = $file->moveTo($directory);
                        if (!$upload) {
                            return "error";
                        }
                        return Files::chargerImagesByRepertoire($repertoire, $this->getDI()->get('config')->application->imgDir);
                    }
                }
            }
        }
    }


    //########### Gestion des gifs ########//

    /**
     * Permet de charger les gifs pour un répertoire
     * @return string
     */
    public function chargerRepertoireGifAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $repertoire = utf8_decode($this->request->get('repertoire'));
                $repertoire = str_replace("@", "'", $repertoire);
                return Files::chargerGifsByRepertoire($repertoire, $this->getDI()->get('config')->application->gifDir);
            }
        }
    }

    /**
     * Méthode pour charger les gifs d'un répertoire
     * @return string
     */
    public function chargerGifGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $gif = $this->request->get('gif');
                return Files::genererDetailGif($gif, $this->getDI()->get('config')->application->gifDir);
            }
        }
    }

    /**
     * Permet de modifier un gif
     * @return boolean|string
     */
    public function modifierGifGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $oldGif = Files::formatFichier('gif', $this->request->get('oldGif'), $this->getDI()->get('config')->application->gifDir);
                $newGif = Files::formatFichier('gif', $this->request->get('newGif'), $this->getDI()->get('config')->application->gifDir);
                $auth = $this->session->get('auth');

                if ($newGif != "" && str_replace(" ", "", $newGif) != "") {
                    $retour = rename(utf8_decode($oldGif), utf8_decode($newGif));

                    if ($retour == "error") {
                        return $retour;
                    } else {
                        //Log de l'action
                        $action = "Modification du gif : " . $oldGif . " -> " . $newGif;
                        $logAdmin = new LogsADMIN();
                        $logAdmin->action = $action;
                        $logAdmin->idPersonnage = $auth['perso']->id;
                        $logAdmin->dateLog = time();
                        $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                        $logAdmin->save();

                        return Files::genererDetailGif($newGif, $this->getDI()->get('config')->application->gifDir);
                    }
                }
            }
        }
    }

    /**
     * Permet de supprimer un gif
     * @return boolean|string
     */
    public function supprimerGifGestionFichierAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $gif = Files::formatFichier('gif', $this->request->get('gif'), $this->getDI()->get('config')->application->gifDir);
                //Suppression du gif
                $retour = unlink(utf8_decode($gif));
                if ($retour == "error") {
                    return $retour;
                } else {
                    //Log de l'action
                    $action = "Suppresion du gif : " . $gif;
                    $logAdmin = new LogsADMIN();
                    $logAdmin->action = $action;
                    $logAdmin->idPersonnage = $auth['perso']->id;
                    $logAdmin->dateLog = time();
                    $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_FICHIER;
                    $logAdmin->save();
                    return Files::genererDetailImage($gif, $this->getDI()->get('config')->application->gifDir);
                }
            }
        }
    }

    /**
     * Méthode qui permet de charger un fichier depuis votre pc
     * sur le serveur
     * @return string
     */
    public function uploadFileGestionGifAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $repertoire = $this->request->get('repertoire');
                $this->view->disable();
                if ($this->request->hasFiles() == true) {
                    foreach ($this->request->getUploadedFiles() as $file) {
                        if ($file == null || empty($file)) {
                            return "error";
                        }
                        $directory = $this->getDI()->get('config')->application->gifDir . $repertoire . "/" . $file->getName();
                        $directory = str_replace('//', '/', $directory);
                        $directory = str_replace('\\', '/', $directory);
                        $directory = str_replace("@", "'", $directory);
                        $upload = $file->moveTo(utf8_decode($directory));
                        if (!$upload) {
                            return "error";
                        }
                        return Files::chargerGifsByRepertoire($repertoire, $this->getDI()->get('config')->application->gifDir);
                    }
                }
            }
        }
    }


    //########### Questionnaires ###########//

    /**
     * Méthode pour afficher le questionnaire d'inscription
     */
    public function consulterQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $auth = $this->session->get('auth');

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    /**
     * Méthode permettant de désactiver un questionnaire
     */
    public function desactiverQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $id = $this->request->get('idQuestionnaire');

                $questionnaire = Questionnaires::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                $questionnaire->isActif = 0;
                $questionnaire->save();

                //Log de l'action
                $action = "Desactivation de la question : " . $questionnaire->question;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
                $logAdmin->save();

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    /**
     * Méthode permettant d'activer un questionnaire
     */
    public function activerQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $id = $this->request->get('idQuestionnaire');

                $questionnaire = Questionnaires::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                $questionnaire->isActif = 1;
                $questionnaire->save();

                //Log de l'action
                $action = "Activation de la question : " . $questionnaire->question;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
                $logAdmin->save();

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    /**
     * Méthode permettant de supprimer une question
     * du questionnaire
     */
    public function supprimerQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $id = $this->request->get('idQuestionnaire');

                $questionnaire = Questionnaires::findFirst(['id = :id:', 'bind' => ['id' => $id]]);

                //Log de l'action
                $action = "Suppression de la question : " . $questionnaire->question;
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
                $logAdmin->save();

                $questionnaire->delete();

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    /**
     * Permet d'afficher la popup du questionnaire
     * pour créer ou modifier une question
     * @return unknown
     */
    public function afficherFormulaireQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $id = $this->request->get('idQuestionnaire');
                $mode = $this->request->get('mode');

                if ($id != null) {
                    $questionnaire = Questionnaires::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                    $this->view->questionnaire = $questionnaire;
                }

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->view->mode = $mode;
                $retour = $this->view->partial('administration/popup/popupQuestionnaire');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de modifier une question
     */
    public function modifierQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $id = $this->request->get('idQuestionnaire');
                $question = $this->request->get('question');
                $choixA = $this->request->get('choixA');
                $choixB = $this->request->get('choixB');
                $choixC = $this->request->get('choixC');
                $reponse = $this->request->get('reponse');
                $paragraphe = $this->request->get('paragraphe');

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                if ($paragraphe == "0") {
                    $paragraphe = null;
                }

                $questionnaire = Questionnaires::findFirst(['id = :id:', 'bind' => ['id' => $id]]);

                $action = "Modification de la question (old :" . $questionnaire->toString();

                $questionnaire->question = $question;
                $questionnaire->choixA = $choixA;
                $questionnaire->choixB = $choixB;
                $questionnaire->choixC = $choixC;
                $questionnaire->reponse = $reponse;
                $questionnaire->idArticle = $idArticle;
                $questionnaire->paragraphe = $paragraphe;
                $questionnaire->type = $type;
                $questionnaire->idType = $idType;
                $questionnaire->save();

                //Log de l'action
                $action .= ", new : " . $questionnaire->toString() . ")";
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
                $logAdmin->save();

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    /**
     * Permet d'ajouter une question
     */
    public function ajouterQuestionnaireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $question = $this->request->get('question');
                $choixA = $this->request->get('choixA');
                $choixB = $this->request->get('choixB');
                $choixC = $this->request->get('choixC');
                $reponse = $this->request->get('reponse');
                $paragraphe = $this->request->get('paragraphe');

                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titreArticle')]]);
                if (!$article) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }

                if ($paragraphe == "0") {
                    $paragraphe = null;
                }

                $questionnaire = new Questionnaires();
                $questionnaire->question = $question;
                $questionnaire->choixA = $choixA;
                $questionnaire->choixB = $choixB;
                $questionnaire->choixC = $choixC;
                $questionnaire->reponse = $reponse;
                $questionnaire->idArticle = $idArticle;
                $questionnaire->paragraphe = $paragraphe;
                $questionnaire->type = $type;
                $questionnaire->idType = $idType;
                $questionnaire->save();

                //Log de l'action
                $action .= "Création de la question : " . $questionnaire->toString();
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
                $logAdmin->save();

                $this->view->pick("administration/questionnaires");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

                $this->view->auth = $auth;
                $this->view->type = $type;
                $this->view->idType = $idType;
                $this->pageview();
            }
        }
    }

    public function chargerParagrapheAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                //Récupération de l'identifiant de l'article : Sinon trouvé, mettre "null"
                $article = Articles::findFirst(['titre = :titre:', 'bind' => ['titre' => $this->request->get('titre')]]);
                if ($article == null) {
                    $idArticle = null;
                } else {
                    $idArticle = $article->id;
                }
                $this->view->disable();
                return Questionnaires::genererListeParagraphe("selectQuestionnaireParagraphe", $idArticle, null);
            }
        }
    }

    //########### Général #############//

    /**
     * Construit la liste des onglets
     * @param unknown $auth
     * @return string
     */
    private function buildListeOnglet($auth) {
        $onglets = "";
        $nbOnglet = 0;

        /* Droits */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_DROITS_CONSULTATION, $auth['autorisations'])) {
            $onglets .= '<div id="droit" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'droit\');"><span class="labelOngletAdministration">Droits</span></div>';
            $nbOnglet++;
        }

        /* Logs */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_MJ_CONSULTATION, $auth['autorisations'])
          || Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_ADMIN_CONSULTATION, $auth['autorisations'])
          || Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_LOGS_DEV_CONSULTATION, $auth['autorisations'])
          || Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_CONSULTATION_ARCHIVE_LOG, $auth['autorisations'])) {
            $onglets .= '<div id="logs" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'logs\');"><span class="labelOngletAdministration">Logs</span></div>';
            $nbOnglet++;
        }

        /* Référentiels */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_REFERENTIELS, $auth['autorisations'])) {
            $onglets .= '<div id="referentiels" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'referentiels\');"><span class="labelOngletAdministration">Référentiels</span></div>';
            $nbOnglet++;
        }

        /* Statistiques */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_STATISTIQUES, $auth['autorisations'])) {
            $onglets .= '<div id="statistiques" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'statistiques\');"><span class="labelOngletAdministration">Statistiques</span></div>';
            $nbOnglet++;
        }

        /* Test */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_PROFIL_TEST, $auth['autorisations'])) {
            $onglets .= '<div id="test" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'test\');"><span class="labelOngletAdministration">Test</span></div>';
            $nbOnglet++;
        }

        /* Images */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_GESTION_IMAGE, $auth['autorisations'])) {
            $onglets .= '<div id="image"  name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'image\');"><span class="labelOngletAdministration">Images</span></div>';
            $nbOnglet++;
        }

        /* Gifs */
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_GESTION_GIF, $auth['autorisations'])) {
            $onglets .= '<div id="gif" name="ongletAdministration" class="ongletAdministration" onclick="openOngletAdministration(\'gif\');"><span class="labelOngletAdministration">Gifs</span></div>';
            $nbOnglet++;
        }

        $onglets .= '<input type="hidden" id="nombreOngletAdministration" name="nombreOngletAdministration" value="' . $nbOnglet . '"/>';
        if ($nbOnglet == 0) {
            //$onglets .= '<input type="hidden" id="redirectionAccueil" name="redirectionAccueil" value="'.$this->getDi()->get("config")->application->baseUri.'/accueil'.'"/>';
            return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
        }
        return $onglets;
    }


    /**
     * Méthode générique pour la page
     */
    private function pageview() {
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/administration/administration.js?v=" . VERSION);
        $this->assets->addJs("js/administration/villes.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/utils/questionnaires.js?v=" . VERSION);
        $this->assets->addJs("js/utils/popupwiki.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->assets->addJs("js/utils/moduleftp.js?v=" . VERSION);
        $this->assets->addJs("js/utils/bonus.js?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/administration.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/administration/villes.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/administration/bonus.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/popup.css?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }

}

