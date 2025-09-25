<?php
    class Cidade {
        private int $id;
        private $nome;
        private $uf;

        public function __construct(string $nome, string $uf)
        {
            $this->nome = $nome;
            $this->uf = $uf;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }