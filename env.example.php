<?php

    class Environment {

        public $ENV = array();

        public function __construct() {
            $this->ENV['DB_HOST'] = 'localhost';
            $this->ENV['DB_USER'] = 'root';
            $this->ENV['DB_PASS'] = '';
            $this->ENV['DB_NAME'] = 'dbname';
            $this->ENV['KEY'] = "your_key";
        }

    }

?>