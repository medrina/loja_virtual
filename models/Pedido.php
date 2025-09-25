<?php
    class Pedido {
        private $id;
        private $valor;
        private array $carrinho;

        public function __construct(float $valor, array $carrinho)
        {
            $this->valor = $valor;
            $this->carrinho = $carrinho;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }