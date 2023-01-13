<?php

/**
 *
 * Classe pour gérer les images
 *
 * @author François-Victor
 *
 */
class Images {

    const TYPE_GIF = 1;
    const TYPE_JPG = 2;
    const TYPE_PNG = 3;

    var $image;
    var $width;
    var $height;
    var $type;

    /**
     * Construit une image
     * @param unknown $fileName
     */
    public function __construct($fileName) {
        list($width, $height, $type, $attr) = getimagesize($fileName);
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
        if ($this->type == Images::TYPE_GIF) {
            $this->image = imagecreatefromgif($fileName);
        } else {
            if ($this->type == Images::TYPE_JPG) {
                $this->image = imagecreatefromjpeg($fileName);
            } else {
                if ($this->type == Images::TYPE_PNG) {
                    $this->image = imagecreatefrompng($fileName);
                } else {
                    $this->image = null;
                }
            }
        }
    }

    /**
     * Retourne la couleur du pixel spécifié
     *
     * La couleur retournée est sous forme de code couleur HTML (#rrggbb)
     * Si le point est hors de l'image on retourne la constante HORS_IMAGE
     *
     * @param integer $x abscisse du pixel
     * @param integer $y ordonnée du pixel
     * @return string Code couleur du pixel
     */
    public function getCouleur($x, $y) {
        if ($x >= $this->width || $x < 0 || $y >= $this->height || $y < 0) {
            return "error";
        } else {
            $tab_couleurs = imageColorsForIndex($this->image, imageColorAt($this->image, $x, $y));
            return sprintf('%02X%02X%02X', $tab_couleurs['red'], $tab_couleurs['green'], $tab_couleurs['blue']);
        }
    }


    /**
     * Dessine un point aux coordonnées (x,y)
     *
     * @param integer $x Abscisse en pixel
     * @param integer $y Ordonnée en pixel
     * @param ressource $couleur Couleur du pixel
     * @return boolean Résultat de la fonction imageSetPixel
     */
    public function point($x, $y, $couleur) {
        return imageSetPixel($this->img, $x, $y, $couleur);
    }

    /**
     * Dessine une ligne aux coordonnées (x1,y1,x2,y2)
     *
     * @param integer $x1 Abscisse 1 en pixel
     * @param integer $y1 Ordonnée 1 en pixel
     * @param integer $x2 Abscisse 2 en pixel
     * @param integer $y2 Ordonnée 2 en pixel
     * @param ressource $couleur Couleur de la ligne
     * @return boolean Résultat de la fonction imageLine
     */
    public function ligne($x1, $y1, $x2, $y2, $couleur) {
        return imageLine($this->img, $x1, $y1, $x2, $y2, $couleur);
    }

    /**
     * Dessine un rectangle aux coordonnées n-o(x1,y1) et s-e(x2,y2)
     *
     * @param integer $x1 Abscisse 1 en pixel
     * @param integer $y1 Ordonnée 1 en pixel
     * @param integer $x2 Abscisse 2 en pixel
     * @param integer $y2 Ordonnée 2 en pixel
     * @param ressource $couleur Couleur du rectangle
     * @return boolean Résultat de la fonction imageRectangle
     */
    public function rectangle($x1, $y1, $x2, $y2, $couleur) {
        return imageRectangle($this->img, $x1, $y1, $x2, $y2, $couleur);
    }

    /**
     * Dessine un cercle aux coordonnées (x,y) de rayon (rayon)
     *
     * @param integer $x Abscisse en pixel
     * @param integer $y Ordonnée en pixel
     * @param integer $rayon Rayon du cercle en pixel
     * @param ressource $couleur Couleur du cercle
     * @return boolean Résultat de la fonction imageEllipse
     */
    public function cercle($x, $y, $rayon, $couleur) {
        return imageEllipse($this->img, $x, $y, $rayon, $rayon, $couleur);
    }

    /**
     * Dessine un disque aux coordonnées (x,y) de rayon (rayon)
     *
     * @param integer $x Abscisse en pixel
     * @param integer $y Ordonnée en pixel
     * @param integer $rayon Rayon du disque en pixel
     * @param ressource $couleur Couleur du disque
     * @return boolean Résultat de la fonction imageEllipseFilled
     */
    public function disque($x, $y, $rayon, $couleur) {
        return imageFilledEllipse($this->img, $x, $y, $rayon, $rayon, $couleur);
    }

    /**
     * Crée une couleur (r,g,b)
     *
     * @param integer $r Rouge (0-255)
     * @param integer $g Vert (0-255)
     * @param integer $b Bleu (0-255)
     * @return integer Identifiant de couleur à utiliser dans des méthodes de dessin
     */
    public function couleur($r, $g, $b) {
        return imageColorAllocate($this->img, $r, $g, $b);
    }
}