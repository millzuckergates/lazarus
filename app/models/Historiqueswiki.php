<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Historiqueswiki extends \Phalcon\Mvc\Model {

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
     * @Column(column="dateModification", type="integer", length=20, nullable=false)
     */
    public $dateModification;

    /**
     *
     * @var string
     * @Column(column="commentaire", type="string", nullable=false)
     */
    public $commentaire;

    /**
     *
     * @var string
     * @Column(column="ancienContenu", type="string", nullable=false)
     */
    public $ancienContenu;

    /**
     *
     * @var string
     * @Column(column="action", type="string", length=50, nullable=false)
     */
    public $action;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("historiqueswiki");
        $this->belongsTo('idArticle', 'Articles', 'id');

    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Historiqueswiki[]|Historiqueswiki|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Historiqueswiki|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Méthode permettant d'ajouter un évenement à l'historique d'un article
     * @param unknown $idArticle
     * @param unknown $idPerso
     * @param unknown $commentaire
     * @param unknown $ancienContenu
     * @param unknown $action
     */
    public static function ajouterHistorique($idArticle, $idPerso, $commentaire, $ancienContenu, $action) {
        $historique = new Historiqueswiki();
        $historique->action = $action;
        $historique->ancienContenu = $ancienContenu;
        $historique->commentaire = $commentaire;
        $historique->dateModification = time();
        $historique->idArticle = $idArticle;
        $historique->idAuteur = $idPerso;
        $historique->save();
    }

}
