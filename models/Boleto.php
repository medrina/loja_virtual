<?php
    class Boleto {
        private int $id;
        private string $codigo;
        private float $valor_custo;
        private float $valor;
        private string $date;

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }