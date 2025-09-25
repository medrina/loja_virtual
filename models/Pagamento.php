<?php
    class Pagamento {
        private int $id;
        private string $status;
        private int $nro_parcelas;
        private int $tipo_tabela;
        private Cliente $cliente;
        private Modalidade $modalidade;
        private Entrega $entrega;
        
        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }