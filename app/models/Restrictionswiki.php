<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Restrictionswiki extends \Phalcon\Mvc\Model {

    // Différents type de restriction
    const RESTRICTIONWIKI_AUTORISATION = "Autorisation";
    const RESTRICTIONWIKI_ROYAUME = "Royaume";
    const RESTRICTIONWIKI_RACE = "Race";
    const RESTRICTIONWIKI_GRADE = "Grade";
    const RESTRICTIONWIKI_RELIGION = "Religion";

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
     * @Column(column="type", type="string", length=80, nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(column="idType", type="integer", length=11, nullable=false)
     */
    public $idType;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=false)
     */
    public $idArticle;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("restrictionswiki");

        //Init Jointure
        $this->belongsTo('idArticle', 'Articles', 'id');
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Restrictionswiki[]|Restrictionswiki|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Restrictionswiki|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Méthode permettant de vérifier les droits
     * @param unknown $perso
     * @param unknown $autorisations
     * @return boolean
     */
    public function verifieDroit($perso, $autorisations) {
        $retour = false;
        if ($this->type == RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION) {
            $autorisation = Autorisations::findFirst(["id =" . $this->idType]);
            $retour = Autorisations::hasAutorisation($autorisation->nomTechnique, $autorisations);
        } else {
            //Si MJ, non concerné par la restriction
            if (Autorisations::hasAutorisation(Autorisations::CONSULTATION_WIKI, $autorisations)) {
                return true;
            } else {
                if ($this->type == RestrictionsWiki::RESTRICTIONWIKI_ROYAUME) {
                    if ($perso->idRoyaume == $this->idType) {
                        $retour = true;
                    }
                } else {
                    if ($this->type == RestrictionsWiki::RESTRICTIONWIKI_RACE) {
                        if ($perso->idRace == $this->idRace) {
                            $retour = true;
                        }
                    } else {
                        if ($this->type == RestrictionsWiki::RESTRICTIONWIKI_GRADE) {
                            //TODO avec les grades
                            $retour = false;
                        } else {
                            if ($this->type == RestrictionsWiki::RESTRICTIONWIKI_RELIGION) {
                                if ($perso->idReligion == $restriction->idReligion) {
                                    $retour = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne la liste des types de restrictions possibles
     * @return string[]
     */
    public static function getListeTypeRestrictions() {
        $listeRestriction = array();
        $listeRestriction[0] = RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION;
        $listeRestriction[1] = RestrictionsWiki::RESTRICTIONWIKI_ROYAUME;
        $listeRestriction[2] = RestrictionsWiki::RESTRICTIONWIKI_RACE;
        $listeRestriction[3] = RestrictionsWiki::RESTRICTIONWIKI_GRADE;
        $listeRestriction[4] = RestrictionsWiki::RESTRICTIONWIKI_RELIGION;
        return $listeRestriction;
    }

    /**
     * Méthode permettant de retourner une liste de restrictions selon son type
     * @param unknown $type
     * @return string
     */
    public static function getListeRestriction($type) {
        $retour = "";
        if ($type == "0") {
            $retour = "";
        } else {
            if ($type == RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION) {
                $listeAutorisation = Autorisations::find(['type = :type:', 'bind' => ['type' => AUTORISATION_WIKI]]);
                if (!empty($listeAutorisation)) {
                    $retour .= "<select id='autorisation' class='listeDeroulante'><option value='0'>Choisissez une autorisation</option>";
                    foreach ($listeAutorisation as $autorisation) {
                        $retour .= "<option value='$autorisation->id'>" . $autorisation->libelle . "</option>";
                    }
                    $retour .= "</select>";
                    $retour .= '<input type="button" id="boutonAddAutorisation" class="buttonPlus" onclick="ajouterAutorisation();"/>';
                } else {
                    $retour .= "<select id='autorisation' class='listeDeroulante'><option value='0'>Il n'y a rien à proposer</option></select>";
                }
            } else {
                if ($type == RestrictionsWiki::RESTRICTIONWIKI_ROYAUME) {
                    $listeRoyaume = Royaumes::find();
                    if (!empty($listeRoyaume)) {
                        $retour .= "<select id='royaume' class='listeDeroulante'><option value='0'>Choisissez un royaume</option>";
                        foreach ($listeRoyaume as $royaume) {
                            $retour .= "<option value='$royaume->id'>" . $royaume->nom . "</option>";
                        }
                        $retour .= "</select>";
                        $retour .= '<input type="button" id="boutonAddAutorisation" class="buttonPlus" onclick="ajouterRoyaume();"/>';
                    } else {
                        $retour .= "<select id='royaume' class='listeDeroulante'><option value='0'>Il n'y a rien à proposer</option></select>";
                    }
                } else {
                    if ($type == RestrictionsWiki::RESTRICTIONWIKI_GRADE) {
                        //TODO : Voir à en faire une autcomplétion
                        $listeGrade = Grades::find();
                        if (!empty($listeGrade)) {
                            $retour .= "<select id='grade' class='listeDeroulante'><option value='0'>Choisissez un grade</option>";
                            foreach ($listeGrade as $grade) {
                                $retour .= "<option value='$grade->id'>" . $grade->nom . "</option>";
                            }
                            $retour .= "</select>";
                            $retour .= '<input type="button" id="boutonAddAutorisation" class="buttonPlus" onclick="ajouterGrade();"/>';
                        } else {
                            $retour .= "<select id='grade' class='listeDeroulante'><option value='0'>Il n'y a rien à proposer</option></select>";
                        }
                    } else {
                        if ($type == RestrictionsWiki::RESTRICTIONWIKI_RELIGION) {
                            $listeReligion = Religions::find();
                            if (!empty($listeReligion)) {
                                $retour .= "<select id='religion' class='listeDeroulante'><option value='0'>Choisissez un religion</option>";
                                foreach ($listeReligion as $religion) {
                                    $retour .= "<option value='$religion->id'>" . $religion->nom . "</option>";
                                }
                                $retour .= "</select>";
                                $retour .= '<input type="button" id="boutonAddAutorisation" class="buttonPlus" onclick="ajouterReligion();"/>';
                            } else {
                                $retour .= "<select id='religion' class='listeDeroulante'><option value='0'>Il n'y a rien à proposer</option></select>";
                            }
                        } else {
                            if ($type == RestrictionsWiki::RESTRICTIONWIKI_RACE) {
                                $listeRace = Races::find();
                                if (!empty($listeRace)) {
                                    $retour .= "<select id='race' class='listeDeroulante'><option value='0'>Choisissez une race</option>";
                                    foreach ($listeRace as $race) {
                                        $retour .= "<option value='$race->id'>" . $race->nom . "</option>";
                                    }
                                    $retour .= "</select>";
                                    $retour .= '<input type="button" id="boutonAddAutorisation" class="buttonPlus" onclick="ajouterRace();"/>';
                                } else {
                                    $retour .= "<select id='race' class='listeDeroulante'><option value='0'>Il n'y a rien à proposer</option></select>";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $retour;
    }
}
