<?php

interface FichierEffet {

    /**
     * Retourne la descritpion détaillée de l'effet
     * @param unknown $effet
     * @param unknown $type
     */
    public function getDescriptionDetaillee($effet, $type);

    /**
     * Retourne le template de l'effet
     * @param unknown $effet
     * @param unknown $type
     */
    public function getTemplate($effet, $type);

    /**
     * Permet de tester les paramètres de l'effet
     * @param unknown $listeParametres
     */
    public function testerParametres($listeParametres);

    /**
     * Permet d'enregistrer un effet
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $effet
     * @param unknown $action
     */
    public function insertAssoc($listeParam, $type, $idType, $effet, $action);

    /**
     * Permet de modifier un effet
     * @param unknown $listeParam
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $effet
     * @param unknown $position
     */
    public function modifierAssoc($listeParam, $type, $idType, $effet, $position);

    /**
     * Retourne une description de l'effet
     * @param unknown $effet
     */
    public function genererDescription($effet);

    /**
     * Permet d'évaluer l'effet "chiffré" et d'en retourner la description
     * @param unknown $effet
     * @param unknown $element
     * @param unknown $auth
     */
    public function genererDescriptionEvaluee($effet, $element, $auth, $type, $mode, $modificateur = null);
}