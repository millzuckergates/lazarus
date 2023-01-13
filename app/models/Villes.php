<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Villes extends \Phalcon\Mvc\Model {

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
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     *
     * @var integer
     * @Column(column="idRoyaumeOrigine", type="integer", length=11, nullable=false)
     */
    public $idRoyaumeOrigine;

    /**
     *
     * @var integer
     * @Column(column="idRoyaumeActuel", type="integer", length=11, nullable=false)
     */
    public $idRoyaumeActuel;

    /**
     *
     * @var string
     * @Column(column="messageAccueil", type="string", nullable=false)
     */
    public $messageAccueil;

    /**
     *
     * @var integer
     * @Column(column="isNaissance", type="integer", length=1, nullable=false)
     */
    public $isNaissance;

    /**
     *
     * @var integer
     * @Column(column="xMin", type="integer", length=6, nullable=true)
     */
    public $xMin;

    /**
     *
     * @var integer
     * @Column(column="xMax", type="integer", length=6, nullable=true)
     */
    public $xMax;

    /**
     *
     * @var integer
     * @Column(column="yMin", type="integer", length=6, nullable=true)
     */
    public $yMin;

    /**
     *
     * @var integer
     * @Column(column="yMax", type="integer", length=6, nullable=true)
     */
    public $yMax;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("villes");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
        $this->hasOne('idRoyaumeOrigine', 'Royaumes', 'id', ['alias' => 'royaumeOrigine']);
        $this->hasOne('idRoyaumeActuel', 'Royaumes', 'id', ['alias' => 'royaumeActuel']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Villes[]|Villes|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Villes|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    public static function genererListeImageVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/villes';
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
        $retour .= '<select id="listeImage" onchange="changerImageVille();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "defaut.png") {
                    $retour .= "<option value='public/img/site/illustrations/villes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/villes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    public function genererListeImage() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/villes';
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
        $retour .= '<select id="listeImage" onchange="changerImageVille();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/villes/" . $fichier) {
                    $retour .= "<option value='public/img/site/illustrations/villes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/villes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    public static function getSelectRoyaumeVide($idSelect) {
        $retour = "";
        $listeRoyaumes = Royaumes::find(['order' => 'nom']);
        if ($listeRoyaumes != false && count($listeRoyaumes) > 0) {
            $retour .= '<select id="' . $idSelect . '">';
            foreach ($listeRoyaumes as $royaume) {
                $retour .= "<option value='" . $royaume->id . "'>" . $royaume->nom . "</option>";
            }
            $retour .= "</select>";
        }
        return $retour;
    }

    public function getSelectRoyaume($idSelect) {
        $retour = "";
        $listeRoyaumes = Royaumes::find(['order' => 'nom']);
        if ($listeRoyaumes != false && count($listeRoyaumes) > 0) {
            $retour .= '<select id="' . $idSelect . '">';
            foreach ($listeRoyaumes as $royaume) {
                if ($idSelect == "royaumeOrigine" && $this->idRoyaumeOrigine == $royaume->id) {
                    $retour .= "<option value='" . $royaume->id . "' selected>" . $royaume->nom . "</option>";
                } else {
                    if ($idSelect == "royaumeActuel" && $this->idRoyaumeActuel == $royaume->id) {
                        $retour .= "<option value='" . $royaume->id . "' selected>" . $royaume->nom . "</option>";
                    } else {
                        $retour .= "<option value='" . $royaume->id . "'>" . $royaume->nom . "</option>";
                    }
                }
            }
            $retour .= "</select>";
        }
        return $retour;
    }

    public function hasCarte() {
        $carte = Cartes::findById($this->id);
        if ($carte == false) {
            return false;
        } else {
            return true;
        }
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
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[idRoyaumeOrigine : " . $this->idRoyaumeOrigine . "], ";
        $retour .= "[idRoyaumeActuel : " . $this->idRoyaumeActuel . "]";
        $retour .= "[messageAccueil : " . $this->messageAccueil . "]";
        $retour .= "[isNaissance : " . $this->isNaissance . "]";
        $retour .= "[xMin : " . $this->xMin . "]";
        $retour .= "[xMax : " . $this->xMax . "]";
        $retour .= "[yMin : " . $this->yMin . "]";
        $retour .= "[yMax : " . $this->yMax . "]";
        return $retour;
    }
}
