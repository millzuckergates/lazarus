<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Droits extends \Phalcon\Mvc\Model {

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
     * @Column(column="libelle", type="string", length=80, nullable=false)
     */
    public $libelle;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("droits");

        //Init Jointure
        $this->hasMany('id', 'AssocPersonnageDroit', 'idDroit', ['alias' => 'assoc_personnage_droit']);
        $this->hasManyToMany('id', 'AssocPersonnageDroit', 'idDroit', 'idPersonnage', 'Personnages', 'id', ['alias' => 'personnages']);

        $this->hasMany('id', 'AssocDroitAutorisation', 'idDroit', ['alias' => 'assoc_droit_autorisation']);
        $this->hasManyToMany('id', 'AssocDroitAutorisation', 'idDroit', 'idAutorisation', 'Autorisations', 'id', ['alias' => 'autorisations']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Droits[]|Droits|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Droits|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de générer la liste des profils
     * @param unknown $auth
     * @return string
     */
    public static function genererListeProfils($auth) {
        $perso = $auth['perso'];
        //Récupération de la liste des profils
        $listeProfil = Droits::find();
        //Récupération du premier profil du perso
        if (!empty($perso->droits)) {
            $idProfilPerso = $perso->droits[0]->id;
        } else {
            //Si pas de profil, juste "joueur"
            $idProfilPerso = 0;
        }
        $retour = "<select id='listeProfils' onChange='chargeListeAutorisations();'><option value='0'>Sélectionnez un profil</option>";
        foreach ($listeProfil as $droit) {
            if ($idProfilPerso == $droit->id) {
                $retour .= "<option id='droit" . $droit->id . "' value='" . $droit->id . "' selected>" . $droit->libelle . "</option>";
            } else {
                $retour .= "<option id='droit" . $droit->id . "' value='" . $droit->id . "'>" . $droit->libelle . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génére la liste des autorisations par profil
     * @param unknown $auth
     * @param unknown $idProfil
     * @return string
     */
    public static function genererListeAutorisations($auth, $idProfil) {
        $perso = $auth['perso'];
        if ($idProfil == null) {
            if (count($perso->droits) > 0) {
                $idProfil = $perso->droits[0]->id;
            } else {
                //Si pas de profil, juste "joueur"
                $idProfil = 0;
            }
        }
        if ($idProfil != 0) {
            $droit = Droits::findFirst(['id = :id:', 'bind' => ['id' => $idProfil]]);
        } else {
            $droit = null;
        }
        //On récupère la liste des autorisations
        $listeAutorisations = Autorisations::find(["order" => "type"]);
        $typeEnCours = "";
        $retour = "";
        $first = true;
        $disable = "disabled";
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_DROITS_MODIFICATION, $auth['autorisations'])) {
            $disable = "";
        }
        if (!empty($listeAutorisations)) {
            foreach ($listeAutorisations as $autorisation) {
                if ($typeEnCours != $autorisation->type) {
                    if ($first == true) {
                        $first = false;
                    } else {
                        $retour .= "</div><br/><br/>";
                    }
                    $typeEnCours = $autorisation->type;
                    $retour .= "<div id='typeAutorisation' class='blocTypeAutorisation'><span class='titreAutorisation'>" . $autorisation->type . "</span>";
                }
                $retour = $retour . "<br/>";
                if ($droit != null && Droits::hasAutorisation($autorisation, $droit->autorisations)) {
                    $retour .= "<input type='checkbox' name='autorisations' value='" . $autorisation->id . "' checked='checked' class='checkboxAutorisation' " . $disable . "><span class='libelleAutorisation'>" . $autorisation->libelle . "</span>";
                } else {
                    $retour .= "<input type='checkbox' name='autorisations' value='" . $autorisation->id . "' class='checkboxAutorisation' " . $disable . "><span class='libelleAutorisation'>" . $autorisation->libelle . "</span>";
                }
            }
            $retour .= "</div>";
        }
        return $retour;
    }

    /**
     * Permet de savoir si un droit est disponible
     * @param unknown $libelle
     * @return boolean
     */
    public static function isDroitAvailable($libelle) {
        $droit = Droits::findFirst(['libelle = :libelle:', 'bind' => ['libelle' => $libelle]]);
        if ($droit) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Créer un droit
     * @param unknown $libelle
     * @return Droits
     */
    public static function creer($libelle) {
        $droit = new Droits();
        $droit->libelle = $libelle;
        $droit->save();
        return $droit;
    }

    /**
     * Vérifie que l'autorisation est dans la liste des droits
     * @param unknown $autorisation
     * @param unknown $listeAutorisation
     * @return boolean
     */
    public static function hasAutorisation($autorisation, $listeAutorisation) {
        foreach ($listeAutorisation as $auto) {
            if ($autorisation->id == $auto->id) {
                return true;
            }
        }
        return false;
    }

    public function genererAutorisationTechnique() {
        $retour = array();
        if (count($this->autorisations) > 0) {
            foreach ($this->autorisations as $autorisation) {
                $retour[count($retour)] = $autorisation->nomTechnique;
            }
        }
        return $retour;
    }
}
