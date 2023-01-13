<?php

/**
 * Interface pour gérer les fichiers des effets
 *
 */
interface FichierTypeMagie {

    /**
     * Permet de générer les informations pour les sorts
     * @param unknown $type
     * @param unknown $sort
     */
    public function genererInformationSort($type, $sort);
}