<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Noteswiki extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(column="idArticle", type="integer", length=11, nullable=false)
     */
    public $idArticle;

    /**
     *
     * @var integer
     * @Column(column="idAuteur", type="integer", length=11, nullable=false)
     */
    public $idAuteur;

    /**
     *
     * @var integer
     * @Column(column="dateNote", type="integer", length=20, nullable=false)
     */
    public $dateNote;

    /**
     *
     * @var string
     * @Column(column="contenu", type="string", nullable=false)
     */
    public $contenu;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("noteswiki");

        //Init Jointure
        $this->belongsTo('idArticle', 'Articles', 'id');
        $this->belongsTo('idAuteur', 'Personnages', 'id', ['alias' => 'auteur']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Noteswiki[]|Noteswiki|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Noteswiki|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
