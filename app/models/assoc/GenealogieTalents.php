<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class GenealogieTalents extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idPere", type="integer", length=11, nullable=false)
     */
    public $idPere;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idFils", type="integer", length=11, nullable=false)
     */
    public $idFils;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("genealogie_talents");

        //Init Jointure
        $this->belongsTo('idPere', 'Talents', 'id', ['alias' => 'peres']);
        $this->belongsTo('idFils', 'Talents', 'id', ['alias' => 'fils']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GenealogieTalents[]|GenealogieTalents|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GenealogieTalents|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
