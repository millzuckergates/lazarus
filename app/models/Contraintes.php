<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Contraintes extends \Phalcon\Mvc\Model {

    /**
     * Constantes pour les types de contraintes
     */
    const TYPE_CONTRAINTE_PERSONNAGE = "Personnage";  //Compétences, Niveaux, Caractéristiques, etc...
    const TYPE_CONTRAINTE_TALENT = "Talent"; //Nombre de points dépensés dans Categorie/Famille/Arbre/Talent/Liaison Fils-Père
    const TYPE_CONTRAINTE_OBJET = "Objet";

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

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("contraintes");

        $this->hasMany('id', 'AssocContraintesParam', 'idContrainte', ['alias' => 'assoc_contrainte_params']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Contraintes[]|Contraintes|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Contraintes|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des différents types de contrainte possibles
     * @return string[]
     */
    public static function getListeType() {
        $retour = array();
        $retour[count($retour)] = Contraintes::TYPE_CONTRAINTE_PERSONNAGE;
        $retour[count($retour)] = Contraintes::TYPE_CONTRAINTE_TALENT;
        $retour[count($retour)] = Contraintes::TYPE_CONTRAINTE_OBJET;
        return $retour;
    }

    /**
     * Construit le select avec la liste des types de la contrainte
     * @return string
     */
    public static function getSelectType() {
        $listeTypeContrainte = Contraintes::getListeType();
        $retour = "<select id='selectTypeContrainte' onchange='chargerContrainteByType();'><option value='all'>Tous</option>";
        if ($listeTypeContrainte != null && !empty($listeTypeContrainte)) {
            foreach ($listeTypeContrainte as $type) {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des contraintes par type
     * @param string $type
     * @return string
     */
    public static function getSelectContrainte($type = false) {
        $retour = "<select id='selectContrainte' onchange='chargerDetailContrainte();'><option value='0'>Sélectionnez une contrainte</option>";
        if (!$type || $type == "all") {
            $listeContraintes = Contraintes::find(['order' => 'type']);
        } else {
            $listeContraintes = Contraintes::find(['type = :type:', 'bind' => ['type' => $type]]);
        }

        if ($listeContraintes != false && count($listeContraintes) > 0) {
            foreach ($listeContraintes as $contrainte) {
                $retour .= "<option value='" . $contrainte->id . "'>" . $contrainte->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère les détails d'une contrainte
     * @param unknown $type
     * @param unknown $assoc
     * @param unknown $idAssoc
     * @param unknown $position
     * @return string
     */
    public function genererDetailContrainte($type, $assoc, $idAssoc, $position) {
        $description = $this->description;
        if ($type != "creation") {
            $this->evaluerContrainte($assoc, $idAssoc, $position);
        }
        $fichier = new $this->fichier();

        $descriptionDetaillee = $fichier->getDescriptionDetaillee($this, $type);
        $template = $fichier->getTemplate($this, $type);

        $retour = "<div id='descriptionContrainte' class='descriptionElement'>" . $description . "</div>";
        if ($type != "edition") {
            $retour .= "<div id='descriptionDetaillee' class='descriptionDetailleeElement'>" . $descriptionDetaillee . "</div>";
        }
        if ($type == "edition" || $type == "creation") {
            $retour .= "<div id='templateContrainte' class='templateElement'>" . $template . "</div>";
        }
        return $retour;
    }

    /**
     * Permet d'évaluer une contrainte
     * @param unknown $assoc
     * @param unknown $idAssoc
     * @param unknown $position
     */
    public function evaluerContrainte($assoc, $idAssoc, $position) {
        $this->chargerListeParam();
        $listeParametreEvalue = array();
        if ($this->listeParametres != null) {
            foreach ($this->listeParametres as $assocContrainteParam) {
                $idParam = $assocContrainteParam->idParam;
                if ($assoc == "sort") {
                    $paramEvalue = AssocSortsContraintesParam::findFirst(['idSort = :idSort: AND idContrainte = :idContrainte: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idSort' => $idAssoc, 'idContrainte' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                    //On récupère le paramètre en cours
                    $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                    $parametre->valeur = $paramEvalue->valeur;
                    $parametre->valeurMin = $paramEvalue->valeurMin;
                    $parametre->valeurMax = $paramEvalue->valeurMax;
                    $parametre->position = $paramEvalue->position;
                    $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                } else {
                    if ($assoc == "familleTalent") {
                        $paramEvalue = AssocFamilletalentsContraintesParam::findFirst(['idFamille = :idFamille: AND idContrainte = :idContrainte: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idFamille' => $idAssoc, 'idContrainte' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                        //On récupère le paramètre en cours
                        $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                        $parametre->valeur = $paramEvalue->valeur;
                        $parametre->valeurMin = $paramEvalue->valeurMin;
                        $parametre->valeurMax = $paramEvalue->valeurMax;
                        $parametre->position = $paramEvalue->position;
                        $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                    } else {
                        if ($assoc == "arbreTalent") {
                            $paramEvalue = AssocArbretalentsContraintesParam::findFirst(['idArbre = :idArbre: AND idContrainte = :idContrainte: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idArbre' => $idAssoc, 'idContrainte' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                            //On récupère le paramètre en cours
                            $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                            $parametre->valeur = $paramEvalue->valeur;
                            $parametre->valeurMin = $paramEvalue->valeurMin;
                            $parametre->valeurMax = $paramEvalue->valeurMax;
                            $parametre->position = $paramEvalue->position;
                            $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                        } else {
                            if ($assoc == "talent") {
                                $paramEvalue = AssocTalentsContraintesParam::findFirst(['idTalent = :idTalent: AND idContrainte = :idContrainte: AND idParam = :idParam: AND position = :position:',
                                  'bind' => ['idTalent' => $idAssoc, 'idContrainte' => $this->id, 'idParam' => $idParam, 'position' => $position], 'order' => "idParam"]);
                                //On récupère le paramètre en cours
                                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                                $parametre->valeur = $paramEvalue->valeur;
                                $parametre->valeurMin = $paramEvalue->valeurMin;
                                $parametre->valeurMax = $paramEvalue->valeurMax;
                                $parametre->position = $paramEvalue->position;
                                $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                            } else {
                                if ($assoc == "competence") {
                                    $paramEvalue = AssocCompetencesContraintesParam::findFirst(['idCompetence = :idCompetence: AND idContrainte = :idContrainte: AND idParam = :idParam: AND position = :position:',
                                      'bind' => ['idCompetence' => $idAssoc, 'idContrainte' => $this->id, 'idParam' => $idParam, 'position' => $position], 'order' => "idParam"]);
                                    //On récupère le paramètre en cours
                                    $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                                    $parametre->valeur = $paramEvalue->valeur;
                                    $parametre->valeurMin = $paramEvalue->valeurMin;
                                    $parametre->valeurMax = $paramEvalue->valeurMax;
                                    $parametre->position = $paramEvalue->position;
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
        $listeParam = AssocContraintesParam::find(['idContrainte = :idContrainte:', 'bind' => ['idContrainte' => $this->id], 'order' => 'position']);
        if ($listeParam != false && count($listeParam) > 0) {
            $this->listeParametres = $listeParam;
        } else {
            $this->listeParametres = null;
        }
    }

    /**
     * Méthode pour tester les paramètres de la contrainte
     *
     * @param unknown $listeParametres
     * @return unknown
     */
    public function testerParametres($listeParametres) {
        //On inclut le fichier de la contrainte
        $fichierContrainte = new $this->fichier();
        $retour = $fichierContrainte->testerParametres($listeParametres);
        return $retour;
    }

    /**
     * Permet d'ajouter une association entre une contrainte et un element
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     */
    public function insertAssoc($listeParam, $type, $idType, $action) {
        //On inclut le fichier de la contrainte
        $this->chargerListeParam();
        $fichierContrainte = new $this->fichier();
        $fichierContrainte->insertAssoc($listeParam, $type, $idType, $this, $action);
    }

    /**
     * Retourne la position occupée par la contrainte créée
     * @param unknown $type
     * @param unknown $idType
     * @return number
     */
    public function getNewPositionContrainte($type, $idType) {
        if ($type == "sort") {
            $maxPosition = AssocSortsContraintesParam::maximum(['column' => 'position', 'condition' => 'idSort = :id:', 'bind' => ['id' => $idType]]);
            if ($maxPosition != false) {
                return $maxPosition + 1;
            } else {
                return 1;
            }
        } else {
            if ($type == "talent") {
                $maxPosition = AssocTalentsContraintesParam::maximum(['column' => 'position', 'condition' => 'idTalent = :id:', 'bind' => ['id' => $idType]]);
                if ($maxPosition != false) {
                    return $maxPosition + 1;
                } else {
                    return 1;
                }
            } else {
                if ($type == "familleTalent") {
                    $maxPosition = AssocFamilletalentsContraintesParam::maximum(['column' => 'position', 'condition' => 'idFamille = :id:', 'bind' => ['id' => $idType]]);
                    if ($maxPosition != false) {
                        return $maxPosition + 1;
                    } else {
                        return 1;
                    }
                } else {
                    if ($type == "arbreTalent") {
                        $maxPosition = AssocArbretalentsContraintesParam::maximum(['column' => 'position', 'condition' => 'idArbre = :id:', 'bind' => ['id' => $idType]]);
                        if ($maxPosition != false) {
                            return $maxPosition + 1;
                        } else {
                            return 1;
                        }
                    } else {
                        if ($type == "competence") {
                            $maxPosition = AssocCompetencesContraintesParam::maximum(['column' => 'position', 'condition' => 'idCompetence = :id:', 'bind' => ['id' => $idType]]);
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
     * Permet de modifier une contrainte
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $position
     */
    public function modifieAssoc($listeParam, $type, $idType, $position) {
        $this->evaluerContrainte($type, $idType, $position);
        //On inclut le fichier de la contrainte
        $fichierContrainte = new $this->fichier();
        $fichierContrainte->modifierAssoc($listeParam, $type, $idType, $this, $position);
    }

    /**
     * Permet de générer la description de la contrainte
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $position
     * @return unknown
     */
    public function genererDescription($type, $idType, $position) {
        $this->evaluerContrainte($type, $idType, $position);
        //On inclut le fichier de la contrainte
        $fichierContrainte = new $this->fichier();
        return $fichierContrainte->genererDescription($this);
    }

    public function isVerif($mode, $auth, $cible) {
        //On inclut le fichier de la contrainte
        $fichierContrainte = new $this->fichier();
        return $fichierContrainte->verif($mode, $auth, $cible, $this);
    }

    public function genererDescriptionPourTalent($mode, $auth) {
        //On inclut le fichier de la contrainte
        $fichierContrainte = new $this->fichier();
        return $fichierContrainte->genererDescription($this);
    }
}
