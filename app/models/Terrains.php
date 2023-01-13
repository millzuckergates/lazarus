<?php

use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Terrains extends \Phalcon\Mvc\Model {

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
     * @Column(column="genre", type="string", length=5, nullable=false)
     */
    public $genre;

    /**
     *
     * @var string
     * @Column(column="nom", type="string", length=60, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="repImage", type="string", length=80, nullable=true)
     */
    public $repImage;

    /**
     *
     * @var int
     * @Column(column="idCompetence", type="int", length=11, nullable=true)
     */
    public $idCompetence;

    /**
     *
     * @var string
     * @Column(column="couleur", type="string", length=7, nullable=false)
     */
    public $couleur;

    /**
     *
     * @var string
     * @Column(column="saison", type="string", length=30, nullable=false)
     */
    public $saison;

    /**
     *
     * @var string
     * @Column(column="repartition", type="string", length=60, nullable=true)
     */
    public $repartition;

    /**
     *
     * @var integer
     * @Column(column="zIndex", type="integer", length=3, nullable=false)
     */
    public $zIndex;

    /**
     *
     * @var string
     * @Column(column="typeAcces", type="string", length=30, nullable=true)
     */
    public $typeAcces;

    /**
     *
     * @var integer
     * @Column(column="bloqueVue", type="integer", length=1, nullable=false)
     */
    public $bloqueVue;

    /**
     *
     * @var integer
     * @Column(column="bloqueMvt", type="integer", length=1, nullable=false)
     */
    public $bloqueMvt;

    /**
     *
     * @var integer
     * @Column(column="baseMvt", type="integer", length=2, nullable=false)
     */
    public $baseMvt;

    /**
     *
     * @var integer
     * @Column(column="baseVision", type="integer", length=2, nullable=false)
     */
    public $baseVision;

    public $listeEffets;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("terrains");

        $this->hasOne('idCompetence', 'Competences', 'id', ['alias' => 'competence']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Terrains[]|Terrains|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Terrains|ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Allows to query the first record with the corresponding id
     *
     * @param int $id
     * @return Terrains|ResultInterface
     */
    public static function findById(int $id): ?ModelInterface {
        return parent::findFirst([
          'cache' => ['key' => 'terrain-Id-'.$id],
          'id = :id:',
          'bind' => ['id' => $id]
        ]);
    }

    /**
     * Allows to query the first record with the corresponding color
     *
     * @param string $couleur
     * @return Terrains|ResultInterface
     */
    public static function findByCouleur(string $couleur): ?ModelInterface {
        return parent::findFirst([
          'cache' => ['key' => 'terrain-Couleur-'.substr($couleur, 1)],
          'couleur = :couleur:',
          'bind' => ['couleur' => $couleur]
        ]);
    }

    /**
     * Retourne une description tronquée pour l'affichage
     * @return string
     */
    public function resumeDescription() {
        if (strlen($this->description) > 250) {
            return substr($this->description, 0, 250) . "...";
        } else {
            return $this->description;
        }
    }

    /**
     * Retourne le nom du terrain
     * @return string
     */
    public function getNom() {
        if ($this->genre == Constantes::FEMININ) {
            return "Une " . $this->nom;
        } else {
            if ($this->genre == Constantes::MASCULIN) {
                return "Un " . $this->nom;
            } else {
                return $this->nom;
            }
        }
    }

    /**
     * Retourne la liste des compétences
     * @return string
     */
    public function getSelectListCompetence() {
        $retour = "<select id='selectCompetenceTerrain'><option value='0'>Aucune</option>";
        $listeCompetences = Competences::find(['type = :type:', 'bind' => ['type' => Competences::COMPETENCE_ENVIRONNEMENT]]);
        if ($listeCompetences != false && count($listeCompetences) > 0) {
            foreach ($listeCompetences as $competence) {
                if ($this->idCompetence == $competence->id) {
                    $retour .= "<option value='" . $competence->id . "' selected>" . $competence->nom . "</option>";
                } else {
                    $retour .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des compétences
     * @return string
     */
    public static function getSelectListCompetenceTerrainsVide() {
        $retour = "<select id='selectCompetenceTerrain'><option value='0'>Aucune</option>";
        $listeCompetences = Competences::find(['type = :type:', 'bind' => ['type' => Competences::COMPETENCE_ENVIRONNEMENT]]);
        if ($listeCompetences != null) {
            foreach ($listeCompetences as $competence) {
                $retour .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne une image aléatoire liée au terrain
     * @return string
     */
    public function getRandomImageTerrain() {
        $numeroImage = $this->getNomImageAleatoire();
        $image = str_replace("/var/www/lazarus", "", str_replace(BASE_PATH, "", $this->repImage . "jour/" . $numeroImage));
        return str_replace("//", "/", $image);
    }

    /**
     * Retourne une image donnée du terrain
     * @param string $idImageTerrain
     * @return string
     */
    public function getImageTerrain($idImageTerrain) {
        $image = str_replace("/var/www/lazarus", "", str_replace(BASE_PATH, "", $this->repImage . "jour/" . $this->getImageNameFromId($idImageTerrain)));
        return str_replace("//", "/", $image);
    }

    /**
     * Retourne un numéro d'image aléatoire
     * @return string
     */
    public function getNomImageAleatoire() {
        //On construit un tableau d'après la répartition
        $resultatRepartition = array();
        $tab = explode(";", $this->repartition);
        $total = 0;
        for ($i = 0; $i < count($tab); $i++) {
            $total = $total + $tab[$i];
            $resultatRepartition[$i + 1] = $total;
        }
        //On lance un dé 100 pour savoir quelle image sera affichée
        $resultat = Des::jetDes(1, 100);
        for ($j = 1; $j < count($resultatRepartition) + 1; $j++) {
            if ($resultatRepartition[$j] > $resultat) {
                return $this->getImageNameFromId($j);
            }
        }
        return $this->getImageNameFromId(1);
    }

    /**
     * Renvoie le nom de l'image correspondant à l'identifiant
     * @param string $id
     * @return string
     */
    private function getImageNameFromId($id) {
        if (file_exists($this->repImage . "jour/" . $id . ".png")) {
            return $id . ".png";
        } else {
            if (file_exists($this->repImage . "jour/" . $id . ".gif")) {
                return $id . ".gif";
            } else {
                if (file_exists($this->repImage . "jour/" . $id . ".jpg")) {
                    return $id . ".jpg";
                }
            }
        }
        return $id . ".png";
    }

    /**
     * Génère la liste des terrains
     * @param unknown $listeTerrains
     * @return string
     */
    public static function genererListeTerrains($listeTerrains) {
        $retour = "<table class='tableTerrainSaison'>";
        $retour .= "<tr>
					<th width='20%'></th>
					<th width='20%'>Nom</th>
					<th width='60%'>Description</th>
				</tr>";
        $i = 0;
        foreach ($listeTerrains as $terrain) {
            $ligne = "ligne_" . $i % 2;
            $retour .= "<tr class='" . $ligne . "' onclick='afficherDetailTerrain(" . $terrain->id . ");'>";
            $retour .= "<td>" . Phalcon\Tag::image([$terrain->getRandomImageTerrain(), "class" => 'imgTerrainReferentiel']) . "</td>";
            $retour .= "<td><span class='nomListeReferentiel'>" . $terrain->getNom() . "</span></td>";
            $retour .= "<td class='descriptionReferentiel'><span>" . str_replace("\n", "<br/>", $terrain->resumeDescription()) . "</span></td>";
            $retour .= "</tr>";
            $i++;
        }
        $retour .= "</table>";
        return $retour;
    }

    /**
     * Génère la liste des images liées au terrain
     * @param string $type
     * @param string $mode
     * @return string
     */
    public function genererListeImagesTerrains(string $type, string $mode) {
        $config = Phalcon\Di::getDefault()->getShared('config');
        $rep = $config->application->imgDir . explode("/public/img", $this->repImage . $type . "/")[1];
        $tabFile = array();
        $retour = "";
        $count = 0;
        if ($dossier = opendir($rep)) {
            while (false !== ($fichier = readdir($dossier))) {
                if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                    $tabFile[count($tabFile)] = $fichier;
                    $count++;
                }
            }
            closedir($dossier);
        }
        if ($count == 0) {
            return "<span class='msgNoImage'> Il n'y a pas d'image dans ce répertoire.</span>";
        } else {
            sort($tabFile);
            for ($i = 0; $i < count($tabFile); $i++) {
                $fichier = $tabFile[$i];
                $image = str_replace("//", "/", $rep . "/" . $fichier);
                $image = str_replace(BASE_PATH, "", $image);
                $blocImage = "<div class='blocImageTerrains'>";
                $blocImage .= '<div class="divImageTerrain">' . Phalcon\Tag::image([$image, "class" => "imgTerrain"]) . '</div>';
                $blocImage .= '<div class="textImageBloc"><span class="nomImageBloc">' . $fichier . '</span>';
                if ($mode == "edition") {
                    $blocImage .= '<input type="button" class="boutonDelete" title="Permet de supprimer cette image" onclick="deleteImageTerrain(\'' . $fichier . '\',\'' . $type . '\');"/>';
                }
                $blocImage .= "</div></div>";
                $retour .= $blocImage;
            }
        }
        return $retour;
    }

    /**
     * Charge la liste des effets
     * @param string $action
     */
    public function chargeListeEffets($action = false) {
        $this->listeEffets = array();
        $listeEffets = AssocTerrainsEffetsParam::find(['idTerrain = :idTerrain: AND action = :action:', 'bind' => ['idTerrain' => $this->id, 'action' => $action], 'order' => 'position']);
        if ($listeEffets != false && count($listeEffets) > 0) {
            $effet = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeEffets as $assocTerrainEffetParam) {
                $compteur++;
                if ($effet == null || $effet->id != $assocTerrainEffetParam->idEffet || $position != $assocTerrainEffetParam->position) {
                    if ($effet != null) {
                        $this->listeEffets[count($this->listeEffets)] = $effet;
                    }
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $assocTerrainEffetParam->idEffet]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocTerrainEffetParam->idParam]]);
                $parametre->valeur = $assocTerrainEffetParam->valeur;
                $parametre->valeurMin = $assocTerrainEffetParam->valeurMin;
                $parametre->valeurMax = $assocTerrainEffetParam->valeurMax;
                $parametre->position = $assocTerrainEffetParam->position;
                $parametre->action = $assocTerrainEffetParam->action;

                $effet->listeParametres[count($effet->listeParametres)] = $parametre;
                if ($compteur == count($listeEffets)) {
                    $this->listeEffets[count($this->listeEffets)] = $effet;
                }
                $position = $assocTerrainEffetParam->position;
            }
        }
    }

    /**
     * Génère la liste des effets par action
     * @param unknown $auth
     * @param unknown $action
     * @return string
     */
    public function genererListeEffetAction($auth, $action) {
        $retour = "";
        $this->chargeListeEffets($action);
        if (count($this->listeEffets) > 0) {
            $retour = "<div class='divListeEffetAction'>";
            $retour .= "<div class='enteteListeEffetAction'><span class='introListeEffetAction'>Effets se délenchant " . $action . "</span></div>";
            $retour .= "<div class='divTableListeEffets'>";
            $retour .= "<table class='tableListeEffets'>";
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
                $retour .= "<td><span class='descriptionEffet'>" . $effet->genererDescription("terrain", $this->id, $effet->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireEffet(\"terrain\"," . $this->id . "," . $effet->id . "," . $effet->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_TERRAINS, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerEffet(" . $effet->id . ",\"terrain\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerEffet(" . $effet->id . ",\"terrain\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
            $retour .= "</div>";
        }
        return $retour;
    }

    /**
     * Méthode pour vérifier le format de la répartition
     * @param unknown $repartition
     * @return boolean
     */
    public static function checkFormatRepartition($repartition) {
        $tab = explode(";", $repartition);
        $total = 0;
        for ($i = 0; $i < count($tab); $i++) {
            if (!is_numeric($tab[$i])) {
                return false;
            }
            $total = $total + intval($tab[$i]);
        }
        if ($total != 100) {
            return false;
        }
        return true;
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
                    $retour .= $effet->genererDescription("terrain", $this->id, $effet->listeParametres[0]->position) . "</br>";
                }
            }
        }
        return $retour;
    }

    /**
     * Méthode pour tracer le terrain
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Nom : " . $this->nom . "], ";
        $retour .= "[Genre : " . $this->genre . "], ";
        $retour .= "[Description : " . $this->description . "], ";
        $retour .= "[RepImage : " . $this->repImage . "], ";
        $retour .= "[idCompetence : " . $this->idCompetence . "], ";
        $retour .= "[Couleur " . $this->couleur . "], ";
        $retour .= "[Saison " . $this->saison . "]";
        $retour .= "[Repartition : " . $this->repartition . "]";
        $retour .= "[ZIndex : " . $this->zIndex . "]";
        $retour .= "[TypeAcces : " . $this->typeAcces . "]";
        $retour .= "[BloqueVue : " . $this->bloqueVue . "]";
        $retour .= "[BloqueMvt : " . $this->bloqueMvt . "]";
        $retour .= "[BaseMvt : " . $this->baseMvt . "]";
        $retour .= "[BaseVision : " . $this->baseVision . "]";
        return $retour;
    }
}
