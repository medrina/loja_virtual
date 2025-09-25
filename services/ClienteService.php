<?php
    class ClienteService extends Cliente {
        private $conexao;
        private Cliente $cliente;

        public function __construct(Connection $conexao, Cliente $cliente)
        {
            $this->conexao = $conexao->conectar();
            $this->cliente = $cliente;
        }

        public function getClienteByID($id) {
            try {
                $query = 'select id, nome, email from cliente where id = :id';
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $consulta = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->cliente->setId($consulta['id']);
                $this->cliente->setNome($consulta['nome']);
                $this->cliente->setEmail($consulta['email']);
                if($consulta) return $consulta;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getCliente() {
            try {
                $query = 'select id, nome, email, senha from cliente where email = :email';
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':email', $this->cliente->getEmail());
                $stmt->execute();
                $consulta = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($consulta) {
                require_once './../helper/funcoes_adicionais.php';
                $senhaVerificada = desencriptarSenha($this->cliente->getSenha(), $consulta['senha']);
                if($senhaVerificada) {
                    session_start();
                    $nome = explode(' ', $consulta['nome']);
                    $_SESSION['nome'] =  $nome[0];
                    $_SESSION['id'] = $consulta['id'];
                    $_SESSION['email'] = $consulta['email'];
                    $cliente = new Cliente();
                    $cliente->setNome($consulta['nome']);
                    $cliente->setId($consulta['id']);
                    $cliente->setEmail($consulta['email']);
                    $carrinho = new Carrinho($cliente);
                    $conexao = new Connection();
                    $resultado = new CarrinhoService($conexao, $carrinho);
                    $resultado = $resultado->carrinho();
                    return true;
                }
            }
        }

        public function salvarCliente() {
            try {
                $query = "SELECT count(*) AS 'quant' FROM cliente WHERE email = :email";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(":email", $this->cliente->getEmail());
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if($resultado['quant'] > 0) return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            try {
                $insert = "INSERT INTO cliente(nome, email, senha) VALUES(:nome, :email, :senha);";
                $stmt = $this->conexao->prepare($insert);
                $stmt->bindValue(':nome', $this->cliente->getNome());
                $stmt->bindValue(':email', $this->cliente->getEmail());
                $stmt->bindValue(':senha', $this->cliente->getSenha());
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                return false;
            }
        }

        public function atualizarDados() {
            $query = '';
            $resultado = 0;
            $this->cliente->setEmail($_SESSION['email']);
            if(isset($_POST['senha'])) {
                require_once '../helper/funcoes_adicionais.php';
                $novaSenha = encriptarSenha($_POST['senha']);
                $this->cliente->setSenha($novaSenha);
                try {
                    $query = "UPDATE cliente SET nome = :nome, email = :email, senha = :senha WHERE id = :id_cliente;";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':nome', $this->cliente->getNome());
                    $stmt->bindValue(':email', $this->cliente->getEmail());
                    $stmt->bindValue(':senha', $this->cliente->getSenha());
                    $stmt->bindValue(':id_cliente', $_SESSION['id']);
                    $resultado = $stmt->execute();
                    $nome = explode(' ', $this->cliente->getNome());
                    $_SESSION['nome'] = $nome[0];
                    return $resultado;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
            else {
                try {
                    $query = "UPDATE cliente SET nome = :nome, email = :email WHERE id = :id_cliente;";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':nome', $this->cliente->getNome());
                    $stmt->bindValue(':email', $this->cliente->getEmail());
                    $stmt->bindValue(':id_cliente', $_SESSION['id']);
                    $resultado = $stmt->execute();
                    $nome = explode(' ', $this->cliente->getNome());
                    $_SESSION['nome'] = $nome[0];
                    return $resultado;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function atualizarDadosClienteCheckout() {
            try {
                $query = "UPDATE cliente SET nome = :nome WHERE id = :id_cliente;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', $_POST['id']);
                $stmt->bindValue(':nome', $_POST['checkout-nome']);
                if($stmt->execute()) {
                    session_start();
                    $_SESSION['nome'] = $_POST['checkout-nome'];
                    return true;
                }
                else return false;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getClienteModal() {
            $carrinho = new Carrinho($this->cliente);
            $conexao = new Connection();
            $resultado = new CarrinhoService($conexao, $carrinho);
            $resultado->carrinho();
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function getUserAdmin() {
            try {
                $query = "SELECT nome, email FROM cliente WHERE id = 1;";
                $stmt = $this->conexao->query($query);
                if($stmt->execute()) {
                    $lista = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $lista;
                }
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function atualizarDadosAdmin() {
            session_start();
            $query = '';
            $resultado = 0;
            if(isset($_POST['senha-confirmar-admin'])) {
                require_once '../helper/funcoes_adicionais.php';
                $novaSenha = encriptarSenha($_POST['senha-confirmar-admin']);
                try {
                    $query = "UPDATE cliente SET nome = :nome, senha = :senha WHERE id = 1";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':nome', $this->cliente->getNome());
                    $stmt->bindValue(':senha', $novaSenha);
                    $resultado = $stmt->execute();
                    $_SESSION['nome'] = $this->cliente->getNome();
                    return $resultado;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
            else {
                try {
                    $query = "UPDATE cliente SET nome = :nome WHERE id = 1";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':nome', $this->cliente->getNome());
                    $_SESSION['nome'] = $this->cliente->getNome();
                    $resultado = $stmt->execute();
                    return $resultado;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

    }