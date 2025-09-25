<?php
    class CarrinhoService extends Cliente {
        private $conexao;
        private Carrinho $carrinho;

        public function __construct(Connection $conexao, Carrinho $carrinho)
        {
            $this->conexao = $conexao->conectar();
            $this->carrinho = $carrinho;
        }

        public function carrinho() {
            try {
                $id = $this->carrinho->__get('cliente')->getID();
                $query = "SELECT count(*) as 'quant' FROM carrinho where id_cliente = $id;";
                $stmt = $this->conexao->query($query);
                $stmt->execute();
                $consulta = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($consulta['quant'] == 0) {
                try {
                    $query = "INSERT INTO carrinho(id_cliente) VALUES($id);";
                    $stmt = $this->conexao->query($query);
                    $_SESSION['status_carrinho'] = false;
                    $_SESSION['id_carrinho'] = $this->conexao->lastInsertId();
                    $conexao = new Connection();
                    $clienteAsaasService = new ClienteAsaasService($conexao, new ClienteAsaas());
                    $clienteAsaasService->criarCadastro();
                    include __DIR__ .'/../views/painel/painel.phtml';
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
            else {
                try {
                    $query = "SELECT id FROM carrinho WHERE id_cliente = $id;";
                    $stmt = $this->conexao->query($query);
                    $stmt->execute();
                    $consulta = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
                $this->carrinho->__set('id', $consulta['id']);
                $_SESSION['id_carrinho'] = $this->carrinho->__get('id');
                require '../classes_aux/ItensCarrinhoAux.php';
                $conexao = new Connection();
                $itens_carrinho = new ItensCarrinhoAux($conexao);
                $listaProdutos = $itens_carrinho->getProdutos($this->carrinho->__get('id'));
            }
        }

    }