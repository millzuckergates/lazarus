<?php

use Phalcon\Mvc\View;

class WikiController extends \Phalcon\Mvc\Controller {

    /**
     * Affiche l'index du wiki
     */
    public function indexAction() {
        //Calcul de la liste des articles de l'index
        $auth = $this->session->get("auth");
        $listeIndex = Articles::getListeIndex();
        $indexAutre = Articles::findFirst(["titre = 'Autres'"]);

        //Set les variables pour la vue
        $this->view->listeIndex = $listeIndex;
        $this->view->auth = $auth;
        $this->view->indexAutre = $indexAutre;

        $this->pageview();
    }

    /**
     * Affiche l'article de l'action
     */
    public function articleAction() {
        $auth = $this->session->get("auth");
        $this->view->auth = $auth;
        if (!empty($this->request->get("id"))) {
            $article = Articles::findFirst($this->request->get("id"));
        } else {
            if (!empty($this->request->get("nomArticle"))) {
                $article = Articles::findFirst(["titre LIKE :titre:", "bind" => ["titre" => $this->request->get("nomArticle")]]);
            }
        }

        if (!empty($article) && $article != false) {
            //check les restrictions
            if (($article->checkRestrictions($auth) || $article->isContributeur($auth['perso']))) {
                $this->view->article = $article;
            } else {
                return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
            }
        } else {
            return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
        }
        $this->pageview();
    }

    /**
     * Lance la recherche
     */
    public function rechercheAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $titre = $this->request->get('titre');
                $contenu = $this->request->get('contenu');
                $motClef = $this->request->get('motClef');
                $status = $this->request->get('status');

                //Récupération de la liste des articles correspondant à la recherche
                $condition = "SELECT * FROM Articles WHERE idHierarchie <> 0";
                if ($titre != "") {
                    $condition .= " AND titre LIKE '%" . $titre . "%'";
                }

                if ($contenu != "") {
                    $motsrecherche = explode(" ", $contenu);
                    for ($i = 0; $i < count($motsrecherche); $i++) {
                        if (strlen($motsrecherche[$i]) > 2) {
                            $condition .= " AND contenu LIKE '%" . $motsrecherche[$i] . "%'";
                        }
                    }
                }

                if ($motClef != "") {
                    //On Récupère le mot clef
                    $motClef = MotsClef::findFirst(array("libelle" => $motClef));
                    if (!empty($motClef) && $motClef != null) {
                        //récupération des articles avec ce mot clef
                        $listeAssoc = AssocArticleMotclef::getArticleByMotClef($motClef->id);
                        if (!empty($listeAssoc)) {
                            $condition .= " AND id IN (";
                            $first = true;
                            foreach ($listeAssoc as $assoc) {
                                if ($first) {
                                    $condition .= $assoc->idArticle;
                                    $first = false;
                                } else {
                                    $condition .= "," . $assoc->idArticle;
                                }
                            }
                            $condition = $condition . ")";
                        }
                    }
                }

                if ($status != "") {
                    $condition .= " AND status LIKE '" . $status . "'";
                }

