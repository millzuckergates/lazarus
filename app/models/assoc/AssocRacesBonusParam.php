<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocRacesBonusParam extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idBonus", type="integer", length=11, nullable=false)
     */
    public $idBonus;

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
     * @Column(column="idParam", type="integer", length=11, nullable=false)
     */
    public $idParam;

    /**
     *
     * @var string
     * @Column(column="valeur", type="string", nullable=true)
     */
    public $valeur;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="position", type="integer", length=2, nullable=false)
     */
    public $position;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_races_bonus_param");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRacesBonusParam[]|AssocRacesBonusParam|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRacesBonusParam|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
