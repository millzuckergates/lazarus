<?php

class ContraintesController extends \Phalcon\Mvc\Controller {

    public function indexAction() {

    }

    /**
     * Permet d'afficher la popup contenant les information de la contrainte
     * @return unknown
     */
    public function afficherFormulaireContrainteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $this->view->auth = $auth;
                if ($mode == "creation") {
                    $retour = $this->view->partial('utils/contraintes/popupContrainteCreation');
                } else {
                    if ($mode == "consultation") {
                        $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                        $position = $this->request->get('position');
                        $this->view->contrainte = $contrainte;
                        $this->view->type = $type;
                        $this->view->idType = $idType;
                        $this->view->position = $position;
                        $retour = $this->view->partial('utils/contraintes/popupContrainteDetail');
                    } else {
                        if ($mode == "edition") {
                            $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                            $position = $this->request->get('position');
                            $this->view->contrainte = $contrainte;
                            $this->view->type = $type;
                            $this->view->idType = $idType;
                            $this->view->position = $position;
                            $retour = $this->view->partial('utils/contraintes/popupContrainteEdition');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de valider l'enregistrement d'une contrainte
     * @return unknown|string
     */
    public function validerContrainteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $action = $this->request->get('action');
                $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                if ($contrainte != false) {
                    $testContrainte = $contrainte->testerParametres($listeParam);
                    if (!is_array($testContrainte)) {
                        return $testContrainte;
                    } else {
                        $contrainte->insertAssoc($testContrainte, $type, $idType, $action);
                        $logDEV = new LogsDEV();
                        if ($type == "sort") {
                            $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Ajout de la contrainte " . $contrainte->nom . " au sort : " . $sort->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                        } else {
                            if ($type == "familleTalent") {
                                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $action = "Ajout de la contrainte " . $contrainte->nom . " à la famille : " . $famille->nom;
                                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                            } else {
                                if ($type == "arbreTalent") {
                                    $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $action = "Ajout de la contrainte " . $contrainte->nom . " à l'arbre : " . $arbre->nom;
                                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                                } else {
                                    if ($type == "talent") {
                                        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                        $action = "Ajout de la contrainte " . $contrainte->nom . " au talent : " . $talent->nom;
                                        $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                                    } else {
                                        if ($type == "competence") {
                                            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                            $action = "Ajout de la contrainte " . $contrainte->nom . " à la compétence : " . $competence->nom;
                                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                                        }
                                    }
                                }
                            }
                        }
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Contrainte non trouvée en base de données.";
                }
            }
        }
    }

    /**
     * Permet de modifier une contrainte
     * @return unknown|string
     */
    public function modifierContrainteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                $position = $this->request->get('position');
                if ($contrainte != false) {
                    $testContrainte = $contrainte->testerParametres($listeParam);
                    if (!is_array($testContrainte)) {
                        return $testContrainte;
                    } else {
                        $contrainte->modifieAssoc($testContrainte, $type, $idType, $position);
                        $logDEV = new LogsDEV();
                        if ($type == "sort") {
                            $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Modification de la contrainte " . $contrainte->nom . " au sort : " . $sort->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                        } else {
                            if ($type == "familleTalent") {
                                $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $action = "Modification de la contrainte " . $contrainte->nom . " à la famille : " . $famille->nom;
                                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                            } else {
                                if ($type == "arbreTalent") {
                                    $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $action = "Modification de la contrainte " . $contrainte->nom . " à l'arbre : " . $arbre->nom;
                                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                                } else {
                                    if ($type == "talent") {
                                        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                        $action = "Modification de la contrainte " . $contrainte->nom . " au talent : " . $talent->nom;
                                        $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                                    } else {
                                        if ($type == "competence") {
                                            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                            $action = "Modification de la contrainte " . $contrainte->nom . " de la compétence : " . $competence->nom;
                                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
                                        }
                                    }
                                }
                            }
                        }
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Contrainte non trouvée en base de données.";
                }
            }
        }
    }

    /**
     * Permet de retirer une contrainte
     */
    public function retirerContrainteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                $position = $this->request->get('position');
                $logDEV = new LogsDEV();
                if ($type == "sort") {
                    $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    $assocs = AssocSortsContraintesParam::find(['idContrainte = :idContrainte: AND idSort = :idSort: AND position = :position:',
                      'bind' => ['idContrainte' => $contrainte->id, 'idSort' => $idType, 'position' => $position]]);
                    $action = "Suppression de la contrainte " . $contrainte->nom . " du sort : " . $sort->nom;
                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_MAGIE;
                } else {
                    if ($type == "familleTalent") {
                        $famille = FamillesTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $assocs = AssocFamilletalentsContraintesParam::find(['idContrainte = :idContrainte: AND idFamille = :idFamille: AND position = :position:',
                          'bind' => ['idContrainte' => $contrainte->id, 'idFamille' => $idType, 'position' => $position]]);
                        $action = "Suppression de la contrainte " . $contrainte->nom . " de la famille : " . $famille->nom;
                        $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                    } else {
                        if ($type == "arbreTalent") {
                            $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $assocs = AssocArbretalentsContraintesParam::find(['idContrainte = :idContrainte: AND idArbre = :idArbre: AND position = :position:',
                              'bind' => ['idContrainte' => $contrainte->id, 'idArbre' => $idType, 'position' => $position]]);
                            $action = "Suppression de la contrainte " . $contrainte->nom . " de l'arbre : " . $arbre->nom;
                            $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                        } else {
                            if ($type == "talent") {
                                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $assocs = AssocTalentsContraintesParam::find(['idContrainte = :idContrainte: AND idTalent = :idTalent: AND position = :position:',
                                  'bind' => ['idContrainte' => $contrainte->id, 'idTalent' => $idType, 'position' => $position]]);
                                $action = "Suppression de la contrainte " . $contrainte->nom . " du talent : " . $talent->nom;
                                $logDEV->typeLog = LogsDEV::TYPE_GESTION_TALENT;
                            } else {
                                if ($type == "competence") {
                                    $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $assocs = AssocCompetencesContraintesParam::find(['idContrainte = :idContrainte: AND idCompetence = :idCompetence: AND position = :position:',
                                      'bind' => ['idContrainte' => $contrainte->id, 'idCompetence' => $idType, 'position' => $position]]);
                                    $action = "Suppression de la contrainte " . $contrainte->nom . " de la compétence : " . $competence->nom;
                                    $logDEV->typeLog = LogsDEV::TYPE_GESTION_COMPETENCE;
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
                $logDEV->action = $action;
                $logDEV->idPersonnage = $auth['perso']->id;
                $logDEV->dateLog = time();
                $logDEV->save();
            }
        }
    }

    /**
     * Charge la liste des contraintes par type
     * @return string
     */
    public function chargerContrainteByTypeAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $type = $this->request->get('type');
                if ($type == "all") {
                    return Contraintes::getSelectContrainte();
                } else {
                    return Contraintes::getSelectContrainte($type);
                }
            }
        }
    }

    /**
     * Permet de charger les détails d'une contrainte
     * @return string
     */
    public function chargerDetailContrainteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idContrainte')]]);
                return $contrainte->genererDetailContrainte("creation", null, null, null);
            }
        }
    }
}
