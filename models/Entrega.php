<?php
    class Entrega {
        private int $id;
        private string $cod_rastreio;
        private int $tempo_entrega;
        private string $formato;
        private string $status;
        private float $valor_com_frete;
        private Endereco $endereco;
        private Cliente $cliente;
        private Pedido $pedido;

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }