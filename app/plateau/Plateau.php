<?php
define('TAILLE_CASE_SUP', 21);

class Plateau {

    /** Date de l'affichage **/
    public $date;

    /** Coordonnées du plateau */
    public $xMin;
    public $yMin;
    public $xMax;
    public $yMax;
    public $idCarte;

    /** Notion d'affichage pour le plateau **/
    public $pxWidth;
    public $pxHeight;
    public Tableau $tableau;

    public $pxWidhtIso;
    public $pxHeightIso;

    /** Taille du tableau pour l'affichage **/
    public $ligneMax;
    public $colonneMax;

    /** Liste des terrains */
    public $listeTerrains = array();

    /**
     * Retourne la periode de la journée pour le plateau
     */
    public function getPeriode($onlyNight = false) {
        if ($this->date->faitJour()) {
            $pct = $this->date->getPctJour();
            if ($pct < 15 || $pct > 85) {
                $periode = 'crepuscule';
            } else {
                $periode = 'jour';
            }
        } else {
            $periode = 'nuit';
        }
        if ($periode == "crepuscule" && $onlyNight) {
            $periode = "jour";
        }
        return $periode;
    }

    /**
     * Permet d'intialiser le tableau
     * @param unknown $perso
     */
    public function initialize($perso) {
        $this->date = new Dates();

        //Récupération des coordonnées du personnage
        //TODo
        $x = 0;
        $y = 0;

        //TODO On adapte avec la vision du personnage
        $vision = 10;

        $this->xMin = $x - $vision;
        $this->yMin = $y - $vision;
        $this->xMax = $x + $vision;
        $this->yMax = $y + $vision;


        //TODO récupérer la carte sur laquelle est le personnage, par défaut, 4
        $this->idCarte = 7;

    }

    /**
     * Permet d'afficher le plateau
     * @param unknown $perso
     * @return string
     */
    public function affiche($perso) {
        $this->initTableau();
        $this->ajouteElements($perso);
        return $this->toHTML();
    }

    /**
     * Permet d'initialiser le tableau
     */
    public function initTableau() {
        //Modif iso
        $this->pxWidth = ($this->xMax - $this->xMin + 1) * Tableau::$largeurIso + Tableau::$largeurIso;
        $this->pxHeight = (($this->yMax - $this->yMin + 1) * Tableau::$hauteurIso) + Tableau::$hauteurIso + 10;
        $this->tableau = new Tableau(array('id' => 'grille'), array('width' => $this->pxWidth . 'px', 'height' => $this->pxHeight . 'px'));
    }

    /**
     * Permet d'ajouter des éléments sur le plateau
     * @param unknown $perso
     */
    public function ajouteElements($perso) {
        $this->ajouteCadre();
        $this->ajouteTerrainsIsometrique($perso);
    }

    /**
     * Permet d'ajouter le cadre
     */
    public function ajouteCadre() {
        $this->tableau->ajouteContenu(Phalcon\Tag::image(["/public/img/site/interface/carte_coin_hg.png", "class" => 'carte_coin_hg']));
        $this->tableau->ajouteContenu(Phalcon\Tag::image(["/public/img/site/interface/carte_coin_hd.png", "class" => 'carte_coin_hd']));
        $this->tableau->ajouteContenu(Phalcon\Tag::image(["/public/img/site/interface/carte_coin_bg.png", "class" => 'carte_coin_bg']));
        $this->tableau->ajouteContenu(Phalcon\Tag::image(["/public/img/site/interface/carte_coin_bd.png", "class" => 'carte_coin_bd']));
        $this->tableau->ajouteContenu('<div style="background-image:url(/public/img/site/interface/carte_bord_h.png)" class="carte_bord_n"></div>');
        $this->tableau->ajouteContenu('<div style="background-image:url(/public/img/site/interface/carte_bord_h.png)" class="carte_bord_s"></div>');
        $this->tableau->ajouteContenu('<div style="background-image:url(/public/img/site/interface/carte_bord_v.png)" class="carte_bord_o"></div>');
        $this->tableau->ajouteContenu('<div style="background-image:url(/public/img/site/interface/carte_bord_v.png)" class="carte_bord_e"></div>');
    }

