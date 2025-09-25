<?php
    class Categoria {
        private int $id;
        private string $nome;

        public function __construct()
        {
            $this->id = 0;
            $this->nome = '';
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }