<?php

    
    class Article extends DatabaseObject {
        protected static $dbTable = 'artikal';
        protected static $dbFields = array('idArtikla', 'ime', 'cijena', 'opis', 'userId');
        protected $id;
        protected $idArtikla;
        public $ime;
        public $cijena;
        public $opis;
        public $userId;

        public function __construct($args) {

            $this->id =                 $args['id'] ?? '';
            $this->idArtikla =          $args['idArtikla'];
            $this->ime =                $args['ime'];
            $this->cijena =             $args['cijena'];
            $this->opis =               $args['opis'];
            $this->userId =            $args['userId'];

        }
    } //end of class
?>