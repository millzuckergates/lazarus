<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class AssocArticleMotclef extends \Phalcon\Mvc\Model {

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idArticle", type="integer", length=11, nullable=false)
     */
    public $idArticle;

    /**
     *
     * @var integer
     * @Primary
     * @Column(column="idMotClef", type="integer", length=11, nullable=false)
     */
    public $idMotClef;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_article_motclef");

        //Init Jointure
        $this->belongsTo('idArticle', 'Articles', 'id', ['alias' => 'articles']);
        $this->belongsTo('idMotClef', 'Motsclef', 'id', ['alias' => 'motsclef']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocArticleMotclef[]|AssocArticleMotclef|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AssocArticleMotclef|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne les id des articles correspondants à l'id du mot clef passé en paramètre
     * @param unknown $idMotClef
     * @return AssocArticleMotclef[]|AssocArticleMotclef|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getArticleByMotClef($idMotClef) {
        return AssocArticleMotclef::find(array("idMotClef" => $idMotClef));
    }
}
