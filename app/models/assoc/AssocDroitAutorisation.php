<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocDroitAutorisation extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idAutorisation", type="integer", length=11, nullable=false)
     */
    public $idAutorisation;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idDroit", type="integer", length=11, nullable=false)
     */
    public $idDroit;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_droit_autorisation");

        //Init Jointure
        $this->belongsTo('idAutorisation', 'Autorisations', 'id', ['alias' => 'autorisations']);
        $this->belongsTo('idDroit', 'Droits', 'id', ['alias' => 'droits']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocDroitAutorisation[]|AssocDroitAutorisation|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocDroitAutorisation|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
