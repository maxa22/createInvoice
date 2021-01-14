<?php
    
    class InvoiceArticle extends DatabaseObject {
        protected static $dbTable = 'artiklifakture';
        protected static $dbFields = array( 'ime', 'cijena', 'kolicina', 'fakturaId');
        protected $id;
        public $ime;
        public $cijena;
        public $kolicina;
        public $fakturaId;

        public function __construct($args) {

            $this->id =                 $args['id'] ?? '';
            $this->ime =                $args['ime'];
            $this->cijena =             $args['cijena'];
            $this->kolicina =           $args['kolicina'];
            $this->fakturaId =          $args['fakturaId'];

        }
    } //end of class
?>