<?php

/*
 * Bonus accordant un choix de talent et l'association
* d'un rang
* Nombre de paramètres : 2
* 	- La liste des talents à choisir
* 	- Le nombre de point
*/

class Bonus_Choix_Talent implements FichierBonus {

    /**
     * {@inheritDoc}
     * @see FichierBonus::getDescriptionDetaillee()
     */
    public function getDescriptionDetaillee($bonus, $type) {
        $description = "";
        if ($type != "creation") {
            $arrayTalents = explode(",", $bonus->listeParametres[0]->valeur);
            $listeTalents = "";
            $first = true;
            if (count($arrayTalents) > 0) {
                for ($i = 0; $i < count($arrayTalents); $i++) {
                    $idTalent = $arrayTalents[$i];
                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                    if ($talent != false) {
                        if ($first) {
                            $first = false;
                            $listeTalents .= $talent->nom;
                        } else {
                            $listeTalents .= ", " . $talent->nom;
                        }
                    }
                }
            }
            $nbPoint = $bonus->listeParametres[1]->valeur;
            $description .= "Confère " . $nbPoint . " point(s) à un talent au choix parmi " . $listeTalents;
        }
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierBonus::genererDescription()
     */
    public function genererDescription($bonus) {
        $arrayTalents = explode(",", $bonus->listeParametres[0]->valeur);
        $listeTalents = "";
        $first = true;
        if (count($arrayTalents) > 0) {
            for ($i = 0; $i < count($arrayTalents); $i++) {
                $idTalent = $arrayTalents[$i];
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                if ($talent != false) {
                    if ($first) {
                        $first = false;
                        $listeTalents .= $talent->nom;
                    } else {
                        $listeTalents .= ", " . $talent->nom;
                    }
                }
            }
        }
        $nbPoint = $bonus->listeParametres[1]->valeur;
        $description = "Confère " . $nbPoint . " point(s) à un talent au choix parmi " . $listeTalents;
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierBonus::getTemplate()
     */
    public function getTemplate($bonus, $type) {
        $template = "";
        $template .= "<input type='hidden' id='nbParam' value='2'/>";
        if ($type == "consultation") {
            $listeParametre = $bonus->listeParametres;

            $template .= "<div id='divParam1'><b>Choix d'un talent parmi :</b>";
            $arrayTalents = explode(",", $bonus->listeParametres[0]->valeur);
            $template .= "<ul>";
            if (count($arrayTalents) > 0) {
                for ($i = 0; $i < count($arrayTalents); $i++) {
                    $idTalent = $arrayTalents[$i];
                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                    if ($talent != false) {
                        $template .= "<li>" . $talent->nom . "</li>";
                    }
                }
            }
            $template .= "</ul>";
            $template .= "</div>";
            $nbPoint = $listeParametre[1]->valeur;
            $template .= "<div id='divParam2'><b>Nombre de point </b>" . $nbPoint;
        } else {
            if ($type == "edition") {
                $listeParametre = $bonus->listeParametres;
                $template .= "<input type='hidden' id='param1' value='" . $listeParametre[0]->valeur . "'/>";
                $template .= "<div id='divParam1'><b>Choix d'un talent parmi :</b>";
                $arrayTalents = explode(",", $listeParametre[0]->valeur);
                $listeExclude = array();
                $template .= "<ul>";
                if (count($arrayTalents) > 0) {
                    for ($i = 0; $i < count($arrayTalents); $i++) {
                        $idTalent = $arrayTalents[$i];
                        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                        if ($talent != false) {
                            $template .= "<li>";
                            $template .= "<div class='alignementTexteBouton'>";
                            $template .= "<span>" . $talent->nom . "</span>";
                            $template .= "<input type='button' class='buttonMoins' onclick='enleverElementBonus(" . $talent->id . ");'/>";
                            $template .= "</div></li>";
                            $listeExclude[count($listeExclude)] = $talent->id;
                        }
                    }
                }
                $template .= "</ul>";
                $template .= "<div id='divSelectTalentBonus'>";
                $template .= "<label for='spanSelectTalentBonus'>Ajouter un Talent </label>";
                $template .= "<span id='spanSelectTalentBonus'>" . Talents::getSelectTalent($listeExclude) . "</span>";
                $template .= "<input type='button' class='buttonPlus' onclick='ajouterElementBonus();'/>";
                $template .= "</div>";
                $template .= "<br/>";
                $nbPoint = $listeParametre[1]->valeur;
                $template .= "<div id='divParam2'><b>Nombre de point </b><input id='param2' style='margin-left: 20px;' size=2 value='" . $nbPoint . "'/>";
                $template .= "</div>";
            } else {
                if ($type == "creation") {
                    $template .= "<input type='hidden' id='param1' value=''/>";
                    $template .= "<div id='divParam1'><b>Choix d'un talent parmi :</b>";
                    $template .= "<div id='divSelectTalentBonus'>";
                    $template .= "<label for='spanSelectTalentBonus'>Ajouter un Talent </label>";
                    $template .= "<span id='spanSelectTalentBonus'>" . Talents::getSelectTalent() . "</span>";
                    $template .= "<input type='button' class='buttonPlus' onclick='ajouterElementBonus();'/>";
                    $template .= "</div>";
                    $template .= "<br/>";
                    $template .= "<div id='divParam2'><b>Nombre de point </b><input id='param2' style='margin-left: 20px;' size=2 value='1'/>";
                    $template .= "</div>";
                }
            }
        }
        return $template;
    }


    /**
     * {@inheritDoc}
     * @see FichiersBonus::testerParametres()
     */
    public function testerParametres($listeParametres) {
        if ($listeParametres == null || $listeParametres == "") {
            return "Erreur : Vous devez renseigner les paramètres.";
        }
        //On découpe la liste
        $listeParams = explode("|", $listeParametres);
        //On vérifie les valeurs obligatoires
        if (count($listeParams) != 3) {
            return "Erreur : Problème technique.";
        }
        // Les paramètres 1 et 2 sont obligatoires
        if ($listeParams[1] == null || $listeParams[1] == "" || $listeParams[1] == "0") {
            return "Erreur : Vous devez sélectionner au moins un talent.";
        }
        if ($listeParams[2] == null || $listeParams[2] == "" || $listeParams[2] == "~") {
            return "Erreur : Vous devez renseigner un nombre de point.";
        }

        //Gestion des tests
        if (is_numeric($listeParams[2]) == false || $listeParams[2] <= 0) {
            return "Erreur : Le nombre de point doit être un entier strictement supérieur à 0.";
        }


        //Construction de la liste de retour
        $finalListe = array();
        $finalListe['param1']['value'] = $listeParams[1];
        $finalListe['param2']['value'] = $listeParams[2];
        return $finalListe;
    }

    /**
     * {@inheritDoc}
     * @see FichiersBonus::insertAssoc()
     */
    public function insertAssoc($listeParam, $type, $idType, $bonus) {
        $position = $bonus->getNewPosition($type, $idType);
        if ($type == "royaume") {
            $assocParam1 = new AssocRoyaumesBonusParam();
            $assocParam1->idRoyaume = $idType;
            $assocParam2 = new AssocRoyaumesBonusParam();
            $assocParam2->idRoyaume = $idType;
        } else {
            if ($type == "race") {
                $assocParam1 = new AssocRacesBonusParam();
                $assocParam1->idRace = $idType;
                $assocParam2 = new AssocRacesBonusParam();
                $assocParam2->idRace = $idType;
            } else {
                if ($type == "religion") {
                    $assocParam1 = new AssocReligionsBonusParam();
                    $assocParam1->idReligion = $idType;
                    $assocParam2 = new AssocReligionsBonusParam();
                    $assocParam2->idReligion = $idType;
                }
            }
        }
        $assocParam1->idBonus = $bonus->id;
        $assocParam1->idParam = $bonus->listeParametres[0]->idParam;
        $assocParam1->valeur = $listeParam['param1']['value'];
        $assocParam1->position = $position;
        $assocParam1->save();

        $assocParam2->idBonus = $bonus->id;
        $assocParam2->idParam = $bonus->listeParametres[1]->idParam;
        $assocParam2->valeur = $listeParam['param2']['value'];
        $assocParam2->position = $position;
        $assocParam2->save();
    }

    /**
     * {@inheritDoc}
     * @see FichiersBonus::insertAssoc()
     */
    public function modifierAssoc($listeParam, $type, $idType, $bonus, $position) {
        if ($type == "royaume") {
            $assocParam1 = AssocRoyaumesBonusParam::findFirst(['idBonus = :idBonus: AND idRoyaume = :idRoyaume: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idBonus' => $bonus->id, 'idRoyaume' => $idType, 'idParam' => $bonus->listeParametres[0]->id, 'position' => $position]]);
            $assocParam2 = AssocRoyaumesBonusParam::findFirst(['idBonus = :idBonus: AND idRoyaume = :idRoyaume: AND idParam = :idParam: AND position = :position:',
              'bind' => ['idBonus' => $bonus->id, 'idRoyaume' => $idType, 'idParam' => $bonus->listeParametres[1]->id, 'position' => $position]]);

        } else {
            if ($type == "race") {
                $assocParam1 = AssocRacesBonusParam::findFirst(['idBonus = :idBonus: AND idRace = :idRace: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idBonus' => $bonus->id, 'idRace' => $idType, 'idParam' => $bonus->listeParametres[0]->id, 'position' => $position]]);
                $assocParam2 = AssocRacesBonusParam::findFirst(['idBonus = :idBonus: AND idRace = :idRace: AND idParam = :idParam: AND position = :position:',
                  'bind' => ['idBonus' => $bonus->id, 'idRace' => $idType, 'idParam' => $bonus->listeParametres[1]->id, 'position' => $position]]);
            } else {
                if ($type == "religion") {
                    $assocParam1 = AssocReligionsBonusParam::findFirst(['idBonus = :idBonus: AND idReligion = :idReligion: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idBonus' => $bonus->id, 'idReligion' => $idType, 'idParam' => $bonus->listeParametres[0]->id, 'position' => $position]]);
                    $assocParam2 = AssocReligionsBonusParam::findFirst(['idBonus = :idBonus: AND idReligion = :idReligion: AND idParam = :idParam: AND position = :position:',
                      'bind' => ['idBonus' => $bonus->id, 'idReligion' => $idType, 'idParam' => $bonus->listeParametres[1]->id, 'position' => $position]]);
                }
            }
        }
        $assocParam1->valeur = $listeParam['param1']['value'];
        $assocParam1->save();
        $assocParam2->valeur = $listeParam['param2']['value'];
        $assocParam2->save();
    }

    /**
     * Permet de générer une image
     * {@inheritDoc}
     * @see FichierBonus::genererImage()
     */
    public function genererImage($bonus) {
        $image = Phalcon\Tag::image([$bonus->image, "class" => 'imageBonus', 'id' => 'imageBonus']);
        return $image;
    }

    public function ajouterChoixMultiple($param1, $param2, $idElement) {
        $param1 = $param1 . "," . $idElement;
        $template = "<input type='hidden' id='nbParam' value='2'/>";
        $template .= "<input type='hidden' id='param1' value='" . $param1 . "'/>";
        $template .= "<div id='divParam1'><b>Choix d'un talent parmi :</b>";
        $arrayTalents = explode(",", $param1);
        $listeExclude = array();
        $template .= "<ul>";
        if (count($arrayTalents) > 0) {
            for ($i = 0; $i < count($arrayTalents); $i++) {
                $idTalent = $arrayTalents[$i];
                $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                if ($talent != false) {
                    $template .= "<li>";
                    $template .= "<div class='alignementTexteBouton'>";
                    $template .= "<span>" . $talent->nom . "</span>";
                    $template .= "<input type='button' class='buttonMoins' onclick='enleverElementBonus(" . $talent->id . ");'/>";
                    $template .= "</div></li>";
                    $listeExclude[count($listeExclude)] = $talent->id;
                }
            }
        }
        $template .= "</ul>";
        $template .= "<div id='divSelectTalentBonus'>";
        $template .= "<label for='spanSelectTalentBonus'>Ajouter un Talent </label>";
        $template .= "<span id='spanSelectTalentBonus'>" . Talents::getSelectTalent($listeExclude) . "</span>";
        $template .= "<input type='button' class='buttonPlus' onclick='ajouterElementBonus();'/>";
        $template .= "</div>";
        $template .= "<br/>";
        $template .= "<div id='divParam2'><b>Nombre de point </b><input id='param2' style='margin-left: 20px;' size=2 value='" . $param2 . "'/>";
        $template .= "</div>";
        return $template;
    }

    public function enleverChoixMultiple($param1, $param2, $idElement) {
        $listeExclude = array();
        $newParam1 = "";
        $first = true;
        $template = "<input type='hidden' id='nbParam' value='2'/>";
        $template .= "<div id='divParam1'><b>Choix d'un talent parmi :</b>";
        $arrayTalents = explode(",", $param1);
        $listeExclude = array();
        $template .= "<ul>";
        if (count($arrayTalents) > 0) {
            for ($i = 0; $i < count($arrayTalents); $i++) {
                $idTalent = $arrayTalents[$i];
                if ($idTalent != $idElement) {
                    $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $idTalent]]);
                    if ($talent != false) {
                        $template .= "<li>";
                        $template .= "<div class='alignementTexteBouton'>";
                        $template .= "<span>" . $talent->nom . "</span>";
                        $template .= "<input type='button' class='buttonMoins' onclick='enleverElementBonus(" . $talent->id . ");'/>";
                        $template .= "</div></li>";
                        $listeExclude[count($listeExclude)] = $talent->id;
                        if ($first) {
                            $first = false;
                            $newParam1 = $talent->id;
                        } else {
                            $newParam1 .= "," . $talent->id;
                        }
                    }
                }
            }
        }
        $template .= "</ul>";
        $template .= "<input type='hidden' id='param1' value='" . $newParam1 . "'/>";
        $template .= "<div id='divSelectTalentBonus'>";
        $template .= "<label for='spanSelectTalentBonus'>Ajouter un Talent </label>";
        $template .= "<span id='spanSelectTalentBonus'>" . Talents::getSelectTalent($listeExclude) . "</span>";
        $template .= "<input type='button' class='buttonPlus' onclick='ajouterElementBonus();'/>";
        $template .= "</div>";
        $template .= "<br/>";
        $template .= "<div id='divParam2'><b>Nombre de point </b><input id='param2' style='margin-left: 20px;' size=2 value='" . $param2 . "'/>";
        $template .= "</div>";
        return $template;
    }
}