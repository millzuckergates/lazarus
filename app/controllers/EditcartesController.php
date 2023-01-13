<?php

use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller as Controller;

class EditcartesController extends Controller {

    /**
     * Affiche l'index de l'édition de cartes
     * @return ?ResponseInterface
     */
    public function indexAction(): ?ResponseInterface {
        if ($this->request->isPost()) {
            $idCarte = $this->request->getPost("idCarte", "int", null);
            $carte = Cartes::findById($idCarte);
            if ($carte) {
                $this->view->nomCarte = $carte->nom;
                $this->view->xMin = $carte->xMin;
                $this->view->xMax = $carte->xMax;
                $this->view->yMin = $carte->yMin;
                $this->view->yMax = $carte->yMax;
                $this->view->idCarte = $carte->id;
                //Calcul de la liste des articles de l'index
                $auth = $this->session->get("auth");
                $this->view->auth = $auth;

                //Initialisation du plateau
                $plateau = new PlateauEdit();
                $plateau->initializeEdit($carte);
                $this->session->set('plateau', $plateau);
                $this->pageview();
            } else {
                return $this->response->redirect('/gameplay');
            }
        } else {
            return $this->response->redirect('/gameplay');
        }
        return null;
    }

    /**
     * Gère l'affichage de la page et les infos
     * css et javascript
     */
    private function pageview() {
        $this->assets->addJs("js/jquery.js?v=" . VERSION);
        $this->assets->addJs("js/utils/box.js?v=" . VERSION);
        $this->assets->addJs("js/global.js?v=" . VERSION);
        $this->assets->addJs("js/editcartes/editCartes.js?v=" . VERSION);
        $this->assets->addCss("css/site/plateau/plateau.css?v=" . VERSION);
        $this->assets->addCss("css/site/plateau/editCartes.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/box.css?v=" . VERSION);
        $this->assets->addCss($this->session->get("cssRepository") . "/style.css?v=" . VERSION);
    }

    /**
     * Permet de charger le plateau de jeu
     * @return string
     */
    public function chargerPlateauJeuAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $nbDone = $this->request->getPost("nbDone", "int", 0);
                $plateau = $this->session->get('plateau');
                $str = "";
                if ($nbDone === 0) {
                    $plateau->initTableau();
                    $str = $plateau->tableau->headHTML();
                }

