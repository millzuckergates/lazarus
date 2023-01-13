<?php

class BonusController extends \Phalcon\Mvc\Controller {

    public function indexAction() {

    }

    /**
     * Permet d'afficher la popup contenant les information du bonus
     * @return unknown
     */
    public function afficherFormulaireBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $mode = $this->request->get('mode');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $this->view->auth = $auth;
                if ($mode == "creation") {
                    $retour = $this->view->partial('utils/bonus/popupBonusCreation');
                } else {
                    if ($mode == "consultation") {
                        $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                        $position = $this->request->get('position');
                        $this->view->bonus = $bonus;
                        $this->view->type = $type;
                        $this->view->idType = $idType;
                        $this->view->position = $position;
                        $retour = $this->view->partial('utils/bonus/popupBonusDetail');
                    } else {
                        if ($mode == "edition") {
                            $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                            $position = $this->request->get('position');
                            $this->view->bonus = $bonus;
                            $this->view->type = $type;
                            $this->view->idType = $idType;
                            $this->view->position = $position;
                            $retour = $this->view->partial('utils/bonus/popupBonusEdition');
                        }
                    }
                }
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de valider l'enregistrement d'un bonus
     * @return unknown|string
     */
    public function validerBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                if ($bonus != false) {
                    $testBonus = $bonus->testerParametres($listeParam);
                    if (!is_array($testBonus)) {
                        return $testBonus;
                    } else {
                        $bonus->insertAssoc($testBonus, $type, $idType);
                        $logADMIN = new LogsADMIN();
                        if ($type == "royaume") {
                            $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Ajout du bonus " . $bonus->nom . " au royaume : " . $royaume->nom;
                            $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                        } else {
                            if ($type == "race") {
                                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $action = "Ajout du bonus " . $bonus->nom . " à la race : " . $race->nom;
                                $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                            } else {
                                if ($type == "religion") {
                                    $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $action = "Ajout du bonus " . $bonus->nom . " à la religion : " . $religion->nom;
                                    $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                                }
                            }
                        }
                        $logADMIN->action = $action;
                        $logADMIN->idPersonnage = $auth['perso']->id;
                        $logADMIN->dateLog = time();
                        $logADMIN->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Bonus non trouvé en base de données.";
                }
            }
        }
    }

    /**
     * Permet de modifier un bonus
     * @return unknown|string
     */
    public function modifierBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $listeParam = $this->request->get('listeParam');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                $position = $this->request->get('position');
                if ($bonus != false) {
                    $testBonus = $bonus->testerParametres($listeParam);
                    if (!is_array($testBonus)) {
                        return $testBonus;
                    } else {
                        $bonus->modifieAssoc($testBonus, $type, $idType, $position);
                        $logADMIN = new LogsADMIN();
                        if ($type == "royaume") {
                            $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $action = "Modification du bonus " . $bonus->nom . " au royaume : " . $royaume->nom;
                            $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                        } else {
                            if ($type == "race") {
                                $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                $action = "Modification du bonus " . $bonus->nom . " à la race : " . $race->nom;
                                $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                            } else {
                                if ($type == "religion") {
                                    $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                                    $action = "Modification du bonus " . $bonus->nom . " à la religion : " . $religion->nom;
                                    $logADMIN->typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                                }
                            }
                        }
                        $logADMIN->action = $action;
                        $logADMIN->idPersonnage = $auth['perso']->id;
                        $logADMIN->dateLog = time();
                        $logADMIN->save();
                        return "success";
                    }
                } else {
                    return "Une erreur s'est produite. Bonus non trouvé en base de données.";
                }
            }
        }
    }

    /**
     * Permet de retirer un bonus
     */
    public function retirerBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                $position = $this->request->get('position');
                if ($type == "royaume") {
                    $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    $assocs = AssocRoyaumesBonusParam::find(['idBonus = :idBonus: AND idRoyaume = :idRoyaume: AND position = :position:',
                      'bind' => ['idBonus' => $bonus->id, 'idRoyaume' => $idType, 'position' => $position]]);
                    $action = "Suppression du bonus " . $bonus->nom . " du royaume : " . $royaume->nom;
                    $typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                } else {
                    if ($type == "race") {
                        $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $assocs = AssocRacesBonusParam::find(['idBonus = :idBonus: AND idRace = :idRace: AND position = :position:',
                          'bind' => ['idBonus' => $bonus->id, 'idRace' => $idType, 'position' => $position]]);
                        $action = "Suppression du bonus " . $bonus->nom . " de la race : " . $race->nom;
                        $typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                    } else {
                        if ($type == "religion") {
                            $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                            $assocs = AssocReligionsBonusParam::find(['idBonus = :idBonus: AND idReligion = :idReligion: AND position = :position:',
                              'bind' => ['idBonus' => $bonus->id, 'idReligion' => $idType, 'position' => $position]]);
                            $action = "Suppression du bonus " . $bonus->nom . " de la religion : " . $religion->nom;
                            $typeLog = LogsADMIN::TYPE_GESTION_REFERENTIELS;
                        }
                    }
                }

                if ($assocs != false && count($assocs) > 0) {
                    foreach ($assocs as $assoc) {
                        $assoc->delete();
                    }
                }
                //Logs de l'action
                $logADMIN = new LogsADMIN();
                $logADMIN->action = $action;
                $logADMIN->typeLog = $typeLog;
                $logADMIN->idPersonnage = $auth['perso']->id;
                $logADMIN->dateLog = time();
                $logADMIN->save();
            }
        }
    }

    /**
     * Permet de charger les détails d'un bonus
     * @return string
     */
    public function chargerDetailBonusAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                return $bonus->genererDetailBonus("creation", null, null, null);
            }
        }
    }

    public function enleverChoixMultipleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $param1 = $this->request->get('param1');
                $param2 = $this->request->get('param2');
                $idElement = $this->request->get('id');
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                if ($bonus != false) {
                    return $bonus->enleverChoixMultiple($param1, $param2, $idElement);
                } else {
                    return "errorProduit";
                }
                return "errorProduit";
            }
        }
    }

    public function ajouterChoixMultipleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $param1 = $this->request->get('param1');
                $param2 = $this->request->get('param2');
                $idElement = $this->request->get('id');
                $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idBonus')]]);
                if ($bonus != false) {
                    return $bonus->ajouterChoixMultiple($param1, $param2, $idElement);
                } else {
                    return "errorProduit";
                }
                return "errorProduit";
            }
        }
    }
}