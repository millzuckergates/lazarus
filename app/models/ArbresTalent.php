<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class ArbresTalent extends \Phalcon\Mvc\Model {

    const HAUTEUR_MATRICE_MIN = 7;
    const LARGEUR_MATRICE = 5;
    const TAILLE_TALENT = 30;
    const HAUTEUR_LIGNE = 60;

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
     * @Column(column="idFamille", type="integer", length=11, nullable=false)
     */
    public $idFamille;

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
     * @Column(column="image", type="string", nullable=false)
     */
    public $image;

    public $listeContraintes = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("arbres_talent");

        //Init Jointer
        $this->hasOne('idFamille', 'FamillesTalent', 'id', ['alias' => 'famille']);
        $this->hasMany('id', 'Talents', 'idArbre', ['alias' => 'talents']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ArbresTalent[]|ArbresTalent|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ArbresTalent|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de générer un arbre de talent en affichage
     * @param unknown $auth
     * @param unknown $height
     * @param unknown $mode
     * @param unknown $compteurArbre
     * @return string
     */
    public function genererArbre($auth, $height, $mode, $compteurArbre) {
        $perso = $auth['perso'];
        // Step 1 - Initialiser le div parent
        $retour = "<div class='blocTotalArbre' id='blocTotalArbre_" . $this->id . "'>";
        $heightpx = $height * ArbresTalent::HAUTEUR_LIGNE + 20;
        $width = ArbresTalent::LARGEUR_MATRICE * (ArbresTalent::TAILLE_TALENT + 30);
        $style = "height:" . $heightpx . "px;width:" . $width . "px;background-image:url(" . $this->image . ");";
        $styleCanvas = "height:" . $heightpx . "px;width:" . $width . "px;";

        // Step 2 - Initialiser le div en-tête
        $retour .= "<div class='divEnteteArbre'>";
        $retour .= "<div class='divTitreArbre' id='divTitreArbre_" . $this->id . "' onMouseOver='afficherDescriptionArbre(" . $this->id . ");' onMouseOut='hideDescriptionArbre(" . $this->id . ");'>";
        $retour .= "<span class='titreArbre'>" . $this->nom . "&nbsp;-&nbsp;</span>";
        $retour .= "<span id='nombreDePointDepense_" . $this->id . "' class='nombreDePointDepense'>0</span>";
        $retour .= "</div>";
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
            $retour .= "<div class='divBoutonsArbre'>";
            $retour .= "<input type='button' class='buttonActualiser' onclick='chargerModifierArbre(" . $this->id . ");'/>";
            $retour .= "<input type='button' class='buttonMoins' onclick='boxSupprimerArbre(" . $this->id . ");'/>";
            $retour .= "</div>";
        }

        $retour .= "</div>";
        $retour .= "<input id='idTalentSelectDelet' type='hidden' value=''/>";
        $retour .= "<input id='compteurArbre_" . $this->id . "' type='hidden' value='" . $compteurArbre . "'/>";

        $retour .= "<div class='arbreTalent' id='arbre_" . $this->id . "' style='" . $style . "'>";
        $retour .= "<canvas class='canvasArbre' id='canvas_" . $this->id . "' style='" . $styleCanvas . "'></canvas>";
        // Menu caché pour les talents
        if ($mode == "admin") {
            $retour .= "<div id='hiddenMenuTalent_" . $this->id . "' class='hiddenMenuTalent' name='hiddenMenuTalent' style='display:none;'>";
            $retour .= "<td><input type='button' class='buttonPlus' id='augmenterTalent_" . $this->id . "'/>";
            if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                $retour .= "<input type='button' class='buttonActualiser' id='modifierTalent_" . $this->id . "'/>";
                $retour .= "<input type='button' class='buttonMoins' id='retirerTalent_" . $this->id . "'/>";
            }
            $retour .= "</div>";
        }
        // Step 3 - Itération sur les talents
        for ($i = 0; $i < $height; $i++) {
            // Step 3.1 - Initialisation div Ligne
            $style = "height:" . ArbresTalent::HAUTEUR_LIGNE . "px;top:" . $i * ArbresTalent::HAUTEUR_LIGNE . "px;";
            $divLigne = "<div class='ligneArbreTalent' style='" . $style . "'>";
            $retour .= $divLigne;
            // Step 3.2 - Récupération des talents de la ligne
            $tailleBlocpx = $width / (ArbresTalent::LARGEUR_MATRICE - 1);
            for ($j = 1; $j < ArbresTalent::LARGEUR_MATRICE; $j++) {
                $talent = Talents::findFirst(['idArbre = :idArbre: AND rang = :rang: AND position = :position:', 'bind' => ['idArbre' => $this->id, 'rang' => $i + 1, 'position' => $j]]);
                if ($talent != false) {
                    $divTalent = $talent->genererDivTalent($auth, $tailleBlocpx, $mode, $this->id, $compteurArbre, false);
                } else {
                    $divTalent = Talents::genererDivTalentVide($auth, $tailleBlocpx, $mode, $i + 1, $j, $this->id, false);
                }
                $retour .= $divTalent;
            }
            $retour .= "</div>";
        }
        $retour .= "</div></div>";
        return $retour;
    }

    /**
     * Permet d'afficher un arbre en mode simulation
     * @param unknown $auth
     * @param unknown $height
     * @param unknown $mode
     * @param unknown $compteurArbre
     * @return string
     */
    public function genererArbreSimulation($auth, $height, $mode, $compteurArbre) {
        $perso = $auth['perso'];
        // Step 1 - Initialiser le div parent
        $retour = "<div class='blocTotalArbre' id='blocTotalArbre_" . $this->id . "'>";
        $heightpx = $height * ArbresTalent::HAUTEUR_LIGNE + 20;
        $width = ArbresTalent::LARGEUR_MATRICE * (ArbresTalent::TAILLE_TALENT + 30);
        $style = "height:" . $heightpx . "px;width:" . $width . "px;background-image:url(" . $this->image . ");";
        $styleCanvas = "height:" . $heightpx . "px;width:" . $width . "px;";

        // Step 2 - Initialiser le div en-tête
        $retour .= "<div class='divEnteteArbre'>";
        $retour .= "<div class='divTitreArbre' id='divTitreArbre_" . $this->id . "' onMouseOver='afficherDescriptionArbre(" . $this->id . ");' onMouseOut='hideDescriptionArbre(" . $this->id . ");'>";
        $retour .= "<span class='titreArbre'>" . $this->nom . "&nbsp;-&nbsp;</span>";
        $retour .= "<span id='nombreDePointDepense_" . $this->id . "' class='nombreDePointDepense'>0</span>";
        $retour .= "</div>";
        $retour .= "</div>";
        $retour .= "<input id='idTalentSelectDelet' type='hidden' value=''/>";
        $retour .= "<input id='compteurArbre_" . $this->id . "' type='hidden' value='" . $compteurArbre . "'/>";
        $retour .= "<div class='arbreTalent' id='arbre_" . $this->id . "' style='" . $style . "'>";
        $retour .= "<canvas class='canvasArbre' id='canvas_" . $this->id . "' style='" . $styleCanvas . "'></canvas>";
        // Step 3 - Itération sur les talents
        for ($i = 0; $i < $height; $i++) {
            // Step 3.1 - Initialisation div Ligne
            $style = "height:" . ArbresTalent::HAUTEUR_LIGNE . "px;top:" . $i * ArbresTalent::HAUTEUR_LIGNE . "px;";
            $divLigne = "<div class='ligneArbreTalent' style='" . $style . "'>";
            $retour .= $divLigne;
            // Step 3.2 - Récupération des talents de la ligne
            $tailleBlocpx = $width / (ArbresTalent::LARGEUR_MATRICE - 1);
            for ($j = 1; $j < ArbresTalent::LARGEUR_MATRICE; $j++) {
                $talent = Talents::findFirst(['idArbre = :idArbre: AND rang = :rang: AND position = :position:', 'bind' => ['idArbre' => $this->id, 'rang' => $i + 1, 'position' => $j]]);
                if ($talent != false) {
                    $divTalent = $talent->genererDivTalent($auth, $tailleBlocpx, $mode, $this->id, $compteurArbre);
                } else {
                    $divTalent = Talents::genererDivTalentVide($auth, $tailleBlocpx, $mode, $i + 1, $j, $this->id);
                }
                $retour .= $divTalent;
            }
            $retour .= "</div>";
        }
        $retour .= "</div></div>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images en mode vide pour un arbre
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImagesVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/arbrestalent';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageArbre();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/arbrestalent/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/arbrestalent/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images disponibles pour un arbre
     * @return string
     */
    public function genererListeImages() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/arbrestalent';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageArbre();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/arbrestalent/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/arbrestalent/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/arbrestalent/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de supprimer un arbre
     * @return string
     */
    public function supprimerArbre() {
        $action = "[ Suppression de l'arbre : " . $this->nom;
        if (isset($this->talents) && count($this->talents) > 0) {
            foreach ($this->talents as $talent) {
                $action .= $talent->supprimerTalent();
            }
        }
        $action .= " ]";
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
                $retour .= $contrainte->genererDescription("arbreTalent", $this->id, $contrainte->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Retourne l'affichage des liste de contraintes
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
                $retour .= "<td><span class='descriptionContrainte'>" . $contrainte->genererDescription("arbreTalent", $this->id, $contrainte->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireContrainte(\"arbreTalent\"," . $this->id . "," . $contrainte->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerContrainte(" . $contrainte->id . ",\"arbreTalent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerContrainte(" . $contrainte->id . ",\"arbreTalent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
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
        $listeContraintes = AssocArbretalentsContraintesParam::find(['idArbre = :idArbre:', 'bind' => ['idArbre' => $this->id], 'order' => 'position']);
        if ($listeContraintes != false && count($listeContraintes) > 0) {
            $contrainte = null;
            $compteur = 0;
            foreach ($listeContraintes as $assocArbreContrainteParam) {
                $compteur++;
                if ($contrainte == null || $contrainte->id != $assocArbreContrainteParam->idContrainte) {
                    if ($contrainte != null) {
                        $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                    }
                    $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $assocArbreContrainteParam->idContrainte]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocArbreContrainteParam->idParam]]);
                $parametre->valeur = $assocArbreContrainteParam->valeur;
                $parametre->valeurMin = $assocArbreContrainteParam->valeurMin;
                $parametre->valeurMax = $assocArbreContrainteParam->valeurMax;
                $parametre->position = $assocArbreContrainteParam->position;

                $contrainte->listeParametres[count($contrainte->listeParametres)] = $parametre;
                if ($compteur == count($listeContraintes)) {
                    $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                }
            }
        }
    }

    /**
     * Transforme l'objet en string
     * @return string
     */
    public function toString() {
        $retour = "id = " . $this->id;
        $retour .= ", nom = " . $this->nom;
        $retour .= ", description = " . $this->description;
        if (isset($this->image)) {
            $retour .= ", image = " . $this->image;
        }
        return $retour;
    }

    /**
     * Retourne le nombre de point d'un personnage dans cet arbre
     * @param unknown $perso
     * @return number
     */
    public function getNombrePointPerso($perso) {
        $nombrePoint = 0;
        if (count($this->talents) > 0) {
            foreach ($this->talents as $talent) {
                $nombrePoint += $perso->listeTalents[$talent->id];
            }
        }
        return $nombrePoint;
    }

    /**
     * Retourne le nombre de point dans un arbre à partir de la simulation
     * @param unknown $tableauTalent
     * @return number|unknown
     */
    public function getNombrePointSimulation($tableauTalent) {
        $nombrePoint = 0;
        if (isset($tableauTalent) && count($tableauTalent) > 0) {
            foreach ($tableauTalent as $talent) {
                if ($talent['idArbre'] == $this->id) {
                    $nombrePoint += $talent['actuel'];
                }
            }
        }
        return $nombrePoint;
    }
}
