<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Bonus extends \Phalcon\Mvc\Model {

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
     * @Column(column="nom", type="string", length=60, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=200, nullable=false)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="fichier", type="string", length=60, nullable=false)
     */
    public $fichier;

    public $listeParametres = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("bonus");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bonus[]|Bonus|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bonus|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    public static function getListeBonus() {
        $retour = "";
        $listeBonus = Bonus::find(['order' => 'nom']);
        $retour .= "<select id='selectBonus' onChange='chargerDetailBonus()'><option value='0'>Aucun</option>";
        if ($listeBonus != false && count($listeBonus) > 0) {
            foreach ($listeBonus as $bonus) {
                $retour .= "<option value='" . $bonus->id . "'>" . $bonus->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    public static function getDescriptionGenerale() {
        $retour = "";
        $listeBonus = Bonus::find(['order' => 'nom']);
        if ($listeBonus != false && count($listeBonus) > 0) {
            $retour .= "<ul id='detailTexteBonus'>";
            foreach ($listeBonus as $bonus) {
                $retour .= "<li class='texteBonus'><span class='nomBonusTexte'>" . $bonus->nom . " : </span><span class='descriptionBonusTexte'>" . $bonus->description . "</span></li>";
            }
            $retour .= "</ul>";
        }
        return $retour;
    }

    public function genererDetailBonus($type, $assoc, $idAssoc, $position) {
        $description = $this->description;
        if ($type != "creation") {
            $this->evaluerBonus($assoc, $idAssoc, $position);
        }
        $fichier = new $this->fichier();

        $descriptionDetaillee = $fichier->getDescriptionDetaillee($this, $type);
        $template = $fichier->getTemplate($this, $type);

        $retour = "<div id='descriptionBonus' class='descriptionElement'>" . $description . "</div>";
        if ($type != "edition") {
            $retour .= "<div id='descriptionDetaillee' class='descriptionDetailleeElement'>" . $descriptionDetaillee . "</div>";
        }
        if ($type == "edition" || $type == "creation") {
            $retour .= "<div id='templateBonus' class='templateElement'>" . $template . "</div>";
        }
        return $retour;
    }

    public function evaluerBonus($assoc, $idAssoc, $position) {
        $this->chargerListeParam();
        $listeParametreEvalue = array();
        if ($this->listeParametres != null) {
            foreach ($this->listeParametres as $assocBonusParam) {
                $idParam = $assocBonusParam->idParam;
                if ($assoc == "royaume") {
                    $paramEvalue = AssocRoyaumesBonusParam::findFirst(['idRoyaume = :idRoyaume: AND idBonus = :idBonus: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idRoyaume' => $idAssoc, 'idBonus' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                } else {
                    if ($assoc == "race") {
                        $paramEvalue = AssocRacesBonusParam::findFirst(['idRace = :idRace: AND idBonus = :idBonus: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idRace' => $idAssoc, 'idBonus' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                    } else {
                        if ($assoc == "religion") {
                            $paramEvalue = AssocReligionsBonusParam::findFirst(['idReligion = :idReligion: AND idBonus = :idBonus: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idReligion' => $idAssoc, 'idBonus' => $this->id, 'idParam' => $idParam, 'position' => $position]]);
                        }
                    }
                }

                if ($paramEvalue != false) {
                    //On récupère le paramètre en cours
                    $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $idParam]]);
                    $parametre->valeur = $paramEvalue->valeur;
                    $parametre->position = $paramEvalue->position;
                    $listeParametreEvalue[count($listeParametreEvalue)] = $parametre;
                }
            }
        }
        $this->listeParametres = $listeParametreEvalue;
    }

    public function chargerListeParam() {
        $listeParam = BonusParam::find(['idBonus = :idBonus:', 'bind' => ['idBonus' => $this->id], 'order' => 'position']);
        if ($listeParam != false && count($listeParam) > 0) {
            $this->listeParametres = $listeParam;
        } else {
            $this->listeParametres = null;
        }
    }

    public function genererImage($type, $idType, $position) {
        $this->evaluerBonus($type, $idType, $position);
        //On inclut le fichier du bonus
        $fichierBonus = new $this->fichier();
        return $fichierBonus->genererImage($this);
    }

    public function genererDescription($type, $idType, $position) {
        $this->evaluerBonus($type, $idType, $position);
        //On inclut le fichier du bonus
        $fichierBonus = new $this->fichier();
        return $fichierBonus->genererDescription($this);
    }

    public function getNewPosition($type, $idType) {
        if ($type == "royaume") {
            $maxPosition = AssocRoyaumesBonusParam::maximum(['column' => 'position', 'condition' => 'idRoyaume = :id:', 'bind' => ['id' => $idType]]);
            if ($maxPosition != false) {
                return $maxPosition + 1;
            } else {
                return 1;
            }
        } else {
            if ($type == "race") {
                $maxPosition = AssocRacesBonusParam::maximum(['column' => 'position', 'condition' => 'idRace = :id:', 'bind' => ['id' => $idType]]);
                if ($maxPosition != false) {
                    return $maxPosition + 1;
                } else {
                    return 1;
                }
            } else {
                if ($type == "religion") {
                    $maxPosition = AssocReligionsBonusParam::maximum(['column' => 'position', 'condition' => 'idReligion = :id:', 'bind' => ['id' => $idType]]);
                    if ($maxPosition != false) {
                        return $maxPosition + 1;
                    } else {
                        return 1;
                    }
                }
            }
        }
    }

    public function enleverChoixMultiple($param1, $param2, $idElement) {
        $fichierBonus = new $this->fichier();
        return $fichierBonus->enleverChoixMultiple($param1, $param2, $idElement);
    }

    public function ajouterChoixMultiple($param1, $param2, $idElement) {
        $fichierBonus = new $this->fichier();
        return $fichierBonus->ajouterChoixMultiple($param1, $param2, $idElement);
    }

    /**
     * Méthode pour tester les paramètres du bonus
     *
     * @param unknown $listeParametres
     * @return unknown
     */
    public function testerParametres($listeParametres) {
        //On inclut le fichier du bonus
        $fichierBonust = new $this->fichier();
        $retour = $fichierBonust->testerParametres($listeParametres);
        return $retour;
    }

    /**
     * Permet d'ajouter une association entre un bonus et un element
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     */
    public function insertAssoc($listeParam, $type, $idType) {
        //On inclut le fichier du bonus
        $this->chargerListeParam();
        $fichierBonus = new $this->fichier();
        $fichierBonus->insertAssoc($listeParam, $type, $idType, $this);
    }

    /**
     * Permet de modifier un bonus
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $position
     */
    public function modifieAssoc($listeParam, $type, $idType, $position) {
        $this->evaluerBonus($type, $idType, $position);
        //On inclut le fichier du bonus
        $fichierBonus = new $this->fichier();
        $fichierBonus->modifierAssoc($listeParam, $type, $idType, $this, $position);
    }
}
