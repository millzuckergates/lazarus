<?php

/**
 * Cette classe sert à définir une "case" du plateau par le biais d'un <div>
 * @author fvpei
 *
 */
class Div {

    /**
     * Largeur de la case en $unite
     * @var unknown
     */
    public $largeur;
    /**
     * Hauteur de la case en $unite
     * @var unknown
     */
    public $hauteur;

    /**
     * Unité pour mesurer la taille de la casse
     * @var string
     */
    public $unite = 'px';
    /**
     * Le contenu de la case
     * @var string
     */
    public $contenu = false;
    /**
     * La class du div ?
     * @var string
     */
    public $classe = false;
    /**
     * L'id de la case
     * @var string
     */
    public $id = false;
    /**
     * La légende la case
     * @var string
     */
    public $legende = false;
    /**
     * action javascript "onmouseover"
     * @var array
     */
    private $over = array();
    /**
     * action javascript "onmouseout"
     * @var array
     */
    private $out = array();
    /**
     * action javascript "onclic"
     * @var array
     */
    private $clic = array();
    /**
     * Le style du div
     * @var array
     */
    private $style = array();

    /**
     * Le constructeur du div
     * @param string $contenu
     * @param string $classe
     * @param string $largeur
     * @param string $hauteur
     */
    public function __construct($contenu = false, $classe = false, $largeur = false, $hauteur = false, $id = false) {
        $this->id = $id;
        $this->largeur = Tableau::$largeurIso;
        $this->hauteur = Tableau::$hauteurIso;
        if (is_array($contenu)) {
            foreach ($contenu as $param => $val) {
                if (isset($this->$param)) {
                    $this->$param = $val; //le $param doit être une des valeures connues de la classe ? //TODO à vérifier
                }
            }
        } else {
            if ($contenu) {
                $this->contenu = array('base' => $contenu);
            }
            $this->classe = $classe;
            if ($largeur) {
                $this->largeur = $largeur;
            }
            if ($hauteur) {
                $this->hauteur = $hauteur;
            }
        }
    }

    /**
     * Ajoute le paramètre porté par la variable $nom
     * dans le contenu
     * @param unknown $nom
     * @param unknown $contenu
     */
    public function ajoute($nom, $contenu) {
        $this->contenu[$nom] = $contenu;
    }

    /**
     * Ajoute une action "onmouseover" sur le div
     * @param string|array $action
     */
    public function addOver($action) {
        if (is_array($action)) {
            $this->over = array_merge($this->over, $action);
        } else {
            $this->over[] = $action;
        }
    }

    /**
     * Retourne la valeur du onmouseover
     * @return string|boolean
     */
    public function getOver() {
        if (sizeof($this->over) > 0) {
            return implode(';', $this->over);
        } else {
            return false;
        }
    }

    /**
     * Ajoute un evenement "onmouseout" sur le div
     * @param string|array $action
     */
    public function addOut($action) {
        if (is_array($action)) {
            $this->out = array_merge($this->out, $action);
        } else {
            $this->out[] = $action;
        }
    }

    /**
     * Retourne la valeur de l'action "onmouseout"
     * @return string|boolean
     */
    public function getOut() {
        if (sizeof($this->out) > 0) {
            return implode(';', $this->out);
        } else {
            return false;
        }
    }

    /**
     * Ajoute une action à l'attribut "onclick"
     * @param array|string $action
     */
    public function addClic($action) {
        if (is_array($action)) {
            $this->clic = array_merge($this->clic, $action);
        } else {
            $this->clic[] = $action;
        }
    }

    /**
     * Retourne les actions "onclic" sur le div
     * @return string|boolean
     */
    public function getClic() {
        if (sizeof($this->clic) > 0) {
            return implode(';', $this->clic);
        } else {
            return false;
        }
    }

    /**
     * Ajoute du style css au div
     * @param string|array $param
     * @param string $val
     */
    public function addStyle($param, $val = false) {
        if (is_array($param)) {
            $this->style = array_merge($this->style, $param);
        } else {
            $this->style[$param] = $val;
        }
    }

    /**
     * Retourne le style css du div (construit)
     * @return string|boolean
     */
    public function getStyle() {
        if (sizeof($this->style) > 0) {
            $style = 'width:' . $this->largeur . $this->unite . ';height:' . $this->hauteur . $this->unite;
            foreach ($this->style as $param => $val) {
                $style .= ';' . $param . ':' . $val;
            }
            return $style;
        } else {
            return false;
        }
    }

    /**
     * Htmlise le div
     * @return string
     */
    public function toHTML() {
        $str = '<div';
        if ($this->id) {
            $str .= ' id="' . $this->id . '"';
        }
        if ($this->classe) {
            $str .= ' class="' . $this->classe . '"';
        }
        if ($this->legende) {
            $str .= ' title="' . $this->legende . '"';
        }
        $str .= ' style="' . $this->getStyle() . '"';
        if (sizeof($this->out) > 0) {
            $str .= ' onMouseOut="' . $this->getOut() . '"';
        }
        if (sizeof($this->over) > 0) {
            $str .= ' onMouseOver="' . $this->getOver() . '"';
        }
        if (sizeof($this->clic) > 0) {
            $str .= ' onClick="' . $this->getClic() . '"';
        }
        $str .= '>';
        if ($this->contenu) {
            foreach ($this->contenu as $element) {
                if ($element instanceof div) {
                    $str .= $element->toHTML();
                } else {
                    $str .= $element;
                }
            }
        }
        $str .= '</div>';
        return $str;
    }
}