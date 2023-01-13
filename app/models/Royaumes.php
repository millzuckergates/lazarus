<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Royaumes extends \Phalcon\Mvc\Model {

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
     * @Column(column="couleur", type="string", length=30, nullable=false)
     */
    public $couleur;

    /**
     *
     * @var string
     * @Column(column="etendard", type="string", length=200, nullable=true)
     */
    public $etendard;

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
        $this->setSource("royaumes");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);

        $this->hasMany('id', 'AssocRoyaumesReligionJouable', 'idRoyaume', ['alias' => 'assoc_royaumes_religion_jouable']);
        $this->hasManyToMany('id', 'AssocRoyaumesReligionJouable', 'idRoyaume', 'idReligion', 'Religions', 'id', ['alias' => 'religionsJouable']);

        $this->hasMany('id', 'AssocRoyaumesRaceJouable', 'idRoyaume', ['alias' => 'assoc_royaumes_race_jouable']);
        $this->hasManyToMany('id', 'AssocRoyaumesRaceJouable', 'idRoyaume', 'idRace', 'Races', 'id', ['alias' => 'racesJouable']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Royaumes[]|Royaumes|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Royaumes|\Phalcon\Mvc\Model\ResultInterface
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
     * (associées au royaume et jouables)
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
                    $retour .= '<input type="button" class="buttonMoins" onclick="supprimerReligionJouable(' . $religion->id . ');"/>';
                }
                $retour .= '</div>';
            }
        } else {
            $retour = "<span class='resultatJouableVide'>Aucune religion jouable n'est autorisée dans ce royaume.</span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des races autorisées
     * (liées au royaume et jouables)
     */
    public function getListeRacesAutorisees() {
        $retour = array();
        if (!empty($this->racesJouable)) {
            foreach ($this->racesJouable as $race) {
                if ($race->isDispoInscription) {
                    $retour[count($retour)] = $race;
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne le détail de la liste des races associées au royaume
     * @param unknown $mode
     * @return string
     */
    public function genererRacesAutorisees($mode) {
        $listeRacesAutorisees = $this->getListeRacesAutorisees();
        if (!empty($listeRacesAutorisees)) {
            $retour = "";
            foreach ($listeRacesAutorisees as $race) {
                $retour .= '<div class="divElementJouable">';
                $retour .= '<span class="libelleElementJouable">' . $race->nom . '</span>';
                if ($mode == "modification") {
                    $retour .= '<input type="button" class="buttonMoins" onclick="supprimerRaceJouable(' . $race->id . ');"/>';
                }
                $retour .= '</div>';
            }
        } else {
            $retour = "<span class='resultatJouableVide'>Aucune race jouable n'est autorisée dans ce royaume.</span>";
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
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterReligionJouable', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter une race jouable dans un royaume."));
        } else {
            $retour = "<span class='resultatJouableVide'>Il n'y a plus de religions jouables disponibles.</span>";
        }
        return $retour;
    }

    /**
     * Permet de générer la liste des étendards disponibles
     * @return string
     */
    public function genererListeEtendardsRoyaume() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/royaumes';
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
        $retour = $retour . '<select id="listeEtendard" onchange="changerImageRoyaume();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->etendard != null && $this->etendard == "public/img/site/illustrations/royaumes/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/royaumes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/royaumes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des étendards disponibles
     * @return string
     */
    public static function genererListeEtendardsRoyaumeVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/royaumes';
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
        $retour = $retour . '<select id="listeEtendard" onchange="changerImageRoyaume();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour = $retour . "<option value='public/img/site/illustrations/royaumes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/royaumes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }


    /**
     * Permet de générer le select pour les races
     * autorisées
     * @return string
     */
    public function genererSelectListeRacesAutorisees() {
        $listeRacesAutorisables = $this->getListeRacesAutorisables();
        $retour = "";
        if (!empty($listeRacesAutorisables)) {
            $retour .= "<select id='selectRaceAutorisable'><option value='0'>Aucune</option>";
            foreach ($listeRacesAutorisables as $race) {
                $retour .= "<option value='" . $race->id . "'>" . $race->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= Phalcon\Tag::SubmitButton(array("", 'id' => 'ajouterRaceJouable', 'class' => 'buttonPlus', 'title' => "Permet d'ajouter une race jouable dans un royaume."));
        } else {
            $retour = "<span class='resultatJouableVide'>Il n'y a plus de races jouables disponibles.</span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des races autorisables pour un royaume
     * @return Races[][]|Races[]|\Phalcon\Mvc\Model\ResultSetInterface[]
     */
    public function getListeRacesAutorisables() {
        $listeRaces = Races::find(['isDispoInscription = 1']);
        $listeAutorisables = array();
        foreach ($listeRaces as $race) {
            $presente = false;
            foreach ($this->racesJouable as $raceJouable) {
                if ($raceJouable->id == $race->id) {
                    $presente = true;
                }
            }
            if (!$presente) {
                $listeAutorisables[count($listeAutorisables)] = $race;
            }
        }
        return $listeAutorisables;
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
     * Permet de générer la liste des villes
     * du royaume
     * @param unknown $auth
     * @return string
     */
    public function genererListeVilles($auth) {
        $retour = "<div class='listeVille'>";
        $listeVilles = Villes::find(['idRoyaumeActuel = :idRoyaume:', 'bind' => ['idRoyaume' => $this->id], 'order' => 'nom']);
        if ($listeVilles != false && count($listeVilles) > 0) {
            foreach ($listeVilles as $ville) {
                $retour .= "<div class='ville'><span class='nomVille' style='color:" . $this->couleur . ";'>" . $ville->nom . "</span>";
                $retour .= "<div class='boutonsDetailVille'><input type='button' class='buttonConsulter ' onclick='afficherVille(" . $ville->id . ");' title='Permet d&#39;afficher le détail de la ville.'/>";
                if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_GERER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerVille(" . $ville->id . ");' title='Permet d&#39;accéder à l&#39;écran d&#39;édition de la ville.'/>";
                }
                $retour .= "</div></div>";
            }
        } else {
            $retour .= "<span class='listeVide'>Aucune ville n'est associée à ce royaume</span>";
        }
        $retour .= "</div>";
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
                $retour .= "<td><span class='imageListeBonus'>" . $bonus->genererImage("royaume", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><span class='nomListeBonus'>" . $bonus->nom . "</span></td>";
                $retour .= "<td><span class='descriptionBonus'>" . $bonus->genererDescription("royaume", $this->id, $bonus->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireBonus(\"royaume\"," . $this->id . "," . $bonus->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_FORMULAIRES_GERER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerBonus(" . $bonus->id . ",\"royaume\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerBonus(" . $bonus->id . ",\"royaume\"," . $this->id . "," . $bonus->listeParametres[0]->position . ");'/>";
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
        $listeBonus = AssocRoyaumesBonusParam::find(['idRoyaume = :idRoyaume:', 'bind' => ['idRoyaume' => $this->id], 'order' => 'position']);
        if ($listeBonus != false && count($listeBonus) > 0) {
            $bonus = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeBonus as $assocRoyaumeBonusParam) {
                $compteur++;
                if ($bonus == null || $bonus->id != $assocRoyaumeBonusParam->idBonus || $position != $assocRoyaumeBonusParam->position) {
                    if ($bonus != null) {
                        $this->listeBonus[count($this->listeBonus)] = $bonus;
                    }
                    $bonus = Bonus::findFirst(['id = :id:', 'bind' => ['id' => $assocRoyaumeBonusParam->idBonus]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocRoyaumeBonusParam->idParam]]);
                $parametre->valeur = $assocRoyaumeBonusParam->valeur;
                $parametre->position = $assocRoyaumeBonusParam->position;

                $bonus->listeParametres[count($bonus->listeParametres)] = $parametre;
                if ($compteur == count($listeBonus)) {
                    $this->listeBonus[count($this->listeBonus)] = $bonus;
                }
                $position = $assocRoyaumeBonusParam->position;
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
        $retour .= "[Etendard : " . $this->etendard . "], ";
        $retour .= "[idArticle : " . $this->idArticle . "], ";
        $retour .= "[Couleur : " . $this->couleur . "], ";
        $retour .= "[Disponible inscription : " . $this->isDispoInscription . "]";
        return $retour;
    }

}
