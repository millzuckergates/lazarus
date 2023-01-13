<?php

/**
 * classe Dates
 *
 * Gère les dates Ideo et Irl
 *
 */
class Dates {

    /**
     * Nom des saisons Ideo
     * @var array
     */
    static $listSaisons = array("saison de l'Aube", "saison du Zénith", "saison du Crépuscule", "saison de la Nuit");

    /**
     * Nom des mois Ideo
     * @var array
     */
    static $listMois = array('Logalios', 'Danurmos', 'Goliarmos', 'Rastel', 'Dilannel', 'Joriamel', 'Fagilias', 'Solianas', 'Tilomias', 'Calinior', 'Filandor', 'Volganor');

    /**
     * Nom des mois IRL
     * @var array
     */
    static $listMoisIRL = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

    /**
     * Nom des jours Ideo
     * @var array
     */
    static $listJours = array('Lüdik', 'Malina', 'Mirion', 'Joriol', 'Valkin', 'Solior', 'Dolink');

    /**
     * Nom des jours IRL
     * @var array
     */
    static $listJoursIRL = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

    /**
     * Heures de lever du soleil, par saison
     * @var array
     */
    static $lever = array(7, 5, 7, 9);

    /**
     * Heures de coucher du soleil, par saison
     * @var array
     */
    static $coucher = array(20, 22, 20, 18);

    /**
     * Timestamp PHP
     * @var integer
     */
    public $timestamp;

    /**
     * Date courante
     * @var integer
     */
    public $now;

    /**
     * jour du mois Ideo
     * @var integer
     */
    public $jour;

    /**
     * annee du cycle IDEO
     * @var integer
     */
    public $annee;

    /**
     * cycle IDEO
     * @var integer
     */
    public $cycle;

    /**
     * jour de l'annee IDEO
     * @var integer
     */
    public $jourAnnee;

    /**
     * jour semaine IDEO
     * @var integer
     */
    public $jourSemaine;

    /**
     * numero du jour ferie (faux par défaut = jour non férié)
     * @var integer
     */
    public $ferie = false;

    /**
     * mois IDEO
     * @var integer
     */
    public $mois;

    /**
     * numero de saison IDEO
     * @var integer
     */
    public $saison;

    /**
     * Indique s'il fait nuit ou pas
     * @var boolean
     */
    public $nuit;

    /**
     * Décalage des cycles ideo par rapport aux années irl
     * @var boolean
     */
    public $decalage;


    /**
     * Constructeur
     *
     * Récupère le timestamp et en tire le mois, jour, année etc...
     *
     * @param integer $timestamp timestamp prédéfini (facultatif)
     */
    function __construct($timestamp = null, $decalage = DECALAGE_ANNEES) {
        date_default_timezone_set("Europe/Paris");
        if (is_null($timestamp)) {
            $this->timestamp = time();
        } else {
            $this->timestamp = $timestamp;
        }

        $this->decalage = $decalage;
        $this->now = time();
        $annee = date('Y', $this->timestamp);
        $this->mois = date('n', $this->timestamp);
        $this->jourAnnee = date('z', $this->timestamp);
        $this->jour = date('j', $this->timestamp);
        $this->jourSemaine = date('w', $this->timestamp);
        if (!$this->jourSemaine) {
            $this->jourSemaine = 7;
        }
        $this->cycle = $annee - $this->decalage;

        if ($this->timestamp < mktime(0, 0, 0, 3, 21, $annee)) {
            $this->saison = 4;
        } elseif ($this->timestamp < mktime(0, 0, 0, 6, 21, $annee)) {
            $this->saison = 1;
        } elseif ($this->timestamp < mktime(0, 0, 0, 9, 21, $annee)) {
            $this->saison = 2;
        } elseif ($this->timestamp < mktime(0, 0, 0, 12, 21, $annee)) {
            $this->saison = 3;
        } else {
            $this->saison = 4;
        }

        $h = date('H', $this->timestamp);
        if ($h >= Dates::$lever[$this->saison - 1] && $h < Dates::$coucher[$this->saison - 1]) {
            $this->nuit = false;
        } else {
            $this->nuit = true;
        }
        $time = floor((60 * date('H', $this->timestamp) + date('i', $this->timestamp)) / 2);
        if (date('z', $this->timestamp) % 2) {
            $time += 12 * 60;
        }
        $h = floor($time / 60);

        if ($h >= Dates::$lever[$this->saison - 1] && $h < Dates::$coucher[$this->saison - 1]) {
            $this->nuit = false;
        } else {
            $this->nuit = true;
        }
    }


