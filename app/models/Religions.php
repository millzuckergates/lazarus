<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Religions extends \Phalcon\Mvc\Model {

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
     * @Column(column="idNatureMagie", type="integer", length=11, nullable=true)
     */
    public $idNatureMagie;

    /**
     *
     * @var integer
     * @Column(column="isDispoInscription", type="integer", length=1, nullable=true)
     */
    public $isDispoInscription;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    public $listeBonus = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("religions");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
        $this->hasOne('idNatureMagie', 'Naturesmagie', 'id', ['alias' => 'natureMagie']);

        $this->hasMany('id', 'AssocRoyaumesReligionJouable', 'idReligion', ['alias' => 'assoc_royaumes_religion_jouable']);
        $this->hasManyToMany('id', 'AssocRoyaumesReligionJouable', 'idReligion', 'idRoyaume', 'Royaumes', 'id', ['alias' => 'royaumesJoues']);

        $this->hasMany('id', 'AssocRacesReligionJouable', 'idReligion', ['alias' => 'assoc_races_religion_jouable']);
        $this->hasManyToMany('id', 'AssocRacesReligionJouable', 'idReligion', 'idRace', 'Races', 'id', ['alias' => 'racesJouees']);

        $this->hasMany('id', 'Dieux', 'idReligion', ['alias' => 'dieux']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Religions[]|Religions|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Religions|\Phalcon\Mvc\Model\ResultInterface
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
     * Retourne la liste des divinités accessibles
     */
    public function genererSelectListeDiviniteDisponible() {
        $listeDivinitesDisponible = $this->getListeDieuxDisponibles();
        if (count($listeDivinitesDisponible) > 0) {
            $retour = "<select id='selectDivinite'><option value='0'>Choisissez une divinité</option>";
            foreach ($listeDivinitesDisponible as $dieu) {
                $retour .= "<option value='" . $dieu->id . "'>" . $dieu->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterDiviniteReligion', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter une divinite dans la religion."));
        } else {
            $retour = "<span class='resultatJouableVide'>Aucune divinité n'est disponible pour cette religion.</span>";
        }
        return $retour;
    }

    /**
     * Méthode retournant la liste des divinités pouvant encore être ajoutée à ce panthéon
     * @return Dieux[][]|Dieux[]|\Phalcon\Mvc\Model\ResultSetInterface[]
     */
    public function getListeDieuxDisponibles() {
        $listeDivinites = Dieux::find(['idReligion = :id:', 'bind' => ['id' => 0]]);
        $listeDivinitesDisponibles = array();
        if (count($listeDivinites) > 0) {
            foreach ($listeDivinites as $divinite) {
                $find = false;
                if (count($this->dieux) > 0) {
                    foreach ($this->dieux as $dieu) {
                        if (!$find) {
                            if ($divinite->id == $dieu->id) {
                                $find = true;
                            }
                        }
                    }
                } else {
                    $listeDivinitesDisponibles[count($listeDivinitesDisponibles)] = $divinite;
                }
                if (!$find) {
                    $listeDivinitesDisponibles[count($listeDivinitesDisponibles)] = $divinite;
                }
            }
        }
        return $listeDivinitesDisponibles;
    }

    /**
     * Permet de construire la liste des divinités présente pour un panthéon
     * @param unknown $mode
     * @return string
     */
    public function genererListeDivinites($mode) {
        $retour = "";
        if (count($this->dieux) > 0) {
            foreach ($this->dieux as $dieu) {
                $retour .= "<div class='dieuReligion'>";
                $retour .= "<div class='dieuReligionImage' onclick='afficherDetailDieu(" . $dieu->id . ");'>" . Phalcon\Tag::image([$dieu->img, "class" => 'iconeMiniatureDieu']) . "</div>";
                $retour .= "<div class='dieuReligionInfo'><span class='nomDiviniteReligion'>" . $dieu->nom . "</span>";
                if ($mode == "modification") {
                    $retour .= '<input type="button" class="buttonMoins" onclick="supprimerDiviniteReligion(' . $dieu->id . ');"/>';
                }
                $retour .= "</div></div>";
            }
        } else {
            $retour .= "<span class='resultatJouableVide'>Cette religion ne prie aucune divinité.</span>";
        }
        return $retour;
    }

    /**
     * Permet de générer la liste des images disponibles
     * @return string
     */
    public function genererListeImagesReligion() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/religions';
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
        $retour .= '<select id="listeImage" onchange="changerImageReligion();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->img != null && $this->img == "public/img/site/illustrations/religions/" . $fichier) {
                    $retour .= "<option value='public/img/site/illustrations/religions/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/religions/" . $fichier . "'>" . $fichier . "</option>";
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
    public static function genererListeImagesReligionVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/religions';
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
        $retour .= '<select id="listeImage" onchange="changerImageReligion();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour .= "<option value='public/img/site/illustrations/religions/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/religions/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Permet de générer les royaumes dans laquelle cette religion
     * est présente à l'inscription
     * @return string
     */
    public function genererPresenceRoyaume() {
        if (count($this->royaumesJoues) > 0) {
            $retour = "<span id='listeRoyaumesJoues'>";
            foreach ($this->royaumesJoues as $royaume) {
                $retour .= " " . $royaume->nom;
            }
            $retour .= "</span>";
        } else {
            $retour = "<span class='resultatJouableVide'>Cette religion n'est jouable dans aucun royaume à l'inscription.</span>";
        }
        return $retour;
    }

    /**
     * Permet de générer les races pouvant pratiquer cette religion
     * à l'inscription
     * @return string
     */
    public function genererPresenceRace() {
        if (count($this->racesJouees) > 0) {
            $retour = "<span id='listeRacesJouees'>";
            foreach ($this->racesJouees as $race) {
                $retour .= " " . $race->nom;
            }
            $retour .= "</span>";
        } else {
            $retour = "<span class='resultatJouableVide'>Cette religion ne peut être adoptée par aucune race à l'inscription.</span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des natures magies
     * @return string
     */
    public static function genererSelectNatureMagieEmpty() {
        $listeNatureMagie = Naturesmagie::find();
        $retour = "<select id='selectNatureMagieReligion'><option value='0'>Choisissez un type de magie</option>";
        foreach ($listeNatureMagie as $natureMagie) {
            $retour .= "<option value='" . $natureMagie->id . "'>" . $natureMagie->nom . "</option>";
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des natures magies
     * @return string
     */
    public function genererSelectNatureMagie() {
        $listeNatureMagie = Naturesmagie::find();
        $retour = "<select id='selectNatureMagieReligion'><option value='0'>Choisissez un type de magie</option>";
        foreach ($listeNatureMagie as $natureMagie) {
            if ($this->idNatureMagie == $natureMagie->id) {
                $retour .= "<option value='" . $natureMagie->id . "' selected>" . $natureMagie->nom . "</option>";
            } else {
                $retour .= "<option value='" . $natureMagie->id . "'>" . $natureMagie->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }


    /**
     * Permet d'afficher la liste des bonus
     * @param unknown $auth
     * @return string
     */
    public function genererListeBonus($auth) {
        $retour = "";
        $this->chargeListeBonus();
        if (count($this->listeBonus) > 0) {
            $retour .= "<table class='tableListeBonus'>";
            $retour .= "<tr>
							<th class='entete' widht='15%'>&nbsp;</th>
							<th class='entete' widht='15%'>Nom</th>
							<th class='entete' width='50%'>Description</th>
							<th class='entete' width='20%'>&nbsp;</th>
						</tr>";
            $ligne = 0;
            foreach ($this->listeBonus as $bonus) {
                $val = $ligne % 2;
                $retour .= "<tr class='ligne_" . $val . "' >";
                $retour .= "<td><span class='imageListeBonus'>" . $bonus->genererImage("religion", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><span class='nomListeBonus'>" . $bonus->nom . "</span></td>";
                $retour .= "<td><span class='descriptionBonus'>" . $bonus->genererDescription("religion", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireBonus(\"religion\"," . $this->id . "," . $bonus->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_GERER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerBonus(" . $bonus->id . ",\"religion\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerBonus(" . $bonus->id . ",\"religion\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span id='listeBonusVide' class='messageInfo'>Il n'y a aucun bonus à afficher.</span>";
        }
        return $retour;
    }

    /**
     * Permet de charger la liste des bonus
     */
    public function chargeListeBonus() {
        $this->listeBonus = array();
        $listeBonus = AssocReligionsBonusParam::find(['idReligion = :idReligion:', 'bind' => ['idReligion' => $this->id], 'order' => 'position']);
        if ($listeBonus != false && count($listeBonus) > 0) {
            $bonus = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeBonus as $assocReligionBonusParam) {
                $compteur++;
                if ($bonus == null || $bonus->id != $assocReligionBonusParam->idBonus || $position != $assocReligionBonusParam->position) {
                    if ($bonus != null) {
                        $this->listeBonus[count($this->listeBonus)] = $bonus;
                    }
                    $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $assocReligionBonusParam->idBonus]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocReligionBonusParam->idParam]]);
                $parametre->valeur = $assocReligionBonusParam->valeur;
                $parametre->position = $assocReligionBonusParam->position;

                $bonus->listeParametres[count($bonus->listeParametres)] = $parametre;
                if ($compteur == count($listeBonus)) {
                    $this->listeBonus[count($this->listeBonus)] = $bonus;
                }
                $position = $assocReligionBonusParam->position;
            }
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
        $retour .= "[Image : " . $this->img . "], ";
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[idNatureMagie " . $this->idNatureMagie . "], ";
        $retour .= "[Disponible inscription : " . $this->isDispoInscription . "]";
        return $retour;
    }
}
