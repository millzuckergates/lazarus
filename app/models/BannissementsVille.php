<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class BannissementsVille extends \Phalcon\Mvc\Model {

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
     * @var integer
     * @Column(column="dateDebut", type="integer", length=11, nullable=false)
     */
    public $dateDebut;

    /**
     *
     * @var integer
     * @Column(column="dateFin", type="integer", length=11, nullable=false)
     */
    public $dateFin;

    /**
     *
     * @var string
     * @Column(column="raison", type="string", nullable=false)
     */
    public $raison;

    /**
     *
     * @var integer
     * @Column(column="idBannisseur", type="integer", length=11, nullable=false)
     */
    public $idBannisseur;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("bannissements_ville");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BannissementsVille[]|BannissementsVille|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BannissementsVille|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

}
