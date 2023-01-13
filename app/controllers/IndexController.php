<?php

class IndexController extends ControllerBase {

    public function indexAction() {
        // Forward to the login form again
        return $this->response->redirect("accueil");
    }

    public function notFoundAction() {
        return $this->response->redirect("accueil");
    }

    public function error404Action() {
        return $this->response->redirect("accueil");
    }
}

