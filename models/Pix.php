<?php
    class Pix {
        private int $id;
        private string $nro_transacao;
        private string $chave;
        private float $valor;
        private DateTime $data;
        
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }