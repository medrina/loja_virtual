<?php
    class Endereco {
        private int $id;
        private string $logradouro;
        private int $numero;
        private string $complemento;
        private string $bairro;
        private string $cep;
        private int $id_cliente;
        private Cliente $cliente;
        private int $id_cidade;
        private Cidade $cidade;

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }