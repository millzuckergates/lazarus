<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Races extends \Phalcon\Mvc\Model {

    //Constantes pour les couleurs de cheveux
    const COULEUR_CHEVEUX_NOIR = "noir";
    const COULEUR_CHEVEUX_BRUN = "brun";
    const COULEUR_CHEVEUX_AUBURN = "auburn";
    const COULEUR_CHEVEUX_ROUX = "roux";
    const COULEUR_CHEVEUX_BLOND = "blond";
    const COULEUR_CHEVEUX_BLANC = "blanc";
    const COULEUR_CHEVEUX_CHAUVE = "chauve";
    const COULEUR_CHEVEUX_GRIS = "gris";

    //Constantes pour les couleurs des yeux
    const COULEUR_YEUX_MARRON = "marron";
    const COULEUR_YEUX_BLEU = "bleu";
    const COULEUR_YEUX_GRIS = "gris";
    const COULEUR_YEUX_NOISETTE = "noisette";
    const COULEUR_YEUX_VERT = "vert";
    const COULEUR_YEUX_NOIR = "noir";
    const COULEUR_YEUX_TURQUOISE = "turquoise";
    const COULEUR_YEUX_AMBRE = "ambre";
    const COULEUR_YEUX_VIOLET = "violet";
    const COULEUR_YEUX_ROUGE = "rouge";
    const COULEUR_YEUX_HAZEL = "hazel";
    const COULEUR_YEUX_VAIRON = "vairon";

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
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=90, nullable=false)
     */
    public $image;

    /**
     *
     * @var integer
     * @Column(column="poidsMin", type="integer", length=5, nullable=true)
     */
    public $poidsMin;

    /**
     *
     * @var integer
     * @Column(column="poidsMax", type="integer", length=5, nullable=true)
     */
    public $poidsMax;

    /**
     *
     * @var integer
     * @Column(column="tailleMin", type="integer", length=5, nullable=true)
     */
    public $tailleMin;

    /**
     *
     * @var integer
     * @Column(column="tailleMax", type="integer", length=5, nullable=true)
     */
    public $tailleMax;

    /**
     *
     * @var integer
     * @Column(column="ageMin", type="integer", length=3, nullable=true)
     */
    public $ageMin;

    /**
     *
     * @var integer
     * @Column(column="ageMax", type="integer", length=5, nullable=true)
     */
    public $ageMax;

    /**
     *
     * @var string
     * @Column(column="yeuxAutorise", type="string", length=300, nullable=true)
     */
    public $yeuxAutorise;

    /**
     *
     * @var string
     * @Column(column="cheveuxAutorise", type="string", length=300, nullable=true)
     */
    public $cheveuxAutorise;

    /**
     *
     * @var integer
     * @Column(column="isDispoInscription", type="integer", length=1, nullable=true)
     */
    public $isDispoInscription;

    public $listeBonus = array();

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("races");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);

        $this->hasMany('id', 'AssocRacesReligionJouable', 'idRace', ['alias' => 'assoc_races_religion_jouable']);
        $this->hasManyToMany('id', 'AssocRacesReligionJouable', 'idRace', 'idReligion', 'Religions', 'id', ['alias' => 'religionsJouable']);

        $this->hasMany('id', 'AssocRoyaumesRaceJouable', 'idRace', ['alias' => 'assoc_royaumes_race_jouable']);
        $this->hasManyToMany('id', 'AssocRoyaumesRaceJouable', 'idRace', 'idRoyaume', 'Royaumes', 'id', ['alias' => 'royaumesJoues']);

        $this->belongsTo('id', 'Dieux', 'idRace', ['alias' => 'divinite']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Races[]|Races|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Races|\Phalcon\Mvc\Model\ResultInterface
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
     * Retourne la liste des religions autorisées
     * (associées à la race)
     */
    public function getListeReligionsAutorisees() {
        $retour = array();
        if (!empty($this->religionsJouable)) {
            foreach ($this->religionsJouable as $religion) {
                if ($religion->isDispoInscription) {
                    $retour[count($retour)] = $religion;
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne la liste des religions autorisées
     */
    public function genererReligionsAutorisees($mode) {
        $listeReligionsAutorisees = $this->getListeReligionsAutorisees();
        if (!empty($listeReligionsAutorisees)) {
            $retour = "";
            foreach ($listeReligionsAutorisees as $religion) {
                $retour .= '<div class="divElementJouable">';
                $retour .= '<span class="libelleElementJouable">' . $religion->nom . '</span>';
                if ($mode == "modification") {
                    $retour .= '<input type="button" class="buttonMoins" onclick="supprimerReligionJouableRace(' . $religion->id . ');"/>';
                }
                $retour .= '</div>';
            }
        } else {
            $retour = "<span class='resultatJouableVide'>Aucune religion jouable n'est autorisée pour cette race.</span>";
        }
        return $retour;
    }


    /**
     * Permet de générer le select pour les religions
     * autorisées
     * @return string
     */
    public function genererSelectListeReligionsAutorisees() {
        $listeReligionAutorisables = $this->getListeReligionsAutorisable();
        $retour = "";
        if (!empty($listeReligionAutorisables)) {
            $retour .= "<select id='selectReligionAutorisable'><option value='0'>Aucune</option>";
            foreach ($listeReligionAutorisables as $religion) {
                $retour .= "<option value='" . $religion->id . "'>" . $religion->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterReligionJouableRace', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter une religion jouable pour une race."));
        } else {
            $retour = "<span class='resultatJouableVide'>Il n'y a plus de religions jouables disponibles.</span>";
        }
        return $retour;
    }


    /**
     * Retourne la liste des religions autorisables à l'inscription pour un royaume
     * @return Religions[][]|Religions[]|\Phalcon\Mvc\Model\ResultSetInterface[]
     */
    public function getListeReligionsAutorisable() {
        $listeReligions = Religions::find(['isDispoInscription = 1']);
        $listeAutorisables = array();
        foreach ($listeReligions as $religion) {
            $presente = false;
            foreach ($this->religionsJouable as $religionJouable) {
                if ($religionJouable->id == $religion->id) {
                    $presente = true;
                }
            }
            if (!$presente) {
                $listeAutorisables[count($listeAutorisables)] = $religion;
            }
        }
        return $listeAutorisables;
    }


    /**
     * Permet de générer la liste des images disponibles
     * @return string
     */
    public function genererListeImagesRace() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/races';
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
        $retour .= '<select id="listeImage" onchange="changerImageRace();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/races/" . $fichier) {
                    $retour .= "<option value='public/img/site/illustrations/races/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/races/" . $fichier . "'>" . $fichier . "</option>";
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
    public static function genererListeImagesRaceVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/races';
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
        $retour .= '<select id="listeImage" onchange="changerImageRace();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour .= "<option value='public/img/site/illustrations/races/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/site/illustrations/races/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Permet de générer les royaumes dans laquelle cette race
     * est présente
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
            $retour = "<span class='resultatJouableVide'>Cette race n'est jouable dans aucun royaume à l'inscription.</span>";
        }
        return $retour;
    }


    /**
     * Genere la liste des checkbox pour la couleur des yeux
     * @return string
     */
    public function genererListeCouleurYeux() {
        $retour = "";
        //Récupère la liste des constantes pour les couleurs des yeux
        $tableauCouleurYeux = Races::getListeCouleurYeux();
        $tableauCouleurAutorise = explode(",", $this->yeuxAutorise);

        if (!empty($tableauCouleurYeux)) {
            foreach ($tableauCouleurYeux as $couleur) {
                if (in_array($couleur, $tableauCouleurAutorise)) {
                    $retour .= "<input type='checkbox' class='physiqueElement' name='couleurYeux' value='" . $couleur . "' checked />" . $couleur . "&nbsp;&nbsp;";
                } else {
                    $retour .= "<input type='checkbox' class='physiqueElement' name='couleurYeux' value='" . $couleur . "' />" . $couleur . "&nbsp;&nbsp;";
                }
            }
        }
        return $retour;
    }

    /**
     * Génère la liste des couleurs pour les cheveux
     * @return string
     */
    public function genererListeCouleurCheveux() {
        $retour = "";
        //Récupère la liste des constantes pour les couleurs des cheveux
        $tableauCouleurCheveux = Races::getListeCouleurCheveux();
        $tableauCouleurAutorise = explode(",", $this->cheveuxAutorise);

        if (!empty($tableauCouleurCheveux)) {
            foreach ($tableauCouleurCheveux as $couleur) {
                if (in_array($couleur, $tableauCouleurAutorise)) {
                    $retour .= "<input type='checkbox' class='physiqueElement' name='couleurCheveux' value='" . $couleur . "' checked />" . $couleur . "&nbsp;&nbsp;";
                } else {
                    $retour .= "<input type='checkbox' class='physiqueElement' name='couleurCheveux' value='" . $couleur . "' />" . $couleur . "&nbsp;&nbsp;";
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne le tableau des constantes pour les couleurs des cheveux
     * @return string[]
     */
    public static function getListeCouleurCheveux() {
        $retour = array();
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_NOIR;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_BRUN;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_AUBURN;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_ROUX;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_BLOND;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_BLANC;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_CHAUVE;
        $retour[count($retour)] = Races::COULEUR_CHEVEUX_GRIS;
        return $retour;
    }

    /**
     * Retourne le tableau des constantes pour les couleurs des yeux
     * @return string[]
     */
    public static function getListeCouleurYeux() {
        $retour = array();
        $retour[count($retour)] = Races::COULEUR_YEUX_MARRON;
        $retour[count($retour)] = Races::COULEUR_YEUX_BLEU;
        $retour[count($retour)] = Races::COULEUR_YEUX_GRIS;
        $retour[count($retour)] = Races::COULEUR_YEUX_NOISETTE;
        $retour[count($retour)] = Races::COULEUR_YEUX_VERT;
        $retour[count($retour)] = Races::COULEUR_YEUX_NOIR;
        $retour[count($retour)] = Races::COULEUR_YEUX_TURQUOISE;
        $retour[count($retour)] = Races::COULEUR_YEUX_AMBRE;
        $retour[count($retour)] = Races::COULEUR_YEUX_VIOLET;
        $retour[count($retour)] = Races::COULEUR_YEUX_ROUGE;
        $retour[count($retour)] = Races::COULEUR_YEUX_HAZEL;
        $retour[count($retour)] = Races::COULEUR_YEUX_VAIRON;
        return $retour;
    }

    /**
     * Genere la liste des checkbox pour la couleur des yeux
     * @return string
     */
    public static function genererAllCouleurYeux() {
        $retour = "";
        //Récupère la liste des constantes pour les couleurs des yeux
        $tableauCouleurYeux = Races::getListeCouleurYeux();
        if (!empty($tableauCouleurYeux)) {
            foreach ($tableauCouleurYeux as $couleur) {
                $retour .= "<input type='checkbox' class='physiqueElement' name='couleurYeux' value='" . $couleur . "' />" . $couleur . "&nbsp;&nbsp;";
            }
        }
        return $retour;
    }

    /**
     * Génère la liste des couleurs pour les cheveux
     * @return string
     */
    public static function getAllCouleurCheveux() {
        $retour = "";
        //Récupère la liste des constantes pour les couleurs des cheveux
        $tableauCouleurCheveux = Races::getListeCouleurCheveux();

        if (!empty($tableauCouleurCheveux)) {
            foreach ($tableauCouleurCheveux as $couleur) {
                $retour .= "<input type='checkbox' class='physiqueElement' name='couleurCheveux' value='" . $couleur . "' />" . $couleur . "&nbsp;&nbsp;";
            }
        }
        return $retour;
    }

    /**
     * Permet de générer l'affichage de la liste des bonus
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
                $retour .= "<td><span class='imageListeBonus'>" . $bonus->genererImage("race", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><span class='nomListeBonus'>" . $bonus->nom . "</span></td>";
                $retour .= "<td><span class='descriptionBonus'>" . $bonus->genererDescription("race", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireBonus(\"race\"," . $this->id . "," . $bonus->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_GERER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerBonus(" . $bonus->id . ",\"race\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerBonus(" . $bonus->id . ",\"race\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
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
     * Charge la liste des bonus
     */
    public function chargeListeBonus() {
        $this->listeBonus = array();
        $listeBonus = AssocRacesBonusParam::find(['idRace = :idRace:', 'bind' => ['idRace' => $this->id], 'order' => 'position']);
        if ($listeBonus != false && count($listeBonus) > 0) {
            $bonus = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeBonus as $assocRaceBonusParam) {
                $compteur++;
                if ($bonus == null || $bonus->id != $assocRaceBonusParam->idBonus || $position != $assocRaceBonusParam->position) {
                    if ($bonus != null) {
                        $this->listeBonus[count($this->listeBonus)] = $bonus;
                    }
                    $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $assocRaceBonusParam->idBonus]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocRaceBonusParam->idParam]]);
                $parametre->valeur = $assocRaceBonusParam->valeur;
                $parametre->position = $assocRaceBonusParam->position;

                $bonus->listeParametres[count($bonus->listeParametres)] = $parametre;
                if ($compteur == count($listeBonus)) {
                    $this->listeBonus[count($this->listeBonus)] = $bonus;
                }
                $position = $assocRaceBonusParam->position;
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
        $retour .= "[Image : " . $this->image . "], ";
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[Poids entre " . $this->poidsMin . " et " . $this->poidsMax . "], ";
        $retour .= "[Taille entre " . $this->tailleMin . " et " . $this->tailleMax . "], ";
        $retour .= "[Age entre " . $this->ageMin . " et " . $this->ageMax . "], ";
        $retour .= "[Couleurs des yeux autorisées : " . $this->yeuxAutorise . "], ";
        $retour .= "[Couleurs de cheveux autorisées : " . $this->cheveuxAutorise . "], ";
        $retour .= "[Disponible inscription : " . $this->isDispoInscription . "]";
        return $retour;
    }
}
