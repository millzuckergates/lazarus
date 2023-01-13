<?php

/*
 * Effet permettant de modifier une caractéristique d'un personnage
*
* Nombre de paramètres : 2
* 	- La caractéristique à modifier
* 	- La formule pour modifier cette caractéristique
*/

class Effet_modifie_carac implements FichierEffet {


    /**
     * {@inheritDoc}
     * @see FichierEffet::getDescriptionDetaillee()
     */
    public function getDescriptionDetaillee($effet, $type) {
        $description = "";
        if ($type != "creation") {
            $description .= "<b>Caracteristique :</b> " . $effet->listeParametres[0]->valeur . "</br>";
            if (empty($effet->listeParametres[1]->valeurMin)) {
                $effet->listeParametres[1]->valeurMin = "~";
            }
            if (empty($effet->listeParametres[1]->valeurMax)) {
                $effet->listeParametres[1]->valeurMax = "~";
            }
            $description .= "<b>Formule : </b>" . $effet->listeParametres[1]->valeur;
            $description .= "<br/><span fontsize='2'><i>(min : " . $effet->listeParametres[1]->valeurMin . ", max : " . $effet->listeParametres[1]->valeurMax . " )</i></span>";

        }
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierEffet::genererDescription()
     */
    public function genererDescription($effet) {
        $retour = "";
        $action = null;
        if (isset($effet->listeParametres[0]->action)) {
            $action = $effet->listeParametres[0]->action;
        }
        //On récupère la caractéristique
        $carac = Caracteristiques::findFirst(['nom = :nom:', 'bind' => ['nom' => $effet->listeParametres[0]->valeur]]);
        if ($carac->genre == Constantes::FEMININ) {
            $retour .= "Modifie la " . $carac->nom . " ";
        } else {
            if ($carac->genre == Constantes::MASCULIN) {
                $retour .= "Modifie le " . $carac->nom . " ";
            } else {
                if ($carac->genre == Constantes::NEUTRE) {
                    $retour .= "Modifie l' " . $carac->nom . " ";
                }
            }
        }

        $retour .= "de " . $effet->listeParametres[1]->valeur . ".";

        if ($action != null) {
            $retour .= "Se déclenche " . $action . ".";
        }

        if (!empty($effet->listeParametres[1]->valeurMin)) {
            $bonusMin = "Minimum : " . $effet->listeParametres[1]->valeurMin;
        }
        if (!empty($effet->listeParametres[1]->valeurMax)) {
            $bonusMax = "Maximum : " . $effet->listeParametres[1]->valeurMax;
        }
        if (isset($bonusMin)) {
            $retour .= " <i>" . $bonusMin . ".</i>";
        }
        if (isset($bonusMax)) {
            $retour .= " <i>" . $bonusMax . ".</i>";
        }
        return $retour;
    }

    /**
     * {@inheritDoc}
     * @see FichierEffet::getTemplate()
     */
    public function getTemplate($effet, $type) {
        $template = "";
        $template .= "<input type='hidden' id='nbParam' value='2'/>";
        if ($type == "consultation") {
            $listeParametre = $effet->listeParametres;
            $template .= "<div id='divParam1'><b>Caracteristique :</b> " . $listeParametre[0]->valeur . "</br></div>";
            if (empty($listeParametre[1]->valeurMin)) {
                $listeParametre[1]->valeurMin = "~";
            }
            if (empty($listeParametre[1]->valeurMax)) {
                $listeParametre[1]->valeurMax = "~";
            }
            $template .= "<div id='divParam2'><b>Formule : </b>" . $listeParametre[1]->valeur;
            $template .= "<br/><span fontsize='2'><i>(min : " . $listeParametre[1]->valeurMin . ", max : " . $listeParametre[1]->valeurMax . " )</i></span></div>";
        } else {
            if ($type == "edition") {
                $listeParametre = $effet->listeParametres;
                $template .= "<div id='divParam1'><b>Caractéristique</b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez une caractéristique</option>";
                $listeCarac = Caracteristiques::getListeCaracs(Caracteristiques::CARAC_PRIMAIRE);
                if ($listeCarac != null && !empty($listeCarac)) {
                    foreach ($listeCarac as $carac) {
                        if ($carac->nom == $listeParametre[0]->valeur) {
                            $template .= "<option value='" . $carac->nom . "' selected>" . $carac->nom . "</option>";
                        } else {
                            $template .= "<option value='" . $carac->nom . "'>" . $carac->nom . "</option>";
                        }
                    }
                }
                $template .= "</select></div>";
                if (empty($listeParametre[1]->valeurMin)) {
                    $listeParametre[1]->valeurMin = "~";
                }
                if (empty($listeParametre[1]->valeurMax)) {
                    $listeParametre[1]->valeurMax = "~";
                }
                $template .= "<div id='divParam2' style='margin-top:8px;'><b>Formule </b><span type='text' id='param2' style='margin-left: 56px;'>" . $listeParametre[1]->valeur . "</span> <input type='button' class='boutonFormule' value='Changer formule' onclick='afficherFormulaireFormule(\"param2\",\"popupEffet\");' title='Permet de modifier la formule'/>";
                $template .= "<br/><span fontsize='2'><i>(min <input type='text' id='param2Min' value='" . $listeParametre[1]->valeurMin . "'/>, max <input type='text' id='param2Max' value='" . $listeParametre[1]->valeurMax . "'/> )</i></span></div>";
            } else {
                if ($type == "creation") {
                    $template .= "<div id='divParam1'><b>Caractéristique</b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez une caractéristique</option>";
                    $listeCarac = Caracteristiques::getListeCaracs(Caracteristiques::CARAC_PRIMAIRE);
                    if ($listeCarac != null && !empty($listeCarac)) {
                        foreach ($listeCarac as $carac) {
                            $template .= "<option value='" . $carac->nom . "'>" . $carac->nom . "</option>";
                        }
                    }
                    $template .= "</select></div>";
                    $template .= "<div id='divParam2'><b>Formule </b><span type='text' id='param2' style='margin-left: 56px;'>~</span><input type='button' class='boutonFormule' value='Changer formule' onclick='afficherFormulaireFormule(\"param2\",\"popupEffet\");' title='Permet de modifier la formule'/>";
                    $template .= "<br/><span fontsize='2'><i>(min <input type='text' id='param2Min' value=''/>, max <input type='text' id='param2Max' value=''/> )</i></span></div>";
                }
            }
        }
        return $template;
    }

    /**
     * {@inheritDoc}
     * @see FichiersEffet::testerParametres()
     */
    public function testerParametres($listeParametres) {
        if ($listeParametres == null || $listeParametres == "") {
            return "Erreur : Vous devez renseigner les paramètres.";
        }

        //On découpe la liste
        $listeParams = explode("|", $listeParametres);
        //On vérifie les valeurs obligatoires
        if (count($listeParams) != 7) {
            return "Erreur : Problème technique.";
        }

        // Les paramètres 1 et 2 sont obligatoires
        if ($listeParams[1] == null || $listeParams[1] == "" || $listeParams[1] == "0") {
            return "Erreur : Vous devez sélectionner une caractéristique à modifier.";
        }

        if ($listeParams[4] == null || $listeParams[4] == "" || $listeParams[4] == "~") {
            return "Erreur : Vous devez entrer une formule.";
        }

        if ($listeParams[5] != null && $listeParams[5] != "") {
            if (!is_numeric($listeParams[5])) {
                return "Erreur : le minimum doit être un entier.";
            }
        }

        if ($listeParams[6] != null && $listeParams[6] != "") {
            if (!is_numeric($listeParams[6])) {
                return "Erreur : le maximum doit être un entier.";
            }
        }

        if ($listeParams[5] != null && $listeParams[5] != "" && $listeParams[6] != null && $listeParams[6] != "") {
            if ($listeParams[5] > $listeParams[6]) {
                return "Erreur : La valeur minimum doit être inférieure à la valeur maximum.";
            }
        }
        //Construction de la liste de retour
        $finalListe = array();
        $finalListe['param1']['value'] = $listeParams[1];
        $finalListe['param1']['min'] = $listeParams[2];
        $finalListe['param1']['max'] = $listeParams[3];

        $finalListe['param2']['value'] = $listeParams[4];
        $finalListe['param2']['min'] = $listeParams[5];
        $finalListe['param2']['max'] = $listeParams[6];
        return $finalListe;
    }

    /**
     * {@inheritDoc}
     * @see FichiersEffet::insertAssoc()
     */
    public function insertAssoc($listeParam, $type, $idType, $effet, $action) {
        $position = $effet->getNewPositionEffet($type, $idType);
        if ($type == "sort") {
            $assocParam1 = new AssocSortsEffetsParam();
            $assocParam1->idEffet = $effet->id;
            $assocParam1->idSort = $idType;
            $assocParam1->idParam = $effet->listeParametres[0]->idParam;
            $assocParam1->valeur = $listeParam['param1']['value'];
            $assocParam1->position = $position;
            $assocParam1->save();
            $assocParam2 = new AssocSortsEffetsParam();
            $assocParam2->idEffet = $effet->id;
            $assocParam2->idSort = $idType;
            $assocParam2->idParam = $effet->listeParametres[1]->idParam;
            $assocParam2->valeur = $listeParam['param2']['value'];
            $assocParam2->valeurMin = $listeParam['param2']['min'];
            $assocParam2->valeurMax = $listeParam['param2']['max'];
            $assocParam2->position = $position;
            $assocParam2->save();
        } else {
            if ($type == "talent") {
                $assocParam1 = new AssocTalentsEffetsParam();
                $assocParam1->idEffet = $effet->id;
                $assocParam1->idTalent = $idType;
                $assocParam1->idParam = $effet->listeParametres[0]->idParam;
                $assocParam1->valeur = $listeParam['param1']['value'];
                $assocParam1->position = $position;
                $assocParam1->save();
                $assocParam2 = new AssocTalentsEffetsParam();
                $assocParam2->idEffet = $effet->id;
                $assocParam2->idTalent = $idType;
                $assocParam2->idParam = $effet->listeParametres[1]->idParam;
                $assocParam2->valeur = $listeParam['param2']['value'];
                $assocParam2->valeurMin = $listeParam['param2']['min'];
                $assocParam2->valeurMax = $listeParam['param2']['max'];
                $assocParam2->position = $position;
                $assocParam2->save();
            } else {
                if ($type == "terrain") {
                    $assocParam1 = new AssocTerrainsEffetsParam();
                    $assocParam1->idEffet = $effet->id;
                    $assocParam1->idTerrain = $idType;
                    $assocParam1->idParam = $effet->listeParametres[0]->idParam;
                    $assocParam1->valeur = $listeParam['param1']['value'];
                    $assocParam1->position = $position;
                    $assocParam1->action = $action;
                    $assocParam1->save();
                    $assocParam2 = new AssocTerrainsEffetsParam();
                    $assocParam2->idEffet = $effet->id;
                    $assocParam2->idTerrain = $idType;
                    $assocParam2->idParam = $effet->listeParametres[1]->idParam;
                    $assocParam2->valeur = $listeParam['param2']['value'];
                    $assocParam2->valeurMin = $listeParam['param2']['min'];
                    $assocParam2->valeurMax = $listeParam['param2']['max'];
                    $assocParam2->position = $position;
                    $assocParam2->action = $action;
                    $assocParam2->save();
                } else {
                    if ($type == "carte") {
                        $assocParam1 = new AssocCartesEffetsParam();
                        $assocParam1->idEffet = $effet->id;
                        $assocParam1->idCarte = $idType;
                        $assocParam1->idParam = $effet->listeParametres[0]->idParam;
                        $assocParam1->valeur = $listeParam['param1']['value'];
                        $assocParam1->position = $position;
                        $assocParam1->action = $action;
                        $assocParam1->save();
                        $assocParam2 = new AssocCartesEffetsParam();
                        $assocParam2->idEffet = $effet->id;
                        $assocParam2->idCarte = $idType;
                        $assocParam2->idParam = $effet->listeParametres[1]->idParam;
                        $assocParam2->valeur = $listeParam['param2']['value'];
                        $assocParam2->valeurMin = $listeParam['param2']['min'];
                        $assocParam2->valeurMax = $listeParam['param2']['max'];
                        $assocParam2->position = $position;
                        $assocParam2->action = $action;
                        $assocParam2->save();
                    } else {
                        if ($type == "competence") {
                            $assocParam1 = new AssocCompetencesEffetsParam();
                            $assocParam1->idEffet = $effet->id;
                            $assocParam1->idCompetence = $idType;
                            $assocParam1->idParam = $effet->listeParametres[0]->idParam;
                            $assocParam1->valeur = $listeParam['param1']['value'];
                            $assocParam1->position = $position;
                            $assocParam1->action = $action;
                            $assocParam1->save();
                            $assocParam2 = new AssocCompetencesEffetsParam();
                            $assocParam2->idEffet = $effet->id;
                            $assocParam2->idCompetence = $idType;
                            $assocParam2->idParam = $effet->listeParametres[1]->idParam;
                            $assocParam2->valeur = $listeParam['param2']['value'];
                            $assocParam2->valeurMin = $listeParam['param2']['min'];
                            $assocParam2->valeurMax = $listeParam['param2']['max'];
                            $assocParam2->position = $position;
                            $assocParam2->action = $action;
                            $assocParam2->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see FichiersEffet::insertAssoc()
     */
    public function modifierAssoc($listeParam, $type, $idType, $effet, $position) {
        if ($type == "sort") {
            $assocParam1 = AssocSortsEffetsParam::findFirst(['idEffet = :idEffet: AND idSort = :idSort: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idEffet' => $effet->id, 'idSort' => $idType, 'idParam' => $effet->listeParametres[0]->id, 'position' => $position]]);
            $assocParam1->valeur = $listeParam['param1']['value'];
            $assocParam1->save();

            $assocParam2 = AssocSortsEffetsParam::findFirst(['idEffet = :idEffet: AND idSort = :idSort: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idEffet' => $effet->id, 'idSort' => $idType, 'idParam' => $effet->listeParametres[1]->id, 'position' => $position]]);
            $assocParam2->valeur = $listeParam['param2']['value'];
            $assocParam2->valeurMin = $listeParam['param2']['min'];
            $assocParam2->valeurMax = $listeParam['param2']['max'];
            $assocParam2->save();
        } else {
            if ($type == "talent") {
                $assocParam1 = AssocTalentsEffetsParam::findFirst(['idEffet = :idEffet: AND idTalent = :idTalent: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idEffet' => $effet->id, 'idTalent' => $idType, 'idParam' => $effet->listeParametres[0]->id, 'position' => $position]]);
                $assocParam1->valeur = $listeParam['param1']['value'];
                $assocParam1->save();

                $assocParam2 = AssocTalentsEffetsParam::findFirst(['idEffet = :idEffet: AND idTalent = :idTalent: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idEffet' => $effet->id, 'idTalent' => $idType, 'idParam' => $effet->listeParametres[1]->id, 'position' => $position]]);
                $assocParam2->valeur = $listeParam['param2']['value'];
                $assocParam2->valeurMin = $listeParam['param2']['min'];
                $assocParam2->valeurMax = $listeParam['param2']['max'];
                $assocParam2->save();
            } else {
                if ($type == "terrain") {
                    $assocParam1 = AssocTerrainsEffetsParam::findFirst(['idEffet = :idEffet: AND idTerrain = :idTerrain: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idEffet' => $effet->id, 'idTerrain' => $idType, 'idParam' => $effet->listeParametres[0]->id, 'position' => $position]]);
                    $assocParam1->valeur = $listeParam['param1']['value'];
                    $assocParam1->save();

                    $assocParam2 = AssocTerrainsEffetsParam::findFirst(['idEffet = :idEffet: AND idTerrain = :idTerrain: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idEffet' => $effet->id, 'idTerrain' => $idType, 'idParam' => $effet->listeParametres[1]->id, 'position' => $position]]);
                    $assocParam2->valeur = $listeParam['param2']['value'];
                    $assocParam2->valeurMin = $listeParam['param2']['min'];
                    $assocParam2->valeurMax = $listeParam['param2']['max'];
                    $assocParam2->save();
                } else {
                    if ($type == "carte") {
                        $assocParam1 = AssocCartesEffetsParam::findFirst(['idEffet = :idEffet: AND idCarte = :idCarte: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idEffet' => $effet->id, 'idCarte' => $idType, 'idParam' => $effet->listeParametres[0]->id, 'position' => $position]]);
                        $assocParam1->valeur = $listeParam['param1']['value'];
                        $assocParam1->save();

                        $assocParam2 = AssocCartesEffetsParam::findFirst(['idEffet = :idEffet: AND idCarte = :idCarte: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idEffet' => $effet->id, 'idCarte' => $idType, 'idParam' => $effet->listeParametres[1]->id, 'position' => $position]]);
                        $assocParam2->valeur = $listeParam['param2']['value'];
                        $assocParam2->valeurMin = $listeParam['param2']['min'];
                        $assocParam2->valeurMax = $listeParam['param2']['max'];
                        $assocParam2->save();
                    } else {
                        if ($type == "competence") {
                            $assocParam1 = AssocCompetencesEffetsParam::findFirst(['idEffet = :idEffet: AND idCompetence = :idCompetence: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idEffet' => $effet->id, 'idCompetence' => $idType, 'idParam' => $effet->listeParametres[0]->id, 'position' => $position]]);
                            $assocParam1->valeur = $listeParam['param1']['value'];
                            $assocParam1->save();

                            $assocParam2 = AssocCompetencesEffetsParam::findFirst(['idEffet = :idEffet: AND idCompetence = :idCompetence: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idEffet' => $effet->id, 'idCompetence' => $idType, 'idParam' => $effet->listeParametres[1]->id, 'position' => $position]]);
                            $assocParam2->valeur = $listeParam['param2']['value'];
                            $assocParam2->valeurMin = $listeParam['param2']['min'];
                            $assocParam2->valeurMax = $listeParam['param2']['max'];
                            $assocParam2->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see FichierEffet::genererDescriptionEvaluee()
     */
    public function genererDescriptionEvaluee($effet, $element, $auth, $type, $mode = false, $modificateur = null) {
        $retour = "";

        $transformeFormule = Fonctions::transformerFormule($effet->listeParametres[1]->valeur, $mode);
        eval("\$result = $transformeFormule;");
        $carac = Caracteristiques::findFirst(['nom = :nom:', 'bind' => ['nom' => $effet->listeParametres[0]->valeur]]);
        if ($carac->genre == Constantes::FEMININ) {
            $retour .= "Modifie la " . $carac->nom . " ";
        } else {
            if ($carac->genre == Constantes::MASCULIN) {
                $retour .= "Modifie le " . $carac->nom . " ";
            } else {
                if ($carac->genre == Constantes::NEUTRE) {
                    $retour .= "Modifie l' " . $carac->nom . " ";
                }
            }
        }
        if ($modificateur != null) {
            $result = $result + $modificateur;
        }
        $retour .= " de " . $result;

        return $retour;
    }
}