<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Effets extends \Phalcon\Mvc\Model {

    /**
     * Constantes pour les types d'effets
     */
    const TYPE_EFFET_DEPLACEMENT = "Mouvement";
    const TYPE_EFFET_VISION = "Vision";
    const TYPE_EFFET_DEGAT = "Dégât direct";
    const TYPE_EFFET_ALTERATION_CARAC = "Altération caractéristique";

    /**
     * Constante pour les actions déclenchant des effets
     */
    const ACTION_DEPLACEMENT = "sur un déplacement";
    const ACTION_SORT = "sur lancement d'un sort";
    const ACTION_ATTAQUE = "sur une attaque";
    const ACTION_CONTACT = "au contact";

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(column="nom", type="string", length=40, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="fichier", type="string", length=255, nullable=true)
     */
    public $fichier;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=80, nullable=true)
     */
    public $type;

    public $listeParametres = array();

    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("effets");

        $this->hasMany('id', 'AssocEffetsParam', 'idEffet', ['alias' => 'assoc_effet_params']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Effets[]|Effets|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Effets|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des différents types d'effets possibles
     * @return string[]
     */
    public static function getListeType() {
        $retour = array();
        $retour[count($retour)] = Effets::TYPE_EFFET_DEPLACEMENT;
        $retour[count($retour)] = Effets::TYPE_EFFET_VISION;
        $retour[count($retour)] = Effets::TYPE_EFFET_DEGAT;
        $retour[count($retour)] = Effets::TYPE_EFFET_ALTERATION_CARAC;
        return $retour;
    }

    /**
     * Construit le select avec la liste des types de l'effet
     * @return string
     */
    public static function getSelectType() {
        $listeTypeEffet = Effets::getListeType();
        $retour = "<select id='selectTypeEffet' onchange='chargerEffetByType();'><option value='all'>Tous</option>";
        if ($listeTypeEffet != null && !empty($listeTypeEffet)) {
            foreach ($listeTypeEffet as $type) {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des effets par type
     * @param string $type
     * @return string
     */
    public static function getSelectEffet($type = false) {
        $retour = "<select id='selectEffet' onchange='chargerDetailEffet();'><option value='0'>Sélectionnez un effet</option>";
        if (!$type || $type == "all") {
            $listeEffets = Effets::find(['order' => 'type']);
        } else {
            $listeEffets = Effets::find(['type = :type:', 'bind' => ['type' => $type]]);
        }

        if ($listeEffets != false && count($listeEffets) > 0) {
            foreach ($listeEffets as $effet) {
                $retour .= "<option value='" . $effet->id . "'>" . $effet->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère les détails d'un effet
     * @param unknown $type
     * @param unknown $assoc
     * @param unknown $idAssoc
     * @param unknown $position
     * @return string
     */
    public function genererDetailEffet($type, $assoc, $idAssoc, $position) {
        $description = $this->description;
        if ($type != "creation") {
            $this->evaluerEffet($assoc, $idAssoc, $position);
        }
        $fichier = new $this->fichier();

        $descriptionDetaillee = $fichier->getDescriptionDetaillee($this, $type);
        $template = $fichier->getTemplate($this, $type);

        $retour = "<div id='descriptionEffet' class='descriptionElement'>" . $description . "</div>";
        if ($type != "edition") {
            $retour .= "<div id='descriptionDetaillee' class='descriptionDetailleeElement'>" . $descriptionDetaillee . "</div>";
        }
        if ($type == "edition" || $type == "creation") {
            $retour .= "<div id='templateEffet' class='templateElement'>" . $template . "</div>";
        }
        return $retour;
    }

    /**
     * Permet d'évaluer un effet
     * @param unknown $assoc
     * @param unknown $idAssoc
     * @param unknown $position
     */
    public function evaluerEffet($assoc, $idAssoc, $position) {
        $this->chargerListeParam();
        $listeParametreEvalue = array();
        if ($this->listeParametres != null) {
            foreach ($this->listeParametres as $assocEffetParam) {
                $idParam = $assocEffetParam->idParam;
                if ($assoc == "sort") {
                    $paramEvalue = AssocSortsEffetsParam::findFirst(['idSort = :idSort: AND idEffet = :idEffet: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idSort' => $idAssoc, 'idEffet' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                    //On récupère le paramètre en cours
                    $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                    $parametre->valeur = $paramEvalue->valeur;
                    $parametre->valeurMin = $paramEvalue->valeurMin;
                    $parametre->valeurMax = $paramEvalue->valeurMax;
                    $parametre->position = $paramEvalue->position;
                    $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                } else {
                    if ($assoc == "terrain") {
                        $paramEvalue = AssocTerrainsEffetsParam::findFirst(['idTerrain = :idTerrain: AND idEffet = :idEffet: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idTerrain' => $idAssoc, 'idEffet' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                        //On récupère le paramètre en cours
                        $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                        $parametre->valeur = $paramEvalue->valeur;
                        $parametre->valeurMin = $paramEvalue->valeurMin;
                        $parametre->valeurMax = $paramEvalue->valeurMax;
                        $parametre->position = $paramEvalue->position;
                        $parametre->action = $paramEvalue->action;
                        $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                    } else {
                        if ($assoc == "carte") {
                            $paramEvalue = AssocCartesEffetsParam::findFirst(['idCarte = :idCarte: AND idEffet = :idEffet: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idCarte' => $idAssoc, 'idEffet' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                            //On récupère le paramètre en cours
                            $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                            $parametre->valeur = $paramEvalue->valeur;
                            $parametre->valeurMin = $paramEvalue->valeurMin;
                            $parametre->valeurMax = $paramEvalue->valeurMax;
                            $parametre->position = $paramEvalue->position;
                            $parametre->action = $paramEvalue->action;
                            $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                        } else {
                            if ($assoc == "talent") {
                                $paramEvalue = AssocTalentsEffetsParam::findFirst(['idTalent = :idTalent: AND idEffet = :idEffet: AND idParam = :idParam: AND position = :position:',
                                  'bind' => ['idTalent' => $idAssoc, 'idEffet' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                                //On récupère le paramètre en cours
                                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                                $parametre->valeur = $paramEvalue->valeur;
                                $parametre->valeurMin = $paramEvalue->valeurMin;
                                $parametre->valeurMax = $paramEvalue->valeurMax;
                                $parametre->position = $paramEvalue->position;
                                $parametre->action = $paramEvalue->action;
                                $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                            } else {
                                if ($assoc == "competence") {
                                    $paramEvalue = AssocCompetencesEffetsParam::findFirst(['idCompetence = :idCompetence: AND idEffet = :idEffet: AND idParam = :idParam: AND position = :position:',
                                      'bind' => ['idCompetence' => $idAssoc, 'idEffet' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                                    //On récupère le paramètre en cours
                                    $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                                    $parametre->valeur = $paramEvalue->valeur;
                                    $parametre->valeurMin = $paramEvalue->valeurMin;
                                    $parametre->valeurMax = $paramEvalue->valeurMax;
                                    $parametre->position = $paramEvalue->position;
                                    $parametre->action = $paramEvalue->action;
                                    $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->listeParametres = $listeParametreEvalue;
    }

    /**
     * Permet de charger la liste des paramètres
     */
    public function chargerListeParam() {
        $listeParam = AssocEffetsParam::find(['idEffet = :idEffet:', 'bind' => ['idEffet' => $this->id], 'order' => 'position']);
        if ($listeParam != false && count($listeParam) > 0) {
            $this->listeParametres = $listeParam;
        } else {
            $this->listeParametres = null;
        }
    }

    /**
     * Méthode pour tester les paramètres de l'effet
     *
     * @param unknown $listeParametres
     * @return unknown
     */
    public function testerParametres($listeParametres) {
        //On inclut le fichier de l'effet
        $fichierEffet = new $this->fichier();
        $retour = $fichierEffet->testerParametres($listeParametres);
        return $retour;
    }

    /**
     * Permet d'ajouter une association entre un effet et un element
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     */
    public function insertAssoc($listeParam, $type, $idType, $action) {
        //On inclut le fichier de l'effet
        $this->chargerListeParam();
        $fichierEffet = new $this->fichier();
        $fichierEffet->insertAssoc($listeParam, $type, $idType, $this, $action);
    }

    /**
     * Retourne la position occupé par l'effet crée
     * @param unknown $type
     * @param unknown $idType
     * @return number
     */
    public function getNewPositionEffet($type, $idType) {
        if ($type == "sort") {
            $maxPosition = AssocSortsEffetsParam::maximum(['column' => 'position', 'condition' => 'idSort = :id:', 'bind' => ['id' => $idType]]);
            if ($maxPosition != false) {
                return $maxPosition + 1;
            } else {
                return 1;
            }
        } else {
            if ($type == "talent") {
                $maxPosition = AssocTalentsEffetsParam::maximum(['column' => 'position', 'condition' => 'idTalent = :id:', 'bind' => ['id' => $idType]]);
                if ($maxPosition != false) {
                    return $maxPosition + 1;
                } else {
                    return 1;
                }
            } else {
                if ($type == "terrain") {
                    $maxPosition = AssocTerrainsEffetsParam::maximum(['column' => 'position', 'condition' => 'idTerrain = :id:', 'bind' => ['id' => $idType]]);
                    if ($maxPosition != false) {
                        return $maxPosition + 1;
                    } else {
                        return 1;
                    }
                } else {
                    if ($type == "carte") {
                        $maxPosition = AssocCartesEffetsParam::maximum(['column' => 'position', 'condition' => 'idCarte = :id:', 'bind' => ['id' => $idType]]);
                        if ($maxPosition != false) {
                            return $maxPosition + 1;
                        } else {
                            return 1;
                        }
                    } else {
                        if ($type == "competence") {
                            $maxPosition = AssocCompetencesEffetsParam::maximum(['column' => 'position', 'condition' => 'idCarte = :id:', 'bind' => ['id' => $idType]]);
                            if ($maxPosition != false) {
                                return $maxPosition + 1;
                            } else {
                                return 1;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Permet de modifier un effet
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $position
     */
    public function modifieAssoc($listeParam, $type, $idType, $position) {
        $this->evaluerEffet($type, $idType, $position);
        //On inclut le fichier de l'effet
        $fichierEffet = new $this->fichier();
        $fichierEffet->modifierAssoc($listeParam, $type, $idType, $this, $position);
    }

    /**
     * Permet de générer la description de l'effet
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $position
     * @return unknown
     */
    public function genererDescription($type, $idType, $position) {
        $this->evaluerEffet($type, $idType, $position);
        //On inclut le fichier de l'effet
        $fichierEffet = new $this->fichier();
        return $fichierEffet->genererDescription($this);
    }

    /**
     * Retourne la liste des actions à partir desquels les effets
     * sont déclenchés
     * @return string[]
     */
    public static function getListeActionEffet() {
        $retour = array();
        $retour[count($retour)] = Effets::ACTION_DEPLACEMENT;
        $retour[count($retour)] = Effets::ACTION_SORT;
        $retour[count($retour)] = Effets::ACTION_ATTAQUE;
        $retour[count($retour)] = Effets::ACTION_CONTACT;
        return $retour;
    }

    /**
     * Retourne le select des liste d'action pour les effets
     * @return string
     */
    public static function genererListeActionEffet() {
        $listeAction = Effets::getListeActionEffet();
        $retour = "<select id='selectActionEffet'>";
        foreach ($listeAction as $action) {
            $retour .= "<option value='" . $action . "'>" . $action . "</option>";
        }
        $retour .= "</select>";
        return $retour;
    }

    public function genererDescriptionEvaluee($element, $auth, $type, $mode, $position, $modificateur = null) {
        $this->evaluerEffet($type, $element->id, $position);
        //On inclut le fichier de l'effet
        $fichierEffet = new $this->fichier();
        return $fichierEffet->genererDescriptionEvaluee($this, $element, $auth, $type, $mode, $modificateur);
    }
}
