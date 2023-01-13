<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

abstract class AbstractMatrices extends Model {

    /**
     *
     * @var integer|null
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public ?int $id = null;

    /**
     *
     * @var integer|null
     * @Column(column="x", type="integer", length=11, nullable=false)
     */
    public ?int $x = null;

    /**
     *
     * @var integer|null
     * @Column(column="y", type="integer", length=11, nullable=false)
     */
    public ?int $y = null;

    /**
     *
     * @var integer|null
     * @Column(column="idCarte", type="integer", length=11, nullable=false)
     */
    public ?int $idCarte = null;

    /**
     *
     * @var integer|null
     * @Column(column="idTerrain", type="integer", length=11, nullable=false)
     */
    public ?int $idTerrain = null;

    /**
     *
     * @var string|null
     * @Column(column="image", type="string", length=255, nullable=false)
     */
    public ?string $image = null;

    /**
     *
     * @var integer|null
     * @Column(column="idCreature", type="integer", length=11, nullable=true)
     */
    public ?int $idCreature = null;

    /**
     *
     * @var integer|null
     * @Column(column="idPersonnage", type="integer", length=11, nullable=true)
     */
    public ?int $idPersonnage = null;

    /**
     *
     * @var integer|null
     * @Column(column="idBatiment", type="integer", length=11, nullable=true)
     */
    public ?int $idBatiment = null;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
    }

    /**
     * @param Segment $segment
     * @return ResultsetInterface|null
     */
    public static function findBySegment(Segment $segment): ?ResultsetInterface {
        $arguments = [
          'idCarte = :idCarte: AND (x BETWEEN :xmin: AND :xmax:) AND (y BETWEEN :ymin: AND :ymax:)',
          'bind' => [
            'xmin' => $segment->xmin,
            'xmax' => $segment->xmax,
            'ymin' => $segment->ymin,
            'ymax' => $segment->ymax,
            'idCarte' => $segment->idCarte
          ],
          'order => x,y'
        ];
        switch ($segment->table) {
            case "Matricesexterieur":
                return Matricesexterieur::find($arguments);
            case "Matricesinterieur":
                return Matricesinterieur::find($arguments);
            case "Matricesmdm" :
                return Matricesmdm::find($arguments);
            case "Matricesville" :
                return Matricesville::find($arguments);
            default:
                return null;
        }
    }

    /**
     * @param Segment $segment
     * @param int $y
     * @return ResultsetInterface|null
     */
    public static function findBySegmentAndY(Segment $segment, int $y): ?ResultsetInterface {
        $arguments = [
          'cache' => ['key' => 'segment-' . $segment->xmin . '-' . $segment->xmax . '-' . $y . '-' . $segment->idCarte],
          'idCarte = :idCarte: AND (x BETWEEN :xmin: AND :xmax:) AND y = :y:',
          'bind' => [
            'xmin' => $segment->xmin,
            'xmax' => $segment->xmax,
            'y' => $y,
            'idCarte' => $segment->idCarte
          ],
          'order => x,y'
        ];
        switch ($segment->table) {
            case "Matricesexterieur":
                return Matricesexterieur::find($arguments);
            case "Matricesinterieur":
                return Matricesinterieur::find($arguments);
            case "Matricesmdm" :
                return Matricesmdm::find($arguments);
            case "Matricesville" :
                return Matricesville::find($arguments);
            default:
                return null;
        }
    }

}
