<?php

    class Invoice extends DatabaseObject {
        protected static $dbTable = 'faktura';
        protected static $dbFields = array('firmaId','broj', 'mjesto', 'kupacId', 'datum', 'nacin','tip', 'rok', 'fakturista', 'userId');
        protected $id;
        public $firmaId;
        public $broj;
        public $mjesto;
        public $kupacId;
        public $nacin;
        public $tip;
        public $datum;
        public $rok;
        public $fakturista;
        public $userId;

        public function __construct($args) {

            $this->id =                 $args['id'] ?? '';
            $this->firmaId =            $args['firmaId'];
            $this->broj =               $args['broj'];
            $this->mjesto =             $args['mjesto'];
            $this->kupacId =            $args['kupacId'];
            $this->nacin =              $args['nacin'];
            $this->tip =                $args['tip'];
            $this->datum =              $args['datum'];
            $this->rok =                $args['rok'];
            $this->fakturista =         $args['fakturista'];
            $this->userId =             $args['userId'];

        }
    } //end of class
?>