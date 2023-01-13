<?php

class JouerController extends \Phalcon\Mvc\Controller {
    /**
     * Affiche l'index du wiki
     */
    public function indexAction() {
        //Calcul de la liste des articles de l'index
        $auth = $this->session->get("auth");
        $this->view->auth = $auth;

        //Initialisation du plateau
        $plateau = new Plateau();
        $plateau->initialize($auth['perso']);
        $this->session->set('plateau', $plateau);

        $this->pageview();
    }

    //Gestion du plateau

    /**
     * Permet de mettre le background du plateau de jeu
     * @return string
     */
    public function setBackgroundAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $auth = $this->session->get('auth');
                $plateau = $this->session->get('plateau');
                if (empty($plateau)) {
                    $plateau = new Plateau();
                    $plateau->initialize($auth['perso']);
                    $this->session->set('plateau', $plateau);
                }

                $backgroundImage = "/public/img/site/fonds_jeu/" . $plateau->getPeriode() . ".jpg";
                //$backgroundImage = "/public/img/site/fonds_jeu/jour.jpg";
                $backgroundImage = str_replace('//', '/', $backgroundImage);
                $backgroundImage = str_replace('\\', '/', $backgroundImage);

                //Retourne l'image à mettre dans le body de la page en background-image
                return 'url("' . $backgroundImage . '")';
            }
        }
    }

    /**
     * Permet de charger le plateau de jeu
     * @return string
     */
    public function chargerPlateauJeuAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $plateau = $this->session->get('plateau');
                $auth = $this->session->get('auth');
                if (empty($plateau) || $plateau == null) {
                    $plateau = new Plateau();
                    $plateau->initialize($auth['perso']);
                    $this->session->set('plateau', $plateau);
                }
                return $plateau->affiche($auth['perso']);
            }
        }
    }

    /**
     * Permet de charger le css des terrains
     * @return unknown
     */
    public function chargerCSSTerrainsAction() {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $plateau = $this->session->get('plateau');
                return $plateau->getStyleTerrains();
            }
        }
    }

    /**
     * Gère l'affichage de la page et les infos
     * css et javascript
     */
    private function pageview() {
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->assets->addJs("js/jouer/plateau.js?v=" . VERSION);
        $this->assets->addCss("css/site/plateau/plateau.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }
}

