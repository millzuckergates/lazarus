<?php

/*
 * Bonus accordant un nombre de point dans un talent
*
* Nombre de paramètres : 2
* 	- Le Talent
* 	- Le Nombre de point
*/

class Bonus_Talent implements FichierBonus {

    /**
     * {@inheritDoc}
     * @see FichierBonus::getDescriptionDetaillee()
     */
    public function getDescriptionDetaillee($bonus, $type) {
        $description = "";
        if ($type != "creation") {
            $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
            $nbPoint = $bonus->listeParametres[1]->valeur;
            $description .= "Place " . $nbPoint . " point dans le talent " . $talent->nom;
        }
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierBonus::genererDescription()
     */
    public function genererDescription($bonus) {
        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
        $nbPoint = $bonus->listeParametres[1]->valeur;
        $description = "Place " . $nbPoint . " point dans le talent " . $talent->nom;
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
            $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[0]->valeur]]);
            $nbPoint = $listeParametre[1]->valeur;
            $template .= "<div id='divParam1'><b>Talent </b> " . $talent->nom . "</br></div>";
            $template .= "<div id='divParam2'><b>Nombre de point </b>" . $nbPoint;
        } else {
            if ($type == "edition") {
                $listeParametre = $bonus->listeParametres;
                $talentSelect = Talents::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[0]->valeur]]);
                $nbPoint = $listeParametre[1]->valeur;
                $template .= "<div id='divParam1'><b>Talent </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez un talent</option>";
                $listeTalents = Talents::find(['order' => 'nom, rang']);
                if ($listeTalents != false && count($listeTalents) > 0) {
                    foreach ($listeTalents as $talent) {
                        if ($talent->id == $talentSelect->id) {
                            $template .= "<option value='" . $talent->id . "' selected>" . $talent->nom . "</option>";
                        } else {
                            $template .= "<option value='" . $talent->id . "'>" . $talent->nom . "</option>";
                        }
                    }
                }
                $template .= "</select></div>";

                $template .= "<div id='divParam2'><b>Nombre de point </b><input type='text' id='param2' value='" . $listeParametre[1]->valeur . "' style='text-align:right;' size=2/>";
                $template .= "</div>";
            } else {
                if ($type == "creation") {
                    $listeParametre = $bonus->listeParametres;
                    $template .= "<div id='divParam1'><b>Talent </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez un talent</option>";
                    $listeTalents = Talents::find(['order' => 'nom, rang']);
                    if ($listeTalents != false && count($listeTalents) > 0) {
                        foreach ($listeTalents as $talent) {
                            $template .= "<option value='" . $talent->id . "'>" . $talent->nom . "</option>";
                        }
                    }
                    $template .= "</select></div>";

                    $template .= "<div id='divParam2'><b>Nombre de point </b><input type='text' id='param2' value='1' style='text-align:right;' size=2/>";
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
            return "Erreur : Vous devez sélectionner un talent.";
        }
        if ($listeParams[2] == null || $listeParams[2] == "" || $listeParams[2] == "~") {
            return "Erreur : Vous devez renseigner un nombre de point.";
        }

        //Gestion des tests
        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $listeParams[1]]]);
        if ($talent == false) {
            return "Erreur : Talent non trouvé.";
        }

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
     * {@inheritDoc}
     * @see FichierBonus::genererImage()
     */
    public function genererImage($bonus) {
        $image = Phalcon\Tag::image([$bonus->image, "class" => 'imageBonus', 'id' => 'imageBonus']);
        $talent = Talents::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
        if ($talent != false) {
            $image = Phalcon\Tag::image([$talent->image, "class" => 'imageBonus', 'id' => 'imageBonus']);
        }
        return $image;
    }
}
