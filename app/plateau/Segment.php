<?php

/**
 * Correspond à un segment de la carte que l'on affiche
 * @author fvpei
 *
 */
class Segment {

    /**
     * Correspond aux coordonnées min
     * @var int
     */
    var $xmin;
    var $ymin;
    /**
     * Correspond aux coordonnées max
     * @var int
     */
    var $xmax;
    var $ymax;

    /**
     * Tableau de case
     * @var array
     */
    var $matrice = array();
    /**
     * Permet de savoir si le segment a déjà été chargé
     * @var string
     */
    var $initialized = false;
    /**
     * Le nom de la table pour récupérer les données
     * @var string
     */
    var $table;

    /**
     * Identifiant de la carte
     * @var int
     */
    var $idCarte;

    /**
     * Global instance
     * @var bool|Segment
     */
    static private $globalInstance = false;

    public function __construct($data) {
        if (!empty($data)) {
            $this->xmax = $data["xmax"];
            $this->xmin = $data["xmin"];
            $this->ymin = $data["ymin"];
            $this->ymax = $data["ymax"];

            $this->table = $data["table"];
            $this->idCarte = $data["idCarte"];

            //On remplit le tableau de cases
            $this->chargeCases();
        }
    }

    /**
     * L'instance globale
     * @return Segment
     */
    public static function getGlobalInstance($data) {
        if (!Segment::$globalInstance) {
            Segment::$globalInstance = new Segment($data);
        }
        return Segment::$globalInstance;
    }

    /**
     * Permet de charger les cases correspondantes au segment
     */
    public function chargeCases() {
        for ($y = $this->ymin; $y <= $this->ymax; $y++) {
            $result = AbstractMatrices::findBySegmentAndY($this, $y);
            $date = new Dates();
            if (count($result) > 0) {
                foreach ($result as $case) {
                    $this->matrice[$case->x][$case->y] = new Cases($case, $this->table, $date->nuit);
                }
            }
        }
    }


    /**
     * Vérifie si la zone est déjà chargé dans le cache
     * @param $xmin
     * @param $xmax
     * @param $ymin
     * @param $ymax
     * @param $idCarte
     * @return boolean
     */
    public function isInitialized($xmin, $xmax, $ymin, $ymax, $idCarte) {
        if (!$this->initialized) {
            return false;
        }
        return $this->xmin >= $xmin && $xmax <= $this->xmax && $this->ymin >= $ymin && $ymax <= $this->ymax && $idCarte == $this->idCarte;
    }

}