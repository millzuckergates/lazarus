<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocCompetencesPersonnage extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idPerso", type="integer", length=11, nullable=false)
     */
    public $idPerso;

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
     * @Column(column="nbPoint", type="integer", length=8, nullable=false)
     */
    public $nbPoint;

    /**
     *
     * @var integer
     * @Column(column="idRang", type="integer", length=11, nullable=false)
     */
    public $idRang;

    /**
     *
     * @var integer
     * @Column(column="idRangBloque", type="integer", length=11, nullable=true)
     */
    public $idRangBloque;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_competences_personnage");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCompetencesPersonnage[]|AssocCompetencesPersonnage|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCompetencesPersonnage|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
