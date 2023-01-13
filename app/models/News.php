<?php

use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class News extends \Phalcon\Mvc\Model {

    //###### TYPE DESTINATAIRE #######//
    const TYPE_ROYAUME = "Royaume";
    const TYPE_RELIGION = "Religion";
    const TYPE_VILLE = "Ville";

    //###### TYPE NEWS #######//
    const TYPE_RP = "RP";
    const TYPE_HRP = "HRP";
    const TYPE_MAD = "MAD";

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
     * @Column(column="titre", type="string", length=250, nullable=false)
     */
    public $titre;

    /**
     *
     * @var integer
     * @Column(column="date", type="integer", length=13, nullable=false)
     */
    public $date;

    /**
     *
     * @var integer
     * @Column(column="idAuteur", type="integer", length=11, nullable=false)
     */
    public $idAuteur;

    /**
     *
     * @var string
     * @Column(column="nomAuteur", type="string", length=150, nullable=false)
     */
    public $nomAuteur;

    /**
     *
     * @var string
     * @Column(column="texte", type="string", nullable=false)
     */
    public $texte;

    /**
     *
     * @var string
     * @Column(column="type", type="string", length=3, nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(column="idDestinataire", type="integer", length=11, nullable=true)
     */
    public $idDestinataire;

    /**
     *
     * @var string
     * @Column(column="typeDestinataire", type="string", length=80, nullable=true)
     */
    public $typeDestinataire;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("news");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return News[]|News|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return News|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Renvoie la liste des types
     * @return array
     */
    public static function getListeType() {
        $retour[0] = News::TYPE_HRP;
        $retour[1] = News::TYPE_RP;
        $retour[2] = News::TYPE_MAD;
        return $retour;
    }

    /**
     * Renvoie la liste des types destinataires
     * @return array
     */
    public static function getListeTypeDestinataire() {
        $retour[0] = News::TYPE_ROYAUME;
        $retour[1] = News::TYPE_RELIGION;
        $retour[2] = News::TYPE_VILLE;
        return $retour;
    }

    public static function getListeNewsFiltrees($auth) {
        //Récupération des différentes listes de news
        $listeNewsHRP = News::find(['type = :type:', 'bind' => ['type' => "HRP"], 'order' => 'date DESC']);
        $listeRetour = array();

        //Non connecté, accès uniquement aux news HRP
        if ($listeNewsHRP != false && count($listeNewsHRP) > 0) {
            foreach ($listeNewsHRP as $news) {
                $listeRetour[$news->date] = $news;
            }
        }

        //Connecté, accès aux news RP correspondant au profil, aux news HRP
        //MAD, accès à toutes les news
        if ($auth != null && isset($auth['perso'])) {
            $perso = $auth['perso'];
            $modeMJ = $auth['modeMJ'];
            //ListeNewsReligion
            $listeNewsReligions = News::find(['type = :type: AND typeDestinataire = :destinataire:', 'bind' => ['type' => 'RP', 'destinataire' => 'Religion'], 'order' => 'date DESC']);
            if ($listeNewsReligions != false && count($listeNewsReligions) > 0) {
                foreach ($listeNewsReligions as $news) {
                    if ($modeMJ == true) {
                        $listeRetour[$news->date] = $news;
                    } else {
                        if ($perso->idReligion == $news->idDestinataire) {
                            $listeRetour[$news->date] = $news;
                        }
                    }
                }
            }
            //ListeNewsRoyaume
            $listeNewsRoyaumes = News::find(['type = :type: AND typeDestinataire = :destinataire:', 'bind' => ['type' => 'RP', 'destinataire' => 'Royaume'], 'order' => 'date DESC']);
            if ($listeNewsRoyaumes != false && count($listeNewsRoyaumes) > 0) {
                foreach ($listeNewsRoyaumes as $news) {
                    if ($modeMJ == true) {
                        $listeRetour[$news->date] = $news;
                    } else {
                        if ($perso->idRoyaume == $news->idDestinataire) {
                            $listeRetour[$news->date] = $news;
                        }
                    }
                }
            }
            //ListeNewsVille
            $listeNewsVilles = News::find(['type = :type: AND typeDestinataire = :destinataire:', 'bind' => ['type' => 'RP', 'destinataire' => 'Ville'], 'order' => 'date DESC']);
            if ($listeNewsVilles != false && count($listeNewsVilles) > 0) {
                foreach ($listeNewsVilles as $news) {
                    if ($modeMJ == true) {
                        $listeRetour[$news->date] = $news;
                    } else {
                        if ($perso->inVille($news->idDestinataire)) {
                            $listeRetour[$news->date] = $news;
                        }
                    }
                }
            }
            //ListeNewsMAD
            if ($modeMJ == true) {
                $listeNewsMAD = News::find(['type = :type:', 'bind' => ['type' => 'MAD'], 'order' => 'date DESC']);
                if ($listeNewsMAD != false && count($listeNewsMAD) > 0) {
                    foreach ($listeNewsMAD as $news) {
                        $listeRetour[$news->date] = $news;
                    }
                }
            }

            //ListeNewsRP
            $listeNewsRP = News::find(['type = :type: AND idDestinataire IS NULL', 'bind' => ['type' => 'RP'], 'order' => 'date DESC']);
            if ($listeNewsRP != false && count($listeNewsRP) > 0) {
                foreach ($listeNewsRP as $news) {
                    $listeRetour[$news->date] = $news;
                }
            }

        }

        //Tri par ordre décroissant
        if (count($listeRetour) > 0) {
            krsort($listeRetour);
        }
        return $listeRetour;
    }

    public static function genererSelectType() {
        $listeType = News::getListeType();
        $retour = "<select id='typeNews' onChange='displayBlocDestinataire();'>";
        foreach ($listeType as $type) {
            $retour .= "<option value='" . $type . "'>" . $type . "</option>";
        }
        $retour .= "</select>";
        return $retour;
    }

    public static function genererSelectDestinataire() {
        $listeType = News::getListeTypeDestinataire();
        $retour = "<select id='destinataireNews' onChange='displayListeDestinataire();'><option value='0'>Choisissez vos destinataires</option>";
        foreach ($listeType as $type) {
            $retour .= "<option value='" . $type . "'>" . $type . "</option>";
        }
        $retour .= "</select>";
        return $retour;
    }

    public static function genererSelectIdDestinataire($destinataire) {
        $retour = "";
        $listeElement = array();
        if ($destinataire == News::TYPE_ROYAUME) {
            $listeElement = Royaumes::find(['order' => 'nom']);
        } else {
            if ($destinataire == News::TYPE_RELIGION) {
                $listeElement = Religions::find(['order' => 'nom']);
            } else {
                if ($destinataire == News::TYPE_VILLE) {
                    $listeElement = Villes::find(['order' => 'nom']);
                }
            }
        }
        $retour .= "<select id='idDestinataireNews'>";
        if ($listeElement != false && count($listeElement) > 0) {
            foreach ($listeElement as $element) {
                $retour .= "<option value='" . $element->id . "'>" . $element->nom . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;

    }

    public function genererSelectDestinataireData() {
        $listeType = News::getListeTypeDestinataire();
        $retour = "<select id='destinataireNews' onChange='displayListeDestinataire();'><option value='0'>Choisissez vos destinataires</option>";
        foreach ($listeType as $type) {
            if ($type == $this->typeDestinataire) {
                $retour .= "<option value='" . $type . "' selected>" . $type . "</option>";
            } else {
                $retour .= "<option value='" . $type . "'>" . $type . "</option>";
            }
        }
        $retour .= "</select>";
        return $retour;
    }

    public function genererSelectIdDestinataireData() {
        $retour = "";
        $listeElement = array();
        if ($this->typeDestinataire == News::TYPE_ROYAUME) {
            $listeElement = Royaumes::find(['order' => 'nom']);
        } else {
            if ($this->typeDestinataire == News::TYPE_RELIGION) {
                $listeElement = Religions::find(['order' => 'nom']);
            } else {
                if ($this->typeDestinataire == News::TYPE_VILLE) {
                    $listeElement = Villes::find(['order' => 'nom']);
                }
            }
        }
        $retour .= "<select id='idDestinataireNews'>";
        if ($listeElement != false && count($listeElement) > 0) {
            foreach ($listeElement as $element) {
                if ($element->id == $this->idDestinataire) {
                    $retour .= "<option value='" . $element->id . "' selected>" . $element->nom . "</option>";
                } else {
                    $retour .= "<option value='" . $element->id . "'>" . $element->nom . "</option>";
                }
            }
        }
        $retour .= "</select>";
        return $retour;

    }

    /**
     * Retourne l'objet sous une chaine de caractère
     * @return string
     */
    public function toString() {
        $retour = "";
        $retour .= "[Titre : " . $this->titre . "], ";
        $retour .= "[Auteur : " . $this->nomAuteur . "], ";
        $retour .= "[Date : " . $this->date . "], ";
        $retour .= "[Texte : " . $this->texte . "], ";
        $retour .= "[idAuteur : " . $this->idAuteur . "], ";
        $retour .= "[Type : " . $this->type . "]";
        $retour .= "[Type Destinataire : " . $this->typeDestinataire . "], ";
        $retour .= "[Id Destinataire : " . $this->idDestinataire . "]";
        return $retour;
    }

}
