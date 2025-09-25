<?php
    class Parcelas {
        private int $id;
        private float $valor_parcela;
        private DateTime $data;
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