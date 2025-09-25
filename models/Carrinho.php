<?php
    class Carrinho {
        private int $id;
        private $data_fechamento;
        private Cliente $cliente;

        public function __construct(Cliente $cliente)
        {
            $this->cliente = $cliente;
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