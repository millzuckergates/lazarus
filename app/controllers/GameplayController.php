<?php

use Phalcon\Mvc\View;

class GameplayController extends \Phalcon\Mvc\Controller {

    public function indexAction() {
        //Calcul de la liste des articles de l'index
        $auth = $this->session->get("auth");

        //Construction de la liste des onglets et placement en session
        $this->session->set("listeOngletGameplay", $this->buildListeOnglet($auth));
        $this->view->auth = $auth;
        $this->pageview();
    }

    /**
     * Permet d'afficher l'onglet choisi
     */
    public function afficherGameplayAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $conteneur = $this->request->get("conteneur");

                switch ($conteneur) {
                    case "cartes":
                        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_MODIFIER, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionCartes");
                        }
                        break;
                    case "terrains":
                        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_TERRAINS, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionTerrains");
                        }
                        break;
                    case "textures":
                        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_TERRAINS, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionTextures");
                        }
                        break;
                    case "caracs":
                        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARACS, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CARAC_PRIMAIRE, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionCarac");
                        }
                        break;
                    case "magie":
                        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionMagies");
                        }
                        break;
                    case "talents":
                        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                            //On récupère la première catégorie et la première famille associé
                            $categorie = CategoriesTalent::findFirst();
                            if ($categorie == false) {
                                $categorie = null;
                                $famille = null;
                            } else {
                                $famille = FamillesTalent::findFirst(["idCategorie = :idCategorie:", "bind" => ['idCategorie' => $categorie->id]]);
                                if ($famille == false) {
                                    $famille = null;
                                }
                            }
                            $this->view->categorie = $categorie;
                            $this->view->famille = $famille;
                            $this->view->pick("gameplay/gestionTalents");
                        }
                        break;
                    case "competences":
                        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                            $this->view->pick("gameplay/gestionCompetence");
                        }
                        break;
                    case "equipements":
                        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_EQUIPEMENT_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_EQUIPEMENT_MODIFIER, $auth['autorisations'])) {
                            $this->view->pick("gameplay/equipements");
                        }
                        break;
                    case "creatures":
                        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CREATURE_CONSULTER, $auth['autorisations'])
                          || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CREATURE_MODIFIER, $auth['autorisations'])) {
                            $this->view->pick("gameplay/creatures");
                        }
                        break;
                    default:
                        return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
                }
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
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
                    if ($type == "NatureMagie") {
                        $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/naturesmagie/' . $name;
                        $resultUpload = copy($url, $destination);
                        if ($id != null && $id != false) {
                            $natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                            $retour = $natureMagie->genererListeImageTypeMagie();
                        } else {
                            $retour = Naturesmagie::genererListeImageTypeMagieVide($this->getDI()->get('config')->application->imgDir);
                        }
                    } else {
                        if ($type == "EcoleMagie") {
                            $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/ecolesmagie/' . $name;
                            $resultUpload = copy($url, $destination);
                            if ($id != null && $id != false) {
                                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                $retour = $ecole->genererListeImageEcoleMagie();
                            } else {
                                $retour = Ecolesmagie::genererListeImageEcoleMagieVide($this->getDI()->get('config')->application->imgDir);
                            }
                        } else {
                            if ($type == "Sort") {
                                $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/sorts/' . $name;
                                $resultUpload = copy($url, $destination);
                                if ($id != null && $id != false) {
                                    $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                    $retour = $sort->genererListeImagesSort();
                                } else {
                                    $retour = Sorts::genererListeImagesSortVide($this->getDI()->get('config')->application->imgDir);
                                }
                            } else {
                                if ($type == "caracs") {
                                    $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/caracteristiques/' . $name;
                                    $resultUpload = copy($url, $destination);
                                    if ($id != null && $id != false) {
                                        $carac = Caracteristiques::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                        $retour = $carac->genererListeImageCarac();
                                    } else {
                                        $retour = Caracteristiques::genererListeImageCaracVide($this->getDI()->get('config')->application->imgDir);
                                    }
                                } else {
                                    if ($type == "carte") {
                                        $destination = $this->getDI()->get('config')->application->carteDir . $name;
                                        $resultUpload = copy($url, $destination);
                                        if ($id != null && $id != false) {
                                            $carte = Cartes::findById($id);
                                            $retour = $carte->genererListeImageCarte();
                                        } else {
                                            $retour = Cartes::genererListeImageCarteVide($this->getDI()->get('config')->application->carteDir);
                                        }
                                    } else {
                                        if ($type == "cartePJ") {
                                            $destination = $this->getDI()->get('config')->application->cartePJDir . $name;
                                            $resultUpload = copy($url, $destination);
                                            if ($id != null && $id != false) {
                                                $carte = Cartes::findById($id);
                                                $retour = $carte->genererListeImageCartePJ();
                                            } else {
                                                $retour = Cartes::genererListeImageCartePJVide($this->getDI()->get('config')->application->carteDir);
                                            }
                                        } else {
                                            if ($type == "categorie") {
                                                $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/categoriestalent/' . $name;
                                                $resultUpload = copy($url, $destination);
                                                if ($id != null && $id != false) {
                                                    $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                                    $retour = $categorie->genererListeImages();
                                                } else {
                                                    $retour = CategoriesTalent::genererListeImagesVide($this->getDI()->get('config')->application->imgDir);
                                                }
                                            } else {
                                                if ($type == "arbre") {
                                                    $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/arbrestalent/' . $name;
                                                    $resultUpload = copy($url, $destination);
                                                    if ($id != null && $id != false) {
                                                        $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                                        $retour = $arbre->genererListeImages();
                                                    } else {
                                                        $retour = ArbresTalent::genererListeImagesVide($this->getDI()->get('config')->application->imgDir);
                                                    }
                                                } else {
                                                    if ($type == "talent") {
                                                        $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/talents/' . $name;
                                                        $resultUpload = copy($url, $destination);
                                                        if ($id != null && $id != false) {
                                                            $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                                            $retour = $talent->genererListeImages();
                                                        } else {
                                                            $retour = Talents::genererListeImagesVide($this->getDI()->get('config')->application->imgDir);
                                                        }
                                                    } else {
                                                        if ($type == "competence") {
                                                            $destination = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/competences/' . $name;
                                                            $resultUpload = copy($url, $destination);
                                                            if ($id != null && $id != false) {
                                                                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $id]]);
                                                                $retour = $competence->genererListeImages();
                                                            } else {
                                                                $retour = Competences::genererListeImagesVide($this->getDI()->get('config')->application->imgDir);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
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

    //########## Gestion des cartes ##########//

    /**
     * Permet de filtrer les cartes
     * @return string
     */
    public function filtrerCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $nom = $this->request->get('nom');
                $saison = $this->request->get('saison');
                $type = $this->request->get('type');
                $auth = $this->session->get('auth');

                if ($type == "Aucun") {
                    $type = null;
                }
                if ($nom == "") {
                    $nom = null;
                }

                return Cartes::getTableCartes($nom, $saison, $type, $auth);
            }
        }
    }

    /**
     * Permet de gérer l'affichage de la page pour les cartes
     * @return unknown
     */
    public function detailCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;
                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/carte/creationCarte');
                } else {
                    if ($mode == "consultation") {
                        $this->view->carte = Cartes::findById($this->request->get('id'));
                        $retour = $this->view->partial('gameplay/formulaire/carte/detailCarte');
                    } else {
                        if ($mode == "edition") {
                            $this->view->carte = Cartes::findById($this->request->get('id'));
                            $retour = $this->view->partial('gameplay/formulaire/carte/editionCarte');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('gameplay/formulaire/carte/listeCartes');
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
     * Permet de charger les spécificités de la carte en fonction de son type
     * @return string
     */
    public function chargerDivTypeCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $type = $this->request->get('type');
                $mode = $this->request->get('mode');
                $carte = Cartes::findById($this->request->get('id') ?? -1);
                if ($carte != false) {
                    return $carte->genererBlocSpecifiqueTypeCarte($mode);
                }
                return Cartes::genererBlocSpecifiqueTypeCarteVide($type);
            }
        }
    }

    /**
     * Montre la liste des effets associés à la carte
     * @return unknown
     */
    public function showlisteEffetsCartesAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $carte = Cartes::findById($this->request->get('id'));
                $this->view->auth = $auth;
                $this->view->element = $carte;
                $this->view->type = "carte";
                $retour = $this->view->partial('utils/effets/listeEffetsAvances');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Montre la liste des statistiques associés à la carte
     * @return unknown
     */
    public function showlisteStatistiqueCartesAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $carte = Cartes::findById($this->request->get('id'));
                $this->view->auth = $auth;
                $this->view->element = $carte;
                $this->view->type = "carte";
                $retour = $this->view->partial('gameplay/formulaire/carte/statistiques');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de charger la carte dans une matrice par morceaux
     * @return string
     */
    public function chargerCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $carte = Cartes::findById($this->request->get('id'));

                if ($this->request->get('first', 'string', 'first') == 'first') {
                    //On supprime l'ancienne carte
                    $carte->cleanMatrice();
                }
                $nbDone = $this->request->get('nbDone', 'int', 0);
                $nbFinal = ($carte->xMax - $carte->xMin) * ($carte->yMax - $carte->yMin);
                if ($nbDone > $nbFinal) {
                    $carte->isChargee = 1;
                    $carte->save();
                    //Logs de l'action
                    $logDev = new LogsDEV();
                    $logDev->action = "Chargement de la carte : " . $carte->nom;
                    $logDev->idPersonnage = $auth['perso']->id;
                    $logDev->dateLog = time();
                    $logDev->typeLog = LogsDEV::TYPE_GESTION_MAP;
                    $logDev->save();
                    $retour = "done";
                } else {
                    //Verification sur la matrice et construction
                    $retour = $carte->buildMatrice($nbDone);

                    if ($retour != "success") {
                        //En cas d'échec, la carte aura bien été supprimée mais pas rechargée.
                        $carte->isChargee = 0;
                        $carte->save();
                    }
                }

                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de modifier la carte
     * @return string
     */
    public function modifierCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $carte = Cartes::findById($this->request->get('idCarte'));
                $nom = $this->request->get('nom');

                //Verification du nom
                if ($carte->nom != $nom) {
                    $verif = Cartes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Verification de la disponibilité de la zone
                $type = $this->request->get('typeCarte');
                $xmin = $this->request->get('xmin');
                $ymin = $this->request->get('ymin');
                $xmax = $this->request->get('xmax');
                $ymax = $this->request->get('ymax');

                $modifLoc = false;
                if ($carte->xMin != $xmin && $carte->xMax != $xmax && $carte->yMax != $ymax && $carte->yMin != $yMin) {
                    $modifLoc = true;
                }

                //Log de l'action
                $action = "Modification de la carte " . $carte->nom . " (old : " . $carte->toString() . ", new :";
                //Si on a changé la localisation, il faut clean la matrice
                if ($modifLoc) {
                    $carte->cleanMatrice();
                    $carte->isCharge = 0;
                }
                //Creation de la carte
                $carte->nom = $nom;
                $carte->description = $this->request->get('description');
                $carte->saison = $this->request->get('saison');
                $carte->type = $type;
                $idCarteCreature = $this->request->get('idCarteCreature');
                if ($idCarteCreature != 0) {
                    $carte->idCarteCreature = $idCarteCreature;
                }
                $carte->idVille = $this->request->get('idVille');
                $carte->idReligion = $this->request->get('idReligion');
                $carte->xRef = $this->request->get('xref');
                $carte->yRef = $this->request->get('yref');
                $carte->xMax = $xmax;
                $carte->xMin = $xmin;
                $carte->yMax = $ymax;
                $carte->yMin = $ymin;
                $carte->image = $this->request->get('image');
                $carte->decouverte = $this->request->get('decouverte');
                if (strpos($this->request->get('imageCartePJ'), "default") !== false) {
                    $carte->cartePJ = $this->request->get('imageCartePJ');
                }
                $carte->save();

                //Chargement de la carte dans une matrice
                if ($modifLoc) {
                    $carte->buildMatrice();
                    $carte->isCharge = 1;
                    $carte->save();
                    return "recharge";
                }

                //On met éventuellement à jour les coordonnées de la ville
                if ($carte->idVille != null || $carte->idVille != "") {
                    $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $carte->idVille]]);
                    if ($ville != false) {
                        $ville->xMin = $carte->xMin;
                        $ville->xMax = $carte->xMax;
                        $ville->yMin = $carte->yMin;
                        $ville->yMax = $carte->yMax;
                        $ville->save();
                    }
                }

                //On recharge la map
                $carte = Cartes::findById($carte->id);
                //Log de l'action
                $logDev = new LogsDEV();
                $logDev->action = $action . " " . $carte->toString() . " )";
                $logDev->idPersonnage = $auth['perso']->id;
                $logDev->dateLog = time();
                $logDev->typeLog = LogsDEV::TYPE_GESTION_MAP;
                $logDev->save();

                return "success";
            }
        }
    }

    /**
     * Permet de créer la carte
     * @return string|number
     */
    public function creerCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');

                //Verification du nom
                $verif = Cartes::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Verification de la disponibilité de la zone
                $type = $this->request->get('typeCarte');
                $xmin = $this->request->get('xmin');
                $ymin = $this->request->get('ymin');
                $xmax = $this->request->get('xmax');
                $ymax = $this->request->get('ymax');

                //Creation de la carte
                $carte = new Cartes();
                $carte->nom = $nom;
                $carte->description = $this->request->get('description');
                $carte->saison = $this->request->get('saison');
                $carte->type = $type;
                $idCarteCreature = $this->request->get('idCarteCreature');
                if ($idCarteCreature != 0) {
                    $carte->idCarteCreature = $idCarteCreature;
                }
                $carte->idVille = $this->request->get('idVille');
                $carte->idReligion = $this->request->get('idReligion');
                $carte->xRef = $this->request->get('xref');
                $carte->yRef = $this->request->get('yref');
                $carte->xMax = $xmax;
                $carte->xMin = $xmin;
                $carte->yMax = $ymax;
                $carte->yMin = $ymin;
                $carte->image = $this->request->get('image');
                $carte->decouverte = $this->request->get('decouverte');
                if (strpos($this->request->get('imageCartePJ'), "default") !== false) {
                    $carte->cartePJ = $this->request->get('imageCartePJ');
                }
                $carte->save();

                //On recharge la map
                $carte = Cartes::findById($carte->id);

                //Log de l'action
                $logDev = new LogsDEV();
                $logDev->action = "Création de la carte : " . $carte->nom . " ( " . $carte->toString() . " )";
                $logDev->idPersonnage = $auth['perso']->id;
                $logDev->dateLog = time();
                $logDev->typeLog = LogsDEV::TYPE_GESTION_MAP;
                $logDev->save();

                //Chargement de la carte dans une matrice
                $isCharge = $this->request->get('isCharge');
                if ($isCharge == 1) {
                    $carte->buildMatrice();
                    $carte->isChargee = 1;
                } else {
                    $carte->isChargee = 0;
                }
                $carte->save();

                //On met éventuellement à jour les coordonnées de la ville
                if ($carte->idVille != null && $carte->idVille != "") {
                    $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $carte->idVille]]);
                    if ($ville != false) {
                        $ville->xMin = $carte->xMin;
                        $ville->xMax = $carte->xMax;
                        $ville->yMin = $carte->yMin;
                        $ville->yMax = $carte->yMax;
                        $ville->save();
                    }
                }

                return $carte->id;
            }
        }
    }

    /**
     * Permet de supprimer la carte
     * @return string
     */
    public function supprimerCarteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $carte = Cartes::findById($this->request->get('id'));

                //On commence par nettoyer les matrices de la carte
                $carte->cleanMatrice();

                //On met éventuellement à jour les coordonnées de la ville
                if ($carte->idVille != null || $carte->idVille != "") {
                    $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $carte->idVille]]);
                    if ($ville != false) {
                        $ville->xMin = 0;
                        $ville->xMax = 0;
                        $ville->yMin = 0;
                        $ville->yMax = 0;
                        $ville->save();
                    }
                }

                //Log de l'action
                $action = "Suppression de la carte " . $carte->nom;

                $carte->delete();
                $logDev = new LogsDEV();
                $logDev->action = $action;
                $logDev->idPersonnage = $auth['perso']->id;
                $logDev->dateLog = time();
                $logDev->typeLog = LogsDEV::TYPE_GESTION_MAP;
                $logDev->save();
                return "success";
            }
        }
    }

    /**
     * Permet d'afficher l'image correspondant à la carte des créatures choisies
     * @return unknown
     */
    public function afficherCarteCreatureAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $carte = Cartes::findById($this->request->get('id'));

                if (isset($carte->image) && $carte->image != null) {
                    $retour = Phalcon\Tag::image([$carte->image, "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
                } else {
                    $retour = Phalcon\Tag::image(['public/img/cartes/default.jpg', "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
                }
                return $retour;
            }
        }
    }

    //########### Gestion de la magie ##########//
    // Type de Magie
    /**
     * Permet de dispatcher les écrans de nature de magie
     * @return unknown
     */
    public function detailTypesMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/magie/creationTypeMagie');
                } else {
                    if ($mode == "consultation") {
                        $this->view->natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/magie/detailTypeMagie');
                    } else {
                        if ($mode == "modification") {
                            $this->view->natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/magie/editionTypeMagie');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('gameplay/formulaire/magie/listeTypesMagie');
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
     * Permet d'ajouter une école de magie à un type de magie
     * @return string
     */
    public function ajouterEcoleNatureMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idNatureMagie = $this->request->get('idNatureMagie');
                $idEcoleMagie = $this->request->get('idEcole');

                $ecoleMagie = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcoleMagie]]);
                if ($ecoleMagie != false) {
                    $ecoleMagie->idNatureMagie = $idNatureMagie;
                    $ecoleMagie->save();
                    $natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idNatureMagie]]);
                    //Logs de l'action
                    $logAdmin = new LogsDEV();
                    $logAdmin->action = "Ajout de l'école : " . $ecoleMagie->nom . " au type de magie : " . $natureMagie->nom;
                    $logAdmin->idPersonnage = $auth['perso']->id;
                    $logAdmin->dateLog = time();
                    $logAdmin->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                    $logAdmin->save();

                    //Retourne le tableau des écoles mise à jours
                    $this->view->disable();
                    return $natureMagie->genererListeEcolesMagie($auth);
                } else {
                    return "errorEcole";
                }
            }
        }
    }

    /**
     * Permet de mettre à jour la liste des écoles disponibles
     * @return string
     */
    public function majListeEcoleDisponibleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idNatureMagie = $this->request->get('idNatureMagie');

                $natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idNatureMagie]]);
                $this->view->disable();
                return $natureMagie->genererSelectListeEcolesAutorisees();
            }
        }
    }

    /**
     * Permet de supprimer une école de magie
     * du type de magie
     * @return string
     */
    public function supprimerEcoleNatureMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idNatureMagie = $this->request->get('idNatureMagie');
                $idEcoleMagie = $this->request->get('idEcole');

                $ecoleMagie = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcoleMagie]]);
                if ($ecoleMagie != false) {
                    $ecoleMagie->idNatureMagie = null;
                    $ecoleMagie->save();
                    $natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idNatureMagie]]);
                    //Logs de l'action
                    $logDEV = new LogsDEV();
                    $logDEV->action = "Suppression de l'école : " . $ecoleMagie->nom . " du type de magie : " . $natureMagie->nom;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                    $logDEV->save();

                    //Retourne le tableau des écoles mise à jours
                    $this->view->disable();
                    return $natureMagie->genererListeEcolesMagie($auth);
                } else {
                    return "errorEcole";
                }
            }
        }
    }

    /**
     * Permet de modifier un type de magie
     * @return string
     */
    public function modifierTypeMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $natureMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idNatureMagie')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                if ($natureMagie->nom != $nom) {
                    $verif = Naturesmagie::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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
                $ancienNom = $natureMagie->nom;
                $action = "Ancien type de magie :" . $natureMagie->toString();

                //Mise à jour
                $natureMagie->nom = $nom;
                $natureMagie->idArticle = $idArticle;
                $natureMagie->bloque = $this->request->get('isBloque');
                $natureMagie->couleur = $this->request->get('couleur');
                $natureMagie->description = $this->request->get('description');
                $natureMagie->image = $this->request->get('image');
                $natureMagie->typeNatureMagie = $this->request->get('type');
                $natureMagie->fichier = str_replace(".php", "", $this->request->get('fichier'));
                $natureMagie->isDispoInscription = $this->request->get('isDispoInscription');
                $natureMagie->save();

                //Log de l'action
                $action = "Modification du type de magie : " . $natureMagie->nom . " (" . $action . ", Nouveau Royaume : " . $natureMagie->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return "success";
            }
        }
    }

    /**
     * Permet de créer un type de magie
     * @return string|number
     */
    public function creerTypeMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Naturesmagie::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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

                //Mise à jour
                $natureMagie = new Naturesmagie();
                $natureMagie->nom = $nom;
                $natureMagie->idArticle = $idArticle;
                $natureMagie->bloque = $this->request->get('isBloque');
                $natureMagie->couleur = $this->request->get('couleur');
                $natureMagie->description = $this->request->get('description');
                $natureMagie->image = $this->request->get('image');
                $natureMagie->typeNatureMagie = $this->request->get('type');
                $natureMagie->fichier = str_replace(".php", "", $this->request->get('fichier'));
                $natureMagie->isDispoInscription = $this->request->get('isDispoInscription');
                $natureMagie->save();

                //Log de l'action
                $action = "Création du type de magie : " . $natureMagie->nom . " (" . $natureMagie->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return $natureMagie->id;
            }
        }
    }

    // Ecoles de Magie

    /**
     * Permet de dispatcher les écrans pour les écoles de magie
     * @return unknown
     */
    public function detailEcolesMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/magie/creationEcoleMagie');
                } else {
                    if ($mode == "consultation") {
                        $this->view->ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/magie/detailEcoleMagie');
                    } else {
                        if ($mode == "modification") {
                            $this->view->ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/magie/editionEcoleMagie');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('gameplay/formulaire/magie/listeEcolesMagie');
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
     * Permet de modifier une école de magie
     * @return string
     */
    public function modifierEcoleMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEcoleMagie')]]);
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                if ($ecole->nom != $nom) {
                    $verif = Ecolesmagie::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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
                $ancienNom = $ecole->nom;
                $action = "Ancienne école de magie :" . $ecole->toString();

                //Mise à jour
                $ecole->nom = $nom;
                $ecole->idArticle = $idArticle;
                $ecole->bloque = $this->request->get('isBloque');
                $ecole->couleur = $this->request->get('couleur');
                $ecole->description = $this->request->get('description');
                $ecole->image = $this->request->get('image');
                $ecole->idNatureMagie = $this->request->get('idNatureMagie');
                $ecole->fichier = str_replace(".php", "", $this->request->get('fichier'));
                $ecole->isDispoInscription = $this->request->get('isDispoInscription');
                $ecole->idCompetence = $this->request->get('idCompetence');
                $ecole->save();

                //Log de l'action
                $action = "Modification de l'école de magie : " . $ecole->nom . " (" . $action . ", Nouvelle école : " . $ecole->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return "success";
            }
        }
    }

    /**
     * Permet de créer une école de magie
     * @return string|number
     */
    public function creerEcoleMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Ecolesmagie::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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
                $ecole = new Ecolesmagie();
                $ecole->nom = $nom;
                $ecole->idArticle = $idArticle;
                $ecole->bloque = $this->request->get('isBloque');
                $ecole->couleur = $this->request->get('couleur');
                $ecole->description = $this->request->get('description');
                $ecole->image = $this->request->get('image');
                $ecole->idNatureMagie = $this->request->get('idNatureMagie');
                $ecole->fichier = str_replace(".php", "", $this->request->get('fichier'));
                $ecole->isDispoInscription = $this->request->get('isDispoInscription');
                $ecole->idCompetence = $this->request->get('idCompetence');
                $ecole->save();

                //Log de l'action
                $action = "Création l'école de magie : " . $ecole->nom . " (" . $ecole->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return $ecole->id;
            }
        }
    }

    /**
     * Permet de retirer un sort de l'école de magie
     * @return string
     */
    public function retirerSortAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idSort = $this->request->get('id');

                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idSort]]);
                $idOldEcole = $sort->idEcoleMagie;
                $sort->idEcoleMagie = null;
                $sort->save();
                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idOldEcole]]);

                //Log de l'action
                $action = "Suppression du sort " . $sort->nom . " pour l'école " . $ecole->nom;
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return $ecole->genererListeSorts($auth);
            }
        }
    }

    /**
     * Permet d'ajouter un sort à l'école de magie
     * @return string
     */
    public function ajouterSortEcoleMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idEcole = $this->request->get('idEcole');
                $idSort = $this->request->get('idSort');

                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idSort]]);
                $sort->idEcoleMagie = $idEcole;
                $sort->save();
                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcole]]);

                //Log de l'action
                $action = "Ajout du sort " . $sort->nom . " pour l'école " . $ecole->nom;
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return $ecole->genererListeSorts($auth);
            }
        }
    }

    /**
     * Met à jour la liste des sorts disponibles
     * @return string
     */
    public function majListeSortDisponibleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idEcole = $this->request->get('idEcole');

                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcole]]);
                $this->view->disable();
                return $ecole->genererSelectListeSortsAutorises();
            }
        }
    }

    // Sorts

    /**
     * Permet de dispatcher l'affichage des écrans des sorts
     * @return unknown
     */
    public function detailSortAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/magie/creationSort');
                } else {
                    if ($mode == "consultation") {
                        $this->view->sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/magie/detailSort');
                    } else {
                        if ($mode == "modification") {
                            $this->view->sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/magie/editionSort');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('gameplay/formulaire/magie/listeSorts');
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
     * Montre la liste des effets liés au sort
     * @return string|void
     */
    public function showListeEffetsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $sort;
                $this->view->type = $this->request->get('type');
                $retour = $this->view->partial('utils/effets/listeEffets');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Montre la liste des contraintes liées au sort
     * @return string
     */
    public function showlisteContraintesAction() {
        //TODO Refaire le shmilblick
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $this->view->disable();
                return $sort->genererListeContraintes($auth);
            }
        }
    }

    /**
     * Montre l'écran des évolutions du sort
     * @return unknown
     */
    public function showlisteEvolutionsAction() {
        //TODO à faire
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $this->view->disable();
                return $sort->genererListeEvolutions($auth);
            }
        }
    }

    /**
     * Permet de créer un sort
     * @return string|number
     */
    public function creerSortAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Sorts::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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
                $sort = new Sorts();
                $sort->nom = $nom;
                $sort->description = $this->request->get('description');
                $sort->image = $this->request->get('image');
                $sort->messageRP = $this->request->get('messageRP');
                $sort->idArticle = $idArticle;
                $sort->idEcoleMagie = $this->request->get('idEcole');
                $sort->arcane = $this->request->get('arcane');
                $sort->mana = $this->request->get('mana');
                $sort->portee = $this->request->get('portee');
                $sort->coutPA = $this->request->get('pa');

                $tableCiblage = new Tableciblage();
                $tableCiblage->save();
                $sort->idCiblage = $tableCiblage->id;

                $duree = $this->request->get('duree');
                if ($duree == "Instantané ") {
                    $sort->duree = 0;
                } else {
                    $sort->duree = $duree;
                }

                $dureeMax = $this->request->get('dureeMax');
                if ($dureeMax == "Non concerné ") {
                    $sort->cumulDuree = 0;
                } else {
                    $sort->cumulDuree = $dureeMax;
                }

                $cumulQuantite = $this->request->get('cumulQuantite');
                if ($cumulQuantite == "Non concerné ") {
                    $sort->cumulQuantite = 0;
                } else {
                    $sort->cumulQuantite = $cumulQuantite;
                }
                $sort->isBloque = $this->request->get('isBloque');
                $sort->esquivable = $this->request->get('esquivable');
                $sort->enseignable = $this->request->get('enseignable');
                $sort->retranscriptibleSort = $this->request->get('retranscriptible');
                $sort->isJS = $this->request->get('isJS');
                $sort->isJSEV = $this->request->get('isJSEV');
                $sort->eventLanceur = $this->request->get('eventLanceur');
                $sort->eventGlobal = $this->request->get('eventGlobal');
                $sort->save();

                //Log de l'action
                $action = "Création du sort : " . $sort->nom . " (" . $sort->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();

                return $sort->id;
            }
        }
    }

    /**
     * Permet de modifier un sort
     * @return string
     */
    public function modifierSortAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $nom = $this->request->get('nom');
                //Vérification de l'unicité du nom
                if ($sort->nom != $nom) {
                    $verif = Sorts::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
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

                $action = "Ancien : " . $sort->toString();
                //Modification
                $sort->nom = $nom;
                $sort->description = $this->request->get('description');
                $sort->image = $this->request->get('image');
                $sort->messageRP = $this->request->get('messageRP');
                $sort->idArticle = $idArticle;
                $idEcole = $this->request->get('idEcole');
                if ($idEcole == "0") {
                    $idEcole = null;
                }
                $sort->idEcoleMagie = $idEcole;
                $sort->arcane = $this->request->get('arcane');
                $sort->mana = $this->request->get('mana');
                $sort->portee = $this->request->get('portee');
                $sort->coutPA = $this->request->get('pa');

                $duree = $this->request->get('duree');
                if ($duree == "Instantané ") {
                    $sort->duree = 0;
                } else {
                    $sort->duree = $duree;
                }

                $dureeMax = $this->request->get('dureeMax');
                if ($dureeMax == "Non concerné") {
                    $sort->cumulDuree = 0;
                } else {
                    $sort->cumulDuree = $dureeMax;
                }

                $cumulQuantite = $this->request->get('cumulQuantite');
                if ($cumulQuantite == "Non concerné") {
                    $sort->cumulQuantite = 0;
                } else {
                    $sort->cumulQuantite = $cumulQuantite;
                }
                $sort->isBloque = $this->request->get('isBloque');
                $sort->esquivable = $this->request->get('esquivable');
                $sort->enseignable = $this->request->get('enseignable');
                $sort->retranscriptibleSort = $this->request->get('retranscriptible');
                $sort->isJS = $this->request->get('isJS');
                $sort->isJSEV = $this->request->get('isJSEV');
                $sort->eventLanceur = $this->request->get('eventLanceur');
                $sort->eventGlobal = $this->request->get('eventGlobal');
                if ($sort->save() == false) {
                    exit("errorProduite");
                }

                //Log de l'action
                $action = "Modification du sort : " . $sort->nom . " (Nouveau : " . $sort->toString() . ", " . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                $logDEV->save();
                $this->view->disable();
            }
        }
    }

    /**
     * Permet de charger les particularités des natures de magie
     * @return unknown
     */
    public function chargeDivParticulariteTypeMagieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $idEcole = $this->request->get('idEcole');
                $idSort = $this->request->get('idSort');

                $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcole]]);
                if ($ecole != false && !empty($ecole->idNatureMagie)) {
                    $typeMagie = Naturesmagie::findFirst(['id = :id:', 'bind' => ['id' => $ecole->idNatureMagie]]);
                    if ($typeMagie != false && !empty($typeMagie->fichier)) {
                        $fichier = new $typeMagie->fichier();
                        return $fichier->genererInformationSort("edition", null);
                    }
                }
            }
        }
    }

    //########### Caractéristiques #############//

    /**
     * Permet de disptacher les écrans des caractéristiques
     * @return unknown
     */
    public function detailCaracAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/carac/creationCarac');
                } else {
                    if ($mode == "consultation") {
                        $this->view->carac = Caracteristiques::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/carac/detailCarac');
                    } else {
                        if ($mode == "edition") {
                            $this->view->carac = Caracteristiques::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/carac/editionCarac');
                        } else {
                            if ($mode == "liste") {
                                $retour = $this->view->partial('gameplay/formulaire/carac/listeCaracs');
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
     * Permet de modifier une caractéristique
     * @return string
     */
    public function modifierCaracAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $carac = Caracteristiques::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $nom = $this->request->get('nom');
                $trigramme = $this->request->get('trigramme');

                //Vérification de l'unicité du nom
                if ($carac->nom != $nom) {
                    $verif = Caracteristiques::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Vérification de l'unicité du trigramme
                if ($carac->nom != $nom) {
                    $verif = Caracteristiques::findFirst(['trigramme = :trigramme:', 'bind' => ['trigramme' => $trigramme]]);
                    if ($verif) {
                        return "errorTrigramme";
                    }
                }

                //Initialisation de l'action de logs
                $ancienNom = $carac->nom;
                $ancienType = $carac->type;
                if ($carac->type != $ancienType && $carac->type == Caracteristiques::CARAC_SECONDAIRE) {
                    return "errorChangementType";
                }

                $action = "Ancienne caractéristique :" . $carac->toString();

                //Mise à jour
                $carac->nom = $nom;
                $carac->trigramme = $trigramme;
                $carac->description = $this->request->get('description');
                $carac->image = $this->request->get('image');
                $carac->isModifiable = $this->request->get('isModifiable');
                $type = $this->request->get('type');;
                $carac->type = $type;
                $valMin = $this->request->get('valMin');
                if ($valMin == "" || empty($valMin)) {
                    $valMin = null;
                }
                $carac->valMin = $valMin;
                $valMax = $this->request->get('valMax');
                if ($valMax == "" || empty($valMax)) {
                    $valMax = null;
                }
                $carac->valMax = $valMax;
                if ($type == Caracteristiques::CARAC_SECONDAIRE) {
                    $carac->formule = $this->request->get('formule');
                }
                $carac->genre = $this->request->get('genre');
                $carac->save();

                //Log de l'action
                $action = "Modification de la caractéristique : " . $carac->nom . " (" . $action . ", Nouvelle caractéristique : " . $carac->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_CARAC;
                $logDEV->save();

                //Gestion des liens avec les compétences
                if ($carac->type != $ancienType && $carac->type == Caracteristiques::CARAC_PRIMAIRE) {
                    $listeCompetence = Competences::find();
                    if ($listeCompetence != false && count($listeCompetence) > 0) {
                        foreach ($listeCompetence as $competence) {
                            $assoc = new AssocCaracsCompetence();
                            $assoc->idCarac = $carac->id;
                            $assoc->idCompetence = $competence->id;
                            $assoc->isInfluenceur = false;
                            $assoc->modificateur = false;
                            $assoc->save();
                        }
                    }
                }

                return "success";
            }
        }
    }

    /**
     * Méthode permettant de créer une Caractéristique
     * @return string
     */
    public function creerCaracAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $trigramme = $this->request->get('trigramme');

                //Vérification de l'unicité du nom
                $verif = Caracteristiques::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Vérification de l'unicité du trigramme
                $verif = Caracteristiques::findFirst(['trigramme = :trigramme:', 'bind' => ['trigramme' => $trigramme]]);
                if ($verif) {
                    return "errorTrigramme";
                }

                //Mise à jour
                $carac = new Caracteristiques();
                $carac->nom = $nom;
                $carac->trigramme = $trigramme;
                $carac->description = $this->request->get('description');
                $carac->image = $this->request->get('image');
                $carac->isModifiable = $this->request->get('isModifiable');
                $type = $this->request->get('type');
                $carac->type = $type;
                $valMin = $this->request->get('valMin');
                if ($valMin == "" || empty($valMin)) {
                    $valMin = null;
                }
                $carac->valMin = $valMin;
                $valMax = $this->request->get('valMax');
                if ($valMax == "" || empty($valMax)) {
                    $valMax = null;
                }
                if ($type == Caracteristiques::CARAC_SECONDAIRE) {
                    $carac->formule = $this->request->get('formule');
                }
                $carac->genre = $this->request->get('genre');
                $carac->save();

                //Log de l'action
                $action = "Création de la caractéristique : " . $carac->nom . " (" . $carac->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_CARAC;
                $logDEV->save();

                //Création de l'assoc avec les compétence
                if ($carac->type == Caracteristiques::CARAC_PRIMAIRE) {
                    $listeCompetence = Competences::find();
                    if ($listeCompetence != false && count($listeCompetence) > 0) {
                        foreach ($listeCompetence as $competence) {
                            $assoc = new AssocCaracsCompetence();
                            $assoc->idCarac = $carac->id;
                            $assoc->idCompetence = $competence->id;
                            $assoc->isInfluenceur = false;
                            $assoc->modificateur = false;
                            $assoc->save();
                        }
                    }
                }
                return $carac->id;
            }
        }
    }


    //########### Terrains #############//

    /**
     * Permet de disptacher les écrans des terrains
     * @return unknown
     */
    public function detailTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/terrains/creationTerrain');
                } elseif ($mode == "consultation") {
                    $this->view->terrain = Terrains::findById($this->request->get('id'));
                    $retour = $this->view->partial('gameplay/formulaire/terrains/detailTerrain');
                } elseif ($mode == "edition") {
                    $this->view->terrain = Terrains::findById($this->request->get('id'));
                    $retour = $this->view->partial('gameplay/formulaire/terrains/editionTerrain');
                } elseif ($mode == "liste") {
                    $retour = $this->view->partial('gameplay/formulaire/terrains/listeTerrains');
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de disptacher les écrans des textures
     * @return unknown
     */
    public function detailTextureAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                $fileUpload = new Phalcon\Forms\Element\File("imageTexture", ["id" => "imageTexture"]);
                $this->view->fileUpload = $fileUpload->render();

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/textures/editionTexture');
                } elseif ($mode == "edition") {
                    $this->view->texture = Textures::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                    $retour = $this->view->partial('gameplay/formulaire/textures/editionTexture');
                } elseif ($mode == "liste") {
                    $retour = $this->view->partial('gameplay/formulaire/textures/listeTextures');
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de charger l'image pour le terrain
     * @return string
     */
    public function uploadImageUrlTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $terrain = Terrains::findById($this->request->get('id'));
                $type = $this->request->get('type');
                $url = $this->request->get('urlFile');

                $name = basename($url);
                list($txt, $ext) = explode(".", $name);
                //check if the files are only image / document
                if ($ext != "png") {
                    return "errorType";
                } else {
                    //On calcul le numéro de l'image
                    $tabName = array();
                    $dir = $terrain->repImage . $type . "/";
                    if ($dossier = opendir($dir)) {
                        while (false !== ($fichier = readdir($dossier))) {
                            if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                                $res = explode(".", $fichier);
                                $tabName[count($tabName)] = $res[0];
                            }
                        }
                        closedir($dossier);
                    }
                    $fichier = "1.png";
                    if (!empty($tabName)) {
                        sort($tabName);
                        $compteur = 1;
                        $verif = false;
                        for ($i = 0; $i < count($tabName); $i++) {
                            if (!$verif) {
                                if ($compteur != $tabName[$i]) {
                                    $verif = true;
                                    $fichier = $compteur . ".png";
                                } else {
                                    $compteur++;
                                }
                            }
                        }
                        if (!$verif) {
                            $fichier = (count($tabName) + 1) . ".png";
                        }
                    }
                    $resultUpload = copy($url, $dir . $fichier);

                    if (!$resultUpload) {
                        return "errorUpload";
                    } else {
                        //Log de l'action
                        $action = "Ajout de l'image : " . $fichier . " (type : " . $type . ")sur le terrain " . $terrain->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                        $logDEV->save();

                        $retour = $terrain->genererListeImagesTerrains($type, "edition");
                        $this->view->disable();
                        return $retour;
                    }
                }
            }
        }
    }

    /**
     * Affiche la liste des effets sur le terrain
     * @return unknown
     */
    public function showlisteEffetsTerrainsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $terrain = Terrains::findById($this->request->get('id'));
                $this->view->auth = $auth;
                $this->view->element = $terrain;
                $this->view->type = "terrain";
                $retour = $this->view->partial('utils/effets/listeEffetsAvances');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Affiche la liste des artisanats sur le terrain
     * @return string
     */
    public function showlisteArtisanatTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $terrain = Terrains::findById($this->request->get('id'));
                $this->view->auth = $auth;
                $this->view->element = $terrain;
                $this->view->type = "terrain";
                //TODO
                //$retour = $this->view->partial('utils/effets/listeEffetsAvances');
                $retour = "TODO Artisanat";
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Effectue une recherche par saison
     * @return string
     */
    public function lancerRechercheSaisonAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $nom = $this->request->get('nom');
                $saison = $this->request->get('saison');

                if (!empty($nom) && $nom != null && $nom != null) {
                    $resultat = Terrains::find(['nom = :nom: AND saison = :saison:', 'bind' => ['nom' => $nom, 'saison' => $saison]]);
                } else {
                    if (saison == Constantes::SAISON_TOUTES) {
                        $retour = $this->view->partial('gameplay/formulaire/terrains/listeTerrains');
                    } else {
                        $resultat = Terrains::find(['saison = :saison:', 'bind' => ['saison' => $saison]]);
                    }
                }
                if ($resultat != false && count($resultat) > 0) {
                    if (count($resultat) == 1) {
                        $retour = "id" . $resultat->id;
                    } else {
                        $retour = Terrains::genererListeTerrains($resultat);
                    }
                } else {
                    $retour = "<span id='listeTerrainVide'>Votre recherche ne retourne aucun résultat.</span>";
                }

                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de modifier le terrain
     * @return string
     */
    public function modifierTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $terrain = Terrains::findById($this->request->get('id'));
                $nom = $this->request->get('nom');
                $couleur = $this->request->get('couleur');
                $repartition = $this->request->get('repartition');
                $saison = $this->request->get('saison');

                //Vérification de l'unicité du nom
                $flagChangementNom = false;
                if ($terrain->nom != $nom) {
                    $verif = Terrains::findFirst(['nom = :nom: AND saison = :saison:', 'bind' => ['nom' => $nom, 'saison' => $saison]]);
                    if ($verif) {
                        return "errorNom";
                    }
                    $flagChangementNom = true;
                }
                //Vérification de l'unicité de la couleur
                if ($terrain->couleur != $couleur) {
                    $verif = Terrains::findByCouleur($couleur);
                    if ($verif) {
                        return "errorColor";
                    }
                }

                //Vérification de la répartition
                $verif = Terrains::checkFormatRepartition($repartition);
                if (!$verif) {
                    return "errorRepartition";
                }
                //Initialisation de l'action de logs
                $ancienNom = $terrain->nom;
                $action = "Ancien terrain :" . $terrain->toString();
                //Mise à jour
                $terrain->nom = $nom;
                $terrain->couleur = $couleur;
                $terrain->description = $this->request->get('description');
                $terrain->genre = $this->request->get('genre');
                $terrain->saison = $saison;
                $terrain->typeAcces = $this->request->get('typeAcces');
                if ($this->request->get('idCompetence') == "0") {
                    $terrain->idCompetence = null;
                } else {
                    $terrain->idCompetence = $this->request->get('idCompetence');
                }
                $terrain->baseMvt = $this->request->get('mvt');
                $terrain->baseVision = $this->request->get('vision');
                $terrain->zIndex = $this->request->get('zindex');
                $terrain->bloqueVue = $this->request->get('bloquevue');
                $terrain->bloqueMvt = $this->request->get('bloquemvt');
                $terrain->repartition = $repartition;
                //Gesion du répertoire
                if ($flagChangementNom) {
                    $oldRepertoire = $terrain->repImage . $ancienNom;
                    $newRepertoire = $terrain->repImage . $nom;
                    rename(utf8_decode($oldRepertoire), utf8_decode($newRepertoire));
                    $terrain->repImage = $newRepertoire;
                }
                $retour = $terrain->save();
                //Log de l'action5
                $action = "Modification du terrain : " . $terrain->nom . " (" . $action . ", Nouveau terrain : " . $terrain->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                $logDEV->save();

                $this->view->disable();
                return "success";
            }
        }
    }

    /**
     * Permet de modifier ou créer le terrain
     * @return string
     */
    public function modifierTextureAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $id = $this->request->get('id');
                if ($id && $id !== "undefined") {
                    $texture = Textures::getFromId($this->request->get('id'));
                } else {
                    $texture = new Textures();
                }
                if ($this->request->get('mode') === "suppression" && $texture) {
                    unlink($texture->image);
                    $texture->delete();
                    return "success";
                }

                if ($this->request->hasFiles()) {
                    $files = $this->request->getUploadedFiles();
                    foreach ($files as $file) {
                        if ($texture->image !== null) {
                            unlink($texture->image);
                        }
                        $texture->image = BASE_PATH . '/public/img/site/interface/textures/' . $file->getName();
                        if (file_exists($texture->image)) {
                            return "imageExist";
                        }
                        // Move the file into the application
                        $file->moveTo($texture->image);
                    }
                }

                //Mise à jour
                $texture->nom = $this->request->get('nom');

                $texture->save();
                //Log de l'action
                if ($id) {
                    $action = "Modification de la texture : " . $texture->nom . " (Ancienne texture :" . $texture->toString() . ", Nouvelle texture : " . $texture->toString() . ")";
                } else {
                    $action = "Création de la texture : " . $texture->nom . " (Nouvelle texture : " . $texture->toString() . ")";
                }
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                $logDEV->save();

                $this->view->disable();
                return "success";
            }
        }
        return "error";
    }

    /**
     * Méthode permettant de créer un terrain
     * @return string|unknown
     */
    public function creerTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $couleur = $this->request->get('couleur');

                //Vérification de l'unicité du nom
                $verif = Terrains::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Vérification de l'unicité de la couleur
                $verif = Terrains::findByCouleur($couleur);
                if ($verif) {
                    return "errorColor";
                }

                //Création
                $terrain = new Terrains();
                $terrain->nom = $nom;
                $terrain->couleur = $couleur;
                $terrain->description = $this->request->get('description');
                $terrain->genre = $this->request->get('genre');
                $terrain->saison = $this->request->get('saison');
                $terrain->typeAcces = $this->request->get('typeAcces');
                if ($this->request->get('idCompetence') == "0") {
                    $terrain->idCompetence = null;
                } else {
                    $terrain->idCompetence = $this->request->get('idCompetence');
                }
                $terrain->baseMvt = $this->request->get('mvt');
                $terrain->baseVision = $this->request->get('vision');
                $terrain->zIndex = $this->request->get('zindex');
                $terrain->bloqueVue = $this->request->get('bloquevue');
                $terrain->bloqueMvt = $this->request->get('bloquemvt');
                $terrain->repartition = 100;

                //Gesion des répertoires
                $dir = $this->getDI()->get('config')->application->terrainDir . $terrain->nom . '/';
                mkdir(utf8_decode($dir));
                chmod(utf8_decode($dir), 0777);
                mkdir(utf8_decode($dir . "nuit/"));
                chmod(utf8_decode($dir . "nuit/"), 0777);
                mkdir(utf8_decode($dir . "jour/"));
                chmod(utf8_decode($dir . "jour/"), 0777);
                mkdir(utf8_decode($dir . "jourBrouillard/"));
                chmod(utf8_decode($dir . "jourBrouillard/"), 0777);
                mkdir(utf8_decode($dir . "nuitBrouillard/"));
                chmod(utf8_decode($dir . "nuitBrouillard/"), 0777);
                $terrain->repImage = $dir;
                $terrain->save();

                //Log de l'action
                $action = "Création du terrain : " . $terrain->nom . " (" . $terrain->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                $logDEV->save();

                $this->view->terrain = Terrains::findById($terrain->id);
                $this->view->auth = $auth;
                $this->view->mode = "edition";
                $retour = $this->view->partial('gameplay/formulaire/terrains/editionTerrain');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de supprimer une image des terrains
     * @return string
     */
    public function deleteImageTerrainAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $terrain = Terrains::findById($this->request->get('idTerrain'));
                $image = $this->request->get('image');
                $type = $this->request->get('type');

                $file = $terrain->repImage . $type . "/" . $image;

                //Suppression de l'image
                $file = str_replace("\\", "/", $file);
                $retour = unlink($file);

                //Log de l'action
                $action = "Suppresion de l'image : " . $image . " pour le terrain " . $terrain->nom;
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                $logDEV->save();

                $retour = $terrain->genererListeImagesTerrains($type, "edition");
                return $retour;

            }
        }
    }


    //########### Talents #############//
    /* Catégories */
    /**
     * Permet de dispatcher les écrans pour les catégories
     * @return unknown
     */
    public function detailCategorieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/talents/categories/creationCategorie');
                } else {
                    if ($mode == "consultation") {
                        $this->view->categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/talents/categories/detailCategorie');
                    } else {
                        if ($mode == "edition") {
                            $this->view->categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/talents/categories/editionCategorie');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer une nouvelle catégorie
     * @return string|number
     */
    public function creerCategorieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $this->view->disable();
                //Vérification de l'unicité du nom
                $verif = CategoriesTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Création de l'objet
                $categorie = new CategoriesTalent();
                $categorie->nom = $nom;
                $categorie->description = $this->request->get('description');
                $categorie->image = $this->request->get('image');
                $categorie->save();

                //Log de l'action
                $action = "Création de la catégorie pour les talents : " . $categorie->nom . " (" . $categorie->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                $this->view->categorie = $categorie;
                $this->view->auth = $auth;

                return $categorie->id;
            }
        }
    }

    /**
     * Permet de modifier une catégorie
     * @return string
     */
    public function modifierCategorieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                //Vérification de l'unicité du nom
                if ($nom != $categorie->nom) {
                    $verif = CategoriesTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Log de l'ancienne catégorie
                $action = $categorie->toString();
                //Création de l'objet
                $categorie->nom = $nom;
                $categorie->description = $this->request->get('description');
                $categorie->image = $this->request->get('image');
                $categorie->save();

                //Log de l'action
                $action = "Modification de la catégorie pour les talents : " . $categorie->nom . " (new :" . $categorie->toString() . ", old :" . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                $this->view->disable();
            }
        }
    }

    /**
     * Permet de supprimer une catégorie
     */
    public function supprimerCategorieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($categorie != false) {
                    $action = $categorie->supprimerCategorie();
                    //Log de l'action
                    $action = $action;
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    $logDEV->save();
                }
            }
        }
    }

    /**
     * Permet d'afficher les contraintes liées à une catégorie
     * @return unknown
     */
    public function showlisteContraintesCategorieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $categorie;
                $this->view->type = $this->request->get('type');
                $retour = $this->view->partial('utils/contraintes/listeContraintes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de charger la page des talents à partir d'une catégorie
     */
    public function chargerPageTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $famille = FamillesTalent::findFirst(["idCategorie = :idCategorie:", "bind" => ['idCategorie' => $categorie->id]]);
                if ($famille == false) {
                    $famille = null;
                }

                $this->view->categorie = $categorie;
                $this->view->famille = $famille;
                $this->view->pick("gameplay/gestionTalents");
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                $this->view->auth = $auth;
                $this->pageview();
            }
        }
    }

    /* Familles */
    /**
     * Permet de dispatcher les écrans pour les familes
     * @return unknown
     */
    public function detailFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idCategorie')]]);

                $this->view->auth = $auth;
                $this->view->mode = $mode;
                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/talents/familles/creationFamille');
                } else {
                    if ($mode == "consultation") {
                        $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        if ($categorie == false) {
                            $categorie = $famille->categorie;
                        }
                        $this->view->famille = $famille;
                        $this->view->categorie = $categorie;
                        $retour = $this->view->partial('gameplay/formulaire/talents/familles/detailFamille');
                    } else {
                        if ($mode == "edition") {
                            $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            if ($categorie == false) {
                                $categorie = $famille->categorie;
                            }
                            $this->view->famille = $famille;
                            $this->view->categorie = $categorie;
                            $retour = $this->view->partial('gameplay/formulaire/talents/familles/editionFamille');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer une famille
     * @return string|number
     */
    public function creerFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $this->view->disable();
                //Vérification de l'unicité du nom
                $verif = FamillesTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Création de l'objet
                $famille = new FamillesTalent();
                $famille->nom = $nom;
                $famille->description = $this->request->get('description');
                $famille->idCategorie = $this->request->get('idCategorie');
                $famille->save();

                //Log de l'action
                $action = "Création de la famille pour les talents : " . $famille->nom . " (" . $famille->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                return $famille->id;
            }
        }
    }

    /**
     * Permet de modifier une famille
     * @return string|number
     */
    public function modifierFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->disable();

                //Vérification de l'unicité du nom
                if ($famille->nom != $nom) {
                    $verif = FamillesTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                $action = $famille->toString();
                //Modification de l'objet
                $famille->nom = $nom;
                $famille->description = $this->request->get('description');
                $famille->save();

                //Log de l'action
                $action = "Modification de la famille pour les talents : " . $famille->nom . " ( new :" . $famille->toString() . ", old :" . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                return $famille->id;
            }
        }
    }

    /**
     * Permet d'afficher la liste des contraintes
     * @return unknown
     */
    public function showlisteContraintesFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $famille;
                $this->view->type = $this->request->get('type');
                $retour = $this->view->partial('utils/contraintes/listeContraintes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de supprimer une famille
     */
    public function supprimerFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($famille != false) {
                    $action = $famille->supprimerFamille();
                    //Log de l'action
                    $action = $action;
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    $logDEV->save();
                }
            }
        }
    }

    /**
     * Perme de recharger une famille
     * @return unknown
     */
    public function chargerFamilleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($this->request->get('idCategorie') != null) {
                    $categorie = CategoriesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idCategorie')]]);
                } else {
                    $categorie = $famille->categorie;
                }
                $this->view->categorie = $categorie;
                $this->view->famille = $famille;
                $this->view->auth = $auth;
                $this->view->simulation = $this->request->get('simulation');
                $retour = $this->view->partial('gameplay/formulaire/talents/generalTalent');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /* Arbres */
    /**
     * Permet de créer un arbre
     * @return string|number
     */
    public function creerArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $this->view->disable();

                //Vérification de l'unicité du nom
                $verif = ArbresTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Création de l'objet
                $arbre = new ArbresTalent();
                $arbre->nom = $nom;
                $arbre->description = $this->request->get('description');
                $arbre->idFamille = $this->request->get('idFamille');
                $arbre->image = $this->request->get('image');
                $arbre->save();

                //Log de l'action
                $action = "Création de l'arbre pour les talents : " . $arbre->nom . " (" . $arbre->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                return $arbre->id;
            }
        }
    }

    /**
     * Permet de modifier un arbre
     * @return string|number
     */
    public function modifierArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->disable();

                //Vérification de l'unicité du nom
                if ($arbre->nom != $nom) {
                    $verif = ArbresTalent::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                $action = $arbre->toString();
                //Modification de l'objet
                $arbre->nom = $nom;
                $arbre->description = $this->request->get('description');
                $arbre->image = $this->request->get('image');
                $arbre->save();

                //Log de l'action
                $action = "Modification de l'arbre pour les talents : " . $arbre->nom . " ( new :" . $arbre->toString() . ", old :" . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                return $arbre->id;
            }
        }
    }

    /**
     * Permet de dispatcher les écrans pour les arbres
     * @return unknown
     */
    public function detailArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idFamille')]]);

                $this->view->auth = $auth;
                $this->view->mode = $mode;
                $this->view->famille = $famille;
                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/talents/arbres/creationArbre');
                } else {
                    if ($mode == "consultation") {
                        $this->view->arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/talents/arbres/detailArbre');
                    } else {
                        if ($mode == "edition") {
                            $this->view->arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/talents/arbres/editionArbre');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'afficher les contraintes liées à l'arbre
     * @return unknown
     */
    public function showlisteContraintesArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $arbre;
                $this->view->type = $this->request->get('type');
                $retour = $this->view->partial('utils/contraintes/listeContraintes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de supprimer un arbre
     */
    public function supprimerArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($arbre != false) {
                    $action = $arbre->supprimerArbre();
                    //Log de l'Action
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    $logDEV->save();
                }
                $this->view->disable();
            }
        }
    }


    /* Talents */
    /**
     * Permet de créer un talent
     * @return string|number
     */
    public function creerTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $position = $this->request->get('position');
                $rang = $this->request->get('rang');
                $idArbre = $this->request->get('idArbre');
                //Vérification de l'unicité du nom
                $verif = Talents::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Création de l'objet
                $talent = new Talents();
                $talent->nom = $nom;
                $talent->description = $this->request->get('description');
                $talent->idArbre = $this->request->get('idArbre');
                $talent->image = $this->request->get('image');
                $talent->niveau_max = $this->request->get('max');
                $talent->rang = $this->request->get('rang');
                $talent->position = $this->request->get('position');
                $talent->isActif = $this->request->get('isActif');
                $talent->save();

                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $talent->id]]);
                //Log de l'action
                $action = "Création du talent :" . $talent->nom . "(" . $talent->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                //Mise à jour de la liste des talents en session
                if (isset($auth['simulationTalent']['listeTalents'])) {
                    $a_json = $auth['simulationTalent']['listeTalents'];
                    $talentRow = array();
                    $talentRow['actuel'] = 0;
                    $talentRow['max'] = $talent->niveau_max;
                    $talentRow['couleur'] = $talent->determineCouleur($auth, 'admin');
                    $talentRow['isActif'] = $talent->isActif;
                    $talentRow['idArbre'] = $talent->idArbre;
                    $talentRow['idFamille'] = $talent->arbre->idFamille;
                    $a_json[$talent->id] = $talentRow;
                    $auth['simulationTalent']['listeTalents'] = $a_json;
                    $this->session->auth = $auth;
                }

                return $talent->id;
            }
        }
    }

    /**
     * Permet de modifier un talent
     * @return string|number
     */
    public function modifierTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                //Vérification de l'unicité du nom
                if ($talent->nom != $nom) {
                    $verif = Talents::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                $action = $talent->toString();

                //Modification de l'objet
                $talent->nom = $nom;
                $talent->description = $this->request->get('description');
                $talent->image = $this->request->get('image');
                $talent->niveau_max = $this->request->get('max');
                $talent->isActif = $this->request->get('isActif');
                $talent->save();

                //Log de l'action
                $action = "Modification du talent :" . $talent->nom . "( new : " . $talent->toString() . ", old : " . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                //Mise à jour de la liste des talents en session
                if (isset($auth['simulationTalent']['listeTalents'][$talent->id])) {
                    $a_json = $auth['simulationTalent']['listeTalents'];
                    $talentRow = $a_json[$talent->id];
                    $talentRow['max'] = $talent->niveau_max;
                    if ($talentRow['max'] < $talentRow['actuel']) {
                        $talentRow['actuel'] = $talentRow['max'];
                    }
                    $talentRow['isActif'] = $talent->isActif;
                    $talentRow['idArbre'] = $talent->idArbre;
                    $talentRow['idFamille'] = $talent->arbre->idFamille;
                    $a_json[$talent->id] = $talentRow;
                    $auth['simulationTalent']['listeTalents'] = $a_json;
                    $talentRow['couleur'] = $talent->determineCouleur($auth, 'admin');
                    $a_json[$talent->id] = $talentRow;
                    $auth['simulationTalent']['listeTalents'] = $a_json;
                    $this->session->auth = $auth;
                }

                return $talent->id;
            }
        }
    }

    /**
     * Permet de dispatcher les écrans pour les talents
     * @return unknown
     */
    public function detailTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArbre')]]);

                $this->view->auth = $auth;
                $this->view->mode = $mode;
                $this->view->arbre = $arbre;
                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/talents/talents/creationTalent');
                } else {
                    if ($mode == "consultation") {
                        $this->view->talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/talents/talents/detailTalent');
                    } else {
                        if ($mode == "edition") {
                            $this->view->talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/talents/talents/editionTalent');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'afficher le formulaire de création d'un talent
     * @return unknown
     */
    public function afficherCreationTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArbre')]]);

                $this->view->auth = $auth;
                $this->view->mode = $mode;
                $this->view->arbre = $arbre;
                $this->view->rang = $this->request->get('rang');
                $this->view->position = $this->request->get('position');

                $retour = $this->view->partial('gameplay/formulaire/talents/talents/creationTalent');

                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'ajouter une généalogie
     * @return string
     */
    public function ajouterGenealogieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $idFils = $this->request->get('idFils');
                $idPere = $this->request->get('idPere');

                $verifExistence = GenealogieTalents::findFirst(['idFils = :idFils: AND idPere = :idPere:', 'bind' => ['idFils' => $idFils, 'idPere' => $idPere]]);
                if ($verifExistence != false) {
                    return "error";
                } else {
                    $genealogie = new GenealogieTalents();
                    $genealogie->idFils = $idFils;
                    $genealogie->idPere = $idPere;
                    $genealogie->save();

                    //Log de l'action
                    $action = "Création d'une généalogie sur le talent :" . $idFils . ", ajout père : " . $idPere;
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    $logDEV->save();

                    //Ajout au tableau
                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idFils]]);
                    $talentCible = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idPere]]);
                    $tabGenealogie = $auth['simulationTalent']['genealogie'];
                    $rowGenealogie = array();
                    $rowGenealogie['idArbre'] = $talent->idArbre;
                    $rowGenealogie['idTalent'] = $talent->id;
                    $rowGenealogie['idPere'] = $talentCible->id;
                    $rowGenealogie['couleur'] = $talent->determineCouleur($auth, "admin");
                    $tabGenealogie[count($tabGenealogie)] = $rowGenealogie;
                    $auth['simulationTalent']['genealogie'] = $tabGenealogie;
                    $this->session->auth = $auth;
                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idFils]]);
                    return $talent->genererGenealogie();
                }
            }
        }
    }

    /**
     * Permet de supprimer une généalogie
     * @return string
     */
    public function supprimerGenealogieAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idFils = $this->request->get('idFils');
                $idPere = $this->request->get('idPere');

                $genealogie = GenealogieTalents::findFirst(['idPere = :idPere: AND idFils = :idFils:', 'bind' => ['idPere' => $idPere, 'idFils' => $idFils]]);
                if ($genealogie != false) {
                    $genealogie->delete();

                    //Log de l'action
                    $action = "Suppression de la généalogie du talent : " . $idFils . " avec le talent : " . $idPere;
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    $logDEV->save();

                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idFils]]);
                    $idTableau = $this->supprimerGenealogieTableau($talent->id, $idPere, $auth, "unique");
                    return $talent->genererGenealogie();
                }
            }
        }
    }

    /**
     * Permet de mettre à jour le tableau javascript
     * @param unknown $idFils
     * @param unknown $idPere
     * @param unknown $auth
     * @param unknown $mode
     */
    private function supprimerGenealogieTableau($idFils, $idPere, $auth, $mode) {
        $tabGenealogie = $auth['simulationTalent']['genealogie'];
        if ($mode == "unique") {
            $id = -1;
            for ($i = 0; $i < count($tabGenealogie); $i++) {
                $element = $tabGenealogie[$i];
                if ($element['idPere'] == $idPere && $element['idTalent'] == $idFils) {
                    $id = $i;
                }
            }
            if ($id != -1) {
                unset($tabGenealogie[$id]);
            }
        } else {
            $id = array();
            for ($i = 0; $i < count($tabGenealogie); $i++) {
                $element = $tabGenealogie[$i];
                if ($element['idPere'] == $idFils || $element['idTalent'] == $idFils) {
                    $id[count($id)] = $i;
                }
            }
            if (count($id) > 0) {
                for ($j = 0; $j < count($id); $j++) {
                    unset($tabGenealogie[$id[$j]]);
                }
            }
        }
        $auth['simulationTalent']['genealogie'] = $tabGenealogie;
        $this->session->auth = $auth;
    }

    /**
     * Permet de supprimer un talent
     */
    public function supprimerTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $idTalent = $talent->id;
                //Décalage des positions de tous les talents précédents
                $listeTalents = Talents::find(['position > :position: AND rang = :rang: AND idArbre = :idArbre:', 'bind' => ['position' => $talent->position, 'rang' => $talent->rang, 'idArbre' => $talent->arbre->id]]);
                if ($listeTalents != false && count($listeTalents) > 0) {
                    foreach ($listeTalents as $talentModif) {
                        $talentModif->position = $talentModif->position - 1;
                        $talentModif->save();
                    }
                }

                //Suppression de toutes les généalogies du talent
                $genealogies = GenealogieTalents::find(['idFils = :idFils: OR idPere = :idPere:', 'bind' => ['idFils' => $talent->id, 'idPere' => $talent->id]]);
                if ($genealogies != false) {
                    $genealogies->delete();
                }

                //Log de l'Action
                $logDEV = new LogsDEV();
                $logDEV->action = "Suppression du talent : " . $talent->toString();
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                $logDEV->save();

                $talent->delete();

                //Mise à jour de la liste des talents en session
                if (isset($auth['simulationTalent']['listeTalents'])) {
                    $a_json = $auth['simulationTalent']['listeTalents'];
                    unset($a_json[$this->request->get('id')]);
                    $auth['simulationTalent']['listeTalents'] = $a_json;
                    $this->session->auth = $auth;
                    $this->supprimerGenealogieTableau($idTalent, null, $auth, "multiple");
                }
            }
        }
    }

    /**
     * Permet d'initialiser le tableau javascript pour les talents
     * @return unknown
     */
    public function initListeTalentsAdminAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idFamille')]]);
                $a_json = array();
                $tabGenealogie = array();
                if ($famille !== null && $famille->arbres !== null && count($famille->arbres) > 0) {
                    $a_json = $auth['simulationTalent']['listeTalents'] ?? array();
                    foreach ($famille->arbres as $arbre) {
                        if ($arbre->talents != null && count($arbre->talents) > 0) {
                            foreach ($arbre->talents as $talent) {
                                $talentRow = array();
                                $talentRow['actuel'] = 0;
                                $talentRow['max'] = $talent->niveau_max;
                                $talentRow['idArbre'] = $talent->idArbre;
                                $talentRow['idFamille'] = $famille->id;
                                $talentRow['isActif'] = $talent->isActif;
                                $a_json[$talent->id] = $talentRow;
                                $auth['simulationTalent']['listeTalents'] = $a_json;
                                $couleur = $talent->determineCouleur($auth, 'admin');
                                $talentRow['couleur'] = $couleur;
                                $a_json[$talent->id] = $talentRow;
                                $auth['simulationTalent']['listeTalents'] = $a_json;

                                if (isset($talent->peres) && count($talent->peres) > 0) {
                                    foreach ($talent->peres as $pere) {
                                        $rowGenealogie = array();
                                        $rowGenealogie['idArbre'] = $talent->idArbre;
                                        $rowGenealogie['idTalent'] = $talent->id;
                                        $rowGenealogie['idPere'] = $pere->id;
                                        $rowGenealogie['couleur'] = $couleur;
                                        $tabGenealogie[count($tabGenealogie)] = $rowGenealogie;
                                    }
                                }
                            }
                        }
                    }
                }
                //On enregistre la liste en session
                $auth['simulationTalent']['listeTalents'] = $a_json;
                $auth['simulationTalent']['genealogie'] = $tabGenealogie;
                $this->session->auth = $auth;

                // on renvoi notre tableau en json
                $this->response->setContent(json_encode($a_json));
                return $this->response;
            }
        }
    }

    /**
     * Permet d'afficher la liste des effets d'un talent
     * @return unknown
     */
    public function showlisteEffetTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $talent;
                $this->view->type = "talent";
                $retour = $this->view->partial('utils/effets/listeEffets');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'afficher la liste des contraintes d'un talent
     * @return unknown
     */
    public function showlisteContraintesTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $talent;
                $this->view->type = "talent";
                $retour = $this->view->partial('utils/contraintes/listeContraintes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'augmenter un talent en mode simulation
     */
    public function augmenterTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idTalent')]]);

                $a_json = $auth['simulationTalent']['listeTalents'];
                $talentRow = $a_json[$talent->id];
                $talentRow['actuel'] = $this->request->get('newActuel');
                if ($talentRow['actuel'] == $talentRow['max']) {
                    $talentRow['couleur'] = "vert";
                } else {
                    if ($talentRow['actuel'] != 0) {
                        $talentRow['couleur'] = "jaune";
                    } else {
                        $talentRow['vcouleur'] = "gris";
                    }
                }

                $a_json[$talent->id] = $talentRow;

                //On enregistre la liste en session
                $auth['simulationTalent']['listeTalents'] = $a_json;
                $this->session->auth = $auth;
            }
        }
    }

    /**
     * Récupère la liste des talents en session
     * @return unknown
     */
    public function getListeTalentAdminAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $a_json = $auth['simulationTalent']['listeTalents'];
                foreach ($a_json as $idTalent => $talent) {
                    $talentObj = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                    if ($talentObj != false) {
                        $talent['couleur'] = $talentObj->determineCouleur($auth, "admin");
                        $a_json[$idTalent] = $talent;
                    } else {
                        unset($a_json[idTalent]);
                    }
                }
                $auth['simulationTalent']['listeTalents'] = $a_json;
                $this->response->setContent(json_encode($a_json));
                return $this->response;
            }
        }
    }

    /**
     * Récupère la liste des généalogies en session
     * @return unknown
     */
    public function getGenealogieAdminAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $a_json = $auth['simulationTalent']['genealogie'];
                if (count($a_json) > 0 && $a_json != null) {
                    for ($i = 0; $i < count($a_json); $i++) {
                        $rowGenealogie = $a_json[$i];
                        $talentFils = Talents::findFirst(['id = :id:', 'bind' => ['id' => $rowGenealogie['idTalent']]]);
                        if ($talentFils != false) {
                            $rowGenealogie['couleur'] = $talentFils->determineCouleur($auth, "admin");
                            $a_json[$i] = $rowGenealogie;
                        }
                    }
                }
                $auth['simulationTalent']['genealogie'] = $a_json;
                $this->response->setContent(json_encode($a_json));
                return $this->response;
            }
        }
    }

    /**
     * Permet d'échanger des talents de place
     * @return unknown
     */
    public function switchTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => explode('_', $this->request->get('idOrigine'))[1]]]);
                $idCible = $this->request->get('idCible');
                $element = explode("_", $idCible);
                if ($element[0] == "new") {
                    $talent->rang = $element[1];
                    $talent->position = $element[2];
                    $talent->save();
                } else {
                    $talentCible = Talents::findFirst(['id = :id:', 'bind' => ['id' => explode('_', $this->request->get('idCible'))[1]]]);
                    $rang = $talentCible->rang;
                    $position = $talentCible->position;
                    $talentCible->rang = $talent->rang;
                    $talentCible->position = $talent->position;
                    $talentCible->save();
                    $talent->rang = $rang;
                    $talent->position = $position;
                    $talent->save();
                }
                return $talent->arbre->idFamille;
            }
        }
    }

    /**
     * Simule un arbre de talent
     * @return string
     */
    public function simulerArbreAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idFamille')]]);
                $type = $this->request->get('type');
                if ($type == "simulation") {
                    $retour = $famille->genererArbresTalents($auth, "admin", true);
                } else {
                    $retour = $famille->genererArbresTalents($auth, "admin", false);
                }
                return $retour;
            }
        }
    }

    /**
     * Permet d'afficher la description des talents
     * @return string
     */
    public function afficherDescriptionTalentAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idTalent')]]);
                $retour = $talent->genererDescriptionDansArbre($auth, "admin");
                return $retour;
            }
        }
    }


    //########### Competences #############//

    /**
     * Permet de dispatcher les écrans des compétences
     * @return unknown
     */
    public function detailRangCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $this->view->pointAAtteindreDefaut = RangsCompetence::getNextPointAAtteindre();
                    $this->view->niveauDefaut = RangsCompetence::getNextLevel();
                    $retour = $this->view->partial('gameplay/formulaire/competences/creationRang');
                } else {
                    if ($mode == "consultation") {
                        $this->view->rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/competences/detailRang');
                    } else {
                        if ($mode == "edition") {
                            $this->view->rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/competences/editionRang');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer un rang pour une compétence
     * @return string|number
     */
    public function creerRangCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = RangsCompetence::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                // Création du rang
                $rang = new RangsCompetence();
                $rang->nom = $nom;
                $rang->description = $this->request->get('description');
                $rang->pointAAtteindre = $this->request->get('point');
                $rang->niveau = $this->request->get('niveau');
                $rang->save();

                // Logs de l'action
                $action = "Création du rang pour les compétences:" . $rang->nom . "(" . $rang->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                $logDEV->save();

                // Modification liée à l'ajout de ce rang pour chaque compétence
                $listeCompetences = Competences::find();
                if ($listeCompetences != false && count($listeCompetences) > 0) {
                    foreach ($listeCompetences as $competence) {
                        //Ajout de l'association
                        $assoc = new AssocRangsCompetence();
                        $assoc->idRang = $rang->id;
                        $assoc->idCompetence = $competence->id;
                        $assoc->nbPointAAtteindre = $competence->coutPA * $rang->pointAAtteindre;
                        $assoc->save();
                    }
                }
                return $rang->id;
            }
        }
    }

    /**
     * Permet de modifier un rang pour une compétence
     * @return string
     */
    public function modifierRangCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                //Vérification de l'unicité du nom
                if ($rang->nom != $nom) {
                    $verif = RangsCompetence::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                //Vérification sur la valeur des points à atteindre
                $point = $this->request->get('point');
                $maxPosition = RangsCompetence::maximum(['column' => 'niveau']);

                //S'il s'agit du rang de niveau 1, on regarde uniquement le second élément, s'il existe
                //S'il s'agit du rang max, on regarde uniquement l'élément précédent, s'il existe
                //Sinon on regarde les précédent et suivant
                if ($rang->niveau == 1) {
                    $rangSuivant = RangsCompetence::findFirst(['niveau = :niveau:', 'bind' => ['niveau' => $rang->niveau + 1]]);
                    if ($rangSuivant != false && $rangSuivant->pointAAtteindre <= $point) {
                        return "errorPointAAtteindre";
                    }
                } else {
                    if ($rang->niveau == $maxPosition) {
                        $rangPrecedent = RangsCompetence::findFirst(['niveau = :niveau:', 'bind' => ['niveau' => $rang->niveau - 1]]);
                        if ($rangPrecedent != false && $rangPrecedent->pointAAtteindre >= $point) {
                            return "errorPointAAtteindre";
                        }
                    } else {
                        $rangSuivant = RangsCompetence::findFirst(['niveau = :niveau:', 'bind' => ['niveau' => $rang->niveau + 1]]);
                        $rangPrecedent = RangsCompetence::findFirst(['niveau = :niveau:', 'bind' => ['niveau' => $rang->niveau - 1]]);
                        if ($rangPrecedent->pointAAtteindre >= $point || $rangSuivant->pointAAtteindre <= $point) {
                            return "errorPointAAtteindre";
                        }
                    }
                }

                $compteurModifComp = 0;
                //Modification des valeurs calculées de base si on a modifié la valeur du rang
                if ($point != $rang->pointAAtteindre) {
                    $listeAssoc = AssocRangsCompetence::find(['idRang = :id:', 'bind' => ['id' => $rang->id]]);
                    if ($listeAssoc != false && count($listeAssoc) > 0) {
                        foreach ($listeAssoc as $assoc) {
                            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $assoc->idCompetence]]);
                            //On vérifie que la valeur n'a pas déjà été customisée.
                            $coutPA = $competence->coutPA;
                            if ($coutPA == 0 || $coutPA == '0') {
                                $coutPA = 1;
                            }
                            if ($coutPA * $rang->pointAAtteindre == $assoc->nbPointAAtteindre) {
                                $assoc->nbPointAAtteindre = $coutPA * $point;
                                $assoc->save();
                                $compteurModifComp++;
                            }
                        }
                    }
                }

                $action = "Ancien rang : " . $rang->toString();

                // Modification du rang
                $rang->nom = $nom;
                $rang->description = $this->request->get('description');
                $rang->pointAAtteindre = $point;
                $rang->save();

                // Logs de l'action
                $action = "Modification du rang pour les compétences:" . $rang->nom . "(Nouveau :" . $rang->toString() . ", " . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                $logDEV->save();

                $this->view->disable();
                return $compteurModifComp . " compétence(s) ont été impactée(s) suite à ce changement.";
            }
        }
    }

    /**
     * Permet de rafraichir la liste des rangs
     * pour les compétences
     * @return string
     */
    public function refreshListeRangAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();

                return RangsCompetence::genererListeRang($auth);
            }
        }
    }

    /**
     * Permet de supprimer une compétence
     * @return string
     */
    public function supprimerRangCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->disable();

                $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                //Vérifier si ce rang est déjà utilisé
                $bloqueCompetence = Competences::count(['idRangBloque = :id:', 'bind' => ['id' => $rang->id]]);
                if ($bloqueCompetence > 0) {
                    return "errorUse";
                }
                $autonomeCompetence = Competences::count(['idRangAutonome = :id:', 'bind' => ['id' => $rang->id]]);
                if ($autonomeCompetence > 0) {
                    return "errorUse";
                }
                $persoBloqueCompetence = AssocCompetencesPersonnage::count(['idRangBloque = :id:', 'bind' => ['id' => $rang->id]]);
                if ($persoBloqueCompetence > 0) {
                    return "errorUse";
                }

                $listeAssoc = AssocRangsCompetence::find(['idRang = :id:', 'bind' => ['id' => $rang->id]]);
                $listeAssoc->delete();

                $rangsAModifier = RangsCompetence::find(['niveau > :niveau:', 'bind' => ['niveau' => $rang->niveau]]);
                if ($rangsAModifier != false && count($rangsAModifier) > 0) {
                    foreach ($rangsAModifier as $modifRang) {
                        $modifRang->niveau = $modifRang->niveau - 1;
                        $modifRang->save();
                    }
                }

                // Logs de l'action
                $action = "Suppression du rang : " . $rang->nom;
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                $logDEV->save();

                $rang->delete();
            }
        }
    }

    /**
     * Permet de disptacher les écrans pour les compétences
     * @return unknown
     */
    public function detailCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');

                $this->view->auth = $auth;
                $this->view->mode = $mode;

                if ($mode == "creation") {
                    $retour = $this->view->partial('gameplay/formulaire/competences/creationCompetence');
                } else {
                    if ($mode == "consultation") {
                        $this->view->competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                        $retour = $this->view->partial('gameplay/formulaire/competences/detailCompetence');
                    } else {
                        if ($mode == "edition") {
                            $this->view->competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                            $retour = $this->view->partial('gameplay/formulaire/competences/editionCompetence');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de créer une compétence
     * @return string|number
     */
    public function creerCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');

                //Vérification de l'unicité du nom
                $verif = Competences::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                if ($verif) {
                    return "errorNom";
                }

                //Création de la compétence
                $competence = new Competences();
                $competence->nom = $nom;
                $competence->description = $this->request->get('description');
                $competence->image = $this->request->get('image');
                $competence->type = $this->request->get('type');
                $competence->isActif = $this->request->get('isActif');
                $isActivable = $this->request->get('isActivable');
                $competence->activable = $isActivable;
                if ($isActivable == "1" || $isActivable == true) {
                    $competence->messageRP = $this->request->get('messageRP');
                    $competence->evenementLanceur = $this->request->get('eventLanceur');
                    $competence->evenementGlobal = $this->request->get('eventGlobal');
                    $competence->coutPA = $this->request->get('coutPA');
                    $competence->isEntrainable = $this->request->get('isEntrainable');
                }
                $competence->isEnseignable = $this->request->get('isEnseignable');
                $idRangAutonomie = $this->request->get('idRangAutonomie');
                if ($idRangAutonomie == "" || $idRangAutonomie == null) {
                    $competence->idRangAutonome = null;
                } else {
                    $competence->idRangAutonome = $this->request->get('idRangAutonomie');
                }
                $competence->save();

                // Logs de l'action
                $action = "Création de la compétence:" . $competence->nom . "(" . $competence->toString() . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                $logDEV->save();

                //Ajout de l'association entre les rangs et les compétences
                $listeRang = RangsCompetence::find(['order' => 'niveau']);
                if ($listeRang != false && count($listeRang) > 0) {
                    foreach ($listeRang as $rang) {
                        $assoc = new AssocRangsCompetence();
                        $assoc->idCompetence = $competence->id;
                        $assoc->idRang = $rang->id;
                        if ($competence->coutPA != null && $competence->coutPA != 0 && $competence->coutPA != "0") {
                            $assoc->nbPointAAtteindre = $rang->pointAAtteindre * $competence->coutPA;
                        } else {
                            $assoc->nbPointAAtteindre = $rang->pointAAtteindre;
                        }
                        $assoc->save();
                    }
                }

                //Ajout de l'association avec les différentes caractéristiques
                $listeCaracteristique = Caracteristiques::find(['type = :type:', 'bind' => ['type' => Caracteristiques::CARAC_PRIMAIRE]]);
                if ($listeCaracteristique != false && count($listeCaracteristique) > 0) {
                    foreach ($listeCaracteristique as $carac) {
                        $assoc = new AssocCaracsCompetence();
                        $assoc->idCarac = $carac->id;
                        $assoc->idCompetence = $competence->id;
                        $assoc->isInfluenceur = 0;
                        $assoc->modificateur = 0;
                        $assoc->save();
                    }
                }
                return $competence->id;
            }
        }
    }

    /**
     * Permet de modifier une compétence
     * @return string
     */
    public function modifierCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $nom = $this->request->get('nom');
                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idCompetence')]]);

                //Vérification de l'unicité du nom
                if ($competence->nom != $nom) {
                    $verif = Competences::findFirst(['nom = :nom:', 'bind' => ['nom' => $nom]]);
                    if ($verif) {
                        return "errorNom";
                    }
                }

                $action = "Ancienne : " . $competence->toString();

                //Modification de la compétence
                $competence->nom = $nom;
                $competence->description = $this->request->get('description');
                $competence->image = $this->request->get('image');
                $competence->type = $this->request->get('type');
                $competence->isActif = $this->request->get('isActif');
                $isActivable = $this->request->get('isActivable');
                $competence->activable = $isActivable;
                $modifCoutPA = false;
                if ($isActivable == "1" || $isActivable == true) {
                    $competence->messageRP = $this->request->get('messageRP');
                    $competence->evenementLanceur = $this->request->get('eventLanceur');
                    $competence->evenementGlobal = $this->request->get('eventGlobal');
                    $coutPA = $this->request->get('coutPA');
                    if ($coutPA != $competence->coutPA) {
                        $modifCoutPA = true;
                    }
                    $competence->coutPA = $this->request->get('coutPA');
                    $competence->isEntrainable = $this->request->get('isEntrainable');
                } else {
                    $competence->messageRP = null;
                    $competence->evenementLanceur = null;
                    $competence->evenementGlobal = null;
                    $competence->coutPA = null;
                    $competence->isEntrainable = null;
                }
                $competence->isEnseignable = $this->request->get('isEnseignable');
                $rangAutonome = $this->request->get('idRangAutonomie');
                if ($rangAutonome == "") {
                    $rangAutonome = null;
                }
                $competence->idRangAutonome = $rangAutonome;
                $competence->save();

                // Logs de l'action
                $action = "Modification de la compétence:" . $competence->nom . "( Nouvelle " . $competence->toString() . ", " . $action . ")";
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                $logDEV->save();

                //Ajout de l'association entre les rangs et les compétences
                if ($modifCoutPA) {
                    $listeRang = RangsCompetence::find(['order' => 'niveau']);
                    if ($listeRang != false && count($listeRang) > 0) {
                        foreach ($listeRang as $rang) {
                            $assoc = AssocRangsCompetence::findFirst(['idRang = :idRang: AND idCompetence = :idCompetence', 'bind' => ['idRang' => $rang->id, 'idCompetence' => $competence->id]]);
                            $assoc->nbPointAAtteindre = $competence->coutPA * $rang->pointAAtteindre;
                            $assoc->save();
                        }
                    }
                }
                if ($modifCoutPA) {
                    return "En modifiant le cout en point d'action, les passages de rang dans la compétence ont été rénitialisé.";
                }
                return "";
            }
        }
    }

    /**
     * Permet de rafraichir la liste des compétences
     * @return string
     */
    public function refreshListeCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $retour = "";
                $listeTypeCompetence = Competences::getListeTypeCompetence();
                foreach ($listeTypeCompetence as $typeCompetence) {
                    $retour .= "<div class='blocCompetences' id='blocCompetences_" . $typeCompetence . "' >";
                    $retour .= Competences::genererBlocCompetence($typeCompetence);
                    $retour .= "</div>";
                }
                return $retour;
            }
        }
    }

    /**
     * Affiche la gestion des effets pour les compétences
     * @return unknown
     */
    public function showlisteEffetCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $competence;
                $this->view->type = "competence";
                $retour = $this->view->partial('utils/effets/listeEffets');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Affiche la gestion des contraintes pour les compétences
     * @return unknown
     */
    public function showlisteContraintesCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $competence;
                $this->view->type = "competence";
                $retour = $this->view->partial('utils/contraintes/listeContraintes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Affiche la gestion des évolutions pour les compétences
     * @return unknown
     */
    public function showlisteEvolutionCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->auth = $auth;
                $this->view->element = $competence;
                $this->view->type = "competence";
                $retour = $this->view->partial('gameplay/formulaire/competences/evolution');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de modifier les évolutions d'une compétence
     * @return string
     */
    public function modifierEvolutionCompetenceAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $tableauRang = explode("@", $this->request->get('listeRang'));
                $resRang = array();
                if (count($tableauRang) > 0) {
                    for ($i = 0; $i < count($tableauRang) - 1; $i++) {
                        $resultat = explode("_", $tableauRang[$i]);
                        if ($resultat[1] == "") {
                            return "errorVide";
                        }
                        $resRang[$resultat[0]] = $resultat[1];
                    }
                }

                $tableauCarac = explode("@", $this->request->get('listeCarac'));
                $resCarac = array();
                $compteur = 0;
                if (count($tableauCarac) > 0) {
                    for ($i = 0; $i < count($tableauCarac) - 1; $i++) {
                        $resultat = explode("_", $tableauCarac[$i]);
                        if ($resultat[1] == "") {
                            return "errorVide";
                        }
                        $compteur += $resultat[1];
                        $resCarac[$resultat[0]]['value'] = $resultat[1];
                        $resCarac[$resultat[0]]['influenceur'] = $resultat[2];
                    }
                }

                //Vérification sur les compteurs
                if ($compteur != 0) {
                    return "erreurCalcul";
                }

                $listeRang = RangsCompetence::find(['order' => 'niveau']);
                if ($listeRang != false && count($listeRang) > 0) {
                    $first = true;
                    $valActu = -1;
                    foreach ($listeRang as $rang) {
                        $value = $resRang[$rang->id];
                        if ($first && $value != 0) {
                            return "errorZero";
                        } else {
                            $first = false;
                        }

                        if ($valActu > $value) {
                            return "errorHierarchie";
                        }
                        $valActu = $value;
                    }
                }

                //On enregistre les donnes
                if ($listeRang != false && count($listeRang) > 0) {
                    foreach ($listeRang as $rang) {
                        $assoc = AssocRangsCompetence::findFirst(['idRang = :idRang: AND idCompetence = :idCompetence:', 'bind' => ['idRang' => $rang->id, 'idCompetence' => $competence->id]]);
                        if ($assoc == false || !isset($resRang[$rang->id])) {
                            return "error";
                        }
                        $assoc->nbPointAAtteindre = $resRang[$rang->id];
                        $assoc->save();
                    }
                }
                $listeCarac = Caracteristiques::find(['type = :type:', 'bind' => ['type' => Caracteristiques::CARAC_PRIMAIRE], 'order' => 'nom']);
                if ($listeCarac != false && count($listeCarac) > 0) {
                    foreach ($listeCarac as $carac) {
                        $assoc = AssocCaracsCompetence::findFirst(['idCarac = :idCarac: AND idCompetence = :idCompetence:', 'bind' => ['idCarac' => $carac->id, 'idCompetence' => $competence->id]]);
                        if ($assoc == false || !isset($resCarac[$carac->id])) {
                            return "error";
                        }
                        $assoc->modificateur = $resCarac[$carac->id]['value'];
                        $assoc->isInfluenceur = $resCarac[$carac->id]['influenceur'];
                        $assoc->save();
                    }
                }

                $rangBloque = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRangBloque')]]);
                if ($rangBloque != false) {
                    $competence->idRangBloque = $rangBloque->id;
                    $competence->save();
                }
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

        /* Cartes */
        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="cartes" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'cartes\');"><span class="labelOngletGameplay">Cartes</span></div>';
            $nbOnglet++;
        }

        /* Terrains ET Textures */
        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_TERRAINS, $auth['autorisations'])) {
            $onglets .= '<div id="terrains" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'terrains\');"><span class="labelOngletGameplay">Terrains</span></div>';
            $nbOnglet++;
            $onglets .= '<div id="textures" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'textures\');"><span class="labelOngletGameplay">Textures</span></div>';
            $nbOnglet++;
        }

        /* Caractéristiques */
        if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARACS, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CARAC_PRIMAIRE, $auth['autorisations'])) {
            $onglets .= '<div id="caracs" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'caracs\');"><span class="labelOngletGameplay">Caractéristiques</span></div>';
            $nbOnglet++;
        }

        /* Magie */
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="magie" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'magie\');"><span class="labelOngletGameplay">Magie</span></div>';
            $nbOnglet++;
        }

        /* Talents */
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="talents" name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'talents\');"><span class="labelOngletGameplay">Talents</span></div>';
            $nbOnglet++;
        }

        /* Compétences */
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="competences"  name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'competences\');"><span class="labelOngletGameplay">Compétences</span></div>';
            $nbOnglet++;
        }

        /* Equipement */
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_EQUIPEMENT_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_EQUIPEMENT_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="equipements"  name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'equipements\');"><span class="labelOngletGameplay">Equipements</span></div>';
            $nbOnglet++;
        }

        /* Créatures */
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CREATURE_CONSULTER, $auth['autorisations']) || Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CREATURE_MODIFIER, $auth['autorisations'])) {
            $onglets .= '<div id="creatures"  name="ongletGameplay" class="ongletGameplay" onclick="openOngletGameplay(\'creatures\');"><span class="labelOngletGameplay">Créatures</span></div>';
            $nbOnglet++;
        }

        $onglets .= '<input type="hidden" id="nombreOngletGameplay" name="nombreOngletGameplay" value="' . $nbOnglet . '"/>';
        if ($nbOnglet == 0) {
            return $this->dispatcher->forward(["controller" => "accueil", "action" => "rediriger",]);
        }
        return $onglets;
    }

    /**
     * Méthode générique pour la page
     */
    private function pageview() {
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/gameplay/gameplay.js?v=" . VERSION);
        $this->assets->addJs("js/gameplay/talents.js?v=" . VERSION);
        $this->assets->addJs("js/gameplay/competences.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->assets->addJs("js/utils/moduleftp.js?v=" . VERSION);
        $this->assets->addJs("js/utils/popupwiki.js?v=" . VERSION);
        $this->assets->addJs("js/utils/formule.js?v=" . VERSION);
        $this->assets->addJs("js/utils/tableCiblage.js?v=" . VERSION);
        $this->assets->addJs("js/gameplay/cartes.js?v=" . VERSION);
        $this->assets->addJs("js/utils/effets.js?v=" . VERSION);
        $this->assets->addJs("js/utils/contraintes.js?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/gameplay.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/gameplay/carte.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/gameplay/talents.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/gameplay/competences.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/popup.css?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }

}

