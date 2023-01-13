<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Personnages extends \Phalcon\Mvc\Model {

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
     * @Column(column="nom", type="string", length=60, nullable=false)
     */
    public $nom;

    /**
     *
     * @var integer
     * @Column(column="id_wp", type="integer", length=11, nullable=false)
     */
    public $id_wp;

    /**
     *
     * @var integer
     * @Column(column="idRoyaume", type="integer", length=11, nullable=false)
     */
    public $idRoyaume;

    /**
     *
     * @var integer
     * @Column(column="idRace", type="integer", length=11, nullable=false)
     */
    public $idRace;

    /**
     *
     * @var integer
     * @Column(column="idReligion", type="integer", length=11, nullable=false)
     */
    public $idReligion;

    /**
     *
     * @var integer
     * @Column(column="x", type="integer", length=11, nullable=false)
     */
    public $x;

    /**
     *
     * @var integer
     * @Column(column="y", type="integer", length=11, nullable=false)
     */
    public $y;

    /**
     *
     * @var string
     * @Column(column="orientation", type="string", length=2, nullable=false)
     */
    public $orientation;

    /**
     *
     * @var string
     * @Column(column="status", type="string", length=80, nullable=false)
     */
    public $status;

    /**
     *
     * @var integer
     * @Column(column="xp", type="integer", length=11, nullable=false)
     */
    public $xp;

    /**
     *
     * @var integer
     * @Column(column="pa", type="integer", length=2, nullable=false)
     */
    public $pa;

    /**
     *
     * @var integer
     * @Column(column="pp", type="integer", length=2, nullable=false)
     */
    public $pp;

    /**
     *
     * @var integer
     * @Column(column="mana", type="integer", length=3, nullable=false)
     */
    public $mana;

    /**
     *
     * @var integer
     * @Column(column="fatigue", type="integer", length=3, nullable=false)
     */
    public $fatigue;

    /**
     *
     * @var integer
     * @Column(column="pv", type="integer", length=5, nullable=false)
     */
    public $pv;

    /**
     *
     * @var string
     * @Column(column="pwd", type="string", length=40, nullable=false)
     */
    public $pwd;

    /**
     *
     * @var string
     * @Column(column="email", type="string", length=90, nullable=false)
     */
    public $email;

    /**
     *
     * @var integer
     * @Column(column="idDieu", type="integer", length=11, nullable=false)
     */
    public $idDieu;

    public $autorisations;
    public $listeTalents;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation() {
        $validator = new Validation();
        $validator->add(
          'email',
          new EmailValidator(
            [
              'model' => $this,
              'message' => 'Please enter a correct email address',
            ]
          )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("personnages");

        //Init jointure
        $this->hasMany('id', 'AssocPersonnageDroit', 'idPersonnage', ['alias' => 'assoc_personnage_droit']);
        $this->hasManyToMany('id', 'AssocPersonnageDroit', 'idPersonnage', 'idDroit', 'Droits', 'id', ['alias' => 'droits']);

        //Initialisation
        $this->init();
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Personnages[]|Personnages|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Personnages|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }


    /**
     * Permet de générer la liste des autorisations
     * @return NULL[]
     */
    public function genererAutorisation() {
        $autorisations = array();
        foreach ($this->droits as $droit) {
            foreach ($droit->autorisations as $autorisation) {
                $autorisations[count($autorisations)] = $autorisation->nomTechnique;
            }
        }
        return $autorisations;
    }

    /**
     * Permet de retourner le thème css du personnage
     * @param unknown $idPerso
     */
    public function getThemeCSS() {
        //TODO gérer les thèmes
        return "css/site/theme/default";
    }

    /**
     * Retourne l'image du piom
     * @param unknown $auth
     * @return string
     */
    public static function getImagePiom($auth) {
        if ($auth == null) {
            return 'img/site/themes/default/box/defaultboximg.png';
        } else {
            return 'img/site/themes/default/box/defaultboximg.png';
        }
    }

    /**
     * Méthode d'initalisation du personnage
     */
    public function init() {
        $this->chargeListeTalents();
    }

    /**
     * Charge dans l'objet la liste des talents du personnage
     */
    public function chargeListeTalents() {
        $this->listeTalents = array();
        //On récupère tous les talents
        $talents = Talents::find();
        if ($talents != false && count($talents) > 0) {
            foreach ($talents as $talent) {
                //On récupère l'association avec le personnage
                $assoc = AssocPersonnagesTalents::findFirst(['idPerso = :idPerso: AND idTalent = :idTalent:', 'bind' => ['idPerso' => $this->id, 'idTalent' => $talent->id]]);
                if ($assoc == false) {
                    $assoc = new AssocPersonnagesTalents();
                    $assoc->idPerso = $this->id;
                    $assoc->idTalent = $talent->id;
                    $assoc->niveau = 0;
                    $assoc->save();
                }
                $this->listeTalents[$talent->id] = $assoc->niveau;
            }
        }
    }

    public function inVille($idVille) {
        //TODO voir comment gérer la présence en ville
        // et si Maire/Gestionnaires, accès à la ville
        return true;
    }
}
