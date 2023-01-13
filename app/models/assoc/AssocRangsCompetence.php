<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocRangsCompetence extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idRang", type="integer", length=11, nullable=false)
     */
    public $idRang;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idCompetence", type="integer", length=11, nullable=false)
     */
    public $idCompetence;

    /**
     *
     * @var integer
     * @Column(column="nbPointAAtteindre", type="integer", length=8, nullable=false)
     */
    public $nbPointAAtteindre;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_rangs_competence");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRangsCompetence[]|AssocRangsCompetence|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocRangsCompetence|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
