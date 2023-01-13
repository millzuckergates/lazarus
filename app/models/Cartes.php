<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Cartes extends Model {

    //Constantes pour les types de cartes
    const TYPE_CARTE_EXTERIEUR = "Carte extérieur";
    const TYPE_CARTE_INTERIEUR = "Carte interieur";
    const TYPE_CARTE_CREATURE = "Carte créature";
    const TYPE_CARTE_MONDE_DES_MORTS = "Monde des morts";
    const TYPE_CARTE_VILLE = "Carte de villes";
    const TYPE_CARTE_DONJON = "Carte donjons";
    const TYPE_CARTE_BG = "Carte champ de bataille";
    const TYPE_CARTE_SANDBOX = "Carte Sand Box";

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
     * @Column(column="xRef", type="integer", length=11, nullable=false)
     */
    public $xRef;

    /**
     *
     * @var integer
     * @Column(column="yRef", type="integer", length=11, nullable=false)
     */
    public $yRef;

    /**
     *
     * @var integer
     * @Column(column="xMin", type="integer", length=11, nullable=false)
     */
    public $xMin;

    /**
     *
     * @var integer
     * @Column(column="yMin", type="integer", length=11, nullable=false)
     */
    public $yMin;

    /**
     *
     * @var integer
     * @Column(column="xMax", type="integer", length=11, nullable=false)
     */
    public $xMax;

    /**
     *
     * @var integer
     * @Column(column="yMax", type="integer", length=11, nullable=false)
     */
    public $yMax;

    /**
     *
     * @var string
     * @Column(column="nom", type="string", length=60, nullable=false)
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
     * @Column(column="type", type="string", length=80, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(column="saison", type="string", length=30, nullable=false)
     */
    public $saison;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=255, nullable=true)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(column="script", type="string", length=80, nullable=true)
     */
    public $script;

    /**
     *
     * @var integer
     * @Column(column="idCarteCreature", type="integer", length=11, nullable=true)
     */
    public $idCarteCreature;

    /**
     *
     * @var integer
     * @Column(column="idVille", type="integer", length=11, nullable=true)
     */
    public $idVille;

    /**
     *
     * @var integer
     * @Column(column="idReligion", type="integer", length=11, nullable=true)
     */
    public $idReligion;

    /**
     *
     * @var string
     * @Column(column="cartePJ", type="string", length=255, nullable=true)
     */
    public $cartePJ;

    /**
     *
     * @var integer
     * @Column(column="decouverte", type="integer", length=1, nullable=false)
     */
    public $decouverte;

    /**
     *
     * @var integer
     * @Column(column="isChargee", type="integer", length=1, nullable=false)
     */
    public $isChargee;

    public $listeEffets;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("cartes");
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param int $id
     * @return ?Cartes|ModelInterface
     */
    public static function findById(int $id): ?Cartes {
        if ($id >= 0) {
            return parent::findFirst([
              'cache' => ['key' => 'carte-Id-' . $id],
              'id = :id:',
              'bind' => ['id' => $id]
            ]);
        }
        return null;
    }

    /**
     * Permet de retourner l'ensemble des cartes correspondant à la recherche
     * @param unknown $nom
     * @param unknown $saison
     * @param unknown $type
     * @param unknown $auth
     * @return string
     */
    public static function getTableCartes($nom, $saison, $type, $auth) {
        $listeMap = Cartes::getListeCartes($nom, $saison, $type);
        if ($listeMap != false && count($listeMap) > 0) {
            $retour = "<table id='tableCartesResult' class='tableCarteSaison'>";
            echo "<tr>
					<th width='20%'></th>
					<th width='20%'>Nom</th>
					<th width='10%'>Type</th>";
            if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_MODIFIER, $auth['autorisations'])) {
                echo "<th width='38%'>Description</th><th width='12%'>Actions</th></tr>";
            } else {
                echo "<th width='50%'>Description</th></tr>";
            }
            $i = 0;
            foreach ($listeMap as $carte) {
                $ligne = "ligne_" . $i % 2;
                echo "<tr class='" . $ligne . "'>";
                echo "<td onclick='afficherDetailCarte(" . $carte->id . ");'>" . Phalcon\Tag::image([$carte->image, "class" => 'iconeCarteImage']) . "</td>";
                echo "<td onclick='afficherDetailCarte(" . $carte->id . ");'><span class='libelleListeCarte'>" . $carte->nom . "</span></td>";
                echo "<td onclick='afficherDetailCarte(" . $carte->id . ");'><span class='libelleListeCarte'>" . $carte->type . "</span></td>";
                echo "<td onclick='afficherDetailCarte(" . $carte->id . ");'><span class='libelleListeCarte'>" . $carte->resumeDescription() . "</span></td>";
                if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_CARTES_MODIFIER, $auth['autorisations'])) {
                    echo "<td>
							<input type='button' class='buttonMoins' onclick='boxDeleteMap(" . $carte->id . ");'/>
							<input type='button' class='buttonActualiser' onclick='editerMap(" . $carte->id . ");'/>
						</td>";
                }
                echo "</tr>";
                $i++;
            }
            $retour .= "</table>";
        } else {
            $retour = "<span id='resultatVideCarte'>Il n'y a aucune carte existante.</span>";
        }
        return $retour;

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
     * Retourne une liste de cartes
     * @param unknown $nom
     * @param unknown $saison
     * @param unknown $type
     * @return Cartes[]|Cartes|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeCartes($nom, $saison, $type) {
        if ($nom == null && $type == null) {
            return Cartes::find(['saison = :saison:', 'bind' => ['saison' => $saison]]);
        } else {
            if ($nom != null && $type == null) {
                return Cartes::find(['saison = :saison: AND nom = :nom:', 'bind' => ['saison' => $saison, 'nom' => $nom]]);
            } else {
                if ($nom == null && $type != null) {
                    return Cartes::find(['saison = :saison: AND type = :type:', 'bind' => ['saison' => $saison, 'type' => $type]]);
                } else {
                    return Cartes::find(['saison = :saison: AND type = :type: AND nom = :nom:', 'bind' => ['saison' => $saison, 'type' => $type, 'nom' => $nom]]);
                }
            }
        }
        return array();
    }

    /**
     * Méthode pour générer la liste des types disponibles
     * pour une carte
     * @param unknown $typeSelect
     * @param unknown $idSelect
     * @return string
     */
    public static function genererSelectTypeCarte($typeSelect, $idSelect) {
        $listeType = Cartes::getListeTypes();
        $retour = "<select id='" . $idSelect . "' onchange='afficherDivTypeCarte();'><option value='Aucun'>Aucun</option>";
        foreach ($listeType as $type) {
            if ($type == $typeSelect) {
                $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
            } else {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Genere la liste des types disponibles pour une carte
     * @return string[]
     */
    public static function getListeTypes() {
        $retour = array();
        $retour[count($retour)] = Cartes::TYPE_CARTE_BG;
        $retour[count($retour)] = Cartes::TYPE_CARTE_CREATURE;
        $retour[count($retour)] = Cartes::TYPE_CARTE_DONJON;
        $retour[count($retour)] = Cartes::TYPE_CARTE_EXTERIEUR;
        $retour[count($retour)] = Cartes::TYPE_CARTE_INTERIEUR;
        $retour[count($retour)] = Cartes::TYPE_CARTE_MONDE_DES_MORTS;
        $retour[count($retour)] = Cartes::TYPE_CARTE_SANDBOX;
        $retour[count($retour)] = Cartes::TYPE_CARTE_VILLE;
        return $retour;
    }

    /**
     * Génère un bloc spécifique pour les maps
     * @param unknown $mode
     * @return string
     */
    public function genererBlocSpecifiqueTypeCarte($mode) {
        $retour = "";
        $type = $this->type;
        $listeMapCreature = Cartes::find(['type = :type:', 'bind' => ['type' => Cartes::TYPE_CARTE_CREATURE]]);
        if ($type == Cartes::TYPE_CARTE_EXTERIEUR || $type == Cartes::TYPE_CARTE_INTERIEUR) {
            if ($mode == "edition") {
                // On ajoute la possibilité d'y ajouter une carte de créature
                $retour .= "<label for='selectCarteCreature'>Carte créature </label>";
                $retour .= "<select id='selectCarteCreature' onChange='afficherCarteCreature();'><option value='0'>Aucune</option>";
                if (count($listeMapCreature) > 0) {
                    foreach ($listeMapCreature as $map) {
                        if (isset($this->carteCreature) && $this->carteCreature != null && $this->carteCreature->id == $map->id) {
                            $retour .= "<option value='" . $map->id . "' selected>" . $map->nom . "</option>";
                        } else {
                            $retour .= "<option value='" . $map->id . "'>" . $map->nom . "</option>";
                        }
                    }
                }
                $retour .= "</select>";
                $retour .= "<span id='imageCarteCreature'>";
                if (isset($this->carteCreature) && $this->carteCreature != null) {
                    $retour .= Phalcon\Tag::image([$this->carteCreature->image, "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
                } else {
                    $retour .= Phalcon\Tag::image(['public/img/cartes/default.jpg', "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
                }
                $retour .= "</span>";
            } else {
                if ($mode == "consultation") {
                    $retour .= "<label for='selectCarteCreature'>Carte créature </label>";
                    if (isset($carte->carteCreature) && $carte->carteCreature != null) {
                        $retour .= "<span id='selectCarteCreature'>" . $carte->carteCreature->nom . "</span>";
                        $retour .= "<span id='imageCarteCreature'>";
                        $retour .= Phalcon\Tag::image([$carte->carteCreature->image, "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
                        $retour .= "</span>";
                    } else {
                        $retour .= "<span id='selectCarteCreature'>Aucune</span>";
                    }
                }
            }
        } else {
            if ($type == Cartes::TYPE_CARTE_VILLE) {
                if ($mode == "edition") {
                    //TODO faire les villes
                    /*$listeVille = Villes::getListeVille();
                     $listeVille = array();
                     // On ajoute la possibilité d'y rattacher une ville (obligatoire)
                     $retour .= "<label for='selectCarteVille'>Ville </label>";
                     $retour .= "<select id='selectCarteVille'>";
                     if(count($listeVille) > 0){
                     foreach($listeVille as $ville){
                     if(isset($carte->idVille) && $carte->idVille != null && $carte->idVille == $ville->id){
                     $retour .= "<option value='".$ville->id."' selected>".$ville->nom."</option>";
                     }else{
                     $retour .= "<option value='".$ville->id."'>".$ville->nom."</option>";
                     }
                     }
                     }
                     $retour .= "</select>";*/
                } else {
                    if ($mode == "consultation") {
                        $retour .= "<label for='selectCarteVille'>Ville </label>";
                        if (isset($this->idVille) && $this->idVille != null) {
                            $ville = Villes::findFirst(['id = :id:', 'bind' => ['id' => $this->idVille]]);
                            $retour .= "<span id='selectCarteVille'>" . $ville->nom . "</span>";
                        } else {
                            $retour .= "<span id='selectCarteVille'>Aucune</span>";
                        }
                    }
                }
            } else {
                if ($type == Cartes::TYPE_CARTE_MONDE_DES_MORTS) {
                    if ($mode == "edition") {
                        //On ajoute le religion associé (obligatoire)
                        $listeReligions = Religions::find();
                        $retour .= "<label for='selectCarteReligion'>Religion </label>";
                        $retour .= "<select id='selectCarteReligion'>";
                        if (count($listeReligions) > 0) {
                            foreach ($listeReligions as $religion) {
                                if (isset($this->idReligion) && $this->idReligion != null && $this->idReligion == $religion->id) {
                                    $retour .= "<option value='" . $religion->id . "' selected>" . $religion->nom . "</option>";
                                } else {
                                    $retour .= "<option value='" . $religion->id . "'>" . $religion->nom . "</option>";
                                }
                            }
                        }
                        $retour .= "</select>";
                    } else {
                        if ($mode == "consultation") {
                            $retour .= "<label for='selectCarteReligion'>Religion </label>";
                            if (isset($this->idReligion) && $this->idReligion != null) {
                                $religion = Religion::findFirst(['id = :id:', 'bind' => ['id' => $this->idReligion]]);
                                $retour .= "<span id='selectCarteReligion'>" . $religion->nom . "</span>";
                            } else {
                                $retour .= "<span id='selectCarteReligion'>Aucune</span>";
                            }
                        }
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Génère un bloc spécifique pour les types de cartes
     * @param unknown $type
     * @return string
     */
    public static function genererBlocSpecifiqueTypeCarteVide($type) {
        $retour = "";
        $listeMapCreature = Cartes::find(['type = :type:', 'bind' => ['type' => Cartes::TYPE_CARTE_CREATURE]]);
        if ($type == Cartes::TYPE_CARTE_EXTERIEUR || $type == Cartes::TYPE_CARTE_INTERIEUR) {
            // On ajoute la possibilité d'y ajouter une carte de créature
            $retour .= "<label for='selectCarteCreature'>Carte créature </label>";
            $retour .= "<select id='selectCarteCreature' onChange='afficherCarteCreature();'><option value='0'>Aucune</option>";
            if (count($listeMapCreature) > 0) {
                foreach ($listeMapCreature as $map) {
                    $retour .= "<option value='" . $map->id . "'>" . $map->nom . "</option>";
                }
            }
            $retour .= "</select>";
            $retour .= "<span id='imageCarteCreature'>";
            $retour .= Phalcon\Tag::image(['public/img/cartes/default.jpg', "class" => 'imgCarteCreature', "id" => "imgCarteCreature"]);
            $retour .= "</span>";
        } else {
            if ($type == Cartes::TYPE_CARTE_VILLE) {
                //TODO faire les villes
                /*$listeVille = Villes::getListeVille();
                 $listeVille = array();
                 // On ajoute la possibilité d'y rattacher une ville (obligatoire)
                 $retour .= "<label for='selectCarteVille'>Ville </label>";
                 $retour .= "<select id='selectCarteVille'>";
                 if(count($listeVille) > 0){
                 foreach($listeVille as $ville){
                 if(isset($carte->idVille) && $carte->idVille != null && $carte->idVille == $ville->id){
                 $retour .= "<option value='".$ville->id."' selected>".$ville->nom."</option>";
                 }else{
                 $retour .= "<option value='".$ville->id."'>".$ville->nom."</option>";
                 }
                 }
                 }
                 $retour .= "</select>";*/
            } else {
                if ($type == Cartes::TYPE_CARTE_MONDE_DES_MORTS) {
                    //On ajoute le religion associé (obligatoire)
                    $listeReligions = Religions::find();
                    $retour .= "<label for='selectCarteReligion'>Religion </label>";
                    $retour .= "<select id='selectCarteReligion'>";
                    if (count($listeReligions) > 0) {
                        foreach ($listeReligions as $religion) {
                            $retour .= "<option value='" . $religion->id . "'>" . $religion->nom . "</option>";
                        }
                    }
                    $retour .= "</select>";
                }
            }
        }
        return $retour;
    }

    /**
     * Génère la liste des images pour les cartes
     * @return string
     */
    public function genererListeImageCarte() {
        $directory = $this->getDI()->get('config')->application->carteDir;
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
        $retour = $retour . '<select id="listeImage" onchange="chargerImageCarte();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/cartes/" . $fichier) {
                    $retour = $retour . "<option value='public/img/cartes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/cartes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }

    /**
     * Retourne la liste des images déjà utilisées
     * par les cartes
     * @return String[]
     */
    public function getListeImageUtilisees() {
        $phql = "SELECT DISTINCT (image) as image FROM Cartes ORDER BY image";
        $rows = $this->modelsManager->executeQuery($phql);
        $listeImage = array();
        if (count($rows) > 0 && $rows != false) {
            foreach ($rows as $image) {
                $listeImage[count($listeImage)] = $image;
            }
        }
        return $listeImage;
    }

    /**
     * Génère la liste des images qui seront affichées aux joueurs
     * @return string
     */
    public function genererListeImageCartePJ() {
        $directory = $this->getDI()->get('config')->application->cartePJDir;
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
        $retour = $retour . '<select id="listeImagePJ" onchange="chargerImageCartePJ();">';
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($this->image != null && $this->image == "public/img/cartesPJ/" . $fichier) {
                    $retour = $retour . "<option value='public/img/cartesPJ/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour = $retour . "<option value='public/img/cartesPJ/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des images pour les cartes
     * @param unknown $directory
     * @return string
     */
    public static function genererListeImageCarteVide($directory) {
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
        $retour .= "<select id='listeImage' onchange='chargerImageCarte();'>";
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour .= "<option value='public/img/cartes/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/cartes/" . $fichier . "'>" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Génère la liste des images correspondant aux cartes pour les joueurs
     * @param unknown $directory
     * @return string
     */
    public static function genererListeImageCartePJVide($directory) {
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
        $retour .= "<select id='listeImagePJ' onchange='chargerImageCartePJ();'>";
        if (!empty($listeFichier)) {
            for ($i = 0; $i < count($listeFichier); $i++) {
                $fichier = $listeFichier[$i];
                if ($fichier == "default.jpg") {
                    $retour .= "<option value='public/img/cartesPJ/" . $fichier . "' selected>" . $fichier . "</option>";
                } else {
                    $retour .= "<option value='public/img/cartesPJ/" . $fichier . ">" . $fichier . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Permet de nettoyer une matrice de ses éléments
     */
    public function cleanMatrice() {
        if ($this->type == Cartes::TYPE_CARTE_EXTERIEUR) {
            $result = Matricesexterieur::find(['idCarte = :idCarte:', 'bind' => ['idCarte' => $this->id]]);
        } else {
            if ($this->type == Cartes::TYPE_CARTE_INTERIEUR) {
                $result = Matricesinterieur::find(['idCarte = :idCarte:', 'bind' => ['idCarte' => $this->id]]);
            } else {
                if ($this->type == Cartes::TYPE_CARTE_VILLE) {
                    $result = Matricesville::find(['idCarte = :idCarte:', 'bind' => ['idCarte' => $this->id]]);
                } else {
                    if ($this->type == Cartes::TYPE_CARTE_MONDE_DES_MORTS) {
                        $result = Matricesmdm::find(['idCarte = :idCarte:', 'bind' => ['idCarte' => $this->id]]);
                    }
                }
            }
        }
        if ($result != false) {
            $result->delete();
        }
    }

    /**
     * Permet de vérifier si un emplacement est disponible
     * @param unknown $type
     * @param unknown $xmin
     * @param unknown $xmax
     * @param unknown $ymin
     * @param unknown $ymax
     * @return boolean
     */
    public static function checkLocalisation($type, $xmin, $xmax, $ymin, $ymax) {
        if ($type == Cartes::TYPE_CARTE_BG) {
            //TODO
        } else {
            if ($type == Cartes::TYPE_CARTE_CREATURE) {
                //TODO
            } else {
                if ($type == Cartes::TYPE_CARTE_DONJON) {
                    //TODo
                } else {
                    if ($type == Cartes::TYPE_CARTE_EXTERIEUR) {
                        $total = Matricesexterieur::count(['x >= :xmin: AND x <= :xmax: AND y >= :ymin: AND y <= :ymax:', 'bind' => ['xmin' => $xmin, 'xmax' => $xmax, 'ymin' => $ymin, 'ymax' => $ymax]]);
                    } else {
                        if ($type == Cartes::TYPE_CARTE_INTERIEUR) {
                            $total = Matricesinterieur::count(['x >= :xmin: AND x <= :xmax: AND y >= :ymin: AND y <= :ymax:', 'bind' => ['xmin' => $xmin, 'xmax' => $xmax, 'ymin' => $ymin, 'ymax' => $ymax]]);
                        } else {
                            if ($type == Cartes::TYPE_CARTE_MONDE_DES_MORTS) {
                                $total = Matricesmdm::count(['x >= :xmin: AND x <= :xmax: AND y >= :ymin: AND y <= :ymax:', 'bind' => ['xmin' => $xmin, 'xmax' => $xmax, 'ymin' => $ymin, 'ymax' => $ymax]]);
                            } else {
                                if ($type == Cartes::TYPE_CARTE_SANDBOX) {
                                    //TODO
                                } else {
                                    if ($type == Cartes::TYPE_CARTE_VILLE) {
                                        $total = Matricesville::count(['x >= :xmin: AND x <= :xmax: AND y >= :ymin: AND y <= :ymax:', 'bind' => ['xmin' => $xmin, 'xmax' => $xmax, 'ymin' => $ymin, 'ymax' => $ymax]]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //Vérification de la place disponible
        if ($total > 0) {
            return false;
        }
        return true;
    }


    /**
     * Retourne la table correspondant à la matrice
     * @return string|NULL
     */
    public function getTableMatrice() {
        switch ($this->type) {
            case Cartes::TYPE_CARTE_EXTERIEUR:
                return "Matricesexterieur";
            case Cartes::TYPE_CARTE_INTERIEUR:
                return "Matricesinterieur";
            case Cartes::TYPE_CARTE_VILLE:
                return "Matricesville";
            case Cartes::TYPE_CARTE_MONDE_DES_MORTS:
                return "Matricesmdm";
            default:
                return null;
        }
    }

    /**
     * Permet de construire une matrice à partir d'une image
     * @param bool|int $nbDone
     * @return string
     */
    public function buildMatrice($nbDone = false) {
        $fileImage = str_replace('//', '/', BASE_PATH . "/" . $this->image);
        $fileImage = str_replace('\\', '/', $fileImage);
        $image = new Images($fileImage);
        if ($image == null) {
            return "errorChargementImage";
        }

        $table = $this->getTableMatrice();
        if ($table == null) {
            return "errorChargementTable";
        }

        $requete = "INSERT INTO " . $table . " (x,y,idCarte,idTerrain,image) VALUES (:x:, :y:,:idCarte:,:idTerrain:,:image:)";
        //Pour chaque case
        $numCase = 0;
        for ($x = $this->xMin; $x <= $this->xMax; $x++) {
            for ($y = $this->yMin; $y <= $this->yMax; $y++) {
                if ($nbDone === false || ($numCase >= $nbDone && $numCase < $nbDone + 1000)) {
                    $query = $this->modelsManager->createQuery($requete);
                    $px = $x;
                    $py = $y;
                    $this->coordsToPx($px, $py);
                    $couleur = $image->getCouleur($px, $py);
                    if ($couleur != "error") {
                        $couleur = "#" . $couleur;
                        $terrain = Terrains::findByCouleur($couleur);
                        if (!$terrain) {
                            $terrain = Terrains::findByCouleur('#000000');
                        }
                    } else {
                        $terrain = Terrains::findByCouleur('#000000');
                    }
                    $query->execute(['x' => $x, 'y' => $y, 'idCarte' => $this->id, 'idTerrain' => $terrain->id, 'image' => $terrain->getRandomImageTerrain()]);
                }
                $numCase++;
            }
        }
        return "success";
    }

    /**
     * Transforme les pixels en coordonnées
     * @param unknown $x
     * @param unknown $y
     */
    public function coordsToPx(&$x, &$y) {
        $x -= $this->xMin;
        $y = $this->yMax - $y;
    }

    /**
     * Charge la liste des effets
     * @param string $action
     */
    public function chargeListeEffets($action = false) {
        $this->listeEffets = array();
        $listeEffets = AssocCartesEffetsParam::find(['idCarte = :idCarte: AND action = :action:', 'bind' => ['idCarte' => $this->id, 'action' => $action], 'order' => 'position']);
        if ($listeEffets != false && count($listeEffets) > 0) {
            $effet = null;
            $compteur = 0;
            $position = 0;
            foreach ($listeEffets as $assocCarteEffetParam) {
                $compteur++;
                if ($effet == null || $effet->id != $assocCarteEffetParam->idEffet || $position != $assocCarteEffetParam->position) {
                    if ($effet != null) {
                        $this->listeEffets[count($this->listeEffets)] = $effet;
                    }
                    $effet = Effets::findFirst(['id = :id:', 'bind' => ['id' => $assocCarteEffetParam->idEffet]]);
                }
                //On récupère le paramètre en cours
                $parametre = Parametres::findFirst(['id = :id:', 'bind' => ['id' => $assocCarteEffetParam->idParam]]);
                $parametre->valeur = $assocCarteEffetParam->valeur;
                $parametre->valeurMin = $assocCarteEffetParam->valeurMin;
                $parametre->valeurMax = $assocCarteEffetParam->valeurMax;
                $parametre->position = $assocCarteEffetParam->position;
                $parametre->action = $assocCarteEffetParam->action;

                $effet->listeParametres[count($effet->listeParametres)] = $parametre;
                if ($compteur == count($listeEffets)) {
                    $this->listeEffets[count($this->listeEffets)] = $effet;
                }
                $position = $assocCarteEffetParam->position;
            }
        }
    }

    /**
     * Génère la liste des effets par action
     * @param unknown $auth
     * @param unknown $action
     * @return string
     */
    public function genererListeEffetAction($auth, $action) {
        $retour = "";
        $this->chargeListeEffets($action);
        if (count($this->listeEffets) > 0) {
            $retour = "<div class='divListeEffetAction'>";
            $retour .= "<div class='enteteListeEffetAction'><span class='introListeEffetAction'>Effets se délenchant " . $action . "</span></div>";
            $retour .= "<div class='divTableListeEffets'>";
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
                $retour .= "<td><span class='descriptionEffet'>" . $effet->genererDescription("carte", $this->id, $effet->listeParametres[0]->position) . "</span></td>";
                $retour .= "<td><input type='button' class='buttonConsulter' onclick='afficherFormulaireEffet(\"carte\"," . $this->id . "," . $effet->id . "," . $effet->listeParametres[0]->position . ");'/>";
                if (Autorisations::hasAutorisation(Autorisations::GESTION_GAMEPLAY_TERRAINS, $auth['autorisations'])) {
                    $retour .= "<input type='button' class='buttonActualiser' onclick='editerEffet(" . $effet->id . ",\"carte\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                    $retour .= "<input type='button' class='buttonMoins' onclick='boxRetirerEffet(" . $effet->id . ",\"carte\"," . $this->id . "," . $effet->listeParametres[0]->position . ");'/>";
                }
                $retour .= "</td>";

                $retour .= "</tr>";
                $ligne++;
            }
            $retour .= "</table>";
            $retour .= "</div>";
        }
        return $retour;
    }

    /**
     * Permet de générer la description de la liste des effets
     * @return string
     */
    public function genererDescriptionGeneraleEffet() {
        $listeAction = Effets::getListeActionEffet();
        $retour = "";
        foreach ($listeAction as $action) {
            $this->chargeListeEffets($action);
            if (count($this->listeEffets) > 0) {
                foreach ($this->listeEffets as $effet) {
                    $retour .= $effet->genererDescription("carte", $this->id, $effet->listeParametres[0]->position) . "</br>";
                }
            }
        }
        return $retour;
    }

    /**
     * Transforme l'objet en string
     * @return string
     */
    public function toString() {
        $retour = "id = " . $this->id;
        $retour .= ", nom = " . $this->nom;
        $retour .= ", xref = " . $this->xRef . ", yref = " . $this->yRef;
        $retour .= ", xMin = " . $this->xMin . ", yMin = " . $this->yMin;
        $retour .= ", xMax = " . $this->xMax . ", yMax = " . $this->yMax;
        $retour .= ", description = " . $this->description;
        $retour .= ", type = " . $this->type;
        $retour .= ", saison = " . $this->saison;
        if (isset($this->image)) {
            $retour .= ", image = " . $this->image;
        }
        if (isset($this->cartePJ)) {
            $retour .= ", cartePJ = " . $this->cartePJ;
        }
        if (isset($this->carteCreature)) {
            $retour .= ", carte Créature = " . $this->carteCreature;
        }
        if (isset($this->decouverte)) {
            $retour .= ", decouverte = " . $this->decouverte;
        }
        if (isset($this->idVille)) {
            $retour .= ", idVille = " . $this->idVille;
        }
        if (isset($this->idReligion)) {
            $retour .= ", idReligion = " . $this->idReligion;
        }
        return $retour;
    }

    /**
     * Permet de construire une matrice à partir d'une image
     * @param $modif object
     * @return void
     */
    public function updateCase(object $modif): void {
        $aux = explode('_', $modif->idTerrain);
        $idTerrain = $aux[0];
        $terrain = Terrains::findById($idTerrain);
        $requete = "UPDATE " . $this->getTableMatrice() . " SET idTerrain = :idTerrain:, image=:image: WHERE x=:x: AND y=:y: AND idCarte=:idCarte:";
        $query = $this->modelsManager->createQuery($requete);

        $imageUrl = $terrain->getImageTerrain($aux[1]);
        $query->execute([
          'x' => $modif->x,
          'y' => $modif->y,
          'idCarte' => $this->id,
          'idTerrain' => $terrain->id,
          'image' => $imageUrl
        ]);
        $requete = "SELECT id FROM " . $this->getTableMatrice() . " WHERE x=:x: AND y=:y: AND idCarte=:idCarte:";
        $query = $this->modelsManager->createQuery($requete);
        $idCase = $query->execute([
          'x' => $modif->x,
          'y' => $modif->y,
          'idCarte' => $this->id
        ]);
        foreach ($modif->idTextures as $numTexture => $idTexture) {
            $texture = AssocCaseTextures::getTextureFromCaseAndCarteAndNumtexture($idCase[0]->id, $this->getTableMatrice(), $numTexture);
            if (!$texture) {
                $texture = new AssocCaseTextures();
                $texture->idCase = $idCase[0]->id;
                $texture->typeCarte = $this->getTableMatrice();
                $texture->numTexture = $numTexture;
            }
            $texture->idTexture = $idTexture;
            $texture->save();
        }
    }
}
