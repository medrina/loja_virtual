<?php
    class Modalidade {
        private int $id;
        private string $tipo;
        
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }