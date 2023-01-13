<?php

/**
 *
 * Classe pour gérer les dés
 *
 * @author François-Victor
 *
 */
class Des {

    /**
     * Simule le résultat de $nombreDes lancés de $amplitudesDes faces
     * @param unknown $nombreDes
     * @param unknown $amplitudeDes
     * @return number
     */
    public static function jetDes($nombreDes, $amplitudeDes) {
        $total = 0;
        for ($i = 0; $i < $nombreDes; $i++) {
            $total = $total + rand(0, $amplitudeDes);
        }
        return $total;
    }

}

?>