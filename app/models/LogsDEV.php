<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class LogsDEV extends \Phalcon\Mvc\Model {

    //Constantes pour les différents types de logs dev
    const TYPE_GESTION_EQUIPEMENT = "gestion équipement";
    const TYPE_GESTION_CREATURE = "gestion créature";
    const TYPE_GESTION_RELAIS = "gestion relais";
    const TYPE_GESTION_MAP = "gestion cartes";
    const TYPE_GESTION_TERRAINS = "gestion terrains";
    const TYPE_GESTION_CARAC = "gestion caractéristiques";
    const TYPE_GESTION_MAGIE = "gestion magie";
    const TYPE_TABLE_CIBLAGE = "table ciblage";
    const TYPE_GESTION_TALENT = "gestion talent";
    const TYPE_GESTION_COMPETENCE = "gestion compétence";

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
     * @Column(column="idPersonnage", type="integer", length=11, nullable=false)
     */
    public $idPersonnage;

    /**
     *
     * @var integer
     * @Column(column="dateLog", type="integer", length=22, nullable=false)
     */
    public $dateLog;

    /**
     *
     * @var string
     * @Column(column="action", type="string", length=600, nullable=false)
     */
    public $action;

    /**
     *
     * @var string
     * @Column(column="typeLog", type="string", length=50, nullable=false)
     */
    public $typeLog;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("logsdev");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LogsDEV[]|LogsDEV|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LogsDEV|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Permet de renvoyer la liste de logs
     * @param unknown $administration
     * @param unknown $type
     * @param unknown $idAuteur
     * @param unknown $idCible
     * @param unknown $page
     * @return LogsDEV[]|LogsDEV|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeLogs($administration, $type, $idAuteur, $page) {
        $logsDev = array();
        //Condition sur le type
        $condition = "";
        $flagType = false;
        $flagAuteur = false;
        if ($type != null && $type != "0") {
            $condition .= 'typeLog = :type:';
            $flagType = true;
        }
        if ($idAuteur != null) {
            if ($condition != "") {
                $condition .= ' AND idPersonnage = :idAuteur:';
            } else {
                $condition .= 'idPersonnage = :idAuteur:';
            }
            $flagAuteur = true;
        }

        //Construction du bind
        if ($flagType && !$flagAuteur) {
            $bind = array("type" => $type);
        } else {
            if ($flagType && $flagAuteur) {
                $bind = array("type" => $type, "idAuteur" => $idAuteur);
            } else {
                if (!$flagType && $flagAuteur) {
                    $bind = array("idAuteur" => $idAuteur);
                }
            }
        }

        //Condition sur le tri
        $trie = $administration['triDate'];
        if ($condition != "") {
            if ($trie == "decroissant") {
                $logsDev = LogsDEV::find([$condition, 'bind' => $bind, "order" => " dateLog DESC", "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            } else {
                $logsDev = LogsDEV::find([$condition, 'bind' => $bind, "order" => " dateLog ASC", "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            }
        } else {
            if ($trie == "decroissant") {
                $logsDev = LogsDEV::find(['order' => 'dateLog DESC', "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            } else {
                $logsDev = LogsDEV::find(['order' => 'dateLog ASC', "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            }
        }

        return $logsDev;
    }

    /**
     * Retourne le nombre de log de la liste
     * @param unknown $idAuteur
     * @param unknown $idCible
     * @param unknown $type
     * @return number
     */
    public static function countListLog($idAuteur, $type) {
        $logsDev = array();
        //Condition sur le type
        $condition = "";
        $flagType = false;
        $flagAuteur = false;
        if ($type != null && $type != "0") {
            $condition .= 'typeLog = :type:';
            $flagType = true;
        }
        if ($idAuteur != null) {
            if ($condition != "") {
                $condition .= ' AND idPersonnage = :idAuteur:';
            } else {
                $condition .= 'idPersonnage = :idAuteur:';
            }
            $flagAuteur = true;
        }

        //Construction du bind
        if ($flagType && !$flagAuteur) {
            $bind = array("type" => $type);
        } else {
            if ($flagType && $flagAuteur) {
                $bind = array("type" => $type, "idAuteur" => $idAuteur);
            } else {
                if (!$flagType && $flagAuteur) {
                    $bind = array("idAuteur" => $idAuteur);
                }
            }
        }

        //Condition sur le tri
        if ($condition != "") {
            $logsDev = LogsDEV::find([$condition, 'bind' => $bind]);
        } else {
            $logsDev = LogsDEV::find();
        }
        return count($logsDev);
    }

    /**
     * Retourne la liste des types disponibles
     */
    public static function getListeTypes() {
        $retour = array();
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_EQUIPEMENT;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_CREATURE;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_RELAIS;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_MAP;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_TERRAINS;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_CARAC;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_MAGIE;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_TALENT;
        $retour[count($retour)] = LogsDEV::TYPE_TABLE_CIBLAGE;
        $retour[count($retour)] = LogsDEV::TYPE_GESTION_COMPETENCE;
        return $retour;
    }

    /**
     * Permet de générer le contenu du csv pour l'export
     * @param unknown $listeLogs
     * @param unknown $dateStart
     * @return string
     */
    public static function genererContenuExport($listeLogs, $dateStart) {
        $contenu = "";
        $lastLog = $listeLogs[count($listeLogs) - 1];
        $dateEnd = date('d-m-Y', $lastLog->dateLog);
        $dateStartFormat = date('d-m-Y', $dateStart);

        $contenu .= "Liste des Logs DEV du " . $dateStartFormat . " au " . $dateEnd . "\r\n\r\n";
        $contenu .= "Auteur;Date;Action\r\n";
        foreach ($listeLogs as $log) {
            $auteur = Personnages::findFirst(['id = :id:', 'bind' => ['id' => $log->idPersonnage]]);
            $contenu .= $auteur->nom . ";";
            $contenu .= date('d/m/Y H:i:s', $log->dateLog) . ";" . str_replace("\n", " - ", $log->action) . "\r\n";
        }
        return $contenu;
    }

}
