<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Questionnaires extends \Phalcon\Mvc\Model {

    //Constantes pour les types de questionnaires
    const QUESTIONNAIRE_ROYAUMES = "royaumes";
    const QUESTIONNAIRE_RACES = "races";
    const QUESTIONNAIRE_RELIGIONS = "religions";
    const QUESTIONNAIRE_DIVINITES = "divinites";

    //Constante pour l'affichage des choix
    const QUESTIONNAIRE_CHOIX_A = "Réponse A";
    const QUESTIONNAIRE_CHOIX_B = "Réponse B";
    const QUESTIONNAIRE_CHOIX_C = "Réponse C";

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(column="question", type="string", length=500, nullable=false)
     */
    public $question;

    /**
     *
     * @var string
     * @Column(column="choixA", type="string", length=250, nullable=false)
     */
    public $choixA;

    /**
     *
     * @var string
     * @Column(column="choixB", type="string", length=250, nullable=false)
     */
    public $choixB;

    /**
     *
     * @var string
     * @Column(column="choixC", type="string", length=250, nullable=false)
     */
    public $choixC;

    /**
     *
     * @var string
     * @Column(column="reponse", type="string", length=10, nullable=false)
     */
    public $reponse;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=true)
     */
    public $idArticle;

    /**
     *
     * @var string
     * @Column(column="paragraphe", type="string", length=150, nullable=true)
     */
    public $paragraphe;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=90, nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(column="idType", type="integer", length=11, nullable=false)
     */
    public $idType;

    /**
     *
     * @var integer
     * @Column(column="isActif", type="integer", length=1, nullable=false)
     */
    public $isActif;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("questionnaires");

        //Init jointure
        $this->hasOne('idArticle', 'Articles', 'id', ['alias' => 'article']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Questionnaires[]|Questionnaires|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Questionnaires|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }


    /**
     * Construit le tableau des questions pour les formulaires
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $isActif
     * @param unknown $auth
     * @return string
     */
    public static function buildTableauQuestionnaire($type, $idType, $isActif, $auth) {
        $retour = "<table class='listeQuestionnaire'>";
        $autorisationEdition = false;
        if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_QUESTIONNAIRE_INSCRIPTION_MODIFIER, $auth['autorisations'])) {
            $autorisationEdition = true;
        }
        $listeQuestionnaires = Questionnaires::find(['type = :type: AND idType = :idType: AND isActif = :isActif:', 'bind' => ['type' => $type, 'idType' => $idType, 'isActif' => $isActif]]);
        if (!empty($listeQuestionnaires) && count($listeQuestionnaires) > 0) {
            $retour .= "<tr>
                    		<th style='width:40%;'>Question</th>
                    		<th class='tableauChoix' style='width:35%;'>Choix</th>
                    		<th style='width:15%;'>Réponse</th>";
            if ($autorisationEdition) {
                $retour .= "<th style='width:10%;'>&nbsp;</th>";
            }
            $retour .= "</tr>";
            $classe = 0;
            foreach ($listeQuestionnaires as $questionnaire) {
                $classe = $classe % 2;
                $retour .= "<tr class='ligne_" . $classe . "'>';
                        	<td>" . $questionnaire->question . "</td>
                        	<td class='tableauChoix'>Choix A : " . $questionnaire->choixA . "<br/>Choix B : " . $questionnaire->choixB . "<br/>Choix C : " . $questionnaire->choixC . "</td>
                        	<td>" . $questionnaire->reponse . "</td>";
                if ($autorisationEdition) {
                    $retour .= "<td class='boutonTableauQuestionnaire'>";
                    if ($isActif) {
                        $retour .= "<input type='button' class='buttonMoins' onclick='desactiverQuestionnaire(" . $questionnaire->id . ");' title='Permet de désactiver ce questionnaire'/>";
                    } else {
                        $retour .= "<input type='button' class='buttonPlus' onclick='activerQuestionnaire(" . $questionnaire->id . ");' title='Permet d\'activer ce questionnaire'/>";
                    }
                    $retour .= "<input type='button' class='buttonActualiser' onclick='afficherFormulaireQuestionnaire(" . $questionnaire->id . ");' title='Affiche la pop-up permettant de modifier ce questionnaire'/>";
                    $retour .= "<input type='button' class='boutonDelete' onclick='boxSupprimerQuestionnaire(" . $questionnaire->id . ");' title='Permet de supprimer ce questionnaire'/></td>";
                }
                $retour .= "</tr>";
                $classe++;
            }
        } else {
            $retour .= "<tr><td>Il n'y a aucun élément à afficher.</td></tr>";
        }
        $retour .= "</table>";
        return $retour;
    }

    /**
     * Permet de générer le titre pour la popup questionnaire
     * @param unknown $type
     * @param unknown $idType
     * @return string
     */
    public static function genererDetailTitre($type, $idType) {
        $retour = "Question ";
        if ($type == "race") {
            $race = Races::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
            $retour .= "pour la race " . $race->nom;
        } else {
            if ($type == "religion") {
                $religion = Religions::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                $retour .= "pour la religion " . $religion->nom;
            } else {
                if ($type == "royaume") {
                    $royaume = Royaumes::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                    $retour .= "pour le royaume " . $royaume->nom;
                } else {
                    if ($type == "divinite") {
                        $royaume = Dieux::findFirst(['id = :id:', 'bind' => ['id' => $idType]]);
                        $retour .= "pour le royaume " . $royaume->nom;
                    } else {
                        if ($type == "global") {
                            $retour .= "globale";
                        }
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Génère le select des listes de réponse
     * @param unknown $idSelect
     * @param unknown $onchange
     * @param unknown $select
     * @return string
     */
    public static function genererListeReponse($idSelect, $onchange, $select) {
        if ($onchange != null) {
            $retour = "<select id='" . $idSelect . "' onchange='" . $onchange . "'><option value='0'>Aucune</option>";
        } else {
            $retour = "<select id='" . $idSelect . "'><option value='0'>Aucune</option>";
        }
        $listeReponse = Questionnaires::getListeReponse();
        for ($i = 0; $i < count($listeReponse); $i++) {
            $reponse = $listeReponse[$i];
            if ($select == $reponse) {
                $retour .= "<option value='" . $reponse . "' selected>" . $reponse . "</option>";
            } else {
                $retour .= "<option value='" . $reponse . "'>" . $reponse . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    /**
     * Construit la liste des choix disponibles
     * @return string[]
     */
    public static function getListeReponse() {
        $retour = array();
        $retour[count($retour)] = Questionnaires::QUESTIONNAIRE_CHOIX_A;
        $retour[count($retour)] = Questionnaires::QUESTIONNAIRE_CHOIX_B;
        $retour[count($retour)] = Questionnaires::QUESTIONNAIRE_CHOIX_C;
        return $retour;
    }

    /**
     * Permet de générer le select de la liste des paragraphes d'un article
     * @param unknown $idSelect
     * @param unknown $idArticle
     * @param unknown $paragraphe
     * @return string
     */
    public static function genererListeParagraphe($idSelect, $idArticle, $select) {
        if ($idArticle == null) {
            $retour = "<select id='" . $idSelect . "'><option value='0'>Aucun</option></select>";
        } else {
            $retour = "<select id='" . $idSelect . "'><option value='0'>Aucun</option>";
            $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $idArticle]]);
            $listeParagraphe = $article->getListeParagraphe();
            if (!empty($listeParagraphe)) {
                for ($i = 0; $i < count($listeParagraphe); $i++) {
                    $paragraphe = $listeParagraphe[$i];
                    if ($select == $paragraphe) {
                        $retour .= "<option value='" . $paragraphe . "' selected>" . $paragraphe . "</option>";
                    } else {
                        $retour .= "<option value='" . $paragraphe . "'>" . $paragraphe . "</option>";
                    }
                }
            }
            $retour .= "</select>";
        }
        return $retour;
    }

    /**
     * Retourne le nombre de question correspondant aux trois paramètres
     * @param unknown $type
     * @param unknown $idType
     * @param unknown $isActif
     * @return number
     */
    public static function getQuestions($type, $idType, $isActif) {
        $listeQuestionnaires = Questionnaires::find(['type = :type: AND idType = :idType: AND isActif = :isActif:', 'bind' => ['type' => $type, 'idType' => $idType, 'isActif' => $isActif]]);
        return count($listeQuestionnaires);
    }

    /**
     * Retourne l'objet sous une chaine de caractère
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Question : " . $this->question . "], ";
        $retour .= "[Choix A : " . $this->choixA . "], ";
        $retour .= "[Choix B : " . $this->choixB . "], ";
        $retour .= "[Choix C : " . $this->choixC . "], ";
        $retour .= "[Réponse : " . $this->reponse . "], ";
        $retour .= "[IdArticle : " . $this->idArticle . "],";
        $retour .= "[Paragraphe : " . $this->paragraphe . "],";
        $retour .= "[Type : " . $this->type . "],";
        $retour .= "[IdType : " . $this->idType . "]";
        return $retour;
    }

}
