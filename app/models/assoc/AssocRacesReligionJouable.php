<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocRacesReligionJouable extends \Phalcon\Mvc\Model {
    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idRace", type="integer", length=11, nullable=false)
     */
    public $idRace;
    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idReligion", type="integer", length=11, nullable=false)
     */
    public $idReligion;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_races_religion_jouable");

        //Init Jointure
        $this->belongsTo('idRace', 'Races', 'id', ['alias' => 'races']);
        $this->belongsTo('idReligion', 'Religions', 'id', ['alias' => 'religions']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRacesReligionJouable[]|AssocRacesReligionJouable|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRacesReligionJouable|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }
}