    /**
     * Retourne le pourcentage de la journée courante
     * @return integer Pct de la journée courante
     */
    public function getPctJour() {
        date_default_timezone_set("Europe/Paris");
        $time = floor((60 * date('H', $this->timestamp) + date('i', $this->timestamp)) / 2);
        if (date('z', $this->timestamp) % 2) {
            $time += 12 * 60;
        }
        $h = floor($time / 60);
        return round(100 * ($h - Dates::$lever[$this->saison - 1]) / (Dates::$coucher[$this->saison - 1] - Dates::$lever[$this->saison - 1]));
    }

    /**
     * Retourne le pourcentage de la nuit courante
     * @return integer Pct de la nuit courante
     */
    public function getPctNuit() {
        date_default_timezone_set("Europe/Paris");
        $time = floor((60 * date('H', $this->timestamp) + date('i', $this->timestamp)) / 2);
        if (date('z', $this->timestamp) % 2) {
            $time += 12 * 60;
        }
        $h = floor($time / 60);
        if ($h < 12) {
            $h += 24;
        }
        return round(100 * ($h - date::$coucher[$this->saison - 1]) / (date::$lever[$this->saison - 1] + 24 - date::$coucher[$this->saison - 1]));
    }

    /**
     * Retourne la saison ideo correspondante en chiffre
     * Attention les saisons commencent à 1
     * @return integer Saison
     */
    public function getSaison() {
        return $this->saison;
    }


    /**
     * Retourne le nom de la saison ideo
     * @return string Nom de la saison
     */
    public function getNomSaison() {
        return date::$listSaisons[$this->saison - 1];
    }

    /**
     * Retourne le nom du mois
     * @return string Nom du mois
     */
    public function getNomMois() {
        return Dates::$listMois[$this->mois - 1];
    }

    /**
     * Retourne le nom du jour
     * @return string Nom du jour
     */
    public function getNomJour() {
        return Dates::$listJours[$this->jourSemaine - 1];
    }

    /**
     * Retourne le mois en chiffres
     * Attention, les mois commencent à 1
     *
     * @return integer mois
     */
    public function getMois() {
        return $this->mois;
    }

    /**
     * Retourne le jour du mois Ideo
     * @return integer jour
     */
    public function getJour() {
        return $this->jour;
    }

    /**
     * Retourne le cycle Ideo
     * @return integer cycle
     */
    public function getCycle() {
        return $this->cycle;
    }

    /**
     * Retourne le mois IRL
     * @return string Nom du mois
     */
    public function getNomMoisIRL() {
        return Dates::$listMoisIRL[date('n', $this->timestamp) - 1];
    }

    /**
     * Retourne le nom du jour IRL
     * @return string Jour de la semaine
     */
    public function getNomJourIRL() {
        return Dates::$listJoursIRL[date('w', $this->timestamp)];
    }

    /**
     * Retourne faux si le jour n'est pas férié, le numero du jour férié sinon
     * @return mixed Numero du jour ferie ou faux si le jour n'est pas férié
     */
    public function estFerie() {
        return $this->ferie;
    }

    /**
     * Retourne vrai si c'est le jour, faux si c'est la nuit
     * @return boolean résultat du test
     */
    public function faitJour() {
        return !$this->nuit;
    }

    /**
     * Retourne vrai si c'est la nuit, faux si c'est le jour
     * @return boolean résultat du test
     */
    public function faitNuit() {
        return $this->nuit;
    }

    /**
     * Retourne vrai si la date définie correspond à hier
     */
    public function estHier() {
        return (floor($this->timestamp / (3600 * 24)) == (floor($this->now / (3600 * 24)) - 1));
    }

    /**
     * Retourne vrai si la date définie correspond à aujourd'hui
     */
    public function estToday() {
        $dureeJour = 3600 * 24;
        return (floor($this->timestamp / $dureeJour) == floor($this->now / $dureeJour));
    }

    /**
     * Retourne vrai si la date définie correspond à demain
     */
    public function estDemain() {
        $dureeJour = 3600 * 24;
        return (floor($this->timestamp / $dureeJour) == (floor($this->now / $dureeJour) + 1));
    }

