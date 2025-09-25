<?php
    class Connection {

        private $dsn = 'mysql:host=localhost;dbname=loja';
        private $usuario = '';
        private $senha = '';
        private $conexao;

        public function conectar() {
            try {
                $this->conexao = new PDO($this->dsn, $this->usuario, $this->senha);
                return $this->conexao;
            }
            catch (PDOException $e) {
                echo 'Erro de conexão: '. $e->getMessage();
            }
        }
    }
?>