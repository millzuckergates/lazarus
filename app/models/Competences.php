<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Competences extends \Phalcon\Mvc\Model {

    /** Types des compétences **/
    const COMPETENCE_MAGIQUE = "Magique";
    const COMPETENCE_ENVIRONNEMENT = "Environnement";
    const COMPETENCE_LANGUE = "Langue";
    const COMPETENCE_MARTIALE = "Martiale";
    const COMPETENCE_ARTISANAT = "Artisanat";
    const COMPETENCE_SOCIALE = "Sociale";

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
     * @Column(column="type", type="string", length=60, nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(column="isActif", type="integer", length=1, nullable=true)
     */
    public $isActif;

    /**
     *
     * @var integer
     * @Column(column="coutPA", type="integer", length=2, nullable=true)
     */
    public $coutPA;

    /**
     *
     * @var integer
     * @Column(column="caracAssoc", type="integer", length=11, nullable=true)
     */
    public $caracAssoc;

    /**
     *
     * @var string
     * @Column(column="messageRP", type="string", nullable=false)
     */
    public $messageRP;

    /**
     *
     * @var string
     * @Column(column="evenementLanceur", type="string", nullable=false)
     */
    public $evenementLanceur;

    /**
     *
     * @var string
     * @Column(column="evenementGlobal", type="string", nullable=true)
     */
    public $evenementGlobal;

    /**
     *
     * @var integer
     * @Column(column="activable", type="integer", length=1, nullable=true)
     */
    public $activable;

    /**
     *
     * @var integer
     * @Column(column="idRangBloque", type="integer", length=11, nullable=true)
     */
    public $idRangBloque;

    /**
     *
     * @var integer
     * @Column(column="isEnseignable", type="integer", length=1)
     */
    public $isEnseignable;

    /**
     *
     * @var integer
     * @Column(column="isEntrainable", type="integer", length=1)
     */
    public $isEntrainable;

    /**
     *
     * @var integer
     * @Column(column="isActif", type="integer", length=11, nullable=true)
     */
    public $idRangAutonome;

    public $listeEffets = array();
    public $listeContraintes = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("competences");

        //Init Jointer
        $this->hasOne('idRangBloque', 'RangsCompetence', 'id', ['alias' => 'rangBloque']);
        $this->hasOne('idRangAutonome', 'RangsCompetence', 'id', ['alias' => 'rangAutonome']);
        $this->hasMany('id', 'AssocCaracsCompetence', 'idCompetence', ['alias' => 'assocCompCarac']);
        $this->hasManyToMany('id', 'AssocCaracsCompetence', 'idCompetence', 'idCarac', 'Caracteristiques', 'id', ['alias' => 'listeCaracteristiques']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Competences[]|Competences|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Competences|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de retourner la liste des types de compétence
     * @return string[]
     */
    public static function getListeTypeCompetence() {
        $retour = array();
        $retour[count($retour)] = Competences::COMPETENCE_SOCIALE;
        $retour[count($retour)] = Competences::COMPETENCE_MAGIQUE;
        $retour[count($retour)] = Competences::COMPETENCE_MARTIALE;
        $retour[count($retour)] = Competences::COMPETENCE_ARTISANAT;
        $retour[count($retour)] = Competences::COMPETENCE_ENVIRONNEMENT;
        $retour[count($retour)] = Competences::COMPETENCE_LANGUE;
        return $retour;
    }

    /**
     * Permet d'afficher un bloc de compétence
     * @param unknown $typeCompetence
     * @return string
     */
    public static function genererBlocCompetence($typeCompetence) {
        $retour = "";
        $retour .= "<div class='enteteBlocCompetence'><span class='titreBlocCompetence'>" . $typeCompetence . "</span></div>";
        $retour .= "<div class='divListeCompetenceType'>";
        $listeCompetences = Competences::find(['type = :type:', 'bind' => ['type' => $typeCompetence], 'order' => 'nom']);
        if ($listeCompetences != false && count($listeCompetences) > 0) {
            //Génération de l'en tête
            foreach ($listeCompetences as $competence) {
                $retour .= "<div class='divCompetence' id='divCompetence_" . $competence->id . "'>";
                $retour .= $competence->genererDivCompetence();
                $retour .= "</div>";
            }
        } else {
            $retour .= "<span class='resultatCompetenceVide'>Aucune compétence</span>";
        }
        $retour .= "</div>";
        return $retour;
    }

    /**
     * Permet de générer le div des compétences
     * @return string
     */
    public function genererDivCompetence() {
        $retour = "";
        $retour .= "<input type='radio' id='competence_" . $this->id . "' name='competence' class='radioCompetence' value='" . $this->id . "' onClick='chargerPopupCompetence(" . $this->id . ");' />";
        //Déterminer la couleur affichée de la compétence en mode "admin"
        $style = $this->determineCouleur();
        $retour .= "<label for='competence_" . $this->id . "' $style>" . $this->nom . "</label>";
        if (isset($this->rangBloque) && $this->rangBloque != null) {
            $retour .= "<span class='limiteRangCompetence' title='Correspond au rang auquel est bloqué l&#39;apprentissage de la compétence.'>" . $this->rangBloque->nom . "</span>";
        } else {
            $retour .= "<span class='limiteRangCompetence' title='Correspond au rang auquel est bloqué l&#39;apprentissage de la compétence.'>Aucun</span>";
        }
        return $retour;
    }

    /**
     * Permet de déterminer la couleur et l'affichage de la compétence
     * @return string
     */
    public function determineCouleur() {
        $style = "class='compClassique'";
        if (!$this->isActif) {
            $style = "class='compRouge' onMouseOver='afficherSpecificiteCompetence(\"Cette compétence est inactive\"," . $this->id . ");' onMouseOut='hideSpecificiteCompetence();'";
        } else {
            $this->chargeListeContraintes();
            if (count($this->listeContraintes) > 0) {
                $style = "class='compOrange' onMouseOver='afficherSpecificiteCompetence(\"Cette compétence a des contraintes\"," . $this->id . ");' onMouseOut='hideSpecificiteCompetence();'";
            }
        }
        return $style;
    }

    /**
     * Charge la liste des effets
     * @param string $action
     */
    public function chargeListeEffets($action = false) {
        $this->listeEffets = array();
        if (!$action) {
            $listeEffets = AssocCompetencesEffetsParam::find(['idCompetence = :idCompetence:', 'bind' => ['idCompetence' => $this->id], 'order' => 'position']);
        } else {
            $listeEffets = AssocCompetencesEffetsParam::find(['idCompetence = :idCompetence: AND action = :action:', 'bind' => ['idCompetence' => $this->id, 'action' => $action], 'order' => 'position']);
        }
        if ($listeEffets != false && count($listeEffets) > 0) {
            $effet = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeEffets as $assocCompetenceEffetParam) {
                $compteur++;
                if ($effet == null || $effet->id != $assocCompetenceEffetParam->idEffet || $position != $assocCompetenceEffetParam->position) {
                    if ($effet != null) {
                        $this->listeEffets[count($this->listeEffets)] = $effet;
                    }
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $assocCompetenceEffetParam->idEffet]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocCompetenceEffetParam->idParam]]);
                $parametre->valeur = $assocCompetenceEffetParam->valeur;
                $parametre->valeurMin = $assocCompetenceEffetParam->valeurMin;
                $parametre->valeurMax = $assocCompetenceEffetParam->valeurMax;
                $parametre->position = $assocCompetenceEffetParam->position;
                $parametre->action = $assocCompetenceEffetParam->action;

                $effet->listeParametres[count($effet->listeParametres)] = $parametre;
                if ($compteur == count($listeEffets)) {
                    $this->listeEffets[count($this->listeEffets)] = $effet;
                }
                $position = $assocCompetenceEffetParam->position;
            }
        }
    }

    /**
     * Génère la liste des effets par action
     * @param unknown $auth
     * @param unknown $action
     * @return string
     */
    public function genererListeEffet($auth) {
        $this->chargeListeEffets();
        if (count($this->listeEffets) > 0) {
            $retour = "<table class='tableListeEffets'>";
            $retour .= "<tr>
						<th class='entete' widht='15%'>Nom</th>
						<th class='entete' width='65%'>Description</th>
						<th class='entete' width='20%'>&nbsp;</th>
					</tr>";
            $ligne = 0;
            foreach ($this->listeEffets as $effet) {
                $val = $ligne % 2;
                $retour .= "<tr class='ligne_" . $val . "' >";
                $retour .= "<td><span class='nomListeEffet'>" . $effet->nom . "</span></td>";
                $retour .= "<td><span class='descriptionEffet'>" . $effet->genererDescription("competence", $this->id, $effet->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireEffet(\"competence\"," . $this->id . "," . $effet->id . "," . $effet->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerEffet(" . $effet->id . ",\"competence\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerEffet(" . $effet->id . ",\"competence\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span class='resultatCompetenceVide'>Il n'y a aucun effet à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Permet de générer la description de la liste des effets
     * @return string
     */
    public function genererDescriptionGeneraleEffet() {
        $listeAction = Effets::getListeActionEffet();
        $retour = "";
        foreach ($listeAction as $action) {
            $this->chargeListeEffets($action);
            if (count($this->listeEffets) > 0) {
                foreach ($this->listeEffets as $effet) {
                    $retour .= $effet->genererDescription("competence", $this->id, $effet->listeParametres[0]->position) . "</br>";
                }
            }
        }
        return $retour;
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
                $retour .= $contrainte->genererDescription("competence", $this->id, $contrainte->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Méthode permettant de charger les contraintes
     */
    public function chargeListeContraintes() {
        $this->listeContraintes = array();
        $listeContraintes = AssocCompetencesContraintesParam::find(['idCompetence = :idCompetence:', 'bind' => ['idCompetence' => $this->id], 'order' => 'position,idParam']);
        if ($listeContraintes != false && count($listeContraintes) > 0) {
            $contrainte = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeContraintes as $assocCompetenceContrainteParam) {
                $compteur++;
                if ($contrainte == null || $contrainte->id != $assocCompetenceContrainteParam->idContrainte || $position != $assocCompetenceContrainteParam->position) {
                    if ($contrainte != null) {
                        $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                    }
                    $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $assocCompetenceContrainteParam->idContrainte]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocCompetenceContrainteParam->idParam]]);
                $parametre->valeur = $assocCompetenceContrainteParam->valeur;
                $parametre->valeurMin = $assocCompetenceContrainteParam->valeurMin;
                $parametre->valeurMax = $assocCompetenceContrainteParam->valeurMax;
                $parametre->position = $assocCompetenceContrainteParam->position;

                $contrainte->listeParametres[count($contrainte->listeParametres)] = $parametre;
                if ($compteur == count($listeContraintes)) {
                    $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                }
                $position = $assocCompetenceContrainteParam->position;
            }
        }
    }

    /**
     * Permet de générer la liste des contraintes
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
                $retour .= "<td><span class='descriptionContrainte'>" . $contrainte->genererDescription("competence", $this->id, $contrainte->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireContrainte(\"competence\"," . $this->id . "," . $contrainte->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerContrainte(" . $contrainte->id . ",\"competence\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerContrainte(" . $contrainte->id . ",\"competence\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span class='resultatCompetenceVide'>Il n'y a aucune contrainte à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Retourne le select des types
     * @return string
     */
    public static function getListeTypeVide() {
        $listeType = Competences::getListeTypeCompetence();
        $retour = "<select id='typeCompetence'>";
        foreach ($listeType as $type) {
            $retour .= "<option value='" . $type . "'>" . $type . "</option>";
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne le select des types
     * @return string
     */
    public function getListeType() {
        $listeType = Competences::getListeTypeCompetence();
        $retour = "<select id='typeCompetence'>";
        foreach ($listeType as $type) {
            if ($this->type == $type) {
                $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
            } else {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des images disponibles
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImagesVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/competences';
        $listeFichier = array();
        if ($dossier = opendir($directory)) {
            while (false !== ($fichier = readdir($dossier))) {
                if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                    $listeFichier[count($listeFichier)] = $fichier;
                }
            }
            closedir($dossier);
        }
        sort($listeFichier);
        $retour = "";
        $retour = $retour . '<select id="listeImage" onchange="changerImageCompetence();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "defaut.png") {
                    $retour = $retour . "<option value='public/img/site/illustrations/competences/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/competences/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Génère la liste des images disponibles
     * @return string
     */
    public function genererListeImages() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/competences';
        $listeFichier = array();
        if ($dossier = opendir($directory)) {
            while (false !== ($fichier = readdir($dossier))) {
                if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                    $listeFichier[count($listeFichier)] = $fichier;
                }
            }
            closedir($dossier);
        }
        sort($listeFichier);
        $retour = "";
        $retour = $retour . '<select id="listeImage" onchange="changerImageCompetence();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/competences/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/competences/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/competences/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des rangs
     * @return string
     */
    public static function getListeRangAutonomieVide() {
        $listeRangs = RangsCompetence::find(['order' => 'niveau']);
        $retour = "<select id='rangAutonomie'><option value='0'>Début</option>";
        if ($listeRangs != false && count($listeRangs) > 0) {
            foreach ($listeRangs as $rang) {
                $retour .= "<option value='" . $rang->id . "'>" . $rang->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des rangs
     * @return string
     */
    public function getListeRangAutonomie() {
        $listeRangs = RangsCompetence::find(['order' => 'niveau']);
        $retour = "<select id='rangAutonomie'><option value='0'>Début</option>";
        if ($listeRangs != false && count($listeRangs) > 0) {
            foreach ($listeRangs as $rang) {
                if ($this->idRangAutonome == $rang->id) {
                    $retour .= "<option value='" . $rang->id . "' selected>" . $rang->nom . "</option>";
                } else {
                    $retour .= "<option value='" . $rang->id . "'>" . $rang->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère le tableau des rangs
     * @param unknown $auth
     * @return string
     */
    public function genererTableRang($auth) {
        $listeRang = RangsCompetence::find(['order' => 'niveau']);
        if ($listeRang != false && count($listeRang) > 0) {
            $retour = "<table class='tableEvolutionCompetence' id='tableRangCompetenceEvolution'>";
            $ligne = 0;
            $listeIdRang = "";
            foreach ($listeRang as $rang) {
                $listeIdRang .= $rang->id . ",";
                $assoc = AssocRangsCompetence::findFirst(['idRang = :idRang: AND idCompetence = :idCompetence:', 'bind' => ['idRang' => $rang->id, 'idCompetence' => $this->id]]);
                $val = $ligne % 2;
                $retour .= "<tr class='ligne_" . $val . "' >";
                $retour .= "<td class='nomEvolutionCompetence'>" . $rang->nom . "</td>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<td class='inputRangEvolutionCompetence'><input type='text' size=5 id='point_" . $rang->id . "' value='" . $assoc->nbPointAAtteindre . "'/></td>";
                } else {
                    $retour .= "<td class='valueRangEvolutionCompetence'>" . $assoc->nbPointAAtteindre . "</td>";
                }
                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span class='resultatVide'>Il n'y a aucun rangs de renseignés.</span>";
        }
        $retour .= "<input type='hidden' id='listeIdRang' value='" . $listeIdRang . "'/>";
        return $retour;
    }

    /**
     * Retourne la liste des rangs
     * @param unknown $auth
     * @return string
     */
    public function getListeRangBloque($auth) {
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
            $retour = "<select id='rangBloque'><option value='0'>Aucun</option>";
            $listeRang = RangsCompetence::find(['order' => 'niveau']);
            if ($listeRang != false && count($listeRang) > 0) {
                foreach ($listeRang as $rang) {
                    if ($this->idRangBloque == $rang->id) {
                        $retour .= "<option value='" . $rang->id . "' selected>" . $rang->nom . "</option>";
                    } else {
                        $retour .= "<option value='" . $rang->id . "'>" . $rang->nom . "</option>";
                    }
                }
            }
            $retour .= "</select>";
        } else {
            if ($this->rangBloque != null && isset($this->rangBloque) && $this->idRangBloque != 0 && $this->idRangBloque != null) {
                $retour = $this->rangBloque->nom;
            } else {
                $retour = " Aucun";
            }
        }
        return $retour;
    }

    /**
     * Génère la table des caractéristiques
     * @param unknown $auth
     * @return string
     */
    public function genererTableCaracteristique($auth) {
        $listeCarac = Caracteristiques::find(['type = :type:', 'bind' => ['type' => Caracteristiques::CARAC_PRIMAIRE], 'order' => 'nom']);
        if ($listeCarac != false && count($listeCarac) > 0) {
            $retour = "<table class='tableEvolutionCompetence' id='tableCaracteristiqueCompetenceEvolution'>";
            $ligne = 0;
            $listeIdCarac = "";
            foreach ($listeCarac as $carac) {
                $listeIdCarac = $carac->id . ",";
                $assoc = AssocCaracsCompetence::findFirst(['idCarac = :idCaracteristique: AND idCompetence = :idCompetence:', 'bind' => ['idCaracteristique' => $carac->id, 'idCompetence' => $this->id]]);
                $val = $ligne % 2;
                $retour .= "<tr class='ligne_" . $val . "' >";
                $retour .= "<td class='nomEvolutionCompetence'>" . $carac->nom . "</td>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<td class='inputCaracEvolutionCompetence'><input type='text' size=5 id='carac_" . $carac->id . "' value='" . $assoc->modificateur . "'/></td>";
                } else {
                    $retour .= "<td class='valueCaracEvolutionCompetence'>" . $assoc->modificateur . "</td>";
                }
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_COMPETENCE_MODIFIER, $auth['autorisations'])) {
                    if ($assoc->isInfluenceur) {
                        $retour .= "<td class='cocheCaracEvolutionCompetence'><input type='checkbox'id='influenceur_" . $carac->id . "' checked/></td>";
                    } else {
                        $retour .= "<td class='cocheCaracEvolutionCompetence'><input type='checkbox'id='influenceur_" . $carac->id . "'/></td>";
                    }
                } else {
                    if ($assoc->isInfluenceur) {
                        $retour .= "<td class='influenceurCaracEvolutionCompetence' title='Influence l'évolution de la compétence'>Influence</td>";
                    } else {
                        $retour .= "<td class='influenceurCaracEvolutionCompetence'></td>";
                    }
                }
                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span class='resultatVide'>Il n'y a aucune caractéristiques de renseignées.</span>";
        }
        $retour .= "<input type='hidden' id='listeIdCarac' value='" . $listeIdCarac . "'/>";
        return $retour;
    }

    /**
     * Permet de retourner un select pour les compétence
     * en excluant celles passées en paramètre
     * @param string $listeExclude
     * @return string
     */
    public static function getSelectCompetence($listeExclude = false) {
        $listeCompetences = Competences::find(['isActif = 1', 'order' => 'nom']);
        $retour = "<select id='listeSelectElement'><option value='0'>Choisissez une compétence</option>";
        if ($listeCompetences != false && count($listeCompetences) > 0) {
            foreach ($listeCompetences as $competence) {
                if ($listeExclude == false || ($listeExclude != false && !in_array($competence->id, $listeExclude))) {
                    $retour .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne l'objet sous une chaine de caractère
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Nom : " . $this->nom . "], ";
        $retour .= "[Description : " . $this->description . "], ";
        $retour .= "[Image : " . $this->image . "], ";
        $retour .= "[type : " . $this->type . "], ";
        $retour .= "[IsActif : " . $this->isActif . "], ";
        $retour .= "[Cout PA : " . $this->coutPA . "]";
        $retour .= "[Message RP : " . $this->messageRP . "]";
        $retour .= "[Event lanceur : " . $this->evenementLanceur . "]";
        $retour .= "[Event global : " . $this->evenementGlobal . "]";
        $retour .= "[Activable : " . $this->activable . "]";
        $retour .= "[Id Rang Bloqué : " . $this->idRangBloque . "]";
        $retour .= "[Is Enseignable : " . $this->isEnseignable . "]";
        $retour .= "[Is Entrainble : " . $this->isEntrainable . "]";
        $retour .= "[Id Rang Autonome : " . $this->idRangAutonome . "]";
        return $retour;
    }
}