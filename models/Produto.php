<?php
    class Produto {
        private $id;
        private string $nome;
        private string $descricao;
        private string $cor;
        private string $imagem_path;
        private float $valor;
        private int $nro_parcelas;
        private float $valor_parcela;
        private int $altura;
        private int $largura;
        private int $comprimento;
        private float $peso;
        private bool $status;
        private Marca $marca;

        public function __construct()
        {
            $this->id = 0;
            $this->nome = '';
            $this->marca = new Marca();
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function setMarca(Marca $marca) {
            $this->marca = $marca;
        }

        public function getObjProduto(): Produto {
            return $this;
        }

        public function getMarca(): Marca {
            return $this->marca;
        }

    }