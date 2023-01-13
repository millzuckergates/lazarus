<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class LogsADMIN extends \Phalcon\Mvc\Model {

    //Constantes pour les différents types de logs admin
    const TYPE_GESTION_REFERENTIELS = "gestion référentiels";
    const TYPE_GESTION_DROIT = "gestion droit";
    const TYPE_GESTION_FICHIER = "gestion des fichiers";
    const TYPE_ARCHIVAGE_LOGS = "archivage des logs";
    const TYPE_GESTION_FORMULAIRE = "gestion des formulaires";
    const TYPE_GESTION_QUESTIONNAIRE = "gestion questionnaire";
    const TYPE_GESTION_ACTUALITE = "gestion actualité";

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
     * @Column(column="idCible", type="integer", length=11, nullable=true)
     */
    public $idCible;

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
        $this->setSource("logsadmin");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LogsADMIN[]|LogsADMIN|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LogsADMIN|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Retourne la liste des types de logs pour les logs ADMIN
     * @return string[]
     */
    public static function getListeTypes() {
        $retour = array();
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_REFERENTIELS;
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_DROIT;
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_FICHIER;
        $retour[count($retour)] = LogsADMIN::TYPE_ARCHIVAGE_LOGS;
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_FORMULAIRE;
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_QUESTIONNAIRE;
        $retour[count($retour)] = LogsADMIN::TYPE_GESTION_ACTUALITE;
        return $retour;
    }

    /**
     * Permet de renvoyer la liste de logs
     * @param unknown $administration
     * @param unknown $type
     * @param unknown $idAuteur
     * @param unknown $idCible
     * @param unknown $page
     * @return LogsADMIN[]|LogsADMIN|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeLogs($administration, $type, $idAuteur, $idCible, $page) {
        $logsAdmin = array();
        //Condition sur le type
        $condition = "";
        $flagType = false;
        $flagCible = false;
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
        if ($idCible != null) {
            if ($condition != "") {
                $condition .= ' AND idCible = :idCible:';
            } else {
                $condition .= 'idCible = :idCible:';
            }
            $flagCible = true;
        }

        //Construction du bind
        if ($flagType && !$flagAuteur && !$flagCible) {
            $bind = array("type" => $type);
        } else {
            if ($flagType && $flagAuteur && !$flagCible) {
                $bind = array("type" => $type, "idAuteur" => $idAuteur);
            } else {
                if ($flagType && !$flagAuteur && $flagCible) {
                    $bind = array("type" => $type, "idCible" => $idCible);
                } else {
                    if ($flagType && $flagAuteur && $flagCible) {
                        $bind = array("type" => $type, "idAuteur" => $idAuteur, "idCible" => $idCible);
                    } else {
                        if (!$flagType && $flagAuteur && $flagCible) {
                            $bind = array("idAuteur" => $idAuteur, "idCible" => $idCible);
                        } else {
                            if (!$flagType && $flagAuteur && !$flagCible) {
                                $bind = array("idAuteur" => $idAuteur);
                            } else {
                                if (!$flagType && !$flagAuteur && $flagCible) {
                                    $bind = array("idCible" => $idCible);
                                }
                            }
                        }
                    }
                }
            }
        }

        //Condition sur le tri
        $trie = $administration['triDate'];
        if ($condition != "") {
            if ($trie == "decroissant") {
                $logsAdmin = LogsADMIN::find([$condition, 'bind' => $bind, "order" => " dateLog DESC", "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            } else {
                $logsAdmin = LogsADMIN::find([$condition, 'bind' => $bind, "order" => " dateLog ASC", "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            }
        } else {
            if ($trie == "decroissant") {
                $logsAdmin = LogsADMIN::find(['order' => 'dateLog DESC', "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            } else {
                $logsAdmin = LogsADMIN::find(['order' => 'dateLog ASC', "limit" => ['number' => $administration['nbEnregistrementParPage'], 'offset' => ($page - 1) * $administration['nbEnregistrementParPage']]]);
            }
        }

        return $logsAdmin;
    }

    /**
     * Retourne le nombre de log de la liste
     * @param unknown $idAuteur
     * @param unknown $idCible
     * @param unknown $type
     * @return number
     */
    public static function countListLog($idAuteur, $idCible, $type) {
        $logsAdmin = array();
        //Condition sur le type
        $condition = "";
        $flagType = false;
        $flagCible = false;
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
        if ($idCible != null) {
            if ($condition != "") {
                $condition .= ' AND idCible = :idCible:';
            } else {
                $condition = 'idCible = :idCible:';
            }
            $flagCible = true;
        }

        //Construction du bind
        if ($flagType && !$flagAuteur && !$flagCible) {
            $bind = array("type" => $type);
        } else {
            if ($flagType && $flagAuteur && !$flagCible) {
                $bind = array("type" => $type, "idAuteur" => $idAuteur);
            } else {
                if ($flagType && !$flagAuteur && $flagCible) {
                    $bind = array("type" => $type, "idCible" => $idCible);
                } else {
                    if ($flagType && $flagAuteur && $flagCible) {
                        $bind = array("type" => $type, "idAuteur" => $idAuteur, "idCible" => $idCible);
                    } else {
                        if (!$flagType && $flagAuteur && $flagCible) {
                            $bind = array("idAuteur" => $idAuteur, "idCible" => $idCible);
                        } else {
                            if (!$flagType && $flagAuteur && !$flagCible) {
                                $bind = array("idAuteur" => $idAuteur);
                            } else {
                                if (!$flagType && !$flagAuteur && $flagCible) {
                                    $bind = array("idCible" => $idCible);
                                }
                            }
                        }
                    }
                }
            }
        }

        //Condition sur le tri
        if ($condition != "") {
            $logsAdmin = LogsADMIN::find([$condition, 'bind' => $bind]);
        } else {
            $logsAdmin = LogsADMIN::find();
        }
        return count($logsAdmin);
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

        $contenu .= "Liste des Logs ADMIN du " . $dateStartFormat . " au " . $dateEnd . "\r\n\r\n";
        $contenu .= "Auteur;Cible;Date;Action\r\n";
        foreach ($listeLogs as $log) {
            $auteur = Personnages::findFirst(['id = :id:', 'bind' => ['id' => $log->idPersonnage]]);
            $contenu .= $auteur->nom . ";";
            if (!empty($log->idCible) && $log->idCible != null) {
                $cible = Personnages::findFirst(['id = :id:', 'bind' => ['id' => $log->idCible]]);
                $contenu .= $cible->nom . ";";
            } else {
                $contenu .= ";";
            }
            $contenu .= date('d/m/Y H:i:s', $log->dateLog) . ";" . str_replace("\n", " - ", $log->action) . "\r\n";
        }
        return $contenu;
    }
}
