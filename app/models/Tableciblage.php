<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Tableciblage extends \Phalcon\Mvc\Model {

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
     * @var integer
     * @Column(column="isCiblePersonnage", type="integer", length=1, nullable=false)
     */
    public $isCiblePersonnage;

    /**
     *
     * @var integer
     * @Column(column="isCibleCreature", type="integer", length=1, nullable=false)
     */
    public $isCibleCreature;

    /**
     *
     * @var integer
     * @Column(column="isCibleEnvironnement", type="integer", length=1, nullable=false)
     */
    public $isCibleEnvironnement;

    /**
     *
     * @var integer
     * @Column(column="isCibleBatiment", type="integer", length=1, nullable=false)
     */
    public $isCibleBatiment;

    /**
     *
     * @var string
     * @Column(column="cibleCreatureAutorisee", type="string", nullable=true)
     */
    public $cibleCreatureAutorisee;

    /**
     *
     * @var string
     * @Column(column="cibleEnvironnementAutorise", type="string", nullable=true)
     */
    public $cibleEnvironnementAutorise;

    /**
     *
     * @var string
     * @Column(column="cibleBatimentAutorise", type="string", nullable=true)
     */
    public $cibleBatimentAutorise;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("tableciblage");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tableciblage[]|Tableciblage|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Tableciblage|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * M??thode permettant de g??n??rer la phrase d'intro d'une table de ciblage
     * @param unknown $type
     * @param unknown $idType
     * @return string
     */
    public static function genererTableCiblagePour($type, $idType) {
        $retour = "";
        if ($type == "sorts") {
            $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
            $retour .= "le sort " . $sort->nom;
        }
        return $retour;
    }

    /**
     * G??n??re un r??sum?? de l'environnement
     * @param unknown $mode
     * @param unknown $auth
     * @return string
     */
    public function getResumeEnvironnement($mode, $auth) {
        $retour = "";
        if ($this->cibleEnvironnementAutorise == "") {
            return "Aucun";
        } else {
            $listeId = explode(",", $this->cibleEnvironnementAutorise);
            if ($listeId[0] == 0) {
                $retour .= "Tous";
                if ($mode == "modification" && Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                    $retour .= " <input type='button' class='buttonMoins' onclick='retirerEnvironnementCiblable(0);'/>";
                }
            } else {
                for ($i = 0; $i < count($listeId); $i++) {
                    $terrain = Terrains::findById($listeId[$i]);
                    if ($retour == "") {
                        $retour .= $terrain->genre . " " . $terrain->nom;
                    } else {
                        $retour .= " ," . $terrain->genre . " " . $terrain->nom;
                    }
                    if ($mode == "modification" && Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                        $retour .= " <input type='button' class='buttonMoins' onclick='retirerEnvironnementCiblable(" . $terrain->id . ");'/>";
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne un select des terrains ciblables
     * @return string
     */
    public function getListeTerrainCiblable() {
        //R??cup??rer la liste des terrains d??j?? renseign??s
        $tableauListeTerrainRenseignes = $this->decoupeListeTerrain();
        //Cas o?? "Tous" est s??lectionn??. Il n'y a alors pas de liste de g??n??r??e
        if (!empty($tableauListeTerrainRenseignes) && $tableauListeTerrainRenseignes[0] == 0) {
            return " ~ ";
        }

        $retour = "<select id='listeTerrainCiblable'><option value='0'>Tous</option>";
        //R??cup??rer la liste de tous les terrains
        $listeTerrains = Terrains::find();
        if ($listeTerrains != false && count($listeTerrains) > 0) {
            foreach ($listeTerrains as $terrain) {
                if (array_search($terrain->id, $tableauListeTerrainRenseignes) === false) {
                    $retour .= "<option value='" . $terrain->id . "'>" . $terrain->genre . " " . $terrain->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des ids des terrains ciblables
     */
    public function decoupeListeTerrain() {
        $retour = array();
        if ($this->cibleEnvironnementAutorise != "") {
            $retour = explode(",", $this->cibleEnvironnementAutorise);
        }
        return $retour;
    }

    /**
     * Permet de retirer un terrain de la liste des terrains cibl??s
     * @param unknown $idTerrain
     */
    public function retirerTerrain($idTerrain) {
        $listeId = explode(",", $this->cibleEnvironnementAutorise);
        $newCibleEnvironnementAutorise = "";
        if ($idTerrain != 0) {
            for ($i = 0; $i < count($listeId); $i++) {
                if ($listeId[$i] != $idTerrain) {
                    if ($newCibleEnvironnementAutorise == "") {
                        $newCibleEnvironnementAutorise = $listeId[$i];
                    } else {
                        $newCibleEnvironnementAutorise = "," . $listeId[$i];
                    }
                }
            }
        }
        $this->cibleEnvironnementAutorise = $newCibleEnvironnementAutorise;
        $this->save();
    }

    /**
     * Permet d'ajouter un terrain ?? la table de ciblage
     * @param unknown $idTerrain
     */
    public function ajouterTerrain($idTerrain) {
        if ($idTerrain == "0") {
            $this->cibleEnvironnementAutorise = 0;
        } else {
            if ($this->cibleEnvironnementAutorise != "") {
                $this->cibleEnvironnementAutorise = $this->cibleEnvironnementAutorise . "," . $idTerrain;
            } else {
                $this->cibleEnvironnementAutorise = $idTerrain;
            }
        }
        $this->save();
    }
}
