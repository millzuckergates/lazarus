<?php

class UtilsController extends \Phalcon\Mvc\Controller {

    public function indexAction() {

    }

    /**
     * Méthode permettant de charger les suggestions d'article pères
     * @return unknown
     */
    public function chargerSuggestionPereAction() {
        if ($this->request->isGet() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $idArticle = $this->request->get("idArticle");
                // on empeche la mise en cache
                header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                // on fixe le content-type
                header('Content-Type: text/html; charset=utf-8');
                // on recupère la recherche
                $recherche = $this->request->get("recherche");
                // si la recherche n'est pas vide
                if (strlen($recherche) > 0) {
                    // on initialise le tableau qui sera transformé en json
                    $a_json = array();
                    $a_json_row = array();

                    $articles = Articles::find(["titre LIKE :titre: AND status = :status: AND id != :id: LIMIT 5",
                        "bind" => ["titre" => '%' . $recherche . '%', "status" => Articles::STATUS_VALIDATED, "id" => $idArticle]
                      ]
                    );
                    if (!empty($articles)) {
                        foreach ($articles as $article) {
                            if ($article->titre != "") {
                                $a_json_row["titre"] = $article->titre;
                                array_push($a_json, $a_json_row);
                            }
                        }
                    }
                }
                // on echo notre tableau en json
                $this->response->setContent(json_encode($a_json));
                return $this->response;
            }
        }
    }

    /**
     * Méthode permettant de charger les suggestions d'article
     * @return unknown
     */
    public function chargerSuggestionArticleAction() {
        if ($this->request->isGet() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                // on empeche la mise en cache
                header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                // on fixe le content-type
                header('Content-Type: text/html; charset=utf-8');
                // on recupère la recherche
                $recherche = $this->request->get("recherche");
                // si la recherche n'est pas vide
                if (strlen($recherche) > 0) {
                    // on initialise le tableau qui sera transformé en json
                    $a_json = array();
                    $a_json_row = array();

                    $articles = Articles::find(["titre LIKE :titre: AND status = :status: LIMIT 5",
                        "bind" => ["titre" => '%' . $recherche . '%', "status" => Articles::STATUS_VALIDATED]
                      ]
                    );
                    if (!empty($articles)) {
                        foreach ($articles as $article) {
                            if ($article->titre != "") {
                                $a_json_row["titre"] = $article->titre;
                                array_push($a_json, $a_json_row);
                            }
                        }
                    }
                }
                // on echo notre tableau en json
                $this->response->setContent(json_encode($a_json));
                return $this->response;
            }
        }
    }

    /**
     * Permet de télécharger un fichier
     */
    public function downloadFileAction() {
        $file = $this->request->get('file');
        $file = str_replace("@", "\\", rawurldecode($file));
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $file . "");
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: binary/octet-stream");
        readfile($file);
    }

    /**
     * Permet de remplir la popup article dans le jeu
     * @return string
     */
    public function openPopupWikiAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $idArticle = $this->request->get('idArticle');
                $article = Articles::findFirst(['id = :id:', 'bind' => ['id' => $idArticle]]);
                if ($article != false) {
                    if ($article->checkRestrictions($auth)) {
                        $this->view->article = $article;
                        $this->view->auth = $auth;
                        $retour = $this->view->partial('utils/popuparticle');
                    } else {
                        $retour = "errorAcces";
                    }
                } else {
                    $retour = "error";
                }
                $this->view->disable();
                return $retour;
            }
        }
    }


    //######## Utilitaires pour la gestion des formules ##########//

    /**
     * Permet de charger la liste des constantes par type
     * @return string
     */
    public function chargerListeConstanteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $type = $this->request->get('type');
                return Constantesjeu::genererListeConstantes($type, 'selectListeConstantes');
            }
        }
    }

    /**
     * Permet de charger la description d'une constante
     * @return string
     */
    public function chargerDescriptionConstanteAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $constanteJeu = Constantesjeu::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('idConstante')]]);
                return $constanteJeu->description;
            }
        }
    }

    /**
     * Permet de remplir la popup formule avec son contenu
     * @return unknown
     */
    public function afficherFormulaireFormuleAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                return $this->view->partial('utils/formule');
            }
        }
    }


    //############ Table de Ciblage ###########//

    /**
     * Permet d'ouvrir la table de ciblage pour les sorts
     * @return unknown
     */
    public function openPopupTableCiblage() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $idSort = $this->request->get('id');
                $sort = Sorts::findFirst(['id = :id:', 'bind' => ['id' => $idSort]]);
                $tableCiblage = Tableciblage::findFirst(['id = :id', 'bind' => ['id' => $sort->idCiblage]]);
                $this->view->tableCiblage = $tableCiblage;
                $this->view->type = "sort";
                $this->view->idType = $sort->id;
                $retour = $this->view->partial('utils/tableciblage.phtml');
                $this->view->disable();
                return $retour;
            }
        }
    }
}

