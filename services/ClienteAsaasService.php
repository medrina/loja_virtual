<?php
    class ClienteAsaasService {
        private $conexao;
        private $clienteAsaas;

        public function __construct(Connection $conexao, ClienteAsaas $clienteAsaas)
        {
            $this->conexao = $conexao->conectar();
            $this->clienteAsaas = $clienteAsaas;
        }

        public function criarCadastro() {
            try {
                $query = "INSERT INTO cliente_asaas(id_cliente) VALUES(:id_cliente);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function atualizarDados() {            
            try {
                $query = "UPDATE cliente_asaas SET telefone = :tel, cpf = :cpf WHERE id_cliente = :id_cliente;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':tel', $this->clienteAsaas->__get('telefone'));
                $stmt->bindValue(':cpf', $this->clienteAsaas->__get('cpf'));
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getClienteAsaas() {
            try {
                $query = "SELECT cpf, telefone FROM cliente_asaas WHERE id_cliente = :id_cliente;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if($resultado) return $resultado;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getTelefoneClienteAsaas() {
            try {
                $query = "SELECT telefone FROM cliente_asaas WHERE id_cliente = :id_cliente;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if($resultado) return $resultado;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

    }