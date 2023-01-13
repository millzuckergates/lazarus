<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

use Phalcon\Mvc\Model;

class Textures extends Model {

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
     * @Column(column="nom", type="string", length=60, nullable=false)
     */
    public $nom;

    /**
     *
     * @var string
     * @Column(column="image", type="string", length=256, nullable=true)
     */
    public $image;

    /**
     * @var array
     */
    public static $listeTextures;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("textures");
    }

    /**
     * Retourne l'image liée à la texture
     * @return string
     */
    public function getImage(): string {
        $image = str_replace("/var/www/lazarus", "", str_replace(BASE_PATH, "", $this->image));
        return str_replace("//", "/", $image);
    }

    /**
     * Méthode pour tracer le terrain
     * @return string
     */
    public function toString(): string {
        $retour = "[Nom : " . $this->nom . "], ";
        $retour .= "[Image : " . $this->image . "]";
        return $retour;
    }

    /**
     * Retourne la texture correspondant à l'id en paramètre
     * @param $id int
     * @return Textures
     */
    public static function getFromId(int $id): Textures {
        if (!isset(Textures::$listeTextures[$id])) {
            Textures::$listeTextures[$id] = Textures::findFirst([
              'cache' => ['key' => 'texture-Id-'.$id],
              'id = :id:',
              'bind' => ['id' => $id]
            ]);
        }
        return Textures::$listeTextures[$id];
    }

    /**
     * Retourne la balise HTML correspondant à l'id en paramètre
     * @param $idTexture int|null
     * @param $numTexture int
     * @return string
     */
    public static function getHTMLFromId(?int $idTexture, int $numTexture): string {
        if ($idTexture != null) {
            $texture = Textures::getFromId($idTexture);
            if ($texture) {
                return Phalcon\Tag::image([
                  $texture->getImage(),
                  "alt" => $texture->nom,
                  "style" => "position: absolute; left: 0;",
                  "idTexture" => "$idTexture",
                  "numTexture" => "$numTexture"
                ]);
            }
        }
        return "";
    }
}
