<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class CategoriesTalent extends \Phalcon\Mvc\Model {

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
     * @Column(column="image", type="string", length=80, nullable=false)
     */
    public $image;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("categories_talent");

        //Init Jointure
        $this->hasMany('id', 'FamillesTalent', 'idCategorie', ['alias' => 'familles']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CategoriesTalent[]|CategoriesTalent|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CategoriesTalent|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    public function genererListeCategorie($auth) {
        $categories = CategoriesTalent::find(['order' => 'nom ASC']);
        $retour = "<div id='listeCategories'>";
        $retour .= "<input type='hidden' id='idCategorieSelect' value='" . $this->id . "'/>";
        $onMouseOverDiv = "";
        if ($categories != false && count($categories) > 0) {
            foreach ($categories as $categorie) {
                if ($this->id == $categorie->id) {
                    $div = "<div class='categorieTalentSelect' id='divCategorieTalent_" . $categorie->id . "' onclick='chargeCategorie(" . $categorie->id . ");' onMouseOver='afficherDescriptionCategorie(" . $categorie->id . ");' onMouseOut='hideDescriptionCategorie(" . $categorie->id . ");'>";
                    $div .= Phalcon\Tag::image([$categorie->image, "class" => "miniatureImageCategorieSelect"]);
                    $div .= "<span class='nomCategorieTitre'>" . $categorie->nom . "</span>";
                    $div .= "</div>";
                } else {
                    $div = "<div class='categorieTalent'  id='divCategorieTalent_" . $categorie->id . "' onclick='chargeCategorie(" . $categorie->id . ");' onMouseOver='afficherDescriptionCategorie(" . $categorie->id . ");' onMouseOut='hideDescriptionCategorie(" . $categorie->id . ");'>";
                    $div .= Phalcon\Tag::image([$categorie->image, "class" => "miniatureImageCategorie"]);
                    $div .= "<span class='nomCategorieTitre'>" . $categorie->nom . "</span>";
                    $div .= "</div>";
                }
                $retour .= $div;
                $onMouseOverDiv .= "<div class='descriptionTalentCategorie' id='divDescriptionCategorie_" . $categorie->id . "' style='display:none;'>";
                $onMouseOverDiv .= "<span class='spanDescriptionTalentCategorie'>" . str_replace("\n", "<br/>", $categorie->description) . "</span>";
                $onMouseOverDiv .= "</div>";
            }
        }
        $retour .= $onMouseOverDiv;
        $retour .= "</div>";

        //Div pour les boutons
        if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
            $retour .= "<div id='divBoutonsCategories'>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'boutonAjouterNouvelleCategorie', 'class' => 'buttonPlus', 'title' => "Permet d'accéder au formulaire de création d'une catégorie."));
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'boutonModifierCategorie', 'class' => 'buttonActualiser', 'title' => "Permet d'accéder au formulaire d'édition d'une catégorie."));
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'boutonSupprimerCategorie', 'class' => 'buttonMoins', 'title' => "Permet de supprimer la catégorie."));
            $retour .= "</div>";
        }

        return $retour;
    }

    public function genererListeFamille($auth, $idFamille, $mode) {
        if (isset($this->familles) && $this->familles != null && count($this->familles) > 0) {
            if ($idFamille == null) {
                $idFamille = $this->familles[0]->id;
            }
            $retour = "<input type='hidden' id='idFamilleSelect' value='" . $idFamille . "'/>";
            $retour .= "<ul class='listeFamillesCategorie'>";
            $onMouseOverDiv = "";
            foreach ($this->familles as $famille) {
                $retour .= "<li class='liFamilleCategorie'>";
                $retour .= "<div class='divFamilleLI' id='divFamilleLI_" . $famille->id . "' onMouseOver='afficherDescriptionFamille(" . $famille->id . ");' onMouseOut='hideDescriptionFamille(" . $famille->id . ");'>";
                if ($famille->id == $idFamille) {
                    $retour .= "<span class='spanFamille spanFamilleSelect' onclick='chargerFamille(" . $famille->id . ");' id='famille_" . $famille->id . "'>" . $famille->nom . "</span>";
                    $retour .= "<span id='nombreDePointDepenseFamille_" . $famille->id . "' class='spanFamille spanFamilleSelect nbPointFamille'>(" . $famille->getNbPoint($auth, 'mode') . ")</span>";
                } else {
                    $retour .= "<span class='spanFamille' onclick='chargerFamille(" . $famille->id . ");' id='famille_" . $famille->id . "'>" . $famille->nom . "</span>";
                    $retour .= "<span id='nombreDePointDepenseFamille_" . $famille->id . "' class='spanFamille nbPointFamille'>(" . $famille->getNbPoint($auth, 'mode') . ")</span>";
                }
                $retour .= "</div>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<div class='divBoutonFamilleLI'>";
                    $retour .= "<input type='button' class='buttonActualiser' onclick='chargerModifierFamille(" . $famille->id . ");' title='Permet de modifier la famille'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxSupprimerFamille(" . $famille->id . ");' title='Permet de supprimer la famille'/>";
                    $retour .= "</div>";
                }
                $retour .= "</li>";
                $onMouseOverDiv .= "<div class='descriptionTalentFamille' id='divDescriptionFamille_" . $famille->id . "' style='display:none;'>";
                $onMouseOverDiv .= "<span class='spanDescriptionTalentFamille'>" . str_replace("\n", "<br/>", $famille->description) . "</span>";
                $onMouseOverDiv .= "</div>";

            }
            $retour .= "</ul>";
            $retour .= $onMouseOverDiv;
        } else {
            $retour = "<span class='resultatTalentVide'>Il n'y a aucune famille de définie.</span>";
        }
        return $retour;
    }

    public static function genererListeImagesVide($repImg) {
        $directory = $repImg . 'site/illustrations/categoriestalent';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageCategorie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "defaut.png") {
                    $retour = $retour . "<option value='public/img/site/illustrations/categoriestalent/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/categoriestalent/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    public function genererListeImages() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/categoriestalent';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageCategorie();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/categoriestalent/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/categoriestalent/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/categoriestalent/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    public function supprimerCategorie() {
        $action = "Suppression de la catégorie : " . $this->nom;
        if (isset($this->familles) && count($this->familles) > 0) {
            foreach ($this->familles as $famille) {
                $action .= $famille->supprimerFamille();
            }
        }
        $this->delete();
        return $action;
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
}
