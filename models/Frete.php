<?php
    class Frete {
        private int $id;
        private float $valor;
        private FormaEnvio $formaEnvio;
        
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }