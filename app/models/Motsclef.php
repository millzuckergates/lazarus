<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Motsclef extends \Phalcon\Mvc\Model {

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
     * @var string
     * @Column(column="libelle", type="string", length=80, nullable=false)
     */
    public $libelle;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("motsclef");

        $this->hasMany('id', 'AssocArticleMotclef', 'idMotClef', ['alias' => 'assoc_article_motclefs']);
        $this->hasManyToMany('id', 'AssocArticleMotclef', 'idMotClef', 'idArticle', 'Articles', 'id', ['alias' => 'listeArticles']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Motsclef[]|Motsclef|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Motsclef|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de renvoyer un mot clef d'après son libellé ou de le créer
     * @param unknown $libelle
     * @return Motsclef|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function creer($libelle) {
        $motClef = MotsClef::findFirst(["libelle = :libelle:", "bind" => ['libelle' => $libelle]]);
        //On vérifie l'unicité du libellé et, s'il existe, on renvoit le mot clef déjà existant
        if (empty($motClef) || $motClef == null || !$motClef) {
            $motClef = new Motsclef();
            $motClef->libelle = $libelle;
            $motClef->save();
        }
        return $motClef;
    }

    /**
     * Permet d'associer un mot clef avec un article
     * @param unknown $article
     */
    public function associeArticle($article) {
        $assoc = new AssocArticleMotclef();
        $assoc->idArticle = $article->id;
        $assoc->idMotClef = $this->id;
        $assoc->save();
    }

}
