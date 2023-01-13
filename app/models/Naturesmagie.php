<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Naturesmagie extends \Phalcon\Mvc\Model {

    const CONSTANTE_MAGIE_DIVINE = "DIVINE";
    const CONSTANTE_MAGIE_PROFANE = "PROFANE";

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
     * @Column(column="image", type="string", length=80, nullable=false)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(column="description", type="string", length=250, nullable=false)
     */
    public $description;

    /**
     *
     * @var integer
     * @Column(column="isDispoInscription", type="integer", length=1, nullable=false)
     */
    public $isDispoInscription;

    /**
     *
     * @var integer
     * @Column(column="bloque", type="integer", length=1, nullable=false)
     */
    public $bloque;

    /**
     *
     * @var string
     * @Column(column="fichier", type="string", length=100, nullable=false)
     */
    public $fichier;

    /**
     *
     * @var string
     * @Column(column="couleur", type="string", length=10, nullable=true)
     */
    public $couleur;

    /**
     *
     * @var string
     * @Column(column="typeNatureMagie", type="string", length=25, nullable=true)
     */
    public $typeNatureMagie;

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
        $this->setSource("naturesmagie");

        $this->hasMany('id', 'Ecolesmagie', 'idNatureMagie', ['alias' => 'ecoles']);
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Naturesmagie[]|Naturesmagie|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Naturesmagie|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Réduit la taille de la description pour en
     * offrir un résumé
     */
    public function resumeDescription() {
        if (strlen($this->description) > 250) {
            return substr($this->description, 0, 250) . "...";
        } else {
            return $this->description;
        }
    }

    /**
     * Permet de générer la liste des écoles de magie d'une nature de magie
     * @param unknown $auth
     */
    public function genererListeEcolesMagie($auth) {
        $retour = "";
        if (count($this->ecoles) > 0) {
            foreach ($this->ecoles as $ecole) {
                $retour .= "<div class='ecoleNatureMagie'>";
                $retour .= "<div class='ecoleNatureMagieImage' onclick='afficherDetailEcole(" . $ecole->id . ");'>" . Phalcon\Tag::image([$ecole->image, "class" => 'iconeMiniatureEcole']) . "</div>";
                $retour .= "<div class='ecoleNatureMagieInfo'><span class='nomEcoleNatureMagie'>" . $ecole->nom . "</span>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_MAGIE_MODIFIER, $auth['autorisations'])) {
                    $retour .= '<input type="button" class="buttonMoins" onclick="supprimerEcoleNatureMagie(' . $ecole->id . ');"/>';
                }
                $retour .= "</div></div>";
            }
        } else {
            $retour .= "<span class='resultatJouableVide'>Il n'y a pas d'écoles de magie associées.</span>";
        }
        return $retour;
    }

    /**
     * Permet de construire le select des écoles encore disponibles
     * @return string
     */
    public function genererSelectListeEcolesAutorisees() {
        $listeEcolesDisponibles = Ecolesmagie::getListeEcolesDisponibles();
        $retour = "";
        if (!empty($listeEcolesDisponibles)) {
            $retour .= "<select id='selectEcoleDisponible'><option value='0'>Aucune</option>";
            foreach ($listeEcolesDisponibles as $ecole) {
                $retour .= "<option value='" . $ecole->id . "'>" . $ecole->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterEcoleNatureMagie', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter une école de magie à cette nature de magie."));
        } else {
            $retour = "<span class='resultatMagieVide'>Il n'y a plus d'écoles de magie disponibles.</span>";
        }
        return $retour;
    }

    /**
     * Permet de générer le select avec la liste des images pour les types de magie
     * @return string
     */
    public function genererListeImageTypeMagie() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/naturesmagie';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageNatureMagie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/naturesmagie/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/naturesmagie/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/naturesmagie/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des Types de Nature de Magie
     * @return string
     */
    public function getSelectTypeNatureMagie() {
        $retour = "<select id='selectTypeNatureMagie'><option value='0'>Aucun</option>";
        $listeTypes = Naturesmagie::getListeTypeNatureMagie();
        for ($i = 0; $i < count($listeTypes); $i++) {
            $type = $listeTypes[$i];
            if ($this->typeNatureMagie == $type) {
                $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
            } else {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Méthode pour retourner la liste des types de natures de magie
     * @return string[]
     */
    public static function getListeTypeNatureMagie() {
        $tableauTypeNatureMagie = array();
        $tableauTypeNatureMagie[0] = Naturesmagie::CONSTANTE_MAGIE_PROFANE;
        $tableauTypeNatureMagie[1] = Naturesmagie::CONSTANTE_MAGIE_DIVINE;
        return $tableauTypeNatureMagie;
    }

    /**
     * Retourne la liste des fichiers de nautres magie
     */
    public static function getListeFiles($repScript) {
        $listeFichier = Files::getFiles($repScript);
        if ($listeFichier == "error") {
            return null;
        }
        return $listeFichier;
    }

    /**
     * Retourne la liste des Types de Nature de Magie
     * @return string
     */
    public function getListeFichiers($repScript) {
        $retour = "<select id='selectFichierNatureMagie'><option value='0'>Aucun</option>";
        $listeFichier = Naturesmagie::getListeFiles($repScript);
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
     * Permet de générer la liste des images pour les natures de magie
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImageTypeMagieVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/naturesmagie';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageNatureMagie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/naturesmagie/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/naturesmagie/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des types de natures magie
     * @return string
     */
    public static function getSelectTypeNatureMagieVide() {
        $retour = "<select id='selectTypeNatureMagie'><option value='0'>Aucun</option>";
        $listeTypes = Naturesmagie::getListeTypeNatureMagie();
        for ($i = 0; $i < count($listeTypes); $i++) {
            $type = $listeTypes[$i];
            if ("default.jpg" == $type) {
                $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
            } else {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des fichiers
     * @return string
     */
    public static function getListeFichiersVide($repScript) {
        $retour = "<select id='selectFichierNatureMagie'><option value='0'>Aucun</option>";
        $listeFichier = Naturesmagie::getListeFiles($repScript);
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
        $retour .= "[Bloque : " . $this->bloque . "]";
        $retour .= "[Fichier : " . $this->fichier . "]";
        $retour .= "[Type : " . $this->typeNatureMagie . "]";
        return $retour;
    }
}
