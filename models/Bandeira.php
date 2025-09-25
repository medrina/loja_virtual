<?php
    class Bandeira {
        private int $id;
        private string $nome;
        private Pagamento $pagamento;
        
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }