<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Constantesjeu extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
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
     * @Column(column="description", type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="valeur", type="string", length=300, nullable=false)
     */
    public $valeur;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=50, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(column="alt", type="string", length=300)
     */
    public $alt;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("constantesjeu");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Constantesjeu[]|Constantesjeu|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Constantesjeu|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Méthode permettant de retourner la liste de toutes les constantes du jeu
     * @return Constantesjeu[]|Constantesjeu|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getAllConstanteJeu() {
        $constantesJeu = Constantesjeu::find();
        return $constantesJeu;
    }

    /**
     * Méthode permettant de retourner une constante de jeu à partir de son nom
     * @param unknown $name
     * @return Constantesjeu|\Phalcon\Mvc\Model\ResultInterface|NULL
     */
    public static function getConstanteJeuByName($name) {
        $constanteJeu = Constantesjeu::findFirst(['nom = :nom:', 'bind' => ['nom' => $name]]);
        if ($constanteJeu != false) {
            return $constanteJeu;
        }
        return null;
    }

    /**
     * Méthode permettant de retourner une constante de jeu à partir de sa valeur
     * @param unknown $value
     * @return number
     */
    public static function getConstanteJeuByValue($value) {
        $constantesJeu = Constantesjeu::findFirst(['valeur = :valeur', 'bind' => ['valeur' => $value]]);
        if ($constantesJeu != false) {
            return $constantesJeu->id;
        }
        return 0;
    }


    /**
     * Méthode pour générer un select de la liste des
     * types de constantes
     * @param unknown $idSelect
     * @return string
     */
    public static function genererListeTypesConstante($idSelect) {
        $reponse = Constantesjeu::find(['distinct' => 'type', 'order' => 'type']);
        $retour = "<select id='" . $idSelect . "' onchange='chargerListeConstante();'><option value='all'>Tous</option>";
        if ($reponse != false && count($reponse) > 0) {
            foreach ($reponse as $type) {
                $retour .= "<option value='" . $type->type . "'>" . $type->type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne un select de la liste des constantes
     * @param unknown $type
     * @param unknown $idSelect
     * @return string
     */
    public static function genererListeConstantes($type, $idSelect) {
        if ($type == "all") {
            $reponse = Constantesjeu::find();
        } else {
            $reponse = Constantesjeu::find(['type = :type:', 'bind' => ['type' => $type]]);
        }
        $retour = "<select id='" . $idSelect . "' onchange='chargerDescriptionConstante();'>";
        if ($reponse != false && count($reponse) > 0) {
            foreach ($reponse as $constante) {
                $retour .= "<option value='" . $constante->id . "'>" . $constante->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

}
