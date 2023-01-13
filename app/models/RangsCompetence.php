<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class RangsCompetence extends \Phalcon\Mvc\Model {

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
     * @Column(column="nom", type="string", length=90, nullable=false)
     */
    public $nom;

    /**
     *
     * @var integer
     * @Column(column="niveau", type="integer", length=2, nullable=false)
     */
    public $niveau;

    /**
     *
     * @var integer
     * @Column(column="pointAAtteindre", type="integer", length=8, nullable=false)
     */
    public $pointAAtteindre;

    /**
     *
     * @var integer
     * @Column(column="description", type="text")
     */
    public $description;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("rangs_competence");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RangsCompetence[]|RangsCompetence|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RangsCompetence|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet d'afficher la liste des rangs
     * @param unknown $auth
     * @return string
     */
    public static function genererListeRang($auth) {
        $retour = "<div class='divTitreListeRangCompetence'>";
        $retour .= "<span class='titreListerangCompetence'>Rangs par défaut</span>";
        $retour .= "</div>";
        $listeRang = RangsCompetence::find(['order' => 'niveau']);
        if ($listeRang != false && count($listeRang) > 0) {
            $retour .= "<table id='tableListeRang' class='tableListeRang'>";
            foreach ($listeRang as $rang) {
                $retour .= "<tr class='rangCompetence' id='rangCompetence_" . $rang->id . "'>";
                $retour .= "<td class='nomRangCompetence' title='" . str_replace("'", "&#39;", $rang->description) . "'>" . $rang->niveau . " - " . $rang->nom . "</td>";
                $retour .= "<td id='pointRang_" . $rang->id . "' class='valueRang' title='Il faut dépenser " . $rang->pointAAtteindre . " pour atteindre ce rang.'>" . $rang->pointAAtteindre . "</td>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<td class='boutonRangActualiser'><input type='button' class='buttonActualiser' onClick='ouvreEditionRang(" . $rang->id . ");' title='Permet d&#39;accéder à l&#39;interface d&#39;édition du rang.'/></td>";
                }
                $retour .= "</tr>";
            }
            $retour .= "</table>";
        } else {
            $retour .= "<span class='resultatRangVide'>Aucun rang n'est encore défini.</span>";
        }
        //Ajout des boutons
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
            $retour .= "<div id='boutonRangCompetence'>";
            $retour .= Phalcon\Tag::SubmitButton(array("Ajouter", 'id' => 'ajouterRangCompetence', 'class' => 'bouton', 'title' => "Permet de créer un nouveau rang."));
            $retour .= "</div>";
        }
        return $retour;
    }

    /**
     * Retourne le prochain niveau pour un rang
     * @return number
     */
    public static function getNextLevel() {
        $maxPosition = RangsCompetence::maximum(['column' => 'niveau']);
        if ($maxPosition != false) {
            return $maxPosition + 1;
        } else {
            return 1;
        }
    }

    /**
     * Retourne le nombre de point max suivant
     * @return number
     */
    public static function getNextPointAAtteindre() {
        $maxPosition = RangsCompetence::maximum(['column' => 'pointAAtteindre']);
        if ($maxPosition != false) {
            return $maxPosition + 1;
        } else {
            return 1;
        }
    }

    /**
     * Retourne l'objet sous une chaine de caractère
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Nom : " . $this->nom . "], ";
        $retour .= "[Niveau : " . $this->niveau . "], ";
        $retour .= "[Point à atteindre : " . $this->pointAAtteindre . "]";
        return $retour;
    }
}