    /**
     * ajoute le terrain dans le tableau
     *
     */
    public function ajouteTerrainsIsometrique($perso = false) {
        $plateau = &$this->tableau;
        $taille = TAILLE_CASE_SUP;
        $carte = Cartes::findById($this->idCarte);
        $data = array("xmin" => $this->xMin,
          "ymin" => $this->yMin,
          "xmax" => $this->xMax,
          "ymax" => $this->yMax,
          "table" => $carte->getTableMatrice(),
          "idCarte" => $carte->id
        );
        $segment = Segment::getGlobalInstance($data);
        if (!$segment->isInitialized($this->xMin, $this->xMax, $this->yMin, $this->yMax, $carte->id)) {
            $segment = new Segment($data);
        }
        //Remplissage du tableau
        $ligneMax = abs($segment->ymax - $segment->ymin) + 1;
        $colonneMax = abs($segment->xmax - $segment->xmin) + 1;
        $this->ligneMax = $ligneMax;
        $this->colonneMax = $colonneMax;
        for ($y = $this->yMax; $y >= $this->yMin; $y--) {
            for ($x = $this->xMin; $x <= $this->xMax; $x++) {
                //TODO décommenter pour le passage avec des perso
                /*if (!$perso || $perso->voitPoint($x, $y)) {
                 $per = '';
                 }else {
                 $per = 'sombre';
                 }*/
                $perception = "";

                if (isset($segment->matrice[$x][$y])) {
                    //On détermine la ligne où se trouve la case
                    $ligne = $ligneMax - abs($x - $this->xMin) - 1;
                    //On déterminer la colonne où se trouve la case
                    $colonne = $colonneMax - abs($y - $this->yMin) - 1;

                    $case = $segment->matrice[$x][$y];
                    $terrain = $case->terrain;
                    $classeCSS = $case->getCSSClassName($perception);
                    $divCase = new Div($case->generateTextures(), $classeCSS);


                    $xPx = $ligne * Tableau::$largeurIso + 30 + TAILLE_CASE_SUP;
                    if ($ligne % 2 == 1) {
                        $yPx = $colonne * Tableau::$hauteurIso + Tableau::$hauteurIso / 2 + 30 + TAILLE_CASE_SUP;
                    }
                    $yPx = $colonne * Tableau::$hauteurIso + 30 + TAILLE_CASE_SUP;

                    //Gestion des actions sur le terrains
                    $divCase->addOver('infosTerrain(' . $case->id . ',\'' . $case->table . '\',' . $xPx . ',' . $yPx . ')');
                    $divCase->addClic('afficherActionsTerrain(' . $case->id . ',\'' . $case->table . '\',' . $xPx . ',' . $yPx . ')');
                    $divCase->addOut('infosTerrain()');

                    $fond = new Div($divCase);
                    //$fond->addStyle(array('clip' => 'rect(' . $top . $right . $bottom . $left . ')'));
                    $plateau->ajouteCase('fond', $fond);
                    if (!isset($this->listeTerrains[$classeCSS])) {
                        if ($perception == "") {
                            $backgroundImage = 'url("' . $case->image . '");';
                        } else {
                            $backgroundImage = 'url("' . $case->image . '");';
                        }
                        $cssStyle = array("backgroundImage" => $backgroundImage,
                          "zIndex" => $terrain->zIndex
                        );
                        $this->listeTerrains[$classeCSS] = $cssStyle;
                    }
                }
            }
            $plateau->nl();
        }
    }

    /**
     * Transforme le tableau en html
     * @return string
     */
    public function toHTML() {
        return $this->tableau->toHTMLIso($this->colonneMax, $this->xMin);
    }

    /**
     * Retourne le javascript de generation des classes CSS correspondant
     * aux terrains pour l'affichage à partir de $this->liste_terrains
     */
    public function getStyleTerrains() {
        $script = '';
        if (isset($this->listeTerrains) && !empty($this->listeTerrains)) {
            foreach ($this->listeTerrains as $classeCSS => $cssStyle) {
                $style = "";
                $style .= "z-index: 6";//.$cssStyle['zIndex'].";";
                $style .= 'position: absolute;width:128px;height:64px;';
                if (false && $ie) {
                    $style .= "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'" . $cssStyle['backgroundImage'] . "\', sizingMethod=\'scale\');";
                    $style .= 'background-image: none;';
                } else {
                    $style .= "background-image: " . $cssStyle['backgroundImage'];
                }
                $script .= 'createCSSClass(\'' . '.' . $classeCSS . '\',\'' . $style . '\');' . NL;
            }
        }
        return $script;
    }
}