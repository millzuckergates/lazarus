<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Dieux extends \Phalcon\Mvc\Model {

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
     * @Column(column="nom", type="string", length=70, nullable=false)
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
     * @Column(column="img", type="string", length=200, nullable=true)
     */
    public $img;

    /**
     *
     * @var integer
     * @Column(column="idReligion", type="integer", length=11, nullable=false)
     */
    public $idReligion;

    /**
     *
     * @var string
     * @Column(column="couleur", type="string", length=10, nullable=false)
     */
    public $couleur;

    /**
     *
     * @var integer
     * @Column(column="idRace", type="integer", length=11, nullable=false)
     */
    public $idRace;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     *
     * @var integer
     * @Column(column="isDispoInscription", type="integer", length=1, nullable=true)
     */
    public $isDispoInscription;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("dieux");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
        $this->hasOne('idRace', 'Races', 'id', ['alias' => 'race']);
        $this->hasOne('idReligion', 'Religions', 'id', ['alias' => 'religion']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Dieux[]|Dieux|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Dieux|\Phalcon\Mvc\Model\ResultInterface
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
     * Permet de générer la liste des images disponibles
     * @return string
     */
    public function genererListeImagesDieu() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/divinites';
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
        $retour .= '<select id="listeImage" onchange="changerImageDieu();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->img != null && $this->img == "public/img/site/illustrations/divinites/" . $fichier) {
                    $retour .= "<option value='public/img/site/illustrations/divinites/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/divinites/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images disponibles
     * @return string
     */
    public static function genererListeImagesDieuVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/divinites';
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
        $retour .= '<select id="listeImage" onchange="changerImageDieu();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour .= "<option value='public/img/site/illustrations/divinites/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/divinites/" . $fichier . "'>" . $fichier . "</option>";
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
        $retour .= "[Image : " . $this->img . "], ";
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[Couleur : " . $this->couleur . "], ";
        $retour .= "[Disponible inscription : " . $this->isDispoInscription . "]";
        $retour .= "[Race : " . $this->race->nom . "]";
        $retour .= "[Religion : " . $this->idReligion . "]";
        return $retour;
    }

}
