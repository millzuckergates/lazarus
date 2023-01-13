<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Sorts extends \Phalcon\Mvc\Model {

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
     * @Column(column="idCiblage", type="integer", length=11, nullable=false)
     */
    public $idCiblage;

    /**
     *
     * @var integer
     * @Column(column="idEcoleMagie", type="integer", length=11, nullable=true)
     */
    public $idEcoleMagie;

    /**
     *
     * @var string
     * @Column(column="nom", type="string", length=32, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=255, nullable=true)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(column="arcane", type="integer", length=2, nullable=false)
     */
    public $arcane;

    /**
     *
     * @var integer
     * @Column(column="retranscriptibleSort", type="integer", length=1, nullable=false)
     */
    public $retranscriptibleSort;

    /**
     *
     * @var string
     * @Column(column="mana", type="string", length=400, nullable=false)
     */
    public $mana;

    /**
     *
     * @var integer
     * @Column(column="enseignable", type="integer", length=1, nullable=false)
     */
    public $enseignable;

    /**
     *
     * @var string
     * @Column(column="portee", type="string", length=400, nullable=false)
     */
    public $portee;

    /**
     *
     * @var integer
     * @Column(column="isBloque", type="integer", length=1, nullable=false)
     */
    public $isBloque;

    /**
     *
     * @var string
     * @Column(column="duree", type="string", length=400, nullable=true)
     */
    public $duree;

    /**
     *
     * @var string
     * @Column(column="cumulQuantite", type="string", length=400, nullable=false)
     */
    public $cumulQuantite;

    /**
     *
     * @var string
     * @Column(column="cumulDuree", type="string", length=400, nullable=false)
     */
    public $cumulDuree;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     *
     * @var string
     * @Column(column="coutPA", type="string", length=400, nullable=false)
     */
    public $coutPA;

    /**
     *
     * @var integer
     * @Column(column="esquivable", type="integer", length=1, nullable=false)
     */
    public $esquivable;

    /**
     *
     * @var string
     * @Column(column="messageRP", type="string", nullable=true)
     */
    public $messageRP;

    /**
     *
     * @var integer
     * @Column(column="isJS", type="integer", length=1, nullable=false)
     */
    public $isJS;

    /**
     *
     * @var integer
     * @Column(column="isJSEV", type="integer", length=1, nullable=false)
     */
    public $isJSEV;

    /**
     *
     * @var string
     * @Column(column="eventLanceur", type="string", nullable=true)
     */
    public $eventLanceur;

    /**
     *
     * @var string
     * @Column(column="eventGlobal", type="string", nullable=true)
     */
    public $eventGlobal;

    public $listeEffets = array();
    public $listeContraintes = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("sorts");

        $this->hasOne('idEcoleMagie', 'Ecolesmagie', 'id', ['alias' => 'ecole']);
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sorts[]|Sorts|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sorts|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des sors disponibles
     * @return Sorts[]|Sorts|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeSortsDisponibles() {
        $listeSorts = Sorts::find(['idEcoleMagie IS NULL OR idEcoleMagie = 0']);
        if ($listeSorts == false || count($listeSorts) < 1) {
            return array();
        } else {
            return $listeSorts;
        }
    }

    /**
     * Retourne une description tronquée
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
     * Retourne la liste des images pour les sorts
     * @return string
     */
    public function genererListeImagesSort() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/sorts';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageSort();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/sorts/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des images pour les sorts
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImagesSortVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/sorts';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageSort();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Construit les éléments particuliers propre au panthéon choisi
     * @param unknown $type
     * @param string $idEcole
     * @return unknown|string
     */
    public function genererDivParticulariteNatureMagie($type, $idEcole = false) {
        if (!$idEcole) {
            if ($this->ecole != null && !empty($this->ecole) && $this->ecole->natureMagie != null && !empty($this->ecole->natureMagie)) {
                $fichier = new $this->ecole->natureMagie->fichier();
                return $fichier->genererInformationSort($type, $this);
            }
        } else {
            $ecole = Ecolesmagie::findFirst(['id = :id:', 'bind' => ['id' => $idEcole]]);
            if ($ecole->natureMagie != null && !empty($ecole->natureMagie)) {
                $fichier = new $ecole->natureMagie->fichier();
                return $fichier->genererInformationSort($type, $this);
            } else {
                return "<span id='specificiteNatureMagie'>Ce sort n'étant lié à aucun type de magie, il n'y a pas de spécificités associées.</span>";
            }
        }
    }

    /**
     * Méthode permettant de charger les effets
     */
    public function chargeListeEffets() {
        $this->listeEffets = array();
        $listeEffets = AssocSortsEffetsParam::find(['idSort = :idSort:', 'bind' => ['idSort' => $this->id], 'order' => 'position']);
        if ($listeEffets != false && count($listeEffets) > 0) {
            $effet = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeEffets as $assocSortEffetParam) {
                $compteur++;
                if ($effet == null || $effet->id != $assocSortEffetParam->idEffet || $position != $assocSortEffetParam->position) {
                    if ($effet != null) {
                        $this->listeEffets[count($this->listeEffets)] = $effet;
                    }
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $assocSortEffetParam->idEffet]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocSortEffetParam->idParam]]);
                $parametre->valeur = $assocSortEffetParam->valeur;
                $parametre->valeurMin = $assocSortEffetParam->valeurMin;
                $parametre->valeurMax = $assocSortEffetParam->valeurMax;
                $parametre->position = $assocSortEffetParam->position;

                $effet->listeParametres[count($effet->listeParametres)] = $parametre;
                if ($compteur == count($listeEffets)) {
                    $this->listeEffets[count($this->listeEffets)] = $effet;
                }
                $position = $assocSortEffetParam->position;
            }
        }
    }

    /**
     * Permet de générer la liste des effets associés au sort
     * @param unknown $auth
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
                $retour .= "<td><span class='descriptionEffet'>" . $effet->genererDescription("sort", $this->id, $effet->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireEffet(\"sort\"," . $this->id . "," . $effet->id . "," . $effet->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerEffet(" . $effet->id . ",\"sort\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerEffet(" . $effet->id . ",\"sort\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span id='listeEffetVide' class='messageInfo'>Il n'y a aucun effet à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Méthode permettant de charger les contraintes
     */
    public function chargeListeContraintes() {
        $this->listeContraintes = array();
        $listeContraintes = AssocSortsContraintesParam::find(['idSort = :idSort:', 'bind' => ['idSort' => $this->id], 'order' => 'position']);
        if ($listeContraintes != false && count($listeContraintes) > 0) {
            $contrainte = null;
            $compteur = 0;
            foreach ($listeContraintes as $assocSortContrainteParam) {
                $compteur++;
                if ($contrainte == null || $contrainte->id != $assocSortContrainteParam->idContrainte) {
                    if ($contrainte != null) {
                        $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                    }
                    $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $assocSortContrainteParam->idContrainte]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocSortContrainteParam->idParam]]);
                $parametre->valeur = $assocSortContrainteParam->valeur;
                $parametre->valeurMin = $assocSortContrainteParam->valeurMin;
                $parametre->valeurMax = $assocSortContrainteParam->valeurMax;
                $parametre->position = $assocSortContrainteParam->position;

                $contrainte->listeParametres[count($contrainte->listeParametres)] = $parametre;
                if ($compteur == count($listeContraintes)) {
                    $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                }
            }
        }
    }

    /**
     * Permet de générer la liste des contraintes
     * @param unknown $auth
     * @return string
     */
    public function genererListeContraintes($auth) {
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
                $retour .= "<td><span class='descriptionContrainte'>" . $contrainte->genererDescription("sorts", $this->id, $contrainte->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireContrainte(\"sorts\"," . $this->id . "," . $contrainte->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerContrainte(" . $contrainte->id . ",\"sorts\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerContrainte(" . $contrainte->id . ",\"sorts\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
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
     * Permet de générer la liste des évolutions
     * @param $auth
     * @return string
     */
    public function genererListeEvolutions($auth) {
        return "TO DO";
    }

    /**
     * Permet de générer la liste des images pour les sorts
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImageSortVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/sorts';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageSort();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des écoles de magie disponibles
     * @return string
     */
    public static function getSelectEcoleMagieVide() {
        $listeEcole = Ecolesmagie::find();
        $retour = "";
        $retour .= "<select id='selectEcoleSort' onChange='chargeDivParticulariteTypeMagie();'><option value='0'>Aucune</option>";
        if (!empty($listeEcole)) {
            foreach ($listeEcole as $ecole) {
                $retour .= "<option value='" . $ecole->id . "'>" . $ecole->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des écoles de magie disponibles
     * @return string
     */
    public function getSelectEcoleMagie() {
        $listeEcole = Ecolesmagie::find();
        $retour = "";
        $retour .= "<select id='selectEcoleSort' onChange='chargeDivParticulariteTypeMagie();'><option value='0'>Aucune</option>";
        if ($listeEcole != false && count($listeEcole) > 0) {
            foreach ($listeEcole as $ecole) {
                if ($this->idEcoleMagie == $ecole->id) {
                    $retour .= "<option value='" . $ecole->id . "' selected>" . $ecole->nom . "</option>";
                } else {
                    $retour .= "<option value='" . $ecole->id . "'>" . $ecole->nom . "</option>";
                }
            }

        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Méthode pour générer la liste des images des sorts
     * @return string
     */
    public function genererListeImageSort() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/sorts';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageSort();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/sorts/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/sorts/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la description de la liste des effets
     * @return string
     */
    public function genererDescriptionGeneraleEffet() {
        $this->chargeListeEffets();
        $retour = "";
        if (count($this->listeEffets) > 0) {
            foreach ($this->listeEffets as $effet) {
                $retour .= $effet->genererDescription("sort", $this->id, $effet->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Permet de retourner l'objet sous forme de string
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= '[Nom : ' . $this->nom . ']';
        $retour .= '[Bloque : ' . $this->isBloque . ']';
        $retour .= '[Description : ' . $this->description . ']';
        $retour .= '[idEcoleMagie : ' . $this->idEcoleMagie . ']';
        $retour .= '[Image : ' . $this->image . ']';
        $retour .= '[idArticle : ' . $this->idArticle . ']';
        $retour .= '[Arcane : ' . $this->arcane . ']';
        $retour .= '[Esquivable : ' . $this->esquivable . ']';
        $retour .= '[Enseignable : ' . $this->enseignable . ']';
        $retour .= '[Retranscriptible : ' . $this->retranscriptibleSort . ']';
        $retour .= '[Mana : ' . $this->mana . ']';
        $retour .= '[Portee : ' . $this->portee . ']';
        $retour .= '[Cout en PA : ' . $this->coutPA . ']';
        $retour .= '[Duree : ' . $this->duree . ']';
        $retour .= '[CumulQuantite : ' . $this->cumulQuantite . ']';
        $retour .= '[CumulDuree : ' . $this->cumulDuree . ']';
        $retour .= '[idCiblage : ' . $this->idCiblage . ']';
        $retour .= '[isJS : ' . $this->isJS . ']';
        $retour .= '[isJSEV : ' . $this->isJSEV . ']';
        $retour .= '[eventLanceur : ' . $this->eventLanceur . ']';
        $retour .= '[eventGlobal : ' . $this->eventGlobal . ']';
        return $retour;
    }

}
