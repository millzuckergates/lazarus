<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocTalentsEffetsParam extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idEffet", type="integer", length=11, nullable=false)
     */
    public $idEffet;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idTalent", type="integer", length=11, nullable=false)
     */
    public $idTalent;

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
     * @var string
     * @Column(column="valeurMin", type="string", nullable=true)
     */
    public $valeurMin;

    /**
     *
     * @var string
     * @Column(column="valeurMax", type="string", nullable=true)
     */
    public $valeurMax;

    /**
     *
     * @var string
     * @Column(column="action", type="string", length=80, nullable=true)
     */
    public $action;

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
        $this->setSource("assoc_talents_effets_param");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocTalentsEffetsParam[]|AssocTalentsEffetsParam|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocTalentsEffetsParam|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
