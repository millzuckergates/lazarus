<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocPersonnagesTalents extends \Phalcon\Mvc\Model {

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
     * @Column(column="idTalent", type="integer", length=11, nullable=false)
     */
    public $idTalent;

    /**
     *
     * @var integer
     * @Column(column="niveau", type="integer", length=2, nullable=true)
     */
    public $niveau;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_personnages_talents");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocPersonnagesTalents[]|AssocPersonnagesTalents|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocPersonnagesTalents|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
