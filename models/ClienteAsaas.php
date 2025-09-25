<?php
    class ClienteAsaas {
        private int $id;
        private string $telefone;
        private string $cpf;
        private Cliente $cliente;

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }