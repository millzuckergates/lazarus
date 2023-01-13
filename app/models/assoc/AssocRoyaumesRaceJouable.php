<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocRoyaumesRaceJouable extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idRoyaume", type="integer", length=11, nullable=false)
     */
    public $idRoyaume;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idRace", type="integer", length=11, nullable=false)
     */
    public $idRace;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_royaumes_race_jouable");

        //Init Jointure
        $this->belongsTo('idRoyaume', 'Royaumes', 'id', ['alias' => 'royaumes']);
        $this->belongsTo('idRace', 'Races', 'id', ['alias' => 'races']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRoyaumesRaceJouable[]|AssocRoyaumesRaceJouable|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRoyaumesRaceJouable|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
