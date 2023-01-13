<?php

interface FichierBonus {
    /**
     * Retourne la descritpion détaillée du bonus
     * @param unknown $bonus
     * @param unknown $type
     */
    public function getDescriptionDetaillee($bonus, $type);

    /**
     * Retourne le template du bonus
     * @param unknown $bonus
     * @param unknown $type
     */
    public function getTemplate($bonus, $type);

    /**
     * Permet de tester les paramètres du bonus
     * @param unknown $listeParametres
     */
    public function testerParametres($listeParametres);

    /**
     * Permet d'enregistrer un bonus
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $bonus
     */
    public function insertAssoc($listeParam, $type, $idType, $bonus);

    /**
     * Permet de modifier un bonus
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $bonus
     * @param unknown $position
     */
    public function modifierAssoc($listeParam, $type, $idType, $bonus, $position);

    /**
     * Retourne une description du bonus
     * @param unknown $bonus
     */
    public function genererDescription($bonus);

    /**
     * Retourne l'image à afficher pour le bonus
     * @param unknown $bonus
     */
    public function genererImage($bonus);
}
