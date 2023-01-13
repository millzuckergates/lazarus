<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocCompetencesEffetsParam extends \Phalcon\Mvc\Model {

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
     * @Column(column="idCompetence", type="integer", length=11, nullable=false)
     */
    public $idCompetence;

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
     * @var integer
     * @Primary
     * @Column(column="position", type="integer", length=2, nullable=false)
     */
    public $position;

    /**
     *
     * @var string
     * @Column(column="action", type="string", length=150, nullable=true)
     */
    public $action;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_competences_effets_param");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCompetencesEffetsParam[]|AssocCompetencesEffetsParam|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocCompetencesEffetsParam|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
