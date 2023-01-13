<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocCartesEffetsParam extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Column(column="idEffet", type="integer", length=11, nullable=false)
     */
    public $idEffet;

    /**
     *
     * @var integer
     * @Column(column="idCarte", type="integer", length=11, nullable=false)
     */
    public $idCarte;

    /**
     *
     * @var integer
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
     * @Column(column="position", type="integer", length=2, nullable=false)
     */
    public $position;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_cartes_effets_param");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCartesEffetsParam[]|AssocCartesEffetsParam|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCartesEffetsParam|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
