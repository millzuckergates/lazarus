<?php

use Phalcon\Session\ManagerInterface;

class PlateauEdit extends Plateau {
    public int $LOAD_BY = 1000;

    /**
     * Permet d'intialiser le tableau
     * @param Cartes $carte
     */
    public function initializeEdit(Cartes $carte) {
        $this->date = new Dates();
        $this->xMin = $carte->xMin;
        $this->yMin = $carte->yMin;
        $this->xMax = $carte->xMax;
        $this->yMax = $carte->yMax;
        $this->idCarte = $carte->id;
    }

    /**
     * Permet d'initialiser le tableau
     */
    public function initTableau() {
        //Modif iso
        $this->pxWidth = ($this->xMax - $this->xMin + 1) * Tableau::$largeurIso + Tableau::$largeurIso;
        $this->pxHeight = (($this->yMax - $this->yMin + 1) * Tableau::$hauteurIso) + Tableau::$hauteurIso + 10;
        $this->tableau = new Tableau(array('id' => 'grille', 'onclick' => "updateCase(event)"), array('width' => $this->pxWidth . 'px', 'height' => $this->pxHeight . 'px'));
    }

    /**
     * Génère le html du tableau partiel
     * @param int $nbDone
     * @return string
     */
    public function htmlTerrainsIsometriqueMorceaux(int $nbDone = 0): string {
        $str = "";
        $carte = Cartes::findById($this->idCarte);
        $xrange = $this->xMax - $this->xMin + 1;
        $yMax = $this->yMax - floor($nbDone / $xrange);
        $yMin = $yMax - floor($this->LOAD_BY / $xrange) - 1;
        $data = array("xmin" => $this->xMin,
            "ymin" => $yMin,
            "xmax" => $this->xMax,
            "ymax" => $yMax,
            "table" => $carte->getTableMatrice(),
            "idCarte" => $carte->id
        );
        $segment = new Segment($data);
        //Remplissage du tableau
        $constTop = 35 + $this->yMax * Tableau::$hauteurIso;
        $constLeft = $this->yMax * Tableau::$largeurIso - 30;
        $numCase = 0;
        for ($y = $this->yMax; $y >= $this->yMin; $y--) {
            for ($x = $this->xMin; $x <= $this->xMax; $x++) {
                if ($numCase >= $nbDone && $numCase < $nbDone + $this->LOAD_BY) {
                    if (isset($segment->matrice[$x][$y])) {
                        $case = $segment->matrice[$x][$y];
                        $classeCSS = $case->getCSSClassName("");
                        $divCase = new Div($case->generateTextures(), $classeCSS);

                        $fond = new Div($divCase, false, false, false, "x" . $x . "y" . $y);
                        $top = ($x - $y) * Tableau::$hauteurIso / 2 + $constTop;
                        $left = ($x + $y) * Tableau::$largeurIso / 2 + $constLeft;
                        $fond->addStyle(array('position' => 'absolute', 'top' => $top . 'px', 'left' => $left . 'px'));
                        $str .= $fond->toHTML();
                        if (!isset($this->listeTerrains[$classeCSS])) {
                            $this->listeTerrains[$classeCSS] = array("backgroundImage" => 'url("' . $case->image . '");',
                              "zIndex" => $case->terrain->zIndex
                            );
                        }
                    }
                }
                $numCase++;
            }
        }
        return $str;
    }
}