    /**
     * Retourne la date formatée de differentes manieres.
     *
     * @param string $type format souhaité : simple ou complet
     * @param string $univers univers souhaité : ideo/rp (par défaut) ou irl/hrp
     * @param boolean $legende Mettre à faut si vous ne voulez pas de légende
     * @return string Date formatée
     */
    public function afficheDate($type, $univers = 'ideo', $legende = true, $le_ok = true) {
        if ($univers == 'rp') {
            $univers = 'ideo';
        } elseif ($univers == 'hrp' || $univers == 'mj') {
            $univers = 'irl';
        }
        $date = false;
        $le = false;
        if ($type == 'simple') {
            if ($this->estHier($univers)) {
                $date = 'hier ';
            } elseif ($this->estToday($univers)) {
                $date = 'aujourd\'hui ';
            } elseif ($this->estDemain($univers)) {
                $date = 'demain ';
            }
        }

        if ($univers == 'irl' && $type != 'complet') {
            if (!$date) {
                $le = true;
                $date = date('d/m/', $this->timestamp) . ($this->cycle + DECALAGE_ANNEES);
            }
        } elseif ($univers == 'irl' && $type == 'complet') {
            $date = $this->getNomJourIRL() . ' ' . date('d', $this->timestamp) . ' ' . $this->getNomMoisIRL() . ' ' . ($this->cycle + DECALAGE_ANNEES);
        } elseif ($univers == 'ideo' && $type != 'complet') {
            if (!$date) {
                $le = true;
                $date = date('d-m-', $this->timestamp) . $this->cycle;
            }
        } elseif ($univers == 'ideo' && $type == 'complet') {
            $le = true;
            $date = $this->getNomJour() . ' ' . $this->jour . ' ' . $this->getNomMois() . ' du ' . $this->getCycle() . 'ème cycle';
        }

        if ($legende) {
            if ($univers == 'ideo') {
                $univers = 'irl';
            } else {
                $univers = 'ideo';
            }
            $date = '<span style="display:inline" title="' . $this->afficheDate($type, $univers, false, false) . ' (' . $univers . ')">' . $date . '</span>';
        }
        if ($le && $le_ok) {
            return 'le ' . $date;
        } else {
            return $date;
        }
    }

    /**
     * Retourne l'heure formatée de differentes manieres.
     *
     * @param string $univers univers souhaité : ideo/rp (par défaut) ou irl/hrp
     * @param boolean $legende Mettre à faut si vous ne voulez pas de légende
     * @return string Heure formatée
     */
    public function afficheHeure($univers = 'ideo', $legende = true) {
        if ($univers == 'rp') {
            $univers = 'ideo';
        } elseif ($univers == 'hrp') {
            $univers = 'irl';
        }

        date_default_timezone_set("Europe/Paris");
        $heure = date('H\hi', $this->timestamp);
        if ($legende && false) {
            if ($univers == 'ideo') {
                $univers = 'irl';
            } else {
                $univers = 'ideo';
            }
            $heure = '<span style="display:inline" title="' . $this->afficheHeure($univers, false) . ' (' . $univers . ')">' . $heure . '</span>';
        }
        return $heure;
    }

    /**
     * Retourne vrai si la date est passée
     * @return boolean résultat du test
     */
    public function estPassee() {
        return $this->timestamp < $this->now;
    }

    /**
     * Retourne vrai si la date n'est pas encore passée
     * @return boolean résultat du test
     */
    public function estFuture() {
        return $this->timestamp > $this->now;
    }

    /**
     * Retourne vrai si la date est correspond à la date actuelle
     * @return boolean résultat du test
     */
    public function estPresente() {
        return $this->timestamp == $this->now;
    }

    /**
     * Génère un timestamp à partir des données de l'objet (jour, mois et cycle ideo)
     *
     * @param integer $j Jour
     * @param integer $m Mois
     * @param integer $c Cycle
     * @param integer $h Heure
     * @param integer $i Minute
     */
    public function genere($j, $m, $c, $h = 12, $i = 0) {
        date_default_timezone_set("Europe/Paris");
        $y = $c + DECALAGE_ANNEES;
        $min = date('Y', 0);
        if ($y <= $min) {
            $this->decalage = $min - $c;
            $y = $min;
        } elseif ($y > 2037) {
            $this->decalage = DECALAGE_ANNEES;
            $y = 2037;
        } else {
            $this->decalage = DECALAGE_ANNEES;
        }

        $this->timestamp = mktime($h, $i, 0, $m, $j, $y);
        $this->__construct($this->timestamp, $this->decalage);
    }
}