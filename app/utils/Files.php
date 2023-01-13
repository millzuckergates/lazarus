<?php

/**
 * Classe pour gérer le ftp
 * @author fvpeigne
 *
 */
class Files {

    const REP_LOG_MJ = 'mj/';
    const REP_LOG_DEV = 'dev/';
    const REP_LOG_ADMIN = 'admin/';

    const REP_FICHIERS_NATUREMAGIE = "naturesmagie/";
    const REP_FICHIERS_ECOLEMAGIE = "ecolesmagie/";


    /**
     * Permet de créer un fichier de logs
     * @param unknown $mode
     * @param unknown $contenu
     * @param unknown $fileName
     * @return string
     */
    public static function createFileLog($mode, $contenu, $fileName, $path) {
        if ($mode == "MJ") {
            $file = $path . Files::REP_LOG_MJ . $fileName;
            $retour = file_put_contents($file, $contenu);
        } else {
            if ($mode == "ADMIN") {
                $file = $path . Files::REP_LOG_ADMIN . $fileName;
                $retour = file_put_contents($file, $contenu);
            } else {
                if ($mode == "DEV") {
                    $file = $path . Files::REP_LOG_DEV . $fileName;
                    $retour = file_put_contents($file, $contenu);
                } else {
                    return "error";
                }
            }
        }

        if ($retour === false) {
            return "error";
        } else {
            return "sucess";
        }
    }

    /**
     * Permet de générer la liste des fichiers
     * consultable à partir du $repertoire
     * @param unknown $repertoire
     * @return string
     */
    public static function getFiles($repertoire) {
        $retour = array();
        //On parcourt le répertoire
        if ($dossier = opendir($repertoire)) {
            while (false !== ($fichier = readdir($dossier))) {
                if ($fichier != '.' && $fichier != '..') {
                    $retour[count($retour)] = $fichier;
                }
            }
            closedir($dossier);
        }
        return $retour;
    }

    /**
     * Construit l'arbre de répertoire pour les images
     * @param unknown $dir
     * @param unknown $dirImage
     * @return string
     */
    public static function mkmapImage($dir, $dirImage, $compteur = 0) {
        $directory = $dirImage;
        $firstIteration = true;
        if ($dir != null) {
            $directory = $directory . $dir;
            $firstIteration = false;
        }
        $retour = "<ul class='listeRepertoire'>";
        $directory = str_replace("\\", "/", $directory);
        $folder = opendir($directory);
        $imgFolderClose = 'site/utils/folder.png';
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = $dir . '/' . $file;
                if (strpbrk('.', $pathfile) == false) {
                    $margin = $compteur * 10;
                    if ($firstIteration) {
                        $retour .= '<li name="racine" class="repertoire" style="margin-left:' . $margin . 'px;">';
                        $retour .= '<span class="folderPlie">';
                        $retour .= '<img id="' . $pathfile . '" class="boutonRepertoireImageFerme" src="/public/img/site/utils/folder.png"  onclick="deplier(\'' . $pathfile . '\');"/></span>';
                        $retour .= '<span  onclick="chargerRepertoire(\'' . $pathfile . '\');">' . $file . '</span></li>';
                    } else {
                        $retour .= '<li name="' . $dir . '" class="repertoire" style="display:none;margin-left:' . $margin . 'px;">';
                        $retour .= '<span class="folderPlie">';
                        $retour .= '<img id="' . $pathfile . '" class="boutonRepertoireImageFerme" src="/public/img/site/utils/folder.png"  onclick="deplier(\'' . $pathfile . '\');"/>';
                        $retour .= '<span  onclick="chargerRepertoire(\'' . $pathfile . '\');">' . $file . '</span></li>';
                    }
                    $retour .= Files::mkmapImage($pathfile, $dirImage, $compteur + 1);
                }
            }
        }
        closedir($folder);
        $retour .= "</ul>";
        return $retour;
    }

    /**
     * Créer l'arbre de navigation dans les répertoires pour les gifs
     * @param unknown $dir
     * @param unknown $dirGif
     * @return string
     */
    public static function mkmapGif($dir, $dirGif, $compteur = 0) {
        $directory = $dirGif;
        $firstIteration = true;
        if ($dir != null) {
            $directory = $directory . $dir;
            $firstIteration = false;
        }
        $retour = "<ul class='listeRepertoire'>";
        $directory = str_replace("\\", "/", $directory);
        $directory = str_replace("@", "'", $directory);
        $folder = opendir($directory);
        $imgFolderClose = 'site/utils/folder.png';
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = $dir . '/' . $file;
                if (strpbrk('.', $pathfile) == false) {
                    $margin = $compteur * 10;
                    $pathfile = str_replace("'", "@", $pathfile);
                    $dir = str_replace("'", "@", $dir);
                    if ($firstIteration) {
                        $retour .= '<li name="racine" class="repertoire" style="margin-left:' . $margin . 'px;">';
                        $retour .= '<span class="folderPlie">';
                        $retour .= '<img id="' . $pathfile . '" class="boutonRepertoireImageFerme" src="/public/img/site/utils/folder.png" onclick="deplier(\'' . $pathfile . '\');"/></span>';
                        $retour .= '<span  onclick="chargerRepertoireGif(\'' . utf8_encode($pathfile) . '\');">' . utf8_encode($file) . '</span></li>';
                    } else {
                        $retour .= '<li name="' . $dir . '" class="repertoire" style="display:none;margin-left:' . $margin . 'px;">';
                        $retour .= '<span class="folderPlie">';
                        $retour .= '<img id="' . $pathfile . '" class="boutonRepertoireImageFerme" src="/public/img/site/utils/folder.png" onclick="deplier(\'' . $pathfile . '\');"/></span>';
                        $retour .= '<span  onclick="chargerRepertoireGif(\'' . utf8_encode($pathfile) . '\');">' . utf8_encode($file) . '</span></li>';
                    }
                    $retour .= Files::mkmapGif($pathfile, $dirGif, $compteur + 1);
                }
            }
        }
        closedir($folder);
        $retour .= "</ul>";
        return $retour;
    }

    /**
     * Permet de générer le bloc de détail d'une image
     * @param unknown $image
     * @param unknown $repImage
     * @return string
     */
    public static function genererDetailImage($image, $repImage) {
        $retour = "";
        if ($image != null) {
            $retour = $retour . '<input type="hidden" id="idPersoAdministrationImage" name="idPersoAdministrationImage" value=""/>';
            //Bricolage pour faire correspondre à l'image à ce que l'on souhaite
            $urlImage = str_replace($repImage, "", $image);
            $urlImage = $repImage . $urlImage;
            $image = str_replace($repImage, "", "/" . $image);
            $image = str_replace("//", "/", $image);
            $retour = $retour . "<div id='imageSelect'><img id='imageSelectImg' src='" . $image . "'/></div>";
            $retour = $retour . "<div class='donneesElementVisuel'><div id='nomImageSelect'><label for='imageEnCours'>Nom </span><input type='text' id='imageEnCours' value='" . $image . "'/></div>";
            $retour = $retour . '<div id="boutonSelectImg"><input type="button" class="bouton" onclick="supprimerImg(\'' . $image . '\');" value="Supprimer"/><input type="button" class="bouton" onclick="modifierImg(\'' . $image . '\');" value="Modifier"/></div>';
            $retour .= "</div>";
        }
        return $retour;
    }

    /**
     * Retourne toutes les images d'un répertoire
     * @param unknown $repertoire
     * @param unknown $repImage
     * @return string
     */
    public static function chargerImagesByRepertoire($repertoire, $repImage) {
        if ($repertoire == null) {
            return "<span id='msgNoImage'> Il n'y a pas d'image dans ce répertoire.</span>";
        } else {
            $retour = "";
            $count = 0;
            $dir = $repImage . $repertoire;
            $dir = str_replace("//", "/", $dir);
            if ($dossier = opendir($dir)) {
                while (false !== ($fichier = readdir($dossier))) {
                    if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                        $blocImage = "<div class='blocImage'>";
                        $imageSource = str_replace("//", "/", $repImage . $repertoire . "/" . $fichier);
                        $imageSource = str_replace(BASE_PATH, SITE_NAME, $repImage . $repertoire . "/" . $fichier);
                        $blocImage = $blocImage . '<div class="imageDuBloc"><img src="' . $imageSource . '" onclick="chargerImage(\'' . $imageSource . '\');" class="miniatureElementVisuel"/></div>';
                        $blocImage = $blocImage . "<div class='textImageBloc'><span class='nomImageBloc'>" . $fichier . "</span></div>";
                        $blocImage = $blocImage . "</div>";
                        $retour = $retour . $blocImage;
                        $count++;
                    }
                }
                closedir($dossier);
            }
        }
        if ($count == 0) {
            return "<span id='msgNoImage'> Il n'y a pas d'image dans ce répertoire.</span>";
        }
        return $retour;
    }

    /**
     * Méthode permettant de supprimer un répertoire et son contenu
     * @param unknown $repertoire
     * @return string
     */
    public static function supprimerRepertoire($repertoire) {
        $listeFiles = scandir($repertoire);
        if ($listeFiles) {
            if (count($listeFiles) > 0) {
                //Suppression de tous les fichiers du répertoire
                foreach ($listeFiles as $file) {
                    if ($file != "." && $file != "..") {
                        if (strpbrk('.', $file) != false && strpbrk('..', $file) != false) {
                            $retour = unlink($repertoire . '/' . $file);
                            if (!$retour) {
                                return "error";
                            }
                        } else {
                            return "errorFils";
                        }
                    }
                }
            }
            $resultat = rmdir($repertoire);
            if ($resultat) {
                return "success";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }

    /**
     * Permet de générer le détail pour un gif
     * @param unknown $gif
     * @param unknown $repGif
     * @return string
     */
    public static function genererDetailGif($gif, $repGif) {
        $retour = "";
        if ($gif != null) {
            //Bricolage pour faire correspondre à l'image à ce que l'on souhaite
            $urlGif = str_replace($repGif, "", $gif);
            $urlGif = $repGif . $urlGif;
            $gif = str_replace($repGif, "", "/" . $gif);
            $gif = str_replace("//", "/", $gif);
            $retour .= "<div id='gifSelect'><img id='gifSelectImg' src='" . $gif . "'/></div>";
            $retour .= "<div class='donneesElementVisuel'><div id='nomGifSelect'><label for='gifEnCours'>Nom </label><input type='text' id='gifEnCours' value='" . $gif . "'/></div>";
            $retour .= '<div id="boutonSelectGif"><input type="button" class="bouton" onclick="deleteGif(\'' . $gif . '\');" value="Supprimer"/><input type="button" class="bouton" onclick="modifierGif(\'' . $gif . '\');" value="Modifier"/></div></div>';
        }
        return $retour;
    }

    /**
     * Permet de charger les gifs pour un répertoire donné
     * @param unknown $repertoire
     * @param unknown $repGif
     * @return string
     */
    public static function chargerGifsByRepertoire($repertoire, $repGif) {
        if ($repertoire == null) {
            return "<span id='msgNoGif'> Il n'y a pas de gif dans ce répertoire.</span>";
        } else {
            $retour = "";
            $count = 0;
            $dir = $repGif . $repertoire;
            $dir = str_replace("//", "/", $dir);
            $dir = str_replace('\\', '/', $dir);
            if ($dossier = opendir($dir)) {
                while (false !== ($fichier = readdir($dossier))) {
                    if ($fichier != "." && $fichier != ".." && strpbrk('.', $fichier) != false) {
                        $blocGif = "<div class='blocGif'>";
                        $imageSource = str_replace("//", "/", $repGif . $repertoire . "/" . $fichier);
                        $imageSource = str_replace(BASE_PATH, SITE_NAME, $repGif . $repertoire . "/" . $fichier);
                        $blocGif .= '<div class="gifDuBloc"><img src="' . utf8_encode($imageSource) . '" onclick="chargerGif(\'' . utf8_encode($imageSource) . '\');" class="miniatureElementVisuel"/></div>';
                        $blocGif .= "<div class='textGifBloc'><span class='nomGifBloc'>" . $fichier . "</span></div>";
                        $blocGif .= "</div>";
                        $retour .= $blocGif;
                        $count++;
                    }
                }
                closedir($dossier);
            }
        }
        if ($count == 0) {
            return "<span id='msgNoGif'> Il n'y a pas d'image dans ce répertoire.</span>";
        }
        return $retour;
    }

    /**
     * Formate les fichiers pour modification
     * @param unknown $type
     * @param unknown $fichier
     * @param unknown $repertoire
     * @return mixed
     */
    public static function formatFichier($type, $fichier, $repertoire) {
        if ($type == "image") {
            $image = str_replace(SITE_NAME . '/public/img', $repertoire, $fichier);
            $image = str_replace('//', '/', $image);
            $image = str_replace('\\', '/', $image);
        } else {
            if ($type == "gif") {
                $image = str_replace(SITE_NAME . '/public/gifs', $repertoire, $fichier);
                $image = str_replace('//', '/', $image);
                $image = str_replace('\\', '/', $image);
                $image = str_replace("@", "'", $image);
            }
        }
        return $image;
    }
}