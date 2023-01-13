<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

/**
 * Types d'Autorisations
 */
define('AUTORISATION_GENERAL', "Général");
define('AUTORISATION_ADMINISTRAION', "Administration");
define('AUTORISATION_CARTE_MJ', "Carte MJ");
define('AUTORISATION_DROITS', "Droits");
define('AUTORISATION_GAMEPLAY', "Gameplay");
define('AUTORISATION_GESTION_PERSONNAGE', "Gestion Personnages");
define('AUTORISATION_WIKI', "Wiki");

class Autorisations extends \Phalcon\Mvc\Model {


    //Autorisation du wiki
    const CONSULTATION_WIKI = "consultation_wiki";
    const GESTION_WIKI = "gestion_wiki";
    const ADMINISTRATION_WIKI = "administration_wiki";
    const UPLOAD_FICHIER_WIKI = "uploadfichier_wiki";

    //Autorisation administration
    const ADMINISTRATION_DROITS_CONSULTATION = "administration_droits_consultation";
    const ADMINISTRATION_DROITS_MODIFICATION = "administration_droits_modification";
    const ADMINISTRATION_REFERENTIELS = "administration_referentiels";
    const ADMINISTRATION_STATISTIQUES = "administration_statistiques";
    const ADMINISTRATION_PROFIL_TEST = "administration_profil_test";
    const ADMINISTRATION_LOGS_MJ_CONSULTATION = "administration_logs_mj_consultation";
    const ADMINISTRATION_LOGS_ADMIN_CONSULTATION = "administration_logs_admin_consultation";
    const ADMINISTRATION_LOGS_DEV_CONSULTATION = "administration_logs_dev_consultation";
    const ADMINISTRATION_GESTION_IMAGE = "administration_gestion_image";
    const ADMINISTRATION_GESTION_GIF = "administration_gestion_gif";
    const ADMINISTRATION_CONSULTATION_ARCHIVE_LOG = "consultation_archive_logs";
    const ADMINISTRATION_FORMULAIRES_CONSULTATION = "consultation_formulaires_administration";
    const ADMINISTRATION_FORMULAIRES_GERER = "gest_formulaires_administration";
    const ADMINISTRATION_QUESTIONNAIRE_INSCRIPTION_CONSULTATION = "consultation_questionnaire_inscription";
    const ADMINISTRATION_QUESTIONNAIRE_INSCRIPTION_MODIFIER = "modifier_questionnaire_inscription";
    const ADMINISTRATION_ACCES_INTERFACE_MJ = "acces_interface_mj";
    const ADMINISTRATION_GESTION_NEWS = "gestion_news";

    //Autorisation gameplay
    const GESTION_GAMEPLAY_TERRAINS = "gameplay_terrains";
    const GESTION_GAMEPLAY_CARACS = "gameplay_caracs";
    const GAMEPLAY_CREATION_CARAC = "gameplay_creation_carac";
    const GAMEPLAY_GESTION_CARAC_PRIMAIRE = "gameplay_gestion_carac_primaire";
    const GAMEPLAY_GESTION_MAGIE_CONSULTER = "gameplay_gestion_magie_consulter";
    const GAMEPLAY_GESTION_MAGIE_MODIFIER = "gameplay_gestion_magie_modifier";
    const GAMEPLAY_GESTION_TALENT_CONSULTER = "gameplay_gestion_talent_consulter";
    const GAMEPLAY_GESTION_TALENT_MODIFIER = "gameplay_gestion_talent_modifier";
    const GAMEPLAY_GESTION_COMPETENCE_CONSULTER = "gameplay_gestion_competence_consulter";
    const GAMEPLAY_GESTION_COMPETENCE_MODIFIER = "gameplay_gestion_competence_modifier";
    const GAMEPLAY_GESTION_EQUIPEMENT_CONSULTER = "gameplay_gestion_equipement_consulter";
    const GAMEPLAY_GESTION_EQUIPEMENT_MODIFIER = "gameplay_gestion_equipement_modifier";
    const GAMEPLAY_GESTION_CREATURE_CONSULTER = "gameplay_gestion_creature_consulter";
    const GAMEPLAY_GESTION_CREATURE_MODIFIER = "gameplay_gestion_creature_modifier";
    const GESTION_GAMEPLAY_CARTES_CONSULTER = "gameplay_cartes_consulter";
    const GESTION_GAMEPLAY_CARTES_MODIFIER = "gameplay_cartes_modifier";

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
     * @Column(column="libelle", type="string", length=80, nullable=false)
     */
    public $libelle;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=100, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(column="nomTechnique", type="string", length=80, nullable=false)
     */
    public $nomTechnique;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("autorisations");

        //Init jointure
        $this->hasMany('id', 'AssocDroitAutorisation', 'idAutorisation', ['alias' => 'assoc_droit_autorisation']);
        $this->hasManyToMany('id', 'AssocDroitAutorisation', 'idAutorisation', 'idDroit', 'Droits', 'id', ['alias' => 'autorisations']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Autorisations[]|Autorisations|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Autorisations|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de savoir si le personnage est autorisé à accéder à cette page
     * @param string $autorisation
     * @param array $autorisations
     * @return boolean
     */
    public static function hasAutorisation($autorisation, $autorisations) {
        if (!empty($autorisations) && $autorisations != null && count($autorisations) > 0) {
            return in_array($autorisation, $autorisations);
        } else {
            return false;
        }
    }

}
