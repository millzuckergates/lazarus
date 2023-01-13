<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Ecolesmagie extends \Phalcon\Mvc\Model {

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
     * @Column(column="idNatureMagie", type="integer", length=11, nullable=true)
     */
    public $idNatureMagie;

    /**
     *
     * @var integer
     * @Column(column="idCompetence", type="integer", length=11, nullable=false)
     */
    public $idCompetence;

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
     * @Column(column="couleur", type="string", length=255, nullable=true)
     */
    public $couleur;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(column="isDispoInscription", type="integer", length=1, nullable=true)
     */
    public $isDispoInscription;

    /**
     *
     * @var integer
     * @Column(column="isBloque", type="integer", length=1, nullable=true)
     */
    public $isBloque;

    /**
     *
     * @var string
     * @Column(column="fichier", type="string", length=255, nullable=false)
     */
    public $fichier;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("ecolesmagie");

        $this->hasMany('id', 'Sorts', 'idEcoleMagie', ['alias' => 'sorts']);
        $this->hasOne('idNatureMagie', 'Naturesmagie', 'id', ['alias' => 'natureMagie']);
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
        $this->hasOne('idCompetence', 'Competences', 'id', ['alias' => 'competence']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Ecolesmagie[]|Ecolesmagie|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Ecolesmagie|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des écoles de magie disponibles
     * @return Ecolesmagie[]|Ecolesmagie|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeEcolesDisponibles() {
        $listeEcolesDisponibles = Ecolesmagie::find(['idNatureMagie IS NULL OR idNatureMagie = 0']);
        if (!$listeEcolesDisponibles) {
            return array();
        } else {
            return $listeEcolesDisponibles;
        }
    }

    /**
     * Méthode pour générer la liste des images de l'école de magie
     * @return string
     */
    public function genererListeImageEcoleMagie() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/ecolesmagie';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageEcoleMagie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/ecolesmagie/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/ecolesmagie/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/ecolesmagie/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images pour les écoles
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImageEcoleMagieVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/ecolesmagie';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageEcoleMagie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/ecolesmagie/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/ecolesmagie/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de résumer la description pour la liste des écoles
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
     * Permet de générer le tableau de la liste des sorts
     * @param unknown $auth
     * @return string
     */
    public function genererListeSorts($auth) {
        if (!empty($this->sorts) && count($this->sorts) > 0) {
            $retour = "<table class='tableListeSort'>";
            $retour = $retour . "<tr><th class='entete' width='20%'>&nbsp;</th>
    								<th class='entete' width='15%'>Nom</th>
    								<th class='entete' witdh='55%'>Description</th>
    								<th class='entete' width='10%'>&nbsp;</th></tr>";
            foreach ($this->sorts as $sort) {
                $retour .= "<tr onclick='afficherDetailSort(" . $sort->id . ")' style='background-color:" . $this->couleur . "'>";
                $retour .= "<td>" . Phalcon\Tag::image([$sort->image, "class" => 'miniatureSort']) . "</td>";
                $retour .= "<td><span class='texteMagieTableau'>" . $sort->nom . "</span></td>";
                $retour .= "<td><span class='texteMagieTableau'>" . $sort->resumeDescription() . "</span></td>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<td><input type='button' class='buttonMoins' onclick='boxRetirerSort(" . $sort->id . ");'/></td>";
                } else {
                    $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherDetailSort(" . $sort->id . ");'/></td>";
                }
                $retour .= "</tr>";
            }
            $retour .= "</table>";
        } else {
            $retour = "<span id='listeSortVide' class='messageInfo'>Il n'y a aucun sort à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des sorts encore disponibles
     * @return string
     */
    public function genererSelectListeSortsAutorises() {
        $listeSortsDisponibles = Sorts::getListeSortsDisponibles();
        $retour = "";
        if (!empty($listeSortsDisponibles) && count($listeSortsDisponibles) > 0) {
            $retour .= "<select id='selectSortDisponible'><option value='0'>Aucun</option>";
            foreach ($listeSortsDisponibles as $sort) {
                $retour .= "<option value='" . $sort->id . "'>" . $sort->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterSortEcoleMagie', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter un sort à une école de magie."));
        } else {
            $retour = "<span class='resultatMagieVide'>Il n'y a plus de sorts disponibles.</span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des types de magie
     * @return string
     */
    public function getSelectTypeNatureMagie() {
        $listeNatureMagie = Naturesmagie::find();
        $retour = "";
        $retour .= "<select id='selectTypeNatureMagieEcole'><option value='0'>Aucun</option>";
        if (!empty($listeNatureMagie)) {
            foreach ($listeNatureMagie as $natureMagie) {
                if ($this->idNatureMagie == $natureMagie->id) {
                    $retour .= "<option value='" . $natureMagie->id . "' selected>" . $natureMagie->nom . "</option>";
                } else {
                    $retour .= "<option value='" . $natureMagie->id . "'>" . $natureMagie->nom . "</option>";
                }
            }

        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des types de magies
     * @return string
     */
    public static function getSelectTypeNatureMagieVide() {
        $listeNatureMagie = Naturesmagie::find();
        $retour = "";
        $retour .= "<select id='selectTypeNatureMagieEcole'><option value='0'>Aucun</option>";
        if (!empty($listeNatureMagie)) {
            foreach ($listeNatureMagie as $natureMagie) {
                $retour .= "<option value='" . $natureMagie->id . "'>" . $natureMagie->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des scripts liés aux écoles
     * @param unknown $repScript
     * @return string
     */
    public function getSelectListFichierEcole($repScript) {
        $retour = "<select id='selectScriptEcole'><option value='0'>Aucun</option>";
        $listeFichier = Ecolesmagie::getListeFiles($repScript);
        if ($listeFichier != null) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->fichier . ".php" == $fichier) {
                    $retour .= "<option value='" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des scripts liés aux écoles
     * @param unknown $repScript
     * @return string
     */
    public static function getSelectListFichierEcoleVide($repScript) {
        $retour = "<select id='selectScriptEcole'><option value='0'>Aucun</option>";
        $listeFichier = Ecolesmagie::getListeFiles($repScript);
        if ($listeFichier != null) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                $retour .= "<option value='" . $fichier . "'>" . $fichier . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des scripts disponibles pour les écoles de magie
     */
    public static function getListeFiles($repScript) {
        $listeFichier = Files::getFiles($repScript);
        if ($listeFichier == "error") {
            return null;
        }
        return $listeFichier;
    }

    /**
     * Retourne la liste des compétences
     * @return string
     */
    public function getSelectListCompetenceEcole() {
        $retour = "<select id='selectCompetenceAssocieeEcole'><option value='0'>Aucune</option>";
        $listeCompetences = Competences::find(['type = :type:', 'bind' => ['type' => Competences::TYPE_MAGIQUE]]);
        if ($listeCompetences != null) {
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
    public static function getSelectListCompetenceEcoleVide() {
        $retour = "<select id='selectCompetenceAssocieeEcole'><option value='0'>Aucune</option>";
        $listeCompetences = Competences::find(['type = :type:', 'bind' => ['type' => Competences::TYPE_MAGIQUE]]);
        if ($listeCompetences != null) {
            foreach ($listeCompetences as $competence) {
                $retour .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Méthode pour afficher l'objet sous la forme d'un string
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Nom : " . $this->nom . "], ";
        $retour .= "[Description : " . $this->description . "], ";
        $retour .= "[Image : " . $this->image . "], ";
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[Couleur " . $this->couleur . "], ";
        $retour .= "[Disponible inscription : " . $this->isDispoInscription . "]";
        $retour .= "[Bloque : " . $this->isBloque . "]";
        $retour .= "[Fichier : " . $this->fichier . "]";
        $retour .= "[IdCompetence : " . $this->idCompetence . "]";
        $retour .= "[IdNatureMagie : " . $this->idNatureMagie . "]";
        return $retour;
    }
}
