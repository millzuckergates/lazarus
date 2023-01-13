<?php

/**
 * Classe correspondant à une case
 * @author fvpei
 *
 */
class Cases {
    var $id;
    var $x;
    var $y;
    var $idCarte;
    var $terrain;
    var $image;
    var $idCreature;
    var $idPersonnage;
    var $idBatiment;
    var $table;
    var $idTextures = [null, null, null];

    /**
     * Permet de construire une case
     * @param object $data
     * @param string $nomTable
     * @param string $moment
     */
    public function __construct(object $data, string $nomTable, string $moment) {
        if (!empty($data)) {
            $this->id = $data->id;
            $this->x = $data->x;
            $this->y = $data->y;
            $this->idCarte = $data->idCarte;
            $this->terrain = Terrains::findById($data->idTerrain);
            $this->image = str_replace('@@@@', $moment, $data->image);
            $this->idCreature = $data->idCreature;
            $this->idPersonnage = $data->idPersonnage;
            $this->idBatiment = $data->idBatiment;
            $this->table = $nomTable;
            $this->idTextures = AssocCaseTextures::getTexturesForCase($this->id, $this->table);
        }
    }

    /**
     * Permet de retourner le nom de la class css associé à la case
     * @param string $perception
     * @return string
     */
    public function getCSSClassName(string $perception): string {
        $className = "t_";
        $test = explode("/", $this->image);
        $image = $test[count($test) - 1];
        $test2 = explode(".", $image);
        $num = $test2[0];
        $className .= $this->terrain->id . "_" . $num;
        if ($perception != "") {
            $className .= "_" . $perception;
        }
        return $className;
    }

    /**
     * Retourn le contenu de la case en termes de textures
     * @return string
     */
    public function generateTextures(): string {
        $aux = "";
        foreach ($this->idTextures as $numTexture => $idTexture) {
            $aux .= Textures::getHTMLFromId($idTexture, $numTexture);
        }
        return $aux;
    }
}