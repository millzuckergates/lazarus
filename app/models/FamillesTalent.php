<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class FamillesTalent extends \Phalcon\Mvc\Model {

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
     * @Column(column="idCategorie", type="integer", length=11, nullable=false)
     */
    public $idCategorie;

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

    public $listeContraintes = array();

    public $arbres;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("familles_talent");

        //Init Jointure
        $this->hasMany('id', 'ArbresTalent', 'idFamille', ['alias' => 'arbres']);
        $this->hasOne('idCategorie', 'CategoriesTalent', 'id', ['alias' => 'categorie']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return FamillesTalent[]|FamillesTalent|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return FamillesTalent|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet d'afficher les arbres liés à la famille
     * @param unknown $auth
     * @param unknown $mode
     * @param string $simulation
     * @return string
     */
    public function genererArbresTalents($auth, $mode, $simulation = true) {
        $retour = "<div id='divListeArbresTalent'>";
        $retour .= "<input type='hidden' value='' id='idArbreSelect'/>";
        //En-tête
        $retour .= "<div class='enteteListeArbreTalent' id='enteteListeArbreTalent'>";
        $retour .= "<span class='titreFamilleListeArbre'>" . $this->nom . "</span>";
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
            $retour .= "<div class='boutonEnteteListeArbreTalent'>";
            if ($simulation == "true") {
                $retour .= "<input type='button' class='bouton' id='boutonModifierLesArbres' value='Modifier' title='Permet de modifier les talents.' onclick='simulerArbre(\"modifier\");'>";
            } else {
                $retour .= "<input type='button' class='bouton' id='boutonSimulerArbres' value='Simuler' title='Permet de voir les arbres en mode simulation.' onclick='simulerArbre(\"simuler\");'>";
            }

            if (count($this->arbres) < 3) {
                $retour .= Phalcon\Tag::SubmitButton(array("Créer Arbre", 'id' => 'boutonAjouterNouvelArbre', 'class' => 'bouton', 'title' => "Permet d'accéder au formulaire de création d'un arbre."));
            }
            $retour .= "</div>";
        }
        $retour .= "</div>";

        //BlocArbre
        $onMouseOverDiv = "";
        $retour .= "<div id='blocArbres'>";
        $retour .= "<div class='descriptionTalent' id='divDescriptionTalent' style='display:none;'></div>";
        if (isset($this->arbres) && $this->arbres != null && count($this->arbres) > 0) {
            $height = ArbresTalent::HAUTEUR_MATRICE_MIN;
            foreach ($this->arbres as $arbre) {
                $hauteur = Talents::maximum(["column" => "rang", "conditions" => "idArbre = :id:", "bind" => ['id' => $arbre->id]]);
                if ($hauteur > $height) {
                    $height = $hauteur;
                }
            }
            $compteurArbre = 0;
            foreach ($this->arbres as $arbre) {
                if ($simulation == "true") {
                    $retour .= $arbre->genererArbreSimulation($auth, $height, $mode, $compteurArbre);
                } else {
                    $retour .= $arbre->genererArbre($auth, $height, $mode, $simulation, $compteurArbre);
                }
                $onMouseOverDiv .= "<div class='descriptionTalentArbre' id='divDescriptionArbre_" . $arbre->id . "' style='display:none;'>";
                $onMouseOverDiv .= "<span class='spanDescriptionTalentArbre'>" . str_replace("\n", "<br/>", $arbre->description) . "</span>";
                $onMouseOverDiv .= "</div>";
                $compteurArbre++;
            }
            $retour .= $onMouseOverDiv;
        } else {
            $retour .= "<span class='resultatTalentVide'>Il n'y a aucun arbre de talent de défini.</span>";
        }
        $retour .= "</div>";
        return $retour;
    }

    /**
     * Permet de supprimer une famille
     * @return string
     */
    public function supprimerFamille() {
        $action = "( Suppression de la famille : " . $this->nom;
        if (isset($this->arbres) && count($this->arbres) > 0) {
            foreach ($this->arbres as $arbre) {
                $action .= $arbre->supprimerArbre();
            }
        }
        $action .= " )";
        $this->delete();
        return $action;
    }

    /**
     * Permet de générer la description de la liste des contraintes
     * @return string
     */
    public function genererDescriptionGeneraleContrainte() {
        $retour = "";
        $this->chargeListeContraintes();
        if (count($this->listeContraintes) > 0) {
            foreach ($this->listeContraintes as $contrainte) {
                $retour .= $contrainte->genererDescription("familleTalent", $this->id, $contrainte->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Permet d'afficher la liste des contraintes
     * @param unknown $auth
     * @return string
     */
    public function genererListeContrainte($auth) {
        $this->chargeListeContraintes();
        if (count($this->listeContraintes) > 0) {
            $retour = "<table class='tableListeContraintes'>";
            $retour .= "<tr>
							<th class='entete' widht='15%'>Nom</th>
							<th class='entete' width='65%'>Description</th>
							<th class='entete' width='20%'>&nbsp;</th>
						</tr>";
            $ligne = 0;
            foreach ($this->listeContraintes as $contrainte) {
                $val = $ligne % 2;
                $retour .= "<tr class='ligne_" . $val . "' >";
                $retour .= "<td><span class='nomListeContrainte'>" . $contrainte->nom . "</span></td>";
                $retour .= "<td><span class='descriptionContrainte'>" . $contrainte->genererDescription("familleTalent", $this->id, $contrainte->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireContrainte(\"familleTalent\"," . $this->id . "," . $contrainte->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerContrainte(" . $contrainte->id . ",\"familleTalent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerContrainte(" . $contrainte->id . ",\"familleTalent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span id='listeContrainteVide' class='messageInfo'>Il n'y a aucune contrainte à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Méthode permettant de charger les contraintes
     */
    public function chargeListeContraintes() {
        $this->listeContraintes = array();
        $listeContraintes = AssocFamilletalentsContraintesParam::find(['idFamille = :idFamille:', 'bind' => ['idFamille' => $this->id], 'order' => 'position']);
        if ($listeContraintes != false && count($listeContraintes) > 0) {
            $contrainte = null;
            $compteur = 0;
            foreach ($listeContraintes as $assocFamilleContrainteParam) {
                $compteur++;
                if ($contrainte == null || $contrainte->id != $assocFamilleContrainteParam->idContrainte) {
                    if ($contrainte != null) {
                        $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                    }
                    $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $assocFamilleContrainteParam->idContrainte]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocFamilleContrainteParam->idParam]]);
                $parametre->valeur = $assocFamilleContrainteParam->valeur;
                $parametre->valeurMin = $assocFamilleContrainteParam->valeurMin;
                $parametre->valeurMax = $assocFamilleContrainteParam->valeurMax;
                $parametre->position = $assocFamilleContrainteParam->position;

                $contrainte->listeParametres[count($contrainte->listeParametres)] = $parametre;
                if ($compteur == count($listeContraintes)) {
                    $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                }
            }
        }
    }

    /**
     * Retourne le nombre de point dépensés dans la famille
     * @param unknown $auth
     * @param unknown $mode
     * @return number
     */
    public function getNbPoint($auth, $mode) {
        $retour = 0;
        if ($mode == "admin") {
            $listeTalent = $auth['simulationTalent']['listeTalents'];
            if (isset($listeTalent) && count($listeTalent) > 0) {
                for ($i = 0; $i < count($listeTalent); $i++) {
                    if ($listeTalent[$i]['idFamille'] == $this->id) {
                        $retour++;
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Transforme l'objet en string
     * @return string
     */
    public function toString() {
        $retour = "id = " . $this->id;
        $retour .= ", nom = " . $this->nom;
        $retour .= ", description = " . $this->description;
        $retour .= ", idCategorie = " . $this->idCategorie;
        return $retour;
    }
}
