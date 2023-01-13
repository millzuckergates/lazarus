<?php

/*
 * Bonus accordant un rang dans une compétence
*
* Nombre de paramètres : 2
* 	- La Compétence
* 	- Le Rang
*/

class Bonus_Competence implements FichierBonus {

    /**
     * {@inheritDoc}
     * @see FichierBonus::getDescriptionDetaillee()
     */
    public function getDescriptionDetaillee($bonus, $type) {
        $description = "";
        if ($type != "creation") {
            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
            $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[1]->valeur]]);
            $description .= "Confère le rang " . $rang->nom . " dans la compétence " . $competence->nom;
        }
        return $description;
    }

    /**
     * {@inheritDoc}
     * @see FichierBonus::genererDescription()
     */
    public function genererDescription($bonus) {
        $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
        $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[1]->valeur]]);
        $description = "Confère le rang " . $rang->nom . " dans la compétence " . $competence->nom;
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
            $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[0]->valeur]]);
            $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[1]->valeur]]);
            $template .= "<div id='divParam1'><b>Compétence </b> " . $competence->nom . "</br></div>";
            $template .= "<div id='divParam2'><b>Rang </b>" . $rang->nom;
        } else {
            if ($type == "edition") {
                $listeParametre = $bonus->listeParametres;
                $competenceSelect = Competences::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[0]->valeur]]);
                $rangSelect = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $listeParametre[1]->valeur]]);
                $template .= "<div id='divParam1'><b>Compétence </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez une compétence</option>";
                $listeCompetences = Competences::find(['order' => 'nom']);
                if ($listeCompetences != false && count($listeCompetences) > 0) {
                    foreach ($listeCompetences as $competence) {
                        if ($competence->id == $competenceSelect->id) {
                            $template .= "<option value='" . $competence->id . "' selected>" . $competence->nom . "</option>";
                        } else {
                            $template .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
                        }
                    }
                }
                $template .= "</select></div>";

                $template .= "<div id='divParam2'><b>Rang </b><select id='param2' style='margin-left: 20px;'><option value='0'>Sélectionnez un rang</option>";
                $listeRangs = RangsCompetence::find(['order' => 'niveau']);
                if ($listeRangs != false && count($listeRangs) > 0) {
                    foreach ($listeRangs as $rang) {
                        if ($rang->id == $rangSelect->id) {
                            $template .= "<option value='" . $rang->id . "' selected>" . $rang->nom . "</option>";
                        } else {
                            $template .= "<option value='" . $rang->id . "'>" . $rang->nom . "</option>";
                        }
                    }
                }
                $template .= "</select></div>";
            } else {
                if ($type == "creation") {
                    $template .= "<div id='divParam1'><b>Compétence </b><select id='param1' style='margin-left: 20px;'><option value='0'>Sélectionnez une compétence</option>";
                    $listeCompetences = Competences::find(['order' => 'nom']);
                    if ($listeCompetences != false && count($listeCompetences) > 0) {
                        foreach ($listeCompetences as $competence) {
                            $template .= "<option value='" . $competence->id . "'>" . $competence->nom . "</option>";
                        }
                    }
                    $template .= "</select></div>";

                    $template .= "<div id='divParam2'><b>Rang </b><select id='param2' style='margin-left: 20px;'><option value='0'>Sélectionnez un rang</option>";
                    $listeRangs = RangsCompetence::find(['order' => 'niveau']);
                    if ($listeRangs != false && count($listeRangs) > 0) {
                        foreach ($listeRangs as $rang) {
                            $template .= "<option value='" . $rang->id . "'>" . $rang->nom . "</option>";
                        }
                    }
                    $template .= "</select></div>";
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
            return "Erreur : Vous devez sélectionner une compétence.";
        }
        if ($listeParams[2] == null || $listeParams[2] == "" || $listeParams[2] == "0") {
            return "Erreur : Vous devez sélectionner un rang.";
        }

        //Gestion des tests
        $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $listeParams[1]]]);
        if ($competence == false) {
            return "Erreur : Compétence non trouvée.";
        }

        $rang = RangsCompetence::findFirst(['id = :id:', 'bind' => ['id' => $listeParams[2]]]);
        if ($rang == false) {
            return "Erreur : Rang non trouvé.";
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
        $competence = Competences::findFirst(['id = :id:', 'bind' => ['id' => $bonus->listeParametres[0]->valeur]]);
        if ($competence != false) {
            $image = Phalcon\Tag::image([$competence->image, "class" => 'imageBonus', 'id' => 'imageBonus']);
        }
        return $image;
    }
}