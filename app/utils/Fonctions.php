<?php

/**
 * Classe avec plein de fonctions utilitaires
 * @author fvpeigne
 *
 */
class Fonctions {

    /**
     * Permet de retourner une chaine entre deux délimiteurs
     * @param string $delimiteur_deb
     * @param string $text_a_fouiller
     * @param string $delimiteur_fin
     * @return string[]
     */
    public static function chope_string_entre_deux_delimiteur(string $delimiteur_deb, string $text_a_fouiller, string $delimiteur_fin): array {
        $retour = array();
        preg_match_all("/" . preg_quote($delimiteur_deb, '/') . "(.+)" . preg_quote($delimiteur_fin, '/') . "/mU", $text_a_fouiller, $retour);
        return $retour[1];
    }

    /**
     * Permet de transformer la formule
     * @param string $formule
     * @param string $mode
     * @return string
     */
    public static function transformerFormule(string $formule, $mode = false): string {
        $decoupeFormule = Fonctions::chope_string_entre_deux_delimiteur("@", $formule, "_");
        if (count($decoupeFormule) > 0) {
            for ($i = 0; $i < count($decoupeFormule); $i++) {
                $constante = Constantesjeu::findFirst(['nom = :nom:', 'bind' => ['nom' => $decoupeFormule[$i]]]);
                if ($constante != false) {
                    if ($mode == "admin") {
                        $formule = str_replace("@" . $constante->nom . "_", $constante->alt, $formule);
                    }
                }
            }
        }
        return $formule;
    }

    /**
     * Formate un texte
     * @param string $contenu
     * @param ?array $auth
     * @param bool $edition
     * @return array|string|string[]|null
     * @noinspection RegExpRedundantEscape
     */
    public static function formatTexte(string $contenu, ?array $auth, bool $edition = false) {
        $autorisations = isset($auth) ? $auth['autorisations'] : null;
        $perso = isset($auth) ? $auth['perso'] : null;
        $texte = str_replace('\\', '', $contenu);
        if ($edition) {
            return str_replace("\n", "&#13;&#10;", $texte);
        }
        $result = $texte;
        //Balise [MJ]
        if (Autorisations::hasAutorisation(Autorisations::CONSULTATION_WIKI, $autorisations)) {
            $result = preg_replace_callback('/\[MJ\](.*?)\[\/MJ\]/s', function ($matches) {
                return '<b style="display:block;">Info MJ</b><div class="infosMJ">' . $matches[1] . '</div>';
            }, $result);
        } else {
            $result = preg_replace_callback('/\[MJ\](.*?)\[\/MJ\]/s', function () {
                return '';
            }, $result);
        }
        //Balise [MJ_BG]
        $pattern = '/\[MJ_BG\](.*?)\[\/MJ_BG\]/s';
        if (!empty($perso) && $perso instanceof Personnages && Autorisations::hasAutorisation(Autorisations::GESTION_WIKI, $autorisations)) {
            $result = preg_replace_callback($pattern, function ($matches) {
                return '<b style="display:block;font-family:Palatino,serif">Info Admin/DEV</b><div class="infosAdmin" >' . $matches[1] . '</div>';
            }, $result);
        } else {
            $result = preg_replace_callback($pattern, function () {
                return '';
            }, $result);
        }
        //Balise Gras/Bold
        $result = preg_replace_callback('/\[[g]\](.*?)\[\/[g]\]/s', function ($matches) {
            return '<b>' . $matches[1] . '</b>';
        }, $result);
        $result = preg_replace_callback('/\[[b]\](.*?)\[\/[b]\]/s', function ($matches) {
            return '<b>' . $matches[1] . '</b>';
        }, $result);
        //Balise Italique
        $result = preg_replace_callback('/\[i\](.*?)\[\/i\]/s', function ($matches) {
            return '<i>' . $matches[1] . '</i>';
        }, $result);
        //Balise Soulignée
        $result = preg_replace_callback('/\[[s]\](.*?)\[\/[s]\]/s', function ($matches) {
            return '<u>' . $matches[1] . '</u>';
        }, $result);
        //Balise Taille
        $result = preg_replace_callback('/\[(taille|size)=([^\]]+)\](.*?)\[\/(taille|size)\]/s', function ($matches) {
            return '<span style="display:inline;font-size:' . $matches[2] . '">' . $matches[3] . '</span>';
        }, $result);
        //Balise Couleur
        $result = preg_replace_callback('/\[(couleur|color)=([^\]]+)\](.*?)\[\/(couleur|color)\]/s', function ($matches) {
            return '<span style="display:inline;color:' . $matches[2] . '">' . $matches[3] . '</span>';
        }, $result);
        //Balise Chronique
        $result = preg_replace_callback('/\[chron=([^\]#]+)#?([^\]]*)\](.*?)\[\/chron\]/', function ($matches) {
            return "'<a href=\"javascript:chronique(\''.addslashes('\\" . $matches[1] . "').'\',\'\\" . $matches[2] . "\')\">\\" . $matches[3] . "</a>'";
        }, $result);
        //Balise Profil
        $result = preg_replace_callback('/\[profil\](.*?)\[\/profil\]/', function ($matches) {
            return "<a class='profil' onClick=\"profilPerso('" . addslashes($matches[1]) . "')\">" . $matches[1] . "</a>";
        }, $result);
        //Le saut de ligne
        $result = preg_replace_callback('/\n/', function () {
            return '<br/>';
        }, $result);
        //Un lien vers une autre page du wiki
        $result = preg_replace_callback('/\[\[([^\]]+)\|(.*?)\]\]/s', function ($matches) {
            return '<a href="?nomArticle=' . $matches[1] . '" class="lienArticleIn">' . $matches[2] . '</a>';
        }, $result);
        $result = preg_replace_callback('/\[\[(.*?)\]\]/s', function ($matches) {
            return '<a href="?nomArticle=' . $matches[1] . '" class="lienArticleIn">' . $matches[1] . '</a>';
        }, $result);
        //Balise citer ou quote
        $result = preg_replace_callback('/\[(citer|quote)\](.*?)\[\/(citer|quote)\]/s', function ($matches) {
            return '<b style="display:block">Citation&nbsp;:</b><div class="citation">' . $matches[2] . '</div>';
        }, $result);
        $result = preg_replace_callback('/\[(citer|quote)=([^\]]+)\](.*?)\[\/(citer|quote)\]/s', function ($matches) {
            return '<b style="display:block">' . $matches[2] . ' a dit&nbsp;:</b><div class="citation">' . $matches[3] . '</div>';
        }, $result);
        //Balise lien
        $result = preg_replace_callback('/(^|\s)http(s)?:\/\/(\S+)/', function ($matches) {
            return '<a href="http' . $matches[2] . '://' . $matches[3] . '" target="_blank">http' . $matches[2] . '://' . $matches[3] . '</a>';
        }, $result);
        $result = preg_replace_callback('/\[(lien|url)=([^\]]+)\](.*?)\[\/(lien|url)\]/', function ($matches) {
            return '<a href="' . $matches[2] . '" target="_blank">' . $matches[3] . '</a>';
        }, $result);
        $result = preg_replace_callback('/\[(lien|url)\](.*?)\[\/(lien|url)\]/', function ($matches) {
            return '<a href="' . $matches[2] . '" target="_blank">' . $matches[2] . '</a>';
        }, $result);
        //Balise Image
        $result = preg_replace_callback('/\[img\](.*?)\.(png|gif|jpeg|jpg|PNG|GIF|JPG|JPEG)\[\/img\]/', function ($matches) {
            return Phalcon\Tag::image($matches[1] . '.' . $matches[2], true);
        }, $result);
        $result = preg_replace_callback('/\[rimg\](.*?)\.(png|gif|jpeg|jpg|PNG|GIF|JPG|JPEG)\[\/rimg\]/', function ($matches) {
            return '<img src="' . $matches[1] . $matches[2] . '" alt="" border="0" style="margin: 5px; float: right">';
        }, $result);
        //Balise Titre
        $result = preg_replace_callback('/\[(Titre|titre)\](.*?)\[\/(Titre|titre)\]/s', function ($matches) {
            return '<h2>' . $matches[2] . '</h2>';
        }, $result);

        //Balise particulières
        try {
            $result = Fonctions::gestionAutorisationTags($perso, $result, $autorisations, "royaume");
            $result = Fonctions::gestionAutorisationTags($perso, $result, $autorisations, "race");
            $result = Fonctions::gestionAutorisationTags($perso, $result, $autorisations, "religion");
        } catch(Exception $e) {}

        //TODO GESTION DES LANGUES
        return $result;
    }

    /**
     * @param string $name
     * @param string $value
     * @param ?string $class
     * @return string
     */
    public static function rawTextArea(string $name, string $value, ?string $class = null): string {
        $elementBuilder = new Phalcon\Html\Helper\Element(new Phalcon\Escaper());
        $options = [
          "id" => $name,
          "name" => $name
        ];
        if ($class !== null) {
            $options["class"] = $class;
        }
        try {
            return $elementBuilder('textarea', $value, $options, true);
        } catch (\Phalcon\Html\Exception $e) {
            return "";
        }
    }

    /**
     * @param $perso
     * @param string $result
     * @param $autorisations
     * @param $type
     * @return array|string|string[]
     * @throws Exception
     * @noinspection RegExpRedundantEscape
     */
    public static function gestionAutorisationTags($perso, string $result, $autorisations, $type) {
        $matches = array();
        preg_match_all('/\['.$type.'=([^\]]+)\](.*?)\[\/'.$type.'\]/s', $result, $matches);
        foreach ($matches[1] as $i => $id) {
            $contenu = $matches[2][$i];
            switch($type) {
                case "race":
                    $object = Races::findFirst($id);
                    break;
                case "religion":
                    $object = Religions::findFirst($id);
                    break;
                case "royaume":
                    if (strlen($id) > 5) {
                        $object = Royaumes::findFirst(["nom = :nom:", 'bind' => ['nom' => $id]]);
                    } else {
                        $object = Royaumes::findFirst($id);
                    }
                    break;
                default:
                    throw new Exception();
            }
            $txt = explode("[EXEPT]", $contenu);
            if (!empty($perso) && $perso instanceof Personnages && Autorisations::hasAutorisation(Autorisations::CONSULTATION_WIKI, $autorisations)) {
                $newContenu = '<b style="display:block;">Info ' . $object->nom . ' : &nbsp;</b><i>' . $txt[0] . '</i>';
            } else {
                if (!empty($perso) && $perso instanceof Personnages && (($type == "race" && $perso->idRace == $object->id) || ($type == "religion" && $perso->idReligion == $object->id) || ($type == "royaume" && $perso->idRoyaume == $object->id))) {
                    // texte à droite de [EXEPT]
                    $newContenu = $txt[0];
                } else {
                    if (!(isset($txt[1]))) {
                        $txt[1] = "";
                    }
                    // texte à gauche de [EXEPT]
                    $txt = explode("[DECO]", $txt[1]);
                    if ($perso instanceof Personnages) {
                        // texte à droite de [DECO]
                        $newContenu = $txt[0];
                    } else {
                        if (!(isset($txt[1]))) {
                            $txt[1] = "";
                        }
                        // texte à gauche de [DECO]
                        $newContenu = $txt[1];
                    }
                }
            }
            $oldContenu = $matches[0][$i];
            $result = str_replace($oldContenu, $newContenu, $result);
        }
        return $result;
    }

    /**
     * Génère le menu par défaut lorsqu'une personne n'est pas connectée
     * @param string $type
     * @param ?array $auth
     * @return string
     * @noinspection HtmlUnknownTarget
     */
    public static function genererMenu(string $type, ?array $auth = null): string {
        return "<ul>"
          . "<li>" . (($type == "init" && ($auth == null || !isset($auth))) ?
            Phalcon\Tag::linkTo(['inscription', '<img alt="inscription" title="Inscription" src="/img/site/interface/menu/BoutonInscription-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) :
            Phalcon\Tag::linkTo(['jouer', '<img alt="jouer" title="Jouer" src="/img/site/interface/menu/BoutonJouer.png" class="imageMenu"/>', 'class' => 'boutonMenu'])
          ) . "</li>"
          . "<li>" . Phalcon\Tag::linkTo(['wiki', '<img alt="wiki" title="Wiki" src="/img/site/interface/menu/BoutonWIKI-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) . "</li>"
          . "<li>" . Phalcon\Tag::linkTo(['regle', '<img alt="regle" title="Règles" src="/img/site/interface/menu/BoutonWIKI-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) . "</li>"
          . "<li>" . Phalcon\Tag::linkTo(['faq', '<img alt="faq" title="FAQ" src="/img/site/interface/menu/BoutonWIKI-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) . "</li>"
          . "<li>" . Phalcon\Tag::linkTo(['forum', '<img alt="forum" title="Forum" src="/img/site/interface/menu/BoutonForums-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) . "</li>"
          . "<li>" . Phalcon\Tag::linkTo(['taverne', '<img alt="taverne" title="Taverne" src="/img/site/interface/menu/BoutonForums-1.png" class="imageMenu"/>', 'class' => 'boutonMenu']) . "</li>"
          . "<li><a href='javascript:void(0);' class='icon' onclick='myFunction()'>&#9776;</a></li></ul>";
    }
}