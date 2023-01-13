<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Chrontasks extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=10, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(column="nom", type="string", length=45, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="function", type="string", length=45, nullable=false)
     */
    public $function;

    /**
     *
     * @var string
     * @Column(column="parameters", type="string", nullable=false)
     */
    public $parameters;

    /**
     *
     * @var integer
     * @Column(column="groupe", type="integer", length=11, nullable=false)
     */
    public $groupe;

    /**
     *
     * @var string
     * @Column(column="nextexec", type="string", nullable=true)
     */
    public $nextexec;

    /**
     *
     * @var string
     * @Column(column="timeinterval", type="string", nullable=true)
     */
    public $timeinterval;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("chrontasks");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Chrontasks[]|Chrontasks|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Chrontasks|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