                $query = $this->modelsManager->createQuery($condition);
                $articles = $query->execute();
                if (count($articles) != 1) {
                    $this->view->pick("wiki/recherche");
                    $this->view->articles = $articles;
                    $auth = $this->session->get("auth");
                    $this->view->auth = $auth;
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $this->response->send();
                } else {
                    $this->view->pick("wiki/article");
                    $this->view->article = $articles[0];
                    $this->view->auth = $auth;
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $this->response->send();
                }
            }
        }
        $this->pageview();
    }

    /**
     * Crée un article en proposition
     */
    public function creerArticleAction() {
        $auth = $this->session->get("auth");

        //On recupère l'article "Autres";
        $article = Articles::creerArticle($auth['perso']->id);
        Historiqueswiki::ajouterHistorique($article->id, $auth['perso']->id, "Création de l'article.", "~", "Création");

        $this->view->article = $article;
        $this->view->auth = $auth;
        $this->view->pick("wiki/editerArticle");
        $this->pageview();
    }

    /**
     * Permet de modifier le "père" de l'article
     * @return string
     */
    public function ajouterPereAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $article = Articles::findFirst($this->request->get("idArticle"));
                $titrePere = $this->request->get("titrePere");
                $auth = $this->session->get("auth");
                if ($titrePere == "") {
                    if (Autorisations::hasAutorisation(Autorisations::ADMINISTRATION_WIKI, $auth['autorisations'])) {
                        $article->idHierarchie = 0;
                        $article->save();
                        //Ajout à l'historique
                        Historiqueswiki::ajouterHistorique($article->id, $auth['perso']->id, "Changement de hiérarchie : A la racine", "~", "Changement hiérarchie");
                        //Generation retour
                        return $article->genererFilArianne();
                    } else {
                        return "errorDroit";
                    }
                } else {
                    //Récupération de l'article père
                    $articlePere = Articles::findFirst(["titre = :titrePere:", "bind" => ["titrePere" => $titrePere]]);
                    if ((empty($articlePere) || $articlePere->status != Articles::STATUS_VALIDATED) && !Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $auth['autorisations'])) {
                        return "errorArticle";
                        die();
                    } else {
                        $article->idHierarchie = $articlePere->id;
                        $article->save();
                        //Historique
                        HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Changement de hiérarchie :" . $titrePere, "~", "Changement hiérarchie");
                        //Generation retour
                        return $article->genererFilArianne();
                    }
                }
            }
        }
    }

    /**
     * Permet d'enregistrer un article
     * @return string
     */
    public function saveArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idArticle = $this->request->get('idArticle');
                $newContenu = $this->request->get('contenu');
                $newTitre = $this->request->get('titre');
                $newImg = $this->request->get('img');

                //Vérification unicité du titre
                $articleTitre = Articles::findFirst(["titre = :titre: AND id != :id:", "bind" => ["titre" => $newTitre, "id" => $idArticle]]);
                if ($articleTitre) {
                    return "errorTitre";
                } else {
                    $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $idArticle]]);
                    $oldArticle = $article;
                    $article->contenu = $newContenu;
                    $article->titre = $newTitre;
                    $article->img = $newImg;
                    $article->status = Articles::STATUS_IN_PROGESS;
                    $article->dateModification = time();
                    $article->save();

                    //Ajout à l'historique
                    $contenu = "Titre : " . $oldArticle->titre . "\n Img :" . $oldArticle->img . "\n \n" . $oldArticle->contenu;
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Modification de l'article", $contenu, "Edition");


                }
            }
        }
    }

    /**
     * Permet de supprimer un article
     * @return string
     */
    public function supprimerArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $idArticle = $this->request->get('idArticle');
                $article = Articles::findFirst($idArticle);
                if ($article->titre == Articles::TITRE_GENERIQUE) {
                    return "error";
                } else {
                    //On supprime l'article
                    $article->delete();
                    //On redirige donc vers l'index
                    return $this->getDi()->get('config')->application->baseUri . "wiki/";
                }
            }
        }
    }

    /**
     * Permet de demander la révision d'un article
     */
    public function demandeRevisionArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $idArticle = $this->request->get("idArticle");
                $auth = $this->session->get("auth");
                $commentaire = $this->request->get("commentaire");

                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $idArticle]]);
                $article->status = Articles::STATUS_DEMANDE_DE_REVISION;
                $article->save();

                //Ajout à l'historique
                HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, $commentaire, "~", "Demande de révision");

            }
        }
    }

    /**
     * Permet de demander la validation d'un article
     * @return string
     */
    public function demanderValidationArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get("idArticle")]]);
                if ($article->status != Articles::STATUS_IN_PROGESS) {
                    return "errorStatus";
                } else {
                    $auth = $this->session->get("auth");
                    $article->status = Articles::STATUS_WAITING_VALIDATION;
                    $article->save();
                    //On ajoute une ligne dans l'historique
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Une validation a été demandée pour cet article", $article->contenu, "Demande de Validation");

                    //Redirection vers l'accueil
                    return $this->getDi()->get('config')->application->baseUri . "wiki/";
                }
            }
        }
    }

    /**
     * Permet de charger la page pour éditer un article
     */
    public function editerArticleAction() {
        if ($this->request->isPost() == true && $this->request->get("idArticle")) {
            $auth = $this->session->get("auth");
            $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get("idArticle")]]);
            if ($article->checkDroitEditer($auth)) {
                $this->view->article = $article;
                $this->view->auth = $auth;
                $this->view->pick("wiki/editerArticle");
                $this->pageview();
            } else {
                return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
            }
        } else {
            return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
        }
    }

    /**
     * Permet de charger la page pour valider un article
     */
    public function validerArticleAction() {
        if ($this->request->isPost() == true) {
            $auth = $this->session->get("auth");
            $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get("idArticle")]]);
            if ($article->checkDroitValider($auth)) {
                $article->status = Articles::STATUS_VALIDATED;
                $article->save();

                //Ajout d'une ligne dans l'historique
                HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "L'article a été validé", $article->contenu, "Validation");

                //Forward vers l'index

                $this->flash->success("L'article a été correctement validé.");
                return $this->response->redirect("wiki");
            } else {
                return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
            }
        }
    }

    /**
     * Méthode permettant de passer un article en révision
     */
    public function reviserArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get('idArticle')]]);
                if ($article->checkDroitReviser($auth)) {
                    $article->status = Articles::STATUS_IN_PROGESS;
                    $article->save();

                    //On ajoute une ligne dans l'historique
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Validation de la demande de révision de l'article", "~", "Révision validée");

                    //Redirection
                    $this->view->article = $article;
                    $this->view->auth = $auth;
                    $this->view->pick("wiki/article");
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $this->response->send();
                } else {
                    return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
                }
            }
        }
    }

    /**
     * Restaurer l'article
     */
    public function restaurerArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get('idArticle')]]);
                if ($article->checkDroitRestaurer($auth)) {
                    $article->status = Articles::STATUS_VALIDATED;
                    $article->save();

                    //On ajoute une ligne dans l'historique
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "L'article a été restauré", "~", "Restauration");

                    //Redirection
                    $this->view->article = $article;
                    $this->view->auth = $auth;
                    $this->view->pick("wiki/article");
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $this->response->send();
                } else {
                    return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
                }
            }
        }
    }

    /**
     * Permet d'annuler une demande de révision d'un article
     */
    public function annulerReviserArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get('idArticle')]]);
                if ($article->checkDroitAnnulerReviser($auth)) {
                    $article->status = Articles::STATUS_VALIDATED;
                    $article->save();

                    //On ajoute une ligne dans l'historique
                    $commentaire = $this->request->get('commentaire');
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, $commentaire, "~", "Demande de révision annulée");

                    //Redirection
                    $this->view->article = $article;
                    $this->view->auth = $auth;
                    $this->view->pick("wiki/article");
                    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                    $this->response->send();
                } else {
                    return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
                }
            }
        }
    }

    /**
     * Méthode pour archiver un article
     * @return string
     */
    public function archiverArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get('idArticle')]]);
                if ($article->checkDroitArchiver($auth)) {
                    $article->status = Articles::STATUS_ARCHIVED;
                    $article->save();

                    //On ajoute une ligne dans l'historique
                    $commentaire = $this->request->get('commentaire');
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, $commentaire, "~", "Archivé");

                    //Redirection vers l'accueil
                    return $this->getDi()->get('config')->application->baseUri . "wiki/";
                } else {
                    return $this->dispatcher->forward(["controller" => "wiki", "action" => "index"]);
                }
            }
        }
    }

    /**
     * Permet de purger les articles
     * @return unknown
     */
    public function purgerArticlesAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $date = mktime(0, 0, 0, date('m'), date('d') - 3, date('y'));
                $connection = $this->getDi()->get("db");
                $connection->execute("DELETE FROM `articles` WHERE `status`='" . Articles::STATUS_PROPOSITION . "' AND `dateModification` < " . $date);
                $this->view->disable();
                $this->response->setContent($connection->affectedRows());
                return $this->response;
            }
        }
    }

    /**
     * Permet de supprimer une note d'un article
     * @return unknown
     */
    public function deleteNoteArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $note = Noteswiki::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idNote')]]);

                $note->delete();

                //Met à jour le bloc de note
                $this->view->article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $note->idArticle]]);
                $this->view->auth = $auth;
                $retour = $this->view->partial('wiki/notes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Permet d'ajouter une note à un article
     * @return unknown
     */
    public function ajouterNoteArticleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $note = new Noteswiki();
                $note->idArticle = $this->request->get('idArticle');
                $note->idAuteur = $auth['perso']->id;
                $note->dateNote = time();
                $note->contenu = $this->request->get('contenu');


                $note->save();

                //Met à jour le bloc de note
                $this->view->article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $note->idArticle]]);
                $this->view->auth = $auth;
                $retour = $this->view->partial('wiki/notes');
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Méthode permettant d'ajouter un mot clef à un article
     * @return string
     */
    public function ajouterMotClefAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $article = Articles::findFirst('id = ' . $this->request->get('idArticle'));
                $auth = $this->session->get("auth");
                $libelleMotClef = $this->request->get('motClef');

                //On recherche si le mot clef existe déjà
                $motClef = Motsclef::creer($libelleMotClef);
                //Verification si l'association existe déjà
                $verif = $article->verifAssociationMotClef($motClef);
                if (!$verif) {
                    $motClef->associeArticle($article);
                    //On rafraichi l'article
                    $article = Articles::findFirst('id = ' . $this->request->get('idArticle'));

                    //Ajout à l'historique
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Ajout du mot clef :" . $libelleMotClef, "~", "Ajout mot Clef");

                    return $article->genererMotClef();
                } else {
                    return "error";
                }
            }
        }
    }

    /**
     * Méthode permettant de supprimer un mot clef
     * @return string
     */
    public function supprimerMotClefAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get("auth");
                $idMotClef = $this->request->get('idMotClef');
                $idArticle = $this->request->get('idArticle');
                $assoc = AssocArticleMotclef::findFirst(['idArticle = :idArticle: AND idMotClef = :idMotClef:', 'bind' => ['idArticle' => $idArticle, 'idMotClef' => $idMotClef]]);
                $assoc->delete();

                $motClef = Motsclef::findFirst(['id = :id:', 'bind' => ['id' => $idMotClef]]);
                HistoriquesWiki::ajouterHistorique($idArticle, $auth['perso']->id, "Suppression du mot clef :" . $motClef->libelle, "~", "Suppression mot Clef");
                //On rafraichi l'article
                $article = Articles::findFirst('id = ' . $this->request->get('idArticle'));

                return $article->genererMotClef();
            }
        }
    }

    /**
     * Permet de lancer la recherche en cliquant sur un des mots clefs
     */
    public function rechercheMotClefAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $condition = "SELECT * FROM Articles WHERE id IN (SELECT idArticle FROM AssocArticleMotclef WHERE idMotClef =" . $this->request->get('idMotClef') . ")";
                $query = $this->modelsManager->createQuery($condition);
                $articles = $query->execute();

                $this->view->pick("wiki/recherche");
                $this->view->articles = $articles;
                $auth = $this->session->get("auth");
                $this->view->auth = $auth;
                $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
                $this->response->send();
                $this->pageview();
            }
        }
    }

    /**
     * Permet de retirer un fils d'un article
     * @return string
     */
    public function retirerFilsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $idArticle = $this->request->get('idArticle');
                $fils = Articles::findFirst(["id = :id:", "bind" => ["id" => $this->request->get('idFils')]]);
                $autre = Articles::findFirst(["titre = 'Autres'"]);
                $auth = $this->session->get('auth');

                $fils->idHierarchie = $autre->id;
                $fils->save();

                //Historique
                HistoriquesWiki::ajouterHistorique($fils->id, $auth['perso']->id, "Changement de hiérarchie : A la racine", "~", "Changement hiérarchie");
                HistoriquesWiki::ajouterHistorique($idArticle, $auth['perso']->id, "Suppression du fils : " . $fils->titre, "~", "Suppression fils");

                //Construction du retour
                $article = Articles::findFirst(["id = :id:", "bind" => ["id" => $idArticle]]);
                return $article->getListeFilsEdition($auth);
            }
        }
    }

    /**
     * Permet de charger les restrictions selon le type selectionné
     * @return string
     */
    public function chargerRestrictionsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $type = $this->request->get('type');
                $retour = Restrictionswiki::getListeRestriction($type);
                $this->view->disable();
                return $retour;
            }
        }
    }

    /**
     * Méthode pour ajouter une restriction sur un article
     * @return string
     */
    public function ajouterRestrictionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                $type = $this->request->get('type');
                $idElement = $this->request->get('id');
                $verif = $article->isRestrictionPresente($type, $idElement);
                if (!$verif) {
                    return "errorexistant";
                } else {
                    $article->addRestriction($type, $idElement);
                    //On rafraichi l'article
                    $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                    return $article->formateRestriction("formulaire", $auth['perso']);

                }
            }
        }
    }

    /**
     * Permet de retirer une restriction sur un article
     * @return string
     */
    public function retirerRestrictionAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $restriction = Restrictionswiki::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idRestriction')]]);
                $restriction->delete();

                //On rafraichi l'article
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                return $article->formateRestriction("formulaire", $auth['perso']);
            }
        }
    }

    /**
     * Permet d'ajotuer un contributeur
     * @return string
     */
    public function ajouterContributeurAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                $contributeur = Personnages::findFirst(['nom = :nom:', 'bind' => ["nom" => $this->request->get('nom')]]);
                if ($contributeur) {
                    if (!$article->isContributeur($contributeur)) {
                        $assoc = new Contributeurswiki();
                        $assoc->idArticle = $article->id;
                        $assoc->idPersonnage = $contributeur->id;
                        $assoc->save();

                        //Historisation
                        HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Ajout du contributeur :" . $contributeur->nom, "~", "Ajout contributeur");

                        //On recharge l'article
                        $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                        return $article->getContributeurs("formulaire");
                    } else {
                        return "errorContrib";
                    }
                } else {
                    return "false";
                }
            }
        }
    }

    /**
     * Permet de retirer un contributeur
     * @return string
     */
    public function retirerContributeurAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $idArticle = $this->request->get('idArticle');
                $contributeur = Personnages::findFirst(['id = :id:', 'bind' => ["id" => $this->request->get('idContributeur')]]);

                $contributeurWiki = Contributeurswiki::findFirst(['idPersonnage = :idPersonnage: AND idArticle = :idArticle:', 'bind' => ['idPersonnage' => $contributeur->id, 'idArticle' => $idArticle]]);
                $contributeurWiki->delete();

                //Historisation
                HistoriquesWiki::ajouterHistorique($idArticle, $auth['perso']->id, "Suppression du contributeur :" . $contributeur->nom, "~", "Suppression contributeur");

                //On recharge l'article
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                return $article->getContributeurs("formulaire");
            }
        }
    }

    /**
     * Permet de changer d'auteur
     * @return string
     */
    public function changerAuteurAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idArticle')]]);
                $auteur = Personnages::findFirst(['nom = :nom:', 'bind' => ["nom" => $this->request->get('nom')]]);
                if ($auteur) {
                    $article->idAuteur = $auteur->id;
                    $article->save();

                    //Historique
                    HistoriquesWiki::ajouterHistorique($article->id, $auth['perso']->id, "Changement d'auteur :" . $auteur->nom, "~", "Changement auteur");
                    return '<span class="profil" id="profilAuteurEdition" onclick="profilPerso(' . $article->idAuteur . ')">' . $auteur->nom . '</span>';
                } else {
                    return "false";
                }
            }
        }
    }

    //

    private function pageview() {
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/wiki/wiki.js?v=" . VERSION);
        $this->assets->addJs("js/utils/balise.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/wiki.css?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }
}

