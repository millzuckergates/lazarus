<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class BonusParam extends \Phalcon\Mvc\Model {

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
     * @Column(column="idParam", type="integer", length=11, nullable=false)
     */
    public $idParam;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="position", type="integer", length=3, nullable=false)
     */
    public $position;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("bonus_param");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BonusParam[]|BonusParam|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BonusParam|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
