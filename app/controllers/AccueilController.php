<?php

use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;

class AccueilController extends Controller {

    public function indexAction() {
        $auth = $this->session->get('auth');
        $this->view->menu = Fonctions::genererMenu("init", $auth);
        $this->view->auth = $auth;
        $this->pageview();
    }

    //############### Fonctions pour la connexion/redirection #########//

    /**
     * Vérifie la connection
     * @return ResponseInterface|void
     */
    public function connectionAction() {
        if ($this->request->isPost()) {
            // Get the data from the user
            $login = $this->request->getPost("login");
            $pwd = $this->request->getPost("pwd");
            // Find the user in the database
            $perso = Personnages::findFirst(["nom = :login: AND pwd = :pwd:",
                "bind" => [
                  "login" => $login,
                  "pwd" => sha1($pwd),
                ]
              ]
            );

            if ($perso !== null) {
                $this->_registerSession($perso);
                $this->flash->success("Welcome " . $perso->nom);
                // Forward to the 'invoices' controller if the user is valid
                return $this->response->redirect("accueil");
            }
            $this->flash->error("Wrong email/password");
        }
        // Forward to the login form again
        $this->dispatcher->forward(["controller" => "accueil",
            "action" => "index",
          ]
        );
    }

    public function deconnexionAction() {
        if ($this->request->isPost()) {
            $this->session->destroy();
            return $this->response->redirect("accueil");
        }
    }

    public function redirigerAction() {
        $this->session->set("redirect", 1);
        return $this->response->redirect("accueil");
    }

    public function annuleRedirectAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $this->session->set("redirect", 0);
            }
        }
    }

    /**
     * Enregistre le personnage en session
     * @param unknown $perso
     */
    private function _registerSession($perso) {
        $this->session->set("auth", [
          "id" => $perso->id,
          "name" => $perso->nom,
          "perso" => $perso,
          "autorisations" => $perso->genererAutorisation(),
          "menu" => Fonctions::genererMenu("perso"),
          "modeMJ" => false
        ]);
        $this->session->set("redirect", 0);
        $this->session->set("errorPage", 0);
        $this->session->set("cssRepository", $perso->getThemeCSS());
    }

    //############### Fonctions pour les gestions MJ #########//
    public function accesMJAction() {
        $acces = $this->request->getPost('acces');
        $auth = $this->session->get("auth");
        $auth['modeMJ'] = $acces;
        $this->session->auth = $auth;
        return $this->response->redirect("accueil");
    }

    //############### Gestions des News #########//
    public function showAddNewsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $this->view->auth = $auth;
                $retour = $this->view->partial('accueil/news/creationNews');
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function displayDestinataireAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $destinataire = $this->request->get('type');
                $retour = News::genererSelectIdDestinataire($destinataire);
                return $retour;
            }
        }
    }

    public function creerNewsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $news = new News();
                $news->idAuteur = $auth['perso']->id;
                $news->date = time();
                $idDestinataire = $this->request->get('idDestinataire');
                if ($idDestinataire == "" || $idDestinataire == null) {
                    $idDestinataire = null;
                }
                $news->idDestinataire = $idDestinataire;
                $news->nomAuteur = $this->request->get('auteur');
                $news->texte = $this->request->get('texte');
                $news->titre = $this->request->get('titre');
                $news->type = $this->request->get('type');
                $news->typeDestinataire = $this->request->get('typeDestinataire');
                $news->save();

                //Logs de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Ajout d'une nouvelle actualité : " . $news->toString();
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                $logAdmin->save();

                $this->view->auth = $auth;
                $retour = $this->view->partial("accueil/listeNews");
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function supprimerNewsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $news = News::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);

                $action = "Suppression de la news :" . $news->titre;
                $news->delete();

                //Logs de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = $action;
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                $logAdmin->save();

                $this->view->auth = $auth;
                $retour = $this->view->partial("accueil/listeNews");
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function openPopupNewsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $news = News::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $this->view->news = $news;
                $this->view->auth = $auth;
                $retour = $this->view->partial("accueil/news/editionNews");
                $this->view->disable();
                return $retour;
            }
        }
    }

    public function modifierNewsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $auth = $this->session->get('auth');
                $news = News::findFirst(['id = :id:', 'bind' => ['id' => $this->request->get('id')]]);
                $action = $news->toString();
                $idDestinataire = $this->request->get('idDestinataire');
                if ($idDestinataire == "" || $idDestinataire == null) {
                    $idDestinataire = null;
                }
                $news->idDestinataire = $idDestinataire;
                $news->nomAuteur = $this->request->get('auteur');
                $news->texte = $this->request->get('texte');
                $news->titre = $this->request->get('titre');
                $news->type = $this->request->get('type');
                $news->typeDestinataire = $this->request->get('typeDestinataire');
                $news->save();

                //Logs de l'action
                $logAdmin = new LogsADMIN();
                $logAdmin->action = "Modifier de l'actualité : " . $news->toString() . " (Anciennement : " . $action . ")";
                $logAdmin->idPersonnage = $auth['perso']->id;
                $logAdmin->dateLog = time();
                $logAdmin->typeLog = LogsADMIN::TYPE_GESTION_DROIT;
                $logAdmin->save();

                $this->view->auth = $auth;
                $retour = $this->view->partial("accueil/listeNews");
                $this->view->disable();
                return $retour;
            }
        }
    }

    //########### Général #############//

    /**
     * Méthode générique pour la page
     */
    private function pageview() {
        $repCSS = "css/site/theme/default";
        $this->session->set("cssRepository", $repCSS);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/balises.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/accueil/accueil.css?v=" . VERSION);
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/accueil/accueil.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/utils/balise.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }
}

