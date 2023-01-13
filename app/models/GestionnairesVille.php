<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class GestionnairesVille extends \Phalcon\Mvc\Model {

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
     * @Column(column="idVille", type="integer", length=11, nullable=false)
     */
    public $idVille;

    /**
     *
     * @var string
     * @Column(column="droit", type="string", length=30, nullable=false)
     */
    public $droit;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("gestionnaires_ville");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GestionnairesVille[]|GestionnairesVille|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GestionnairesVille|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
