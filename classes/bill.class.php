<?php

    class Bill extends DatabaseObject {

        protected static $dbTable = 'fiskalniRacun';
        protected static $dbFields = ['broj', 'datum', 'slika', 'userId'];
        protected $id;
        public $broj;
        public $datum;
        public $slika;
        public $userId;

        public function __construct($args) {
            $this->id =               $args['id'] ?? '';
            $this->broj =             $args['broj'];
            $this->datum =            $args['datum'];
            $this->slika =            $args['slika'];
            $this->userId =           $args['userId'];
            if($this->slika) {
                $imageClass = new Image($this->slika);
                $this->slika = $imageClass->getImage();
            }
        }
    }