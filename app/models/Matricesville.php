<?php

class Matricesville extends AbstractMatrices {

    /**
     * Initialize method for model.
     */
    public function initialize() {
        parent::initialize();
        $this->setSource("matricesville");
    }

}
