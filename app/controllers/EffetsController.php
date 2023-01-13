<?php

class EffetsController extends \Phalcon\Mvc\Controller {

    public function indexAction() {

    }

    /**
     * Permet d'afficher la popup contenant les information de l'effet
     * @return string|void
     */
    public function afficherFormulaireEffetAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');

                $this->view->auth = $auth;

                if ($mode == "creation") {
                    $retour = $this->view->partial('utils/effets/popupEffetCreation');
                } else {
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEffet')]]);
                    $position = $this->request->get('position');
                    $this->view->effet = $effet;
                    $this->view->type = $type;
                    $this->view->idType = $idType;
                    $this->view->position = $position;
                    if ($mode == "consultation") {
                        $retour = $this->view->partial('utils/effets/popupEffetDetail');
                    } elseif ($mode == "edition") {
                        $retour = $this->view->partial('utils/effets/popupEffetEdition');
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de valider l'enregistrement d'un effet
     * @return string|void
     */
    public function validerEffetAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $action = $this->request->get('action');
                $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEffet')]]);
                if ($effet != false) {
                    $testEffet = $effet->testerParametres($listeParam);
                    if (!is_array($testEffet)) {
                        return $testEffet;
                    } else {
                        $effet->insertAssoc($testEffet, $type, $idType, $action);
                        $logDEV = new LogsDEV();
                        if ($type == "sort") {
                            $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Ajout de l'effet " . $effet->nom . " au sort : " . $sort->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                        } elseif ($type == "terrain") {
                            $terrain = Terrains::findById($idType);
                            $action = "Ajout de l'effet " . $effet->nom . " au terrain : " . $terrain->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                        } elseif ($type == "talent") {
                            $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Ajout de l'effet " . $effet->nom . " au talent : " . $talent->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                        } elseif ($type == "carte") {
                            $carte = Cartes::findById($idType);
                            $action = "Ajout de l'effet " . $effet->nom . " à la carte : " . $carte->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAP;
                        } elseif ($type == "competence") {
                            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Ajout de l'effet " . $effet->nom . " à la compétence : " . $competence->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                        }
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Effet non trouvé en base de données.";
                }
            }
        }
    }

    /**
     * Permet de modifier un effet
     * @return string|void
     */
    public function modifierEffetAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEffet')]]);
                $position = $this->request->get('position');
                if ($effet != false) {
                    $testEffet = $effet->testerParametres($listeParam);
                    if (!is_array($testEffet)) {
                        return $testEffet;
                    } else {
                        $effet->modifieAssoc($testEffet, $type, $idType, $position);
                        $logDEV = new LogsDEV();
                        if ($type == "sort") {
                            $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Modification de l'effet " . $effet->nom . " au sort : " . $sort->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                        } elseif ($type == "terrain") {
                            $terrain = Terrains::findById($idType);
                            $action = "Modification de l'effet " . $effet->nom . " au terrain : " . $terrain->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                        } elseif ($type == "talent") {
                            $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Modification de l'effet " . $effet->nom . " du talent : " . $talent->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                        } elseif ($type == "carte") {
                            $carte = Cartes::findById($idType);
                            $action = "Modification de l'effet " . $effet->nom . " à la carte : " . $carte->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAP;
                        } elseif ($type == "competence") {
                            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Modification de l'effet " . $effet->nom . " à la compétence : " . $competence->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                        }
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Effet non trouvé en base de données.";
                }
            }
        }
    }

    /**
     * Permet de retirer un effet
     */
    public function retirerEffetAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEffet')]]);
                $position = $this->request->get('position');
                if ($type == "sort") {
                    $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    $assocs = AssocSortsEffetsParam::find(['idEffet = :idEffet: AND idSort = :idSort: AND position = :position:',
                      'bind' => ['idEffet' => $effet->id, 'idSort' => $idType, 'position' => $position]]);
                    $action = "Suppression de l'effet " . $effet->nom . " du sort : " . $sort->nom;
                    $typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                } else {
                    if ($type == "terrain") {
                        $terrain = Terrains::findById($idType);
                        $assocs = AssocTerrainsEffetsParam::find(['idEffet = :idEffet: AND idTerrain = :idTerrain: AND position = :position:',
                          'bind' => ['idEffet' => $effet->id, 'idTerrain' => $idType, 'position' => $position]]);
                        $action = "Suppression de l'effet " . $effet->nom . " du terrain : " . $terrain->nom;
                        $typeLog = LogsDEV::TYPE_GESTION_TERRAINS;
                    } else {
                        if ($type == "carte") {
                            $carte = Cartes::findById($idType);
                            $assocs = AssocCartesEffetsParam::find(['idEffet = :idEffet: AND idCarte = :idCarte: AND position = :position:',
                              'bind' => ['idEffet' => $effet->id, 'idCarte' => $idType, 'position' => $position]]);
                            $action = "Suppression de l'effet " . $effet->nom . " de la carte : " . $carte->nom;
                            $typeLog = LogsDEV::TYPE_GESTION_MAP;
                        } else {
                            if ($type == "talent") {
                                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $assocs = AssocTalentsEffetsParam::find(['idEffet = :idEffet: AND idTalent = :idTalent: AND position = :position:',
                                  'bind' => ['idEffet' => $effet->id, 'idTalent' => $idType, 'position' => $position]]);
                                $action = "Suppression de l'effet " . $effet->nom . " du talent : " . $talent->nom;
                                $typeLog = LogsDEV::TYPE_GESTION_TALENT;
                            } else {
                                if ($type == "competence") {
                                    $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $assocs = AssocCompetenceEffetsParam::find(['idEffet = :idEffet: AND idCompetence = :idCompetence: AND position = :position:',
                                      'bind' => ['idEffet' => $effet->id, 'idCompetence' => $idType, 'position' => $position]]);
                                    $action = "Suppression de l'effet " . $effet->nom . " de la compétence : " . $competence->nom;
                                    $typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                                }
                            }
                        }
                    }
                }


                if ($assocs != false && count($assocs) > 0) {
                    foreach ($assocs as $assoc) {
                        $assoc->delete();
                    }
                }
                //Logs de l'action
                $logDEV = new LogsDEV();
                $logDEV->action = $action;
                $logDEV->typeLog = $typeLog;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->save();
            }
        }
    }

    /**
     * Charge la liste des effets par type
     * @return string
     */
    public function chargerEffetByTypeAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $type = $this->request->get('type');

                if ($type == "all") {
                    return Effets::getSelectEffet();
                } else {
                    return Effets::getSelectEffet($type);
                }
            }
        }
    }

    /**
     * Permet de charger les détails d'un effet
     * @return string
     */
    public function chargerDetailEffetAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idEffet')]]);
                return $effet->genererDetailEffet("creation", null, null, null);
            }
        }
    }
}