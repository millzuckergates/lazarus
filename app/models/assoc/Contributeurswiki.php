<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Contributeurswiki extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idPersonnage", type="integer", length=11, nullable=false)
     */
    public $idPersonnage;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idArticle", type="integer", length=11, nullable=false)
     */
    public $idArticle;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("contributeurswiki");

        //Init Jointure
        $this->belongsTo('idPersonnage', 'Personnages', 'id', ['alias' => 'personnages']);
        $this->belongsTo('idArticle', 'Articles', 'id', ['alias' => 'articles']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Contributeurswiki[]|Contributeurswiki|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Contributeurswiki|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
