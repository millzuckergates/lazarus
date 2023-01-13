<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Talents extends \Phalcon\Mvc\Model {

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
     * @Column(column="idArbre", type="integer", length=11, nullable=false)
     */
    public $idArbre;

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
     * @var integer
     * @Column(column="isActif", type="integer", length=1, nullable=false)
     */
    public $isActif;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=80, nullable=false)
     */
    public $image;

    /**
     *
     * @var integer
     * @Column(column="niveau_max", type="integer", length=2, nullable=false)
     */
    public $niveau_max;

    /**
     *
     * @var integer
     * @Column(column="rang", type="integer", length=2, nullable=false)
     */
    public $rang;

    /**
     *
     * @var integer
     * @Column(column="position", type="integer", length=2, nullable=false)
     */
    public $position;

    public $listeEffets = array();
    public $listeContraintes = array();
    public $niveau;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("talents");

        //Init Jointure
        $this->hasMany('id', 'GenealogieTalents', 'idFils', ['alias' => 'genealogie_pere']);
        $this->hasManyToMany('id', 'GenealogieTalents', 'idFils', 'idPere', 'Talents', 'id', ['alias' => 'peres']);
        $this->hasOne('idArbre', 'ArbresTalent', 'id', ['alias' => 'arbre']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Talents[]|Talents|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Talents|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Charge la liste des effets
     * @param string $action
     */
    public function chargeListeEffets() {
        $this->listeEffets = array();
        $listeEffets = AssocTalentsEffetsParam::find(['idTalent = :idTalent:', 'bind' => ['idTalent' => $this->id], 'order' => 'position']);
        if ($listeEffets != false && count($listeEffets) > 0) {
            $effet = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeEffets as $assocTalentEffetParam) {
                $compteur++;
                if ($effet == null || $effet->id != $assocTalentEffetParam->idEffet || $position != $assocTalentEffetParam->position) {
                    if ($effet != null) {
                        $this->listeEffets[count($this->listeEffets)] = $effet;
                    }
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $assocTalentEffetParam->idEffet]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocTalentEffetParam->idParam]]);
                $parametre->valeur = $assocTalentEffetParam->valeur;
                $parametre->valeurMin = $assocTalentEffetParam->valeurMin;
                $parametre->valeurMax = $assocTalentEffetParam->valeurMax;
                $parametre->position = $assocTalentEffetParam->position;

                $effet->listeParametres[count($effet->listeParametres)] = $parametre;
                if ($compteur == count($listeEffets)) {
                    $this->listeEffets[count($this->listeEffets)] = $effet;
                }
                $position = $assocTalentEffetParam->position;
            }
        }
    }

    /**
     * Génère la liste des effets par action
     * @param unknown $auth
     * @param unknown $action
     * @return string
     */
    public function genererListeEffet($auth) {
        $retour = "";
        $this->chargeListeEffets();
        if (count($this->listeEffets) > 0) {
            $retour .= "<table class='tableListeEffets'>";
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
                $retour .= "<td><span class='descriptionEffet'>" . $effet->genererDescription("talent", $this->id, $effet->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireEffet(\"talent\"," . $this->id . "," . $effet->id . "," . $effet->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerEffet(" . $effet->id . ",\"talent\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerEffet(" . $effet->id . ",\"talent\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
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
     * Permet de générer la description de la liste des effets
     * @return string
     */
    public function genererDescriptionGeneraleEffet() {
        $this->chargeListeEffets();
        $retour = "";
        if (count($this->listeEffets) > 0) {
            foreach ($this->listeEffets as $effet) {
                $retour .= $effet->genererDescription("talent", $this->id, $effet->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Permet de générer la description de la liste des contraintes
     * @return string
     */
    public function genererDescriptionGeneraleContrainte() {
        $retour = "";
        $this->chargeListeContraintes();
        if (count($this->listeContraintes) > 0) {
            foreach ($this->listeContraintes as $contrainte) {
                $retour .= $contrainte->genererDescription("talent", $this->id, $contrainte->listeParametres[0]->position) . "</br>";
            }
        }
        return $retour;
    }

    /**
     * Méthode permettant de charger les contraintes
     */
    public function chargeListeContraintes() {
        $this->listeContraintes = array();

        $listeContraintes = AssocTalentsContraintesParam::find(['idTalent = :idTalent:', 'bind' => ['idTalent' => $this->id], 'order' => 'position,idParam']);
        if ($listeContraintes != false && count($listeContraintes) > 0) {
            $contrainte = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeContraintes as $assocTalentContrainteParam) {
                $compteur++;
                if ($contrainte == null || $contrainte->id != $assocTalentContrainteParam->idContrainte || $position != $assocTalentContrainteParam->position) {
                    if ($contrainte != null) {
                        $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                    }
                    $contrainte = Contraintes::findFirst(['id = :id:', 'bind' => ['id' => $assocTalentContrainteParam->idContrainte]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocTalentContrainteParam->idParam]]);
                $parametre->valeur = $assocTalentContrainteParam->valeur;
                $parametre->valeurMin = $assocTalentContrainteParam->valeurMin;
                $parametre->valeurMax = $assocTalentContrainteParam->valeurMax;
                $parametre->position = $assocTalentContrainteParam->position;

                $contrainte->listeParametres[count($contrainte->listeParametres)] = $parametre;
                if ($compteur == count($listeContraintes)) {
                    $this->listeContraintes[count($this->listeContraintes)] = $contrainte;
                }
                $position = $assocTalentContrainteParam->position;
            }
        }
    }

    /**
     * Permet de générer la liste des contraintes
     * @param unknown $auth
     * @return string
     */
    public function genererListeContrainte($auth) {
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
                $retour .= "<td><span class='descriptionContrainte'>" . $contrainte->genererDescription("talent", $this->id, $contrainte->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireContrainte(\"talent\"," . $this->id . "," . $contrainte->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GAMEPLAY_GESTION_TALENT_MODIFIER, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerContrainte(" . $contrainte->id . ",\"talent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerContrainte(" . $contrainte->id . ",\"talent\"," . $this->id . "," . $contrainte->listeParametres[0]->position . ");'/>";
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
     * Retourne l'affichage d'un talent
     * @param unknown $auth
     * @param unknown $tailleBlocpx
     * @param unknown $mode
     * @param unknown $idArbre
     * @param unknown $compteurTalent
     * @param string $simulation
     * @return string
     */
    public function genererDivTalent($auth, $tailleBlocpx, $mode, $idArbre, $compteurTalent, $simulation = true) {
        $perso = $auth['perso'];
        $posX = ($this->position - 1) * $tailleBlocpx + ($tailleBlocpx / 2 - ArbresTalent::TAILLE_TALENT);
        $posY = ($this->rang - 1) * ArbresTalent::HAUTEUR_LIGNE + ((ArbresTalent::HAUTEUR_LIGNE - ArbresTalent::TAILLE_TALENT) / 2);
        $style = "background-image:url(" . $this->image . ");";
        //On détermine la couleur
        $couleur = $this->determineCouleur($auth, $mode);
        if ($couleur == "gris") {
            $styleNumber = "color : rgb(176,176,176)";
        } else {
            if ($couleur == "vert") {
                $styleNumber = "color: rgb(46, 255, 0);";
            } else {
                if ($couleur == "jaune") {
                    $styleNumber = "color: rgb(255, 209, 0);";
                }
            }
        }
        if ($mode == "admin") {
            $ratio = "0/" . $this->niveau_max;
            if (!$simulation) {
                $retour = "<div class='imgTalentMiniature' id='talent_" . $this->id . "' style='" . $style . "' onclick='displayOptionTalent(" . $this->id . ", " . $idArbre . ");'>";
            } else {
                $retour = "<div class='imgTalentMiniature' id='talent_" . $this->id . "' style='" . $style . "' onMouseOver='afficherDescriptionTalent(" . $this->id . "," . $compteurTalent . ");' onMouseOut='hideDescriptionTalent(" . $this->id . ");'>";
            }
            $retour .= "<div id='imageNonActifTalent_" . $this->id . "'></div>";
            $retour .= "<input type='hidden' id='talent_actu_" . $this->id . "' value='0'/>";
            $retour .= "<input type='hidden' id='talent_max_" . $this->id . "' value='" . $this->niveau_max . "'/>";
            $retour .= "<div class='buttonnumber' id='number_" . $this->id . "' style='" . $styleNumber . "'>" . $ratio . "</div>";
            $retour .= Phalcon\Tag::image(['img/site/illustrations/talents/spacer.gif', "class" => 'talenticon', "style" => 'background-position: -224px 0px;', 'id' => 'spacer_' . $this->id]);
            $retour .= "</div>";
        }
        //TODO mode joueur
        return $retour;
    }

    /**
     * Retourne l'affichage d'un talent vide
     * @param unknown $auth
     * @param unknown $tailleBlocpx
     * @param unknown $mode
     * @param unknown $rang
     * @param unknown $position
     * @param unknown $idArbre
     * @param string $simulation
     * @return string
     */
    public static function genererDivTalentVide($auth, $tailleBlocpx, $mode, $rang, $position, $idArbre, $simulation = true) {
        $perso = $auth['perso'];
        $posX = ($position - 1) * $tailleBlocpx + ($tailleBlocpx / 2 - ArbresTalent::TAILLE_TALENT);
        $posY = ($rang - 1) * ArbresTalent::HAUTEUR_LIGNE + ((ArbresTalent::HAUTEUR_LIGNE - ArbresTalent::TAILLE_TALENT) / 2);
        //TODO mettre l'image avec un "+" vert
        if ($simulation) {
            $style = "background-image:url(public/img/site/vide.gif);";
        } else {
            $style = "background-image:url(public/img/site/illustrations/talents/plus.png);";
        }
        //On détermine la couleur
        $couleur = "gris";
        if ($mode == "admin") {
            if ($simulation) {
                $retour = "<div class='imgTalentMiniature' id='new_" . $rang . "_" . $position . "' style='" . $style . "'>";
                $retour .= Phalcon\Tag::image(['img/site/vide.gif', "class" => 'talenticonvide']);
            } else {
                $retour = "<div class='imgTalentMiniature' id='new_" . $rang . "_" . $position . "' style='" . $style . "' onclick='afficherCreationTalent(" . $rang . ", " . $position . ", " . $idArbre . ");' >";
                $retour .= Phalcon\Tag::image(['img/site/illustrations/talents/spacer.gif', "class" => 'talenticon', "style" => 'background-position: 0px 0px;']);
            }
            $retour .= "</div>";
        }
        //TODO mode joueur
        return $retour;
    }

    /**
     * Permet de déterminer la couleur d'un talent
     * @param unknown $auth
     * @param unknown $mode
     * @return string
     */
    public function determineCouleur($auth, $mode) {
        $dispo = $this->isDisponible($auth, $mode);
        //Mode Admin
        if ($mode == "admin") {
            if ($dispo) {
                if (isset($auth['simulationTalent']['listeTalents'][$this->id])) {
                    $max = $auth['simulationTalent']['listeTalents'][$this->id]['max'];
                    $actuel = $auth['simulationTalent']['listeTalents'][$this->id]['actuel'];
                    if ($max == $actuel) {
                        return "vert";
                    }
                }
                return "jaune";
            } else {
                return "gris";
            }
        }
        //Mode Joueur
        //TODO
    }

    /**
     * Détermine si un talent est disponible
     * @param unknown $auth
     * @param unknown $mode
     * @param unknown $cible
     * @return boolean
     */
    public function isDisponible($auth, $mode, $cible = null) {
        //Initialisation
        $retour = true;
        if ($this->isActif == false) {
            return false;
        }

        //On vérifie si les pères sont vérifiés
        if (isset($this->peres) && count($this->peres) > 0) {
            foreach ($this->peres as $pere) {
                if ($mode == "admin") {
                    $tabTalent = $auth['simulationTalent']['listeTalents'];
                    if (isset($tabTalent[$pere->id])) {
                        $pere = $tabTalent[$pere->id];
                        if ($pere['couleur'] != "vert") {
                            return false;
                        }
                    }
                }
            }
        }

        //On Charge les contraintes du talents
        $this->chargeListeContraintes();
        if (!empty($this->listeContraintes)) {
            foreach ($this->listeContraintes as $contrainte) {
                if ($retour) {
                    $retour = $contrainte->isVerif($mode, $auth, $cible);
                }
            }
        }
        return $retour;
    }

    /**
     * Retourne la liste des images pour un talent
     * @param unknown $imgDir
     * @return string
     */
    public static function genererListeImagesVide($imgDir) {
        $directory = $imgDir . 'site/illustrations/talents';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageTalent();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "defaut.png") {
                    $retour = $retour . "<option value='public/img/site/illustrations/talents/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/talents/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet de générer la liste des images pour un talent
     * @return string
     */
    public function genererListeImages() {
        $directory = $this->getDI()->get('config')->application->imgDir . 'site/illustrations/talents';
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
        $retour = $retour . '<select id="listeImage" onchange="changerImageTalent();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/site/illustrations/talents/" . $fichier) {
                    $retour = $retour . "<option value='public/img/site/illustrations/talents/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/site/illustrations/talents/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Permet d'afficher la généalogie d'un talent
     * @return string
     */
    public function genererGenealogie() {
        $retour = "";
        if (isset($this->peres) && count($this->peres) > 0) {
            foreach ($this->peres as $pere) {
                $retour .= "<div class='genealogieTalent'>";
                $retour .= "Dépendant du talent " . $pere->nom;
                $retour .= "<input type='button' class='buttonMoins' onclick='supprimerGenealogie(" . $this->id . "," . $pere->id . ");'/>";
                $retour .= "</div>";
            }
        }
        $retour .= "<div id='boutonAjouterPere'>";
        $retour .= $this->genererSelectPere();
        $retour .= "</div>";
        return $retour;
    }

    /** Génère la liste des pires disponibles
     * @return string
     */
    public function genererSelectPere() {
        $retour = "";
        $perePotentiel = Talents::find(["idArbre = :idArbre: AND rang <= :rang:", 'bind' => ['idArbre' => $this->idArbre, 'rang' => $this->rang]]);
        if ($perePotentiel != false && count($perePotentiel) > 0) {
            $retour .= "<label for='selectTalentPere'>Pere </label>";
            $retour .= "<div id='divSelectTalentPere'>";
            $retour .= "<select id='selectTalentPere'>";
            foreach ($perePotentiel as $pere) {
                $retour .= "<option value='" . $pere->id . "'>" . $pere->nom . "</option>";
            }
            $retour .= "</select>";
            $retour .= "<input type='button' class='buttonPlus' onclick='ajouterGenealogie();'/>";
            $retour .= "</div>";
        } else {
            $retour .= "Aucun talent n'est éligible.";
        }
        return $retour;
    }

    /**
     * Permet de supprimer un talent
     * @return string
     */
    public function supprimerTalent() {
        $action = "- Suppression du talent : " . $this->nom;
        //On clean la généalogie
        $listeGenealogie = GenealogieTalents::find(['idPere = :idPere: OR idFils = :idFils:', 'bind' => ['idPere' => $this->id, 'idFils' => $this->id]]);
        $listeGenealogie->delete();
        $this->delete();
        return $action;
    }

    /**
     * Permet d'afficher la description de l'effet dans l'arbre, au survol
     * @param unknown $auth
     * @param unknown $mode
     * @return string
     */
    public function genererDescriptionDansArbre($auth, $mode) {
        $retour = "<div class='divDescriptionTalentArbre'>";
        if ($mode == "admin" || $mode == "simulation") {
            $tabTalent = $auth['simulationTalent']['listeTalents'];
            $retour .= "<span class='descriptionTalentNom'><strong>" . $this->nom . "</strong></span><br/>";
            $retour .= "<span class='descriptionTalentRang' id='descriptionTalentRang'>Rang " . $tabTalent[$this->id]['actuel'] . "/" . $tabTalent[$this->id]['max'] . "</span><br/>";
            $retour .= "<span class='descriptionTalentContrainte'>" . $this->genererDescriptionContrainte($auth, $mode) . "</span>";
            if ($tabTalent[$this->id]['actuel'] != 0) {
                $retour .= "<span class='descriptionTalentEffet'>" . $this->genererDescriptionEffet($auth, $mode) . "</span>";
            }
            if ($tabTalent[$this->id]['actuel'] != $tabTalent[$this->id]['max'] && $tabTalent[$this->id]['max'] != 0) {
                $reponse = $this->genererDescriptionEffetSuivant($auth, $mode);
                if ($reponse == "") {
                    $retour .= $reponse;
                } else {
                    $retour .= "<br/>" . $reponse;
                }
            }
            $retour .= "<span class='descriptionTalentDescription'>" . str_replace("\n", "<br/>", $this->description) . "</span>";
        }
        $retour .= "</div>";
        return $retour;
    }

    /**
     * Génère une description pour les contraintes
     * @param unknown $auth
     * @param unknown $mode
     * @return string
     */
    public function genererDescriptionContrainte($auth, $mode) {
        $this->chargeListeContraintes();
        $retour = "";
        if ($mode == "admin" || $mode == "simulation") {
            $tabTalent = $auth['simulationTalent']['listeTalents'];
            if (!empty($this->listeContraintes)) {
                foreach ($this->listeContraintes as $contrainte) {
                    if (!$contrainte->isVerif($mode, $auth, null)) {
                        $retour .= "<span class='talentContrainteRouge'>" . $contrainte->genererDescriptionPourTalent($auth, $mode) . "</span><br/>";
                    } else {
                        $retour .= "<span class='talentContrainteVert'>" . $contrainte->genererDescriptionPourTalent($auth, $mode) . "</span><br/>";
                    }
                }
            }

            if (!empty($this->peres) && count($this->peres) > 0) {
                foreach ($this->peres as $pere) {
                    $tabPere = $tabTalent[$pere->id];
                    if ($tabPere['actuel'] == $tabPere['max']) {
                        $retour .= "<span class='talentContrainteVert'>Nécessite " . $tabPere['max'] . " points en " . $pere->nom . "</span><br/>";
                    } else {
                        $retour .= "<span class='talentContrainteRouge'>Nécessite " . $tabPere['max'] . " points en " . $pere->nom . "</span><br/>";
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Génère la description des effets
     * @param unknown $auth
     * @param unknown $mode
     * @return string
     */
    public function genererDescriptionEffet($auth, $mode) {
        $this->chargeListeEffets();
        $retour = "";
        if (isset($this->listeEffets) && count($this->listeEffets) > 0) {
            foreach ($this->listeEffets as $effet) {
                $retour .= $effet->genererDescriptionEvaluee($this, $auth, "talent", $mode, $effet->listeParametres[0]->position) . "<br/>";
            }
        }
        return $retour;
    }

    /**
     * Anticipe la description de l'effet suivant
     * @param unknown $auth
     * @param unknown $mode
     * @return string
     */
    public function genererDescriptionEffetSuivant($auth, $mode) {
        $this->chargeListeEffets();
        $retour = "<span class='rangSuivant'>Rang suivant :</span><br/>";
        if (isset($this->listeEffets) && count($this->listeEffets) > 0) {
            $retour .= "<span class='descriptionTalentEffet'>";
            foreach ($this->listeEffets as $effet) {
                $retour .= $effet->genererDescriptionEvaluee($this, $auth, "talent", $mode, $effet->listeParametres[0]->position, 1) . "<br/>";
            }
            $retour .= "</span>";
        } else {
            $retour = "";
        }
        return $retour;
    }

    /**
     * Permet de retourner un select pour les talents
     * en excluant ceux passés en paramètre
     * @param string $listeExclude
     * @return string
     */
    public static function getSelectTalent($listeExclude = false) {
        $listeTalents = Talents::find(['isActif = 1', 'order' => 'nom']);
        $retour = "<select id='listeSelectElement'><option value='0'>Choisissez un talent</option>";
        if ($listeTalents != false && count($listeTalents) > 0) {
            foreach ($listeTalents as $talent) {
                if ($listeExclude == false || ($listeExclude != false && !in_array($talent->id, $listeExclude))) {
                    $retour .= "<option value='" . $talent->id . "'>" . $talent->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
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
        $retour .= ", idArbre : " . $this->idArbre;
        $retour .= ",isActif : " . $this->isActif;
        $retour .= ", niveau Max : " . $this->niveau_max;
        $retour .= ", rang : " . $this->rang;
        $retour .= ", position : " . $this->position;
        return $retour;
    }
}
