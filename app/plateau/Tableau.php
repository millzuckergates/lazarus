<?php

/**
 * classe tableau
 *
 * Permet de construire, modifier puis retourner un tableau HTML
 *
 */
class Tableau {
    /**
     * Paramètres du tableau
     * @var array
     */
    public $liste_params = array();

    /**
     * Attribut de style du tableau
     * @var array
     */
    public $style = array();

    /**
     * Cases
     * @var array
     */
    public $cases = array();

    /**
     * Contenu
     * @var array
     */
    public $contenu = array();

    /**
     * Ligne courante
     * @var integer
     */
    public $ligne = 0;

    /**
     * Taille de base des cases
     * @var integer
     */
    static $taille_case = 60;

    static $largeurIso = 128;
    static $hauteurIso = 64;

    /**
     * Initialise le tableau avec les paramètres et le style souhaité
     *
     * @param array $liste_params Liste des paramètres du tableau
     * @param array $style Liste des attribut de style du tableau
     */
    public function __construct($liste_params = array(), $style = array()) {
        $this->liste_params = $liste_params;
        $this->style = $style;
    }

    /**
     * Crée une nouvelle ligne
     */
    public function nl() {
        $this->ligne++;
        $this->cases[$this->ligne] = array();
    }

    /**
     * Ajoute une case sur la ligne courante
     *
     * @param array $liste_params Paramètres de la case
     * @param array $over Fonctions à appeler au passage de la souris
     * @param array $out Fonctions à appeler à la sortie de la souris
     * @param array $clic Fonctions à appeler au clic de la souris
     * @param array $contenu Contenu html à mettre dans la case
     */
    public function ajouteCase($nom, $contenu, $classe = false, $largeur = false, $hauteur = false) {
        if ($contenu instanceof div) {
            $this->cases[$this->ligne][] = array($nom => $contenu);
        } else {
            $this->cases[$this->ligne][] = array($nom => new Div($contenu, $classe, $largeur, $hauteur));
        }
    }


    /**
     * Ajoute une case sur la ligne courante
     *
     * @param unknown $ligne
     * @param unknown $col
     * @param unknown $nom
     * @param unknown $case
     */
    public function placeCase($ligne, $col, $nom, $case) {
        if (!@$this->cases[$ligne][$col]) {
            $this->cases[$ligne][$col] = array();
        }
        $this->cases[$ligne][$col][$nom] = $case;
    }

    /**
     * Retourne la case du tableau située à la ligne/colonne spécifiée
     *
     * @param integer $ligne Ligne de la case
     * @param integer $colonne Colonne de la case
     * @param return object div Case du tableau
     */
    public function getCase($ligne, $colonne, $nom) {
        if (!$this->caseExiste($ligne, $colonne, $nom)) {
            return false;
        } else {
            return $this->cases[$ligne][$colonne][$nom];
        }
    }

    /**
     * Indique si la case du tableau existe
     *
     * @param integer $ligne Ligne de la case
     * @param integer $colonne Colonne de la case
     * @param boolean Case existante ou non
     */
    public function caseExiste($ligne, $colonne, $nom) {
        return isset($this->cases[$ligne][$colonne][$nom]);
    }

    /**
     * Indique si une case est vide (aucun contenu)
     *
     * Si la case n'existe pas on retourne vrai.
     *
     * @param integer $ligne Ligne de la case
     * @param integer $colonne Colonne de la case
     * @return boolean Case vide ou non
     */
    public function estVide($ligne, $colonne) {
        if (!isset($this->cases[$ligne][$colonne])) {
            return false;
        } elseif (!$this->cases[$ligne][$colonne]) {
            return false;
        } else {
            return (sizeof($this->cases[$ligne][$colonne]) == 1);
        }
    }

    /**
     * Détruit une case du tableau
     *
     *
     * @param integer $ligne Ligne de la case
     * @param integer $colonne Colonne de la case
     */
    public function detruireCase($ligne, $colonne, $nom) {
        unset($this->cases[$ligne][$colonne][$nom]);
    }

    /**
     * Génère le code HTML du tableau et le retourne
     *
     * @return string code HTML du tableau
     */
    public function toHTMLIso($ligneMax, $colonneMax) {
        $str = $this->headHTML();

        $indicColonne = $colonneMax;
        $ligneIndicateur = 0;
        foreach ($this->cases as $ligne) {
            $leftLigneBase = $indicColonne * Tableau::$largeurIso / 2 - 30;
            $topLigneBase = $ligneIndicateur * Tableau::$hauteurIso / 2 + 35;

            $colonne = 0;
            foreach ($ligne as $cases) {
                foreach ($cases as $nom => $case) {
                    $top = $topLigneBase + ($colonne * Tableau::$hauteurIso / 2);
                    $left = $leftLigneBase + $colonne * Tableau::$largeurIso / 2;
                    $case->addStyle(array('position' => 'absolute', 'top' => $top . 'px', 'left' => $left . 'px'));
                    $str .= $case->toHTML() . NL;
                }
                $colonne++;
            }
            $indicColonne--;
            $ligneIndicateur++;
        }
        foreach ($this->contenu as $element) {
            if ($element instanceof div) {
                $str .= $element->toHTML() . NL;
            } else {
                $str .= $element . NL;
            }
        }
        $str .= '</div>';
        return $str;
    }

    /**
     * Retourne la balise ouvrante du tableau
     * @return string
     */
    public function headHTML(): string {
        $str = '<div';
        foreach ($this->liste_params as $param => $val) {
            $str .= ' ' . $param . '="' . $val . '"';
        }
        $str .= ' style="position:relative';
        foreach ($this->style as $param => $val) {
            $str .= ';' . $param . ':' . $val;
        }
        return $str . '">' . NL;
    }


    /**
     * Ajoute du contenu
     *
     * @param mixed $contenu Contenu html (ou objet div)
     */
    public function ajouteContenu($contenu) {
        $this->contenu[] = $contenu;
    }
}