<?php

use Phalcon\Mvc\Controller;

class BugController extends Controller {

    public function indexAction() {
        $auth = $this->session->get("auth");
        $this->view->setVar("auth", $auth);
        $token = uniqid();
        $this->session->set("token_bug", $token);
        $this->view->setVar("token", $token);
        $this->pageview();
    }

    public function creerIssueAction() {
        $this->view->disable();
        $reponse = new Phalcon\Http\Response();
        $this->session->set("retour-bug", 1);
        if ($this->request->isPost() == true && $this->session->has("auth")) {
            if (
              $this->request->getPost("token") === $this->session->get("token_bug") &&
              $this->request->hasPost("description") &&
              $this->request->hasPost("titre")
            ) {
                $this->session->remove("token_bug");
                $github = new GitHub();
                $titre = $this->request->getPost("titre");
                $urlImage = null;
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles()[0];
                    if (in_array($file->getRealType(), ["image/png", "image/jpeg"])) {
                        $fileContent = base64_encode(file_get_contents($file->getTempName()));
                        $aux = explode('.', $file->getName());
                        $extension = $aux[count($aux) - 1];
                        $urlImage = $github->ajouterImage(uniqid("imageIssue") . '.' . $extension, $fileContent, $titre);
                    }
                }
                $retourAPI = $github->creerIssue($titre, $this->request->getPost("description") . (isset($urlImage) ? "\n![image](" . $urlImage . ")" : ""));
                if ($retourAPI === 0) {
                    $this->session->set("retour-bug", 0);
                }
            }
        }
        return $reponse->redirect('bug');
    }

    /**
     * Méthode générique pour la page
     */
    private function pageview() {
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/balises.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/bug/bug.css?v=" . VERSION);
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/utils/balise.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->view->setTemplateAfter("common");
    }
}