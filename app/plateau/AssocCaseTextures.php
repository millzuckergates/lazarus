<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;

class AssocCaseTextures extends Model {

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
     * @Column(column="idCase", type="integer", length=11, nullable=false)
     */
    public $idCase;

    /**
     *
     * @var integer
     * @Column(column="idTexture", type="integer", length=11, nullable=false)
     */
    public $idTexture;

    /**
     *
     * @var string
     * @Column(column="typeCarte", type="string", length=30, nullable=false)
     */
    public $typeCarte;

    /**
     *
     * @var integer
     * @Column(column="numTexture", type="integer", length=11, nullable=false)
     */
    public $numTexture;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("assoc_case_textures");
    }

    /**
     * @param integer $idCase
     * @param string $typeCarte
     * @return array
     */
    public static function getTexturesForCase(int $idCase, string $typeCarte): array {
        $textures = AssocCaseTextures::find([
          'cache' => ['key' => 'textureAssoc-Id-'.$idCase.'-'.$typeCarte],
          "idCase = :idCase: AND typeCarte = :typeCarte:",
          "bind" => ["idCase" => $idCase, "typeCarte" => $typeCarte]
        ]);
        $ret = [];
        if ($textures) {
            foreach ($textures as $texture) {
                $ret[$texture->numTexture] = $texture->idTexture;
            }
        }
        krsort($ret, SORT_NUMERIC);
        return $ret;
    }

    /**
     * @param int $idCase
     * @param string $typeCarte
     * @param int $numTexture
     * @return ModelInterface
     */
    public static function getTextureFromCaseAndCarteAndNumtexture(int $idCase, string  $typeCarte, int $numTexture): ModelInterface {
        return AssocCaseTextures::findFirst([
          "idCase = :idCase: AND typeCarte = :typeCarte: AND numTexture = :numTexture:",
          "bind" => ["idCase" => $idCase, "typeCarte" => $typeCarte, "numTexture" => $numTexture]
        ]);
    }
}
