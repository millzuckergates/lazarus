<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Articles extends Model {

    //Constantes pour le statut des articles
    const STATUS_IN_PROGESS = "En progression";
    const STATUS_WAITING_VALIDATION = "En attente de validation";
    const STATUS_VALIDATED = "Validé";
    const STATUS_ARCHIVED = "Archivé";
    const STATUS_DEMANDE_DE_REVISION = "Demande de révision";
    const STATUS_PROPOSITION = "Proposition";

    //Constantes pour les titres particuliers
    const TITRE_GENERIQUE = "Autres";
    const IMAGE_GENERIQUE = "public/img/wiki/imagerandom_wiki.jpg";

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public int $id;

    /**
     *
     * @var string
     * @Column(column="titre", type="string", length=100, nullable=false)
     */
    public string $titre;

    /**
     *
     * @var ?string
     * @Column(column="contenu", type="string", nullable=true)
     */
    public ?string $contenu;

    /**
     *
     * @var ?integer
     * @Column(column="idAuteur", type="integer", length=11, nullable=true)
     */
    public ?int $idAuteur;

    /**
     *
     * @var integer
     * @Column(column="idHierarchie", type="integer", length=11, nullable=false)
     */
    public int $idHierarchie;

    /**
     *
     * @var string
     * @Column(column="status", type="string", length=32, nullable=false)
     */
    public string $status;

    /**
     *
     * @var integer
     * @Column(column="dateCreation", type="integer", length=20, nullable=false)
     */
    public int $dateCreation;

    /**
     *
     * @var ?integer
     * @Column(column="dateModification", type="integer", length=20, nullable=true)
     */
    public ?int $dateModification;

    /**
     *
     * @var ?string
     * @Column(column="img", type="string", length=300, nullable=true)
     */
    public ?string $img;

    public $auteur;

    /**
     * Initialize method for model.
     */
    public function initialize() {
        $this->setSchema("lazarus");
        $this->setSource("articles");

        //Init Jointure
        $this->hasMany('id', 'Restrictionswiki', 'idArticle', ['alias' => 'listeRestrictions']);
        $this->hasMany('id', 'Noteswiki', 'idArticle', ['alias' => 'notes']);
        $this->hasMany('id', 'Historiqueswiki', 'idArticle', ['alias' => 'historiques', 'params' => ['order' => 'dateModification DESC']]);

        $this->hasMany('id', 'Contributeurswiki', 'idArticle', ['alias' => 'contributeurs_wiki']);
        $this->hasManyToMany('id', 'Contributeurswiki', 'idArticle', 'idPersonnage', 'Personnages', 'id', ['alias' => 'contributeurs']);

        $this->hasMany('id', 'AssocArticleMotclef', 'idArticle', ['alias' => 'assoc_article_motclefs']);
        $this->hasManyToMany('id', 'AssocArticleMotclef', 'idArticle', 'idMotClef', 'Motsclef', 'id', ['alias' => 'listeMotsClef']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Articles[]|Articles|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Articles|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null): ?ModelInterface {
        return parent::findFirst($parameters);
    }

    /**
     * Returne la liste des articles composant l'index
     * @return Articles[]|ResultsetInterface
     */
    public static function getListeIndex(): ResultsetInterface {
        return Articles::find(["conditions" => "idHierarchie = ?1 AND titre != ?2",
          "bind" => [1 => 0, 2 => "Autres"],
          "order" => "titre"]);
    }

    /**
     * Permet de retourner la liste des fils de l'article passé en paramere
     * @param string $id
     * @return Articles[]|Articles|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function getListeFils(string $id) {
        return Articles::find(["idHierarchie = " . $id]);
    }

    /**
     * Méthode permettant d'afficher l'index du wiki
     * @param ?array $auth
     * @param string $type
     * @return string
     */
    public function afficherArticleIndex(?array $auth, string $type): string {
        $perso = isset($auth) ? $auth['perso'] : null;
        $autorisations = isset($auth) ? $auth['autorisations'] : null;
        if (!empty($autorisations)) {
            if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $autorisations)) {
                // Si Dev, accès à tout
                return $this->htmlTitre($type, true);
            } else {
                if (Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $autorisations)) {
                    //Si MJ BG -> Voit tous les articles (droit "Gestion wiki") - Sauf les archivés et les propositions pas enregistrées
                    if ($this->status != Articles::STATUS_ARCHIVED && $this->status != Articles::STATUS_PROPOSITION) {
                        switch ($this->status) {
                            case Articles::STATUS_VALIDATED :
                            case Articles::STATUS_IN_PROGESS :
                            case Articles::STATUS_WAITING_VALIDATION :
                            case Articles::STATUS_DEMANDE_DE_REVISION :
                                return $this->htmlTitre($type, true);
                        }
                    }
                } else {
                    if (Autorisations::hasAutorisation(Autorisations::CONSULTATION_WIKI, $autorisations)) {
                        //SI MJ -> Voit les articles Validés et ceux en cours accessibles aux MJ
                        if ($this->status == Articles::STATUS_VALIDATED) {
                            return $this->htmlTitre($type);
                        } else {
                            if ($this->status == Articles::STATUS_IN_PROGESS) {
                                //On vérifie qu'il n'y a pas de restriction de DEV ou de MJ BG sur l'article
                                if (!$this->hasRestriction(RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION, Autorisations::GESTION_WIKI) && !$this->hasRestriction(RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION, Autorisations::ADMINISTRATION_WIKI)) {
                                    return $this->htmlTitre($type, true);
                                }
                            }
                        }
                    } else {
                        //Le cas des joueurs standards. Accès aux articles validés dont les restrictions leur vont, plus ceux qui sont en cours auxquels ils participent (auteur/contributeur)
                        if ($this->status == Articles::STATUS_VALIDATED && $this->checkRestrictions($auth)) {
                            return $this->htmlTitre($type);
                        } else {
                            if ($this->status == Articles::STATUS_IN_PROGESS) {
                                if ($perso->id == $this->idAuteur || $this->isContributeur($perso)) {
                                    return $this->htmlTitre($type);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            //L'article n'est disponible que s'il n'y a pas de restrictions et qu'il est validé
            if (empty($this->listeRestrictions) && $this->status == Articles::STATUS_VALIDATED) {
                return $this->htmlTitre($type);
            }
        }
        return "";
    }

    /**
     * Génère le code HTML du titre de l'article pour l'index
     * @param string $type
     * @param bool $status
     * @return string
     */
    private function htmlTitre(string $type, bool $status = false): string {
        if ($type == "titre") {
            if ($status) {
                return '<span class="titre ' . str_replace(" ", "", $this->status) . '">' . $this->titre . '</span>';
            }
            return '<span class="titreIndex">' . $this->titre . '</span>';
        } elseif ($type == "fils") {
            return '<li class="fils ' . str_replace(" ", "", $this->status) . '">' . Phalcon\Tag::linkTo(["wiki/article?id=" . $this->id, $this->titre, "class" => str_replace(" ", "", $this->status)]) . '</li>';
        }
        return "";
    }

    /**
     * Permet de check les restrictions sur un article
     * @param ?array $auth
     * @return boolean
     */
    public function checkRestrictions(?array $auth): bool {
        if (!empty($this->listeRestrictions) && isset($auth['perso'])) {
            foreach ($this->listeRestrictions as $restriction) {
                if (!$restriction->verifieDroit($auth['perso'], $auth['autorisations'])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Méthode permettant de savoir si un article a une restriction particulière
     * @param string $type
     * @param string $valeur
     * @return boolean
     */
    public function hasRestriction(string $type, string $valeur): bool {
        if (!empty($this->listeRestrictions)) {
            foreach ($this->listeRestrictions as $restriction) {
                if ($type == $restriction->type) {
                    switch($type) {
                        case RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION:
                            $autorisation = Autorisations::findFirst($restriction->idType);
                            return ($valeur == $autorisation->nomTechnique);
                        case RestrictionsWiki::RESTRICTIONWIKI_ROYAUME:
                            $royaume = Royaumes::findFirst($restriction->idType);
                            return ($valeur == $royaume->nom);
                        case RestrictionsWiki::RESTRICTIONWIKI_RACE:
                            $race = Races::findFirst($restriction->idType);
                            return ($valeur == $race->nom);
                        case RestrictionsWiki::RESTRICTIONWIKI_GRADE:
                            $grade = Grades::findFirst($restriction->idType);
                            return ($valeur == $grade->nom);
                        case RestrictionsWiki::RESTRICTIONWIKI_RELIGION:
                            $religion = Religions::findFirst($restriction->idType);
                            return ($valeur == $religion->nom);
                    }
                }
            }
        }
        return false;
    }

    /**
     * Méthode permettant d'identifier si le personnage est un contributeur
     * @param Personnages $perso
     * @return boolean
     */
    public function isContributeur(Personnages $perso): bool {
        if (!empty($this->contributeurs)) {
            foreach ($this->contributeurs as $contributeur) {
                if ($contributeur->id == $perso->id) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Méthode permettant de générer la liste des mots clefs
     * @return string
     */
    public function genererMotClef(): string {
        $retour = "";
        if (!empty($this->listeMotsClef)) {
            foreach ($this->listeMotsClef as $motClef) {
                $retour .= "<div class='motClef' onclick='rechercheMotClef(" . $motClef->id . ");'><span class='valeurMotClef'>" . $motClef->libelle . "</span></div>";
                $retour .= '<input type="button" class="buttonDeleteMotClef" onclick="boxRetirerMotClef(' . $motClef->id . ');"/>';
            }
        }
        $retour .= "<input type='hidden' id='idMotclefDelete' name='idMotclefDelete' value=''/>";
        return $retour;
    }

    /**
     * Méthode pour générer le fil d'arianne
     * @return string
     */
    public function genererFilArianne(): string {
        $listeParents = $this->getListeParents($this->idHierarchie);
        $filArianne = "";
        if (!empty($listeParents)) {
            $filArianne = "<span class='filArianne'>" . Phalcon\Tag::linkTo(["wiki/", "Index"]) . "</span>";
            $filArianneBis = "";
            foreach ($listeParents as $parent) {
                if ($parent) {
                    $filArianneBis = "<span class='filArianne'>&nbsp; > &nbsp;" . Phalcon\Tag::linkTo(["wiki/article?id=" . $parent->id, $parent->titre]) . "</span>" . $filArianneBis;
                }
            }
        }
        return $filArianne . $filArianneBis;
    }

    /**
     * Permet de retourner le fil d'Arianne à partir de l'identifiant du père
     * @param int $idPere
     * @return array
     */
    private function getListeParents(int $idPere): array {
        $filArianne = array();
        $pere = Articles::findFirst(["id = :id:",
          "bind" => ["id" => $idPere]
        ]);
        $filArianne[0] = $pere;
        if ($pere) {
            if ($pere->idHierarchie != 0) {
                do {
                    $parent = Articles::findFirst($pere->idHierarchie);
                    $filArianne[count($filArianne)] = $parent;
                    $pere = $parent;
                } while ($pere->idHierarchie != 0);
            }
        }
        return $filArianne;
    }

    /**
     * Construit la liste des contributeurs
     * @param string $mode
     * @return string
     */
    public function getContributeurs(string $mode): string {
        $contributeurs = "";
        if (!empty($this->contributeurs)) {
            foreach ($this->contributeurs as $contributeur) {
                $contributeurs .= "<div class='contributeur'>";
                $contributeurs .= '<span class="profil" onclick="profilPerso(' . $contributeur->id . ')">' . $contributeur->nom . '</span>';
                if ($mode == "formulaire") {
                    $contributeurs .= "<input type='button' class='buttonMoinsContributeur' onclick='retirerContributeur(" . $contributeur->id . ");'/>";
                }
                $contributeurs .= "</div>";
            }
        }
        if ($mode == "formulaire") {
            $contributeurs .= "<input type='button' class='buttonPlus' id='boutonAjouterContributeur' onclick='boxAjouterContributeur();'/>";
        }
        return $contributeurs;
    }

    /**
     * Retourne le texte d'un article
     * @param ?array $auth
     * @param bool $edition
     * @return array|string|string[]|null
     */
    public function formateTexteArticle(?array $auth, bool $edition = false) {
        return Fonctions::formatTexte($this->contenu, $auth, $edition);
    }

    /**
     * Formate les restrictions pour l'article
     * @param string $mode
     * @param Personnages $perso
     * @return string
     */
    public function formateRestriction(string $mode, Personnages $perso): string {
        $retour = "";
        if (!empty($this->listeRestrictions)) {
            foreach ($this->listeRestrictions as $restriction) {
                $retour .= "<div class='divLibelleAutorisation'><span class='libelleAutorisation'>" . $restriction->type . "&nbsp;:&nbsp;";
                switch ($restriction->type) {
                    case RestrictionsWiki::RESTRICTIONWIKI_AUTORISATION :
                        $autorisation = Autorisations::findFirst($restriction->idType);
                        $retour .= $autorisation->libelle . "</span>";
                        break;
                    case RestrictionsWiki::RESTRICTIONWIKI_ROYAUME :
                        $royaume = Royaumes::findFirst($restriction->idType);
                        $retour .= $royaume->nom . "</span>";
                        break;
                    case RestrictionsWiki::RESTRICTIONWIKI_RACE :
                        $race = Races::findFirst($restriction->idType);
                        $retour .= $race->nom . "</span>";
                        break;
                    case RestrictionsWiki::RESTRICTIONWIKI_GRADE :
                        $grade = Grades::findFirst($restriction->idType);
                        $retour .= $grade->nom . "</span>";
                        break;
                    case RestrictionsWiki::RESTRICTIONWIKI_RELIGION :
                        $religion = Religions::findFirst($restriction->idType);
                        $retour .= $religion->nom . "</span>";
                        break;
                }
                if ($mode == "formulaire") {
                    $retour .= '<input type="button" class="buttonMoinsAutorisationWiki" onclick="retirerRestriction(' . $restriction->id . ');"/></div>';
                } else {
                    $retour .= "</div>";
                }
                $retour = $retour . '<br/>';
            }
        }
        /*Bloc d'ajout pour le mode edition*/
        if ($mode == "formulaire") {
            $listeTypeRestriction = RestrictionsWiki::getListeTypeRestrictions();
            $retour = $retour . "<select id='typeRestriction' class='listeDeroulante' onChange='chargerListeRestriction();'><option value='0'>Choisissez une restriction</option>";
            for ($i = 0; $i < count($listeTypeRestriction); $i++) {
                $restriction = $listeTypeRestriction[$i];
                $retour .= "<option value='$restriction'>" . $restriction . "</option>";
            }
            $retour = $retour . "</select>";
            $retour = $retour . "<span id='choixRestriction'></span>";
        }
        return $retour;
    }

    /**
     * Retourne la liste des status pour les articles
     * @return string[]
     */
    public static function getListeStatusArticles(): array {
        $listeStatus = array();
        $listeStatus[0] = Articles::STATUS_IN_PROGESS;
        $listeStatus[1] = Articles::STATUS_WAITING_VALIDATION;
        $listeStatus[2] = Articles::STATUS_DEMANDE_DE_REVISION;
        $listeStatus[3] = Articles::STATUS_VALIDATED;
        $listeStatus[4] = Articles::STATUS_ARCHIVED;
        return $listeStatus;
    }

    /**
     * Permet de créer un article
     * @param int $idPerso
     * @return Articles
     */
    public static function creerArticle(int $idPerso): Articles {
        $autre = Articles::findFirst(["titre = :titre:", "bind" => ["titre" => Articles::TITRE_GENERIQUE]]);
        $article = new Articles();
        $article->contenu = "Votre texte";
        $article->dateCreation = time();
        $article->dateModification = time();
        $article->idAuteur = $idPerso;
        $article->idHierarchie = $autre->id;
        $article->status = Articles::STATUS_PROPOSITION;
        $article->img = Articles::IMAGE_GENERIQUE;
        $article->titre = "Votre titre";
        $article->save();
        return $article;
    }

    /**
     * Permet de vérifier les droits d'édition sur un article
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitEditer(?array $auth): bool {
        $perso = isset($auth) ? $auth['perso'] : null;
        $autorisations = isset($auth) ? $auth['autorisations'] : null;
        //Personne ne peut modifier un article au status "en attente de validation" au status "en demande de revision" ou au status "archive"
        if ($this->status != Articles::STATUS_WAITING_VALIDATION && $this->status != Articles::STATUS_DEMANDE_DE_REVISION && $this->status != Articles::STATUS_ARCHIVED) {
            //Si MJ BG ou ADMIN BG
            if (Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $autorisations)) {
                return true;
            }
            //Si statut de l'article est IN PROGRESS
            if ($this->status == Articles::STATUS_IN_PROGESS || $this->status == Articles::STATUS_PROPOSITION) {
                if (!empty($perso) && $perso instanceof Personnages) {
                    //S'il s'agit de l'auteur
                    if ($this->idAuteur == $perso->id) {
                        return true;
                    }
                    //S'il s'agit d'un contributeur
                    if (!empty($this->contributeurs)) {
                        foreach ($this->contributeurs as $contributeur) {
                            if ($contributeur->id == $perso->id) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès à l'historique
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitHistorique(?array $auth): bool {
        //Si MJ BG et Admin BG, afficher tout le temps
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $auth['autorisations'])) {
            return true;
        }

        //Si le status est IN_PROGRESS les membres ont accès à l'historique
        //Si statut de l'article est IN PROGRESS
        if ($this->status == Articles::STATUS_IN_PROGESS) {
            if (!empty($auth['perso'])) {
                //S'il s'agit de l'auteur
                if ($this->idAuteur == $auth['perso']->id) {
                    return true;
                }
                //S'il s'agit d'un contributeur
                if (!empty($this->contributeurs)) {
                    foreach ($this->contributeurs as $contributeur) {
                        if ($contributeur->id == $auth['perso']->id) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }


    /**
     * Méthode pour gérer l'accès aux droits de révisions
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitReviser(?array $auth): bool {
        //Si MJ BG ou ADMIN BG et status Waiting Validation ou status Demande Révision
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $auth['autorisations']) && ($this->status == Articles::STATUS_WAITING_VALIDATION || $this->status == Articles::STATUS_DEMANDE_DE_REVISION)) {
            return true;
        }
        //Si ADMIN BG et status Archivé
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $auth['autorisations']) && $this->status == Articles::STATUS_ARCHIVED) {
            return true;
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès aux droits de restauration
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitRestaurer(?array $auth): bool {
        //Si ADMIN BG et status Archive
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $auth['autorisations']) && $this->status == Articles::STATUS_ARCHIVED) {
            return true;
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès aux droits d'annulation
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitAnnulerReviser(?array $auth): bool {
        //Si MJ BG ou ADMIN BG et Demande Révision
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $auth['autorisations']) && $this->status == Articles::STATUS_DEMANDE_DE_REVISION) {
            return true;
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès aux droits de validation
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitValider(?array $auth): bool {
        //Si MJ BG ou ADMIN BG et status Waiting Validation
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $auth['autorisations']) && $this->status == Articles::STATUS_WAITING_VALIDATION) {
            return true;
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès aux droits de suppression
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitSupprimer(?array $auth): bool {
        //Si ADMIN BG - On peut supprimer n'importe quel article
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $auth['autorisations'])) {
            return true;
        }
        return false;
    }

    /**
     * Méthode pour gérer l'accès aux droits d'archivage
     * @param ?array $auth
     * @return boolean
     */
    public function checkDroitArchiver(?array $auth): bool {
        //Si ADMIN BG - On ne peut archiver que les articles au statut validé ou en cours de progression
        if (isset($auth) && Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $auth['autorisations']) && ($this->status == Articles::STATUS_VALIDATED || $this->status == Articles::STATUS_IN_PROGESS || $this->status == Articles::STATUS_DEMANDE_DE_REVISION)) {
            return true;
        }
        return false;
    }

    /**
     * Vérifie si l'association entre l'article et ce mot clef
     * existe déjà
     * @param Motsclef $motClef
     * @return boolean
     */
    public function verifAssociationMotClef(Motsclef $motClef): bool {
        foreach ($this->listeMotsClef as $motClefEnCours) {
            if ($motClef->id == $motClefEnCours->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retourne la liste des fils pour l'édition
     * @param ?array $auth
     * @return string
     */
    public function getListeFilsEdition(?array $auth): string {
        $retour = "<inpyt type='hidden' name='idFils' id='idFils' value=''/>";
        $listeFils = Articles::getListeFils($this->id);
        if (!empty($listeFils)) {
            foreach ($listeFils as $fils) {
                if ($this->checkRestrictions($auth)) {
                    $retour .= '<div class="divFilsArticle">';
                    $retour .= '<span class="fils">' . Phalcon\Tag::linkTo(["wiki/article?id=" . $fils->id, $fils->titre]);
                    $retour .= "</span>";
                    $retour .= '<input type="button" class="buttonMoinsFils" onclick="boxRetirerFils(' . $fils->id . ');"/>';
                    $retour .= '</div>';
                }
            }
        }
        return $retour;
    }

    /**
     * Permet d'ajouter une restriction
     * @param string $type
     * @param int $id
     */
    public function addRestriction(string $type, int $id) {
        $restriction = new Restrictionswiki();
        $restriction->idArticle = $this->id;
        $restriction->type = $type;
        $restriction->idType = $id;
        $restriction->save();
    }

    /**
     * Méthode pour vérifier qu'une restriction est présente ou non sur l'article
     * @param string $type
     * @param int $id
     * @return boolean
     */
    public function isRestrictionPresente(string $type, int $id): bool {
        if (!empty($this->listeRestrictions)) {
            foreach ($this->listeRestrictions as $restriction) {
                if ($id == $restriction->idType && $type == $restriction->type) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Permet de récupérer la liste des paragraphes (id des titres)
     * d'un article
     * @return string[]
     */
    public function getListeParagraphe(): array {
        $texte = $this->contenu;
        $texte = str_replace('\\', '', $texte);
        $texte = str_replace('[titre]', '[Titre]', $texte);
        $texte = str_replace('[/titre]', '[/Titre]', $texte);
        return Fonctions::chope_string_entre_deux_delimiteur("[Titre]", $texte, "[/Titre]");
    }
}
