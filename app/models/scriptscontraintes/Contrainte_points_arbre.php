<?php

/*
* Contrainte permettant d'imposer de distribuer un nombre de point minimum
* dans un arbre de talent pour pouvoir être débloquée.
*
* Nombre de paramètres : 2
* 	- L'id de l'Arbre à vérifier
* 	- La formule pour modifier cette caractéristique
*/

class Contrainte_points_arbre implements FichierContrainte {


    /**
     * {@inheritDoc}
     * @see FichierContrainte::getDescriptionDetaillee()
     */
    public function getDescriptionDetaillee($contrainte, $type) {
        $description = "";
        if ($type != "creation") {
            $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $contrainte->listeParametres[0]->valeur]]);
            $description .= "<b>Arbre </b> " . $arbre->nom . "</br>";
            $description .= "<b>Nombre de point nécessaire </b>" . $contrainte->listeParametres[1]->valeur;
        }
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierContrainte::genererDescription()
     */
    public function genererDescription($contrainte) {
        $retour = "";
        $action = null;
        if (isset($contrainte->listeParametres[0]->action)) {
            $action = $contrainte->listeParametres[0]->action;
        }
        //On récupère l'arbre
        $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $contrainte->listeParametres[0]->valeur]]);
        $valeur = $contrainte->listeParametres[1]->valeur;

        $retour = "Requiert " . $valeur . " points dans " . $arbre->nom;
        return $retour;
    }

    /**
     * {@inheritDoc}
     * @see FichierContrainte::getTemplate()
     */
    public function getTemplate($contrainte, $type) {
        $template = "";
        $template .= "<input type='hidden' id='nbParam' value='2'/>";
        if ($type == "consultation") {
            $listeParametre = $contrainte->listeParametres;
            $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $contrainte->listeParametres[0]->valeur]]);
            $template .= "<div id='divParam1'><b>Arbre </b> " . $arbre->nom . "</br></div>";
            $template .= "<div id='divParam2'><b>Nombre de point nécessaire </b>" . $listeParametre[1]->valeur;
        } else {
            if ($type == "edition") {
                $listeParametre = $contrainte->listeParametres;
                $template .= "<div id='divParam1'><b>Arbre </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez un arbre</option>";
                $listeArbre = ArbresTalent::find();
                if ($listeArbre != false && count($listeArbre) > 0) {
                    foreach ($listeArbre as $arbre) {
                        if ($arbre->id == $listeParametre[0]->valeur) {
                            $template .= "<option value='" . $arbre->id . "' selected>" . $arbre->nom . "</option>";
                        } else {
                            $template .= "<option value='" . $arbre->id . "'>" . $arbre->nom . "</option>";
                        }
                    }
                }
                $template .= "</select></div>";
                $template .= "<div id='divParam2' style='margin-top:8px;'><b>Nombre de point nécessaire </b><input type='text' id='param2' size=3 style='margin-left: 56px;' site='3' value='" . $listeParametre[1]->valeur . "'/></div>";
            } else {
                if ($type == "creation") {
                    $template .= "<div id='divParam1'><b>Arbre </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez un arbre</option>";
                    $listeArbre = ArbresTalent::find();
                    if ($listeArbre != false && count($listeArbre) > 0) {
                        foreach ($listeArbre as $arbre) {
                            $template .= "<option value='" . $arbre->id . "'>" . $arbre->nom . "</option>";
                        }
                    }
                    $template .= "</select></div>";
                    $template .= "<div id='divParam2' style='margin-top:8px;'><b>Nombre de point nécessaire </b><input type='text' id='param2' size=3 style='margin-left: 56px;' site='3' value='0'/></div>";
                }
            }
        }
        return $template;
    }

    /**
     * {@inheritDoc}
     * @see FichiersContrainte::testerParametres()
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
            return "Erreur : Vous devez sélectionner un arbre.";
        }

        if ($listeParams[4] == null || $listeParams[4] == "" || $listeParams[4] == "~") {
            return "Erreur : Vous devez entrer un nombre de point.";
        }

        if (!is_numeric($listeParams[4]) || $listeParams[4] <= 0) {
            return "Erreur : le nombre de point doit être un entier position.";
        }

        //Construction de la liste de retour
        $finalListe = array();
        $finalListe['param1']['value'] = $listeParams[1];
        $finalListe['param2']['value'] = $listeParams[4];
        return $finalListe;
    }

    /**
     * {@inheritDoc}
     * @see FichiersContrainte::insertAssoc()
     */
    public function insertAssoc($listeParam, $type, $idType, $contrainte, $action) {
        $position = $contrainte->getNewPositionContrainte($type, $idType);
        if ($type == "talent") {
            $assocParam1 = new AssocTalentsContraintesParam();
            $assocParam2 = new AssocTalentsContraintesParam();
            $assocParam1->idTalent = $idType;
            $assocParam2->idTalent = $idType;
        } else {
            if ($type == "familleTalent") {
                $assocParam1 = new AssocFamilletalentsContraintesParam();
                $assocParam2 = new AssocFamilletalentsContraintesParam();
                $assocParam1->idFamille = $idType;
                $assocParam2->idFamille = $idType;
            } else {
                if ($type == "arbreTalent") {
                    $assocParam1 = new AssocArbretalentsContraintesParam();
                    $assocParam2 = new AssocArbretalentsContraintesParam();
                    $assocParam1->idArbre = $idType;
                    $assocParam2->idArbre = $idType;
                } else {
                    if ($type == "sort") {
                        $assocParam1 = new AssocSortsContraintesParam();
                        $assocParam2 = new AssocSortsContraintesParam();
                        $assocParam1->idSort = $idType;
                        $assocParam2->idSort = $idType;
                    } else {
                        if ($type == "competence") {
                            $assocParam1 = new AssocCompetencesContraintesParam();
                            $assocParam2 = new AssocCompetencesContraintesParam();
                            $assocParam1->idCompetence = $idType;
                            $assocParam2->idCompetence = $idType;
                        }
                    }
                }
            }
        }
        $assocParam1->idContrainte = $contrainte->id;
        $assocParam1->idParam = $contrainte->listeParametres[0]->idParam;
        $assocParam1->valeur = $listeParam['param1']['value'];
        $assocParam1->position = $position;
        $assocParam1->save();
        $assocParam2->idContrainte = $contrainte->id;
        $assocParam2->idParam = $contrainte->listeParametres[1]->idParam;
        $assocParam2->valeur = $listeParam['param2']['value'];
        $assocParam2->position = $position;
        $assocParam2->save();
    }

    /**
     * {@inheritDoc}
     * @see FichiersContrainte::insertAssoc()
     */
    public function modifierAssoc($listeParam, $type, $idType, $contrainte, $position) {
        if ($type == "talent") {
            $assocParam1 = AssocTalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idTalent = :idTalent: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idContrainte' => $contrainte->id, 'idTalent' => $idType, 'idParam' => $contrainte->listeParametres[0]->id, 'position' => $position]]);

            $assocParam2 = AssocTalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idTalent = :idTalent: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idContrainte' => $contrainte->id, 'idTalent' => $idType, 'idParam' => $contrainte->listeParametres[1]->id, 'position' => $position]]);
        } else {
            if ($type == "familleTalent") {
                $assocParam1 = AssocFamilletalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idFamille = :idFamille: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idContrainte' => $contrainte->id, 'idFamille' => $idType, 'idParam' => $contrainte->listeParametres[0]->id, 'position' => $position]]);

                $assocParam2 = AssocFamilletalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idFamille = :idFamille: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idContrainte' => $contrainte->id, 'idFamille' => $idType, 'idParam' => $contrainte->listeParametres[1]->id, 'position' => $position]]);
            } else {
                if ($type == "arbreTalent") {
                    $assocParam1 = AssocArbretalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idArbre = :idArbre: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idContrainte' => $contrainte->id, 'idArbre' => $idType, 'idParam' => $contrainte->listeParametres[0]->id, 'position' => $position]]);

                    $assocParam2 = AssocArbretalentsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idArbre = :idArbre: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idContrainte' => $contrainte->id, 'idArbre' => $idType, 'idParam' => $contrainte->listeParametres[1]->id, 'position' => $position]]);
                } else {
                    if ($type == "sort") {
                        $assocParam1 = AssocSortsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idSort = :idSort: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idContrainte' => $contrainte->id, 'idSort' => $idType, 'idParam' => $contrainte->listeParametres[0]->id, 'position' => $position]]);

                        $assocParam2 = AssocSortsContraintesParam::findFirst(['idContrainte = :idContrainte: AND idSort = :idSort: AND idParam = :idParam: AND position = :position:',
                          'bind' => ['idContrainte' => $contrainte->id, 'idSort' => $idType, 'idParam' => $contrainte->listeParametres[1]->id, 'position' => $position]]);
                    } else {
                        if ($type == "competence") {
                            $assocParam1 = AssocCompetencesContraintesParam::findFirst(['idContrainte = :idContrainte: AND idCompetence = :idCompetence: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idContrainte' => $contrainte->id, 'idCompetence' => $idType, 'idParam' => $contrainte->listeParametres[0]->id, 'position' => $position]]);

                            $assocParam2 = AssocCompetencesContraintesParam::findFirst(['idContrainte = :idContrainte: AND idCompetence = :idCompetence: AND idParam = :idParam: AND position = :position:',
                              'bind' => ['idContrainte' => $contrainte->id, 'idCompetence' => $idType, 'idParam' => $contrainte->listeParametres[1]->id, 'position' => $position]]);
                        }
                    }
                }
            }
        }
        $assocParam1->valeur = $listeParam['param1']['value'];
        $assocParam1->save();
        $assocParam2->valeur = $listeParam['param2']['value'];
        $assocParam2->save();
    }

    /**
     * {@inheritDoc}
     * @see FichiersContrainte::verif()
     */
    public function verif($mode, $auth, $cible, $contrainte) {
        $arbre = ArbresTalent::findFirst(['id = :id:', 'bind' => ['id' => $contrainte->listeParametres[0]->valeur]]);
        if ($mode == "admin") {
            $nbPoint = $arbre->getNombrePointSimulation($auth['simulationTalent']['listeTalents']);
            if ($nbPoint >= $contrainte->listeParametres[1]->valeur) {
                return true;
            } else {
                return false;
            }
        } else {
            $perso = $auth['perso'];
            $nbPoint = $arbre->getNombrePointPerso($perso);
            if ($nbPoint >= $contrainte->listeParametres[1]->valeur) {
                return true;
            } else {
                return false;
            }
        }
    }
}