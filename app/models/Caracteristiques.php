<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Caracteristiques extends \Phalcon\Mvc\Model {

    //Constantes pour les types
    const CARAC_PRIMAIRE = "Primaire";
    const CARAC_SECONDAIRE = "Secondaire";

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
     * @Column(column="description", type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=300, nullable=false)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=50, nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(column="isModifiable", type="integer", length=1, nullable=false)
     */
    public $isModifiable;

    /**
     *
     * @var string
     * @Column(column="trigramme", type="string", length=3, nullable=false)
     */
    public $trigramme;

    /**
     *
     * @var string
     * @Column(column="formule", type="string", nullable=true)
     */
    public $formule;

    /**
     *
     * @var integer
     * @Column(column="valMin", type="integer", length=5, nullable=true)
     */
    public $valMin;

    /**
     *
     * @var integer
     * @Column(column="valMax", type="integer", length=5, nullable=true)
     */
    public $valMax;

    /**
     *
     * @var string
     * @Column(column="genre", type="string", length=15, nullable=false)
     */
    public $genre;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("caracteristiques");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Caracteristiques[]|Caracteristiques|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Caracteristiques|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des types de caracs
     * @return string[]
     */
    public static function getListeType() {
        $liste = array();
        $liste[count($liste)] = Caracteristiques::CARAC_PRIMAIRE;
        $liste[count($liste)] = Caracteristiques::CARAC_SECONDAIRE;
        return $liste;
    }

    /**
     * Retourne la liste des caractéristiques selon un filtre sur le
     * type
     * @param unknown $filtre
     * @return NULL|Caracteristiques[]
     */
    public static function getListeCaracs($filtre = false) {
        $listeCarac = array();
        if ($filtre != false) {
            $listeCarac = Caracteristiques::find(['type = :type:', 'bind' => ['type' => $filtre], 'order' => 'nom']);
        } else {
            $listeCarac = Caracteristiques::find();
        }
        if ($listeCarac != false && count($listeCarac) > 0) {
            return $listeCarac;
        }
        return $listeCarac;
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
     * Permet de générer la liste des images pour les caractéristiques
     * @return string
     */
    public function genererListeImageCarac() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/caracteristiques';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageCarac();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/caracteristiques/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/caracteristiques/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/caracteristiques/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images
     * @param unknown $imgDir
     */
    public static function genererListeImageCaracVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/caracteristiques';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageCarac();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "defaut.png") {
                    $retour = $retour . "<option value='public/img/site/illustrations/caracteristiques/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/caracteristiques/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }


    /**
     * Génère la liste des types
     * @param unknown $auth
     * @return string
     */
    public function genererListeType($auth) {
        $listeType = Caracteristiques::getListeType();
        $retour = "";
        $retour .= "<select id='selectListeTypeCarac' onChange='afficherBlocFormule();'><option value='0'>Aucune</option>";
        if (!empty($listeType)) {
            foreach ($listeType as $type) {
                if ($type == Caracteristiques::CARAC_PRIMAIRE && Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CARAC_PRIMAIRE, $auth['autorisations'])) {
                    if ($this->type == $type) {
                        $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
                    } else {
                        $retour .= "<option value='" . $type . "'>" . $type . "</option>";
                    }
                } else {
                    if ($this->type == $type) {
                        $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
                    } else {
                        $retour .= "<option value='" . $type . "'>" . $type . "</option>";
                    }
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des types
     * @param unknown $auth
     * @return string
     */
    public static function genererListeTypeVide($auth) {
        $listeType = Caracteristiques::getListeType();
        $retour = "";
        $retour .= "<select id='selectListeTypeCarac' onChange='afficherBlocFormule();'><option value='0'>Aucune</option>";
        if (!empty($listeType)) {
            foreach ($listeType as $type) {
                if ($type == Caracteristiques::CARAC_PRIMAIRE && Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_CARAC_PRIMAIRE, $auth['autorisations'])) {
                    $retour .= "<option value='" . $type . "'>" . $type . "</option>";
                } else {
                    $retour .= "<option value='" . $type . "'>" . $type . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Transforme l'objet en chaine de caractère
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "Nom : " . $this->nom . ", ";
        $retour .= "Genre : " . $this->genre . ", ";
        $retour .= "Description :" . $this->description . ", ";
        $retour .= "Image :" . $this->image . ", ";
        $retour .= "Type :" . $this->type . ", ";
        $retour .= "Trigramme : " . $this->trigramme . ", ";
        if (isset($this->formule)) {
            $retour .= "Formule : " . $this->formule . ", ";
        }
        if (isset($this->valeurMin)) {
            $retour .= "Valeur min : " . $this->valeurMin;
        }
        if (isset($this->valeurMax)) {
            $retour .= "Valeur max : " . $this->valeurMax;
        }
        if ($this->isModifiable == 0) {
            $retour .= "Modifiable : Non, ";
        } else {
            $retour .= "Modifiable : Oui, ";
        }
        return $retour;
    }
}