                if ($nbDone > ($plateau->xMax - $plateau->xMin) * ($plateau->yMax - $plateau->yMin)) {
                    return "</div>";
                } else {
                    return $str . $plateau->htmlTerrainsIsometriqueMorceaux($nbDone);
                }
            }
        }
        return "";
    }

    /**
     * Permet de charger le css des terrains
     * @return string
     */
    public function chargerCSSTerrainsAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $plateau = $this->session->get('plateau');
                return $plateau->getStyleTerrains();
            }
        }
        return "";
    }

    /**
     * Permet de charger la liste des terrains
     * @return string
     */

    public function chargerTerrainsAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $terrains = Terrains::find(['saison = :saison:', 'bind' => ['saison' => Constantes::SAISON_TOUTES]]);
                $html = "<div class=\"listeTerrains\">";
                $script = "";
                foreach ($terrains as $terrain) {
                    $terrainNom = $terrain->nom;
                    $html .= "<div class=\"clicTerrain\">";
                    $div = new Div($terrainNom, "terrainNom", 229, 30, str_replace(" ", "_", $terrainNom));
                    $html .= "<p id=\"arrow\"> › </p>";
                    $div->addStyle("display", "inline-block");
                    $div->addClic("displayPaletteTerrain(\'" . str_replace(" ", "_", $terrainNom) . "\')");
                    $html .= $div->toHTML();
                    $html .= "</div>";
                    $html .= "<div id=\"" . str_replace(" ", "_", $terrain->nom) . "_images\" style=\"display: none;\">";
                    $nbImages = count(explode(';', $terrain->repartition));
                    // Affiche la liste de terrains dans un tableau
                    for ($i = 1; $i <= $nbImages; $i++) {
                        $aux = $this->generateHtmlTerrainEdit($terrain, $i);
                        $html .= $aux["html"];
                        $script .= $aux["script"];
                    }
                    $html .= "</div>";               
                }
                $html .= "</div>"; 
                $script .= "jQuery('#terrains').html('$html');";
                return $script;
            }
        }
        return "";
    }

    /**
     * Permet de charger la liste des textures
     * @return string
     */
    public function chargerTexturesAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $textures = Textures::find();
                $html = "<div class=\"listeTexturesPalette\">";
                $script = "";
                foreach ($textures as $texture) {
                    $idDiv = str_replace(" ", "_", $texture->nom);
                    $idTexture = $texture->id;
                    $htmlImage = Textures::getHTMLFromId($idTexture, -1);
                    $divTexture = new Div($htmlImage, "textureImage", 80, 40, $idDiv);
                    $divTexture->addStyle(["position" => "relative", "top" => "0", "left" => "0"]);
                    $textureButton = new Div($divTexture, false, 80, 40);
                    $textureButton->addStyle("margin", "10px 0");
                    $textureButton->addStyle("display", "inline-block");
                    $textureButton->addClic("currentTexture=\'${idTexture}\';$(\'#textureSelectionnee\').html($(\'#" . $idDiv . "\').clone().attr(\'id\', \'\'));");
                    $html .= $textureButton->toHTML();
                }
                $html .= "</div><div id=\"suppBtn\">";
                // Affichage de l'image du bouton de suppression de texture (child)
                $html .= "<p class=\'suppTextureTitre\'>Supprimer une texture</p>";
                $divTexture = new Div(false, false, 60, 30, "suppTexture");
                $divTexture->addClic("$(\'#suppTexture\').css(\'border\',\'2px inset grey\');$(\'#grille\').css(\'cursor\',\'pointer\');");
                $divTexture->addStyle("margin", "0"); // /!\ Ne pas supprimer /!\ Fais disparaitre l'image si la propriété est supprimée ou placée dans le fichier scss               
                // Création du bouton de suppression de texture (container)
                $textureButton = new Div($divTexture, false, 80, 40, "suppTextureBloc");
                $textureButton->addClic("currentTexture=null;$(\'#textureSelectionnee\').html($(\'#suppTexture\').clone().attr(\'id\', \'\'));");
                $html .= $textureButton->toHTML() .
                  "</div>" . "</div>";
                $script .= "jQuery('#textures').html('$html');";
                return $script;
            }
        }
        return "";
    }

    /**
     * Génère le HTML pour mettre à jour la carte
     * @return string
     */
    public function updateCaseAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $idTerrain = $this->request->getPost("idTerrain", "string", null);
                $divCase = new Div($this->contenuFromTexture(), "t_${idTerrain}");

                return $divCase->toHTML();
            }
        }
        return "";
    }

    /**
     * Retourne le contenu des textures
     * @return string
     */
    private function contenuFromTexture(): string {
        $textures = json_decode(html_entity_decode($this->request->getPost("idTextures", "string", null)));
        $contenu = "";
        if ($textures) {
            $aux = [];
            foreach ($textures as $numTexture => $idTexture) {
                $aux[$numTexture] = $idTexture;
            }
            krsort($aux, SORT_NUMERIC);
            foreach ($aux as $numTexture => $idTexture) {
                $contenu .= Textures::getHTMLFromId($idTexture, $numTexture);
            }
        }
        return $contenu;
    }

    /**
     * Sauvegarde les modifications de la carte
     * @return string
     */
    public function saveCarteAction(): string {
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true) {
                $this->view->disable();
                $idCarte = $this->request->getPost("idCarte", "int", null);
                $modifications = json_decode(html_entity_decode($this->request->getPost("modifications", "string", null)));
                $carte = Cartes::findById($idCarte);
                if (!$carte) {
                    return "Carte non trouvée";
                }
                foreach ($modifications as $modification) {
                    $modification->idTextures = json_decode(html_entity_decode($modification->idTextures));
                    $carte->updateCase($modification);
                }

                return "Carte mise à jour";
            }
        }
        return "";
    }

    /**
     * Génère le HTML pour l'édition de cartes à partir d'un terrain (liste des terrains)
     * @param Terrains $terrain
     * @param integer $idImage
     * @return array
     */
    private function generateHtmlTerrainEdit(Terrains $terrain, int $idImage): array {
        $idTerrain = $terrain->id;
        $urlImage = $terrain->getImageTerrain($idImage);
        $className = "t_" . $idTerrain . "_" . $idImage;
        $divTerrain = new Div(false, $className);
        $divTerrain->addStyle(["position" => "relative", "top" => "0", "left" => "0", "background-size" => "100%", "background-position" =>"center", "background-repeat" =>"no-repeat", "width" =>"80px", "height" =>"80px" ]);
        $terrainButton = new Div($divTerrain);
        $terrainButton->addStyle(["display" => "inline-block", "width" => "80px", "height" => "80px"]);
        // $terrainButton->addOut("this.style.backgroundColor=\'transparent\'");
        $terrainButton->addClic("currentTerrain=\'${idTerrain}_${idImage}\';$(\'#terrainSelectionne\').html(\'\').removeClass().addClass(\'$className\');");
        return [
          "html" => $terrainButton->toHTML(),
          "script" => "createCSSClass('" . '.' . $className . "','z-index: " . $terrain->zIndex . ";width:128px;height:64px;background-image: url(\"" . $urlImage . '");\');' . NL
        ];
    }
}