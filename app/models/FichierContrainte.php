<?php

interface FichierContrainte {

    /**
     * Retourne la descritpion détaillée de la contrainte
     * @param unknown $contrainte
     * @param unknown $type
     */
    public function getDescriptionDetaillee($contrainte, $type);

    /**
     * Retourne le template de la contrainte
     * @param unknown $contrainte
     * @param unknown $type
     */
    public function getTemplate($contrainte, $type);

    /**
     * Permet de tester les paramètres de la contrainte
     * @param unknown $listeParametres
     */
    public function testerParametres($listeParametres);

    /**
     * Permet d'enregistrer une contrainte
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $contrainte
     * @param unknown $action
     */
    public function insertAssoc($listeParam, $type, $idType, $contrainte, $action);

    /**
     * Permet de modifier une contrainte
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $contrainte
     * @param unknown $position
     */
    public function modifierAssoc($listeParam, $type, $idType, $contrainte, $position);

    /**
     * Retourne une description de la contrainte
     * @param unknown $contrainte
     */
    public function genererDescription($contrainte);

    /**
     * Permet de vérifier la contrainte
     * @param unknown $mode
     * @param unknown $auth
     * @param unknown $cible
     */
    public function verif($mode, $auth, $cible, $contrainte);

}