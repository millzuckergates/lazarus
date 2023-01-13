<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocCaracsCompetence extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idCarac", type="integer", length=11, nullable=false)
     */
    public $idCarac;

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
     * @Column(column="modificateur", type="integer", length=8, nullable=false)
     */
    public $modificateur;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="isInfluenceur", type="integer", length=1, nullable=false)
     */
    public $isInfluenceur;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_caracs_competence");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCaracsCompetence[]|AssocCaracsCompetence|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCaracsCompetence|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
