<?php

class TableciblageController extends \Phalcon\Mvc\Controller {

    public function indexAction() {

    }

    /**
     * Permet d'ouvrir la table de ciblage pour les sorts
     * @return unknown
     */
    public function openPopupTableCiblageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idSort = $this->request->get('idSort');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idSort]]);
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $sort->idCiblage]]);
                $this->view->tableCiblage = $tableCiblage;
                $this->view->type = "sort";
                $this->view->idType = $sort->id;
                $this->view->auth = $auth;
                $retour = $this->view->partial('utils/tableciblage');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Méthode permettant de supprimer le ciblage des créatures
     */
    public function deleteCreaturesAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($tableCiblage != false) {
                    $tableCiblage->isCibleCreature = 0;
                    $tableCiblage->cibleCreatureAutorisee = "";
                    $tableCiblage->save();

                    //Logs de l'action
                    if ($type == "sort") {
                        $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $action = "Suppression de ciblage des créatures pour le sort : " . $sort->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                        $logDEV->save();
                    }
                }
                $this->view->disable();
            }
        }
    }

    /**
     * Méthode permettant de supprimer le ciblage des environnements
     */
    public function deleteEnvironnementAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($tableCiblage != false) {
                    $tableCiblage->isCibleEnvironnement = 0;
                    $tableCiblage->cibleEnvironnementAutorise = "";
                    $tableCiblage->save();

                    //Logs de l'action
                    if ($type == "sort") {
                        $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $action = "Suppression de ciblage des environnements pour le sort : " . $sort->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                        $logDEV->save();
                    }
                }
                $this->view->disable();
            }
        }
    }

    /**
     * Permet de mettre à jour la liste des terrains ciblables
     * @return string
     */
    public function genererListeTerrainCiblableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->disable();
                return $tableCiblage->getListeTerrainCiblable();
            }
        }
    }

    /**
     * Permet de retirer un terrain de la liste des terrains ciblables
     * @return string
     */
    public function retirerEnvironnementCiblableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $idTerrain = $this->request->get('idTerrain');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $tableCiblage->retirerTerrain($idTerrain);
                //Refresh
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($type == "sort") {
                    $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    if ($idTerrain == 0) {
                        $action = "Suppression du ciblage de tous les terrains pour le sort : " . $sort->nom;
                    } else {
                        $terrain = Terrains::findById($idTerrain);
                        $action = "Suppression du ciblage du terrain : " . $terrain->genre . " " . $terrain->nom . " pour le sort :" . $sort->nom;
                    }

                    //Log de l'action
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                    $logDEV->save();
                }
                $this->view->disable();
                //On regenère le résumé
                return $tableCiblage->getResumeEnvironnement("modification", $perso);
            }
        }
    }

    /**
     * Permet d'ajouter un terrain à la liste des terrains ciblabes
     * @return string
     */
    public function ajouterTerrainCiblableAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $idTerrain = $this->request->get('idTerrain');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $tableCiblage->ajouterTerrain($idTerrain);
                //Refresh
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                if ($type == "sort") {
                    $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    if ($idTerrain == 0) {
                        $action = "Ajout du ciblage de tous les terrains pour le sort : " . $sort->nom;
                    } else {
                        $terrain = Terrains::findById($idTerrain);
                        $action = "Ajout du ciblage du terrain : " . $terrain->genre . " " . $terrain->nom . " pour le sort :" . $sort->nom;
                    }

                    //Log de l'action
                    $logDEV = new LogsDEV();
                    $logDEV->action = $action;
                    $logDEV->idPersonnage = $auth['perso']->id;
                    $logDEV->dateLog = time();
                    $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                    $logDEV->save();
                }
                $this->view->disable();
                //On regenère le résumé
                return $tableCiblage->getResumeEnvironnement("modification", $perso);
            }
        }
    }

    /**
     * Permet d'afficher la table de ciblage en mode édition
     * @return unknown
     */
    public function editerTableCiblageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->tableCiblage = $tableCiblage;
                $this->view->type = "sort";
                $this->view->idType = $idType;
                $this->view->auth = $auth;
                $retour = $this->view->partial('utils/tableciblageEdition');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet de repasser en mode consultation
     * de la table de ciblage
     */
    public function annulerEditionTableCiblageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->tableCiblage = $tableCiblage;
                $this->view->type = "sort";
                $this->view->idType = $sort->id;
                $this->view->auth = $auth;
                $retour = $this->view->partial('utils/tableciblage.phtml');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Autorise le ciblage des environnements
     */
    public function autoriseEnvironnementAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                if ($tableCiblage != false) {
                    $tableCiblage->isCibleEnvironnement = 1;
                    $tableCiblage->save();

                    //Logs de l'action
                    if ($type == "sort") {
                        $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $action = "Autorisation de ciblage des environnements pour le sort : " . $sort->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                        $logDEV->save();
                    }
                }
                $this->view->disable();
            }
        }
    }

    /**
     * Autorise le ciblage des personnages
     */
    public function autoriserPersonnageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                if ($tableCiblage != false) {
                    $tableCiblage->isCiblePersonnage = 1;
                    $tableCiblage->save();

                    //Logs de l'action
                    if ($type == "sort") {
                        $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $action = "Autorisation de ciblage des personnages pour le sort : " . $sort->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                        $logDEV->save();
                    }
                }
                $this->view->disable();
            }
        }
    }

    /**
     * Interdire le ciblage des personnages
     */
    public function interdirePersonnageAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $type = $this->request->get('type');
                $idType = $this->request->get('idType');
                $tableCiblage = Tableciblage::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                if ($tableCiblage != false) {
                    $tableCiblage->isCiblePersonnage = 0;
                    $tableCiblage->save();

                    //Logs de l'action
                    if ($type == "sort") {
                        $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $action = "Interdiction de ciblage des personnages pour le sort : " . $sort->nom;
                        $logDEV = new LogsDEV();
                        $logDEV->action = $action;
                        $logDEV->idPersonnage = $auth['perso']->id;
                        $logDEV->dateLog = time();
                        $logDEV->typeLog = LogsDEV::TYPE_TABLE_CIBLAGE;
                        $logDEV->save();
                    }
                }
                $this->view->disable();
            }
        }
    }
}

