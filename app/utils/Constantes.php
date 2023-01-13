<?php

/**
 * Classe pour les constantes du jeu
 * @author fvpeigne
 *
 */
class Constantes {

    //###### SAISON #######//
    const SAISON_TOUTES = "Toutes";
    const SAISON_HIVER = "Hiver";
    const SAISON_PRINTEMPS = "Printemps";
    const SAISON_ETE = "Eté";
    const SAISON_AUTOMNE = "Automne";

    /**
     * Renvoie la liste des saisons
     * @return string
     */
    public static function getListeSaison() {
        $retour[0] = Constantes::SAISON_TOUTES;
        $retour[1] = Constantes::SAISON_HIVER;
        $retour[2] = Constantes::SAISON_PRINTEMPS;
        $retour[3] = Constantes::SAISON_ETE;
        $retour[4] = Constantes::SAISON_AUTOMNE;
        return $retour;
    }

    /**
     * Méthode pour générer le select sur les saison
     * @param unknown $saisonSelect
     * @param unknown $idSelect
     * @return string
     */
    public static function genererSelectSaison($saisonSelect, $idSelect) {
        if ($saisonSelect == null) {
            $saisonSelect = Constantes::SAISON_TOUTES;
        }
        $listeSaison = Constantes::getListeSaison();
        $retour = "<select id='" . $idSelect . "'>";
        foreach ($listeSaison as $saison) {
            if ($saison == $saisonSelect) {
                $retour .= "<option value='" . $saison . "' selected>" . $saison . "</option>";
            } else {
                $retour .= "<option value='" . $saison . "'>" . $saison . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    //####### GENRE ######//
    const FEMININ = "Feminin";
    const MASCULIN = "Masculin";
    const NEUTRE = "Neutre";

    /**
     * Renvoie la liste des genres
     * @return string
     */
    public static function getListeGenre() {
        $retour[0] = Constantes::FEMININ;
        $retour[1] = Constantes::MASCULIN;
        $retour[2] = Constantes::NEUTRE;
        return $retour;
    }

    /**
     * Méthode pour générer le select sur les genres
     * @param unknown $genreSelect
     * @param unknown $idSelect
     * @return string
     */
    public static function genererSelectGenre($genreSelect, $idSelect) {
        if ($genreSelect == null) {
            $genreSelect = Constantes::FEMININ;
        }
        $listeGenre = Constantes::getListeGenre();
        $retour = "<select id='" . $idSelect . "'>";
        foreach ($listeGenre as $genre) {
            if ($genre == $genreSelect) {
                $retour .= "<option value='" . $genre . "' selected>" . $genre . "</option>";
            } else {
                $retour .= "<option value='" . $genre . "'>" . $genre . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    //######### Type d'Acces #########//
    //Constantes pour les types d'accès
    const ACCES_TOUS = "Tous";
    const ACCES_MARITIME = "Maritime";
    const ACCES_TERRESTRE = "Terrestre";
    const ACCES_MORT = "Mort";

    /**
     * Genre la liste des types d'accès
     * @return string
     */
    public static function getListeAcces() {
        $retour[0] = Constantes::ACCES_TOUS;
        $retour[1] = Constantes::ACCES_TERRESTRE;
        $retour[2] = Constantes::ACCES_MARITIME;
        $retour[3] = Constantes::ACCES_MORT;
        return $retour;
    }

    /**
     * Retourne le select pour la liste des accès
     * @param unknown $accesSelect
     * @param unknown $idSelect
     * @return string
     */
    public static function genererSelectAcces($accesSelect, $idSelect) {
        if ($accesSelect == null) {
            $accesSelect = Constantes::ACCES_TOUS;
        }
        $listeAcces = Constantes::getListeAcces();
        $retour = "<select id='" . $idSelect . "'>";
        foreach ($listeAcces as $acces) {
            if ($acces == $accesSelect) {
                $retour = $retour . "<option value='" . $acces . "' selected>" . $acces . "</option>";
            } else {
                $retour = $retour . "<option value='" . $acces . "'>" . $acces . "</option>";
            }
        }
        $retour = $retour . "</select>";
        return $retour;
    }
}
