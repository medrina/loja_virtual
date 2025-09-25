<?php
    class EnderecoService {
        private $conexao;
        private $endereco;

        public function __construct(Connection $conexao, Endereco $endereco)
        {
            $this->conexao = $conexao->conectar();
            $this->endereco = $endereco;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function getEnderecoByCliente2(Cliente $cliente) {
            $status = 'status';
            try {
                $query = "SELECT id, logradouro, numero, complemento, bairro, cep, id_cidade
                                FROM endereco e
                                WHERE (". $status ." = 1) AND id_cliente = :id";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $cliente->getID());
                if($stmt->execute()) {
                    $endereco = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $array = [];
                    $i = 0;
                    foreach($endereco as $indice => $valor) {
                        $end = new Endereco();
                        $end->__set('id', $endereco[$indice]['id']);
                        $end->__set('logradouro', $endereco[$indice]['logradouro']);
                        $end->__set('numero', $endereco[$indice]['numero']);
                        $end->__set('bairro', $endereco[$indice]['bairro']);
                        $end->__set('cep', $endereco[$indice]['cep']);
                        $end->__set('id_cidade', $endereco[$indice]['id_cidade']);
                        $end->__set('complemento', $endereco[$indice]['complemento']);
                        $array[$i] = [
                            'id_cliente' => $cliente->getID(),
                            'endereco' => $end
                        ];
                        $i++;
                    }
                    return $array;
                }
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getCidadeByID(int $id) {
            try {
                $query = "SELECT nome, uf FROM cidade WHERE id = :id_cidade";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cidade', $id);
                if($stmt->execute()) {
                    $cidade = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cidade = new Cidade($cidade['nome'], $cidade['uf']);
                    $this->endereco->__set('cidade', $cidade);
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getCidadeByID_2(int $id) {
            try {
                $query = "SELECT nome, uf FROM cidade WHERE id = :id_cidade";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cidade', $id);
                if($stmt->execute()) {
                    $cidade = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cidade = new Cidade($cidade['nome'], $cidade['uf']);
                    return $cidade;
                }
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function criarEndereco() {
            $id_cidade = 0;
            $status = 'status';
            try {
                $query = "SELECT count(*) AS 'quant' FROM endereco 
                    WHERE
                        (". $status ." = 1) AND
                        id_cliente = :id AND id_cidade = (SELECT id FROM cidade WHERE nome = :cidade) AND 
                        cep = :cep AND
                        numero = :numero AND
                        complemento = :complemento AND
                        bairro = :bairro AND
                        logradouro = :logradouro";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $this->endereco->__get('cliente')->getID());
                $stmt->bindValue(':cidade', $this->endereco->__get('cidade')->__get('nome'));
                $stmt->bindValue(':cep', $this->endereco->__get('cep'));
                $stmt->bindValue(':numero', $this->endereco->__get('numero'));
                $stmt->bindValue(':complemento', $this->endereco->__get('complemento'));
                $stmt->bindValue(':bairro', $this->endereco->__get('bairro'));
                $stmt->bindValue(':logradouro', $this->endereco->__get('logradouro'));
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($resultado['quant'] > 0) return false;
            else {
                try {
                    $query = "SELECT id FROM cidade WHERE nome = :cidade";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':cidade', $this->endereco->__get('cidade')->__get('nome'));
                    $stmt->execute();
                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
                if($resultado) $id_cidade = $resultado['id'];
                else {
                    try {
                        $query = "INSERT INTO cidade(nome, uf) VALUES(:nome, :uf);";
                        $stmt = $this->conexao->prepare($query);
                        $stmt->bindValue(':nome', $this->endereco->__get('cidade')->__get('nome'));
                        $stmt->bindValue(':uf', $this->endereco->__get('cidade')->__get('uf'));
                        $stmt->execute();
                        $id_cidade = $this->conexao->lastInsertId();
                    }
                    catch(PDOException $e) {
                        echo 'erro: '. $e->getMessage();
                    }
                }
                try {
                    $status = 'status';
                    $query = "INSERT INTO endereco(logradouro, numero, complemento, bairro, cep, ". $status .", id_cliente, id_cidade) 
                                    VALUES(:logradouro, :numero, :complemento, :bairro, :cep, :st, :id_cliente, :id_cidade);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':logradouro', $this->endereco->__get('logradouro'));
                    $stmt->bindValue(':numero', $this->endereco->__get('numero'));
                    $stmt->bindValue(':complemento', $this->endereco->__get('complemento'));
                    $stmt->bindValue(':bairro', $this->endereco->__get('bairro'));
                    $stmt->bindValue(':cep', $this->endereco->__get('cep'));
                    $stmt->bindValue(':st', 1);
                    $stmt->bindValue(':id_cliente', $this->endereco->__get('cliente')->getID());
                    $stmt->bindValue(':id_cidade', $id_cidade);
                    if($stmt->execute()) return true;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function getListaEnderecos() {
            $status = 'e.status';
            try {
                $query = "SELECT e.id, logradouro, numero, complemento, bairro, cep, nome, uf
                    FROM endereco e JOIN cidade c
                    ON e.id_cidade = c.id
                    WHERE (". $status ." = 1) AND id_cliente = :id_cliente ORDER BY e.id_cidade";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_cliente', $this->endereco->__get('cliente')->getID());
                $stmt->execute();
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if($resultado) return $resultado;
                else return null;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getEnderecoById($id) {
            try {
                $query = "SELECT e.id AS id_endereco, c.id AS id_cidade, logradouro, numero, complemento, bairro, cep, nome, uf
                    FROM endereco e JOIN cidade c
                    ON e.id_cidade = c.id
                    WHERE e.id = :id";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function atualizarEndereco() {
            try {
                $query = "SELECT count(*) AS 'quant' FROM endereco 
                    WHERE 
                        id_cidade = (SELECT id FROM cidade WHERE nome = :cidade) AND 
                        cep = :cep AND
                        numero = :numero AND
                        complemento = :complemento AND
                        bairro = :bairro AND
                        logradouro = :logradouro AND
                        id_cliente = :id_cliente
                        ";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':cidade', $this->endereco->__get('cidade')->__get('nome'));
                $stmt->bindValue(':cep', $this->endereco->__get('cep'));
                $stmt->bindValue(':numero', $this->endereco->__get('numero'));
                $stmt->bindValue(':complemento', $this->endereco->__get('complemento'));
                $stmt->bindValue(':bairro', $this->endereco->__get('bairro'));
                $stmt->bindValue(':logradouro', $this->endereco->__get('logradouro'));
                session_start();
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($resultado['quant'] > 0) return false;
            else {
                try {
                    $query = "SELECT id FROM cidade WHERE nome = :cidade";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':cidade', $this->endereco->__get('cidade')->__get('nome'));
                    $stmt->execute();
                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
                if($resultado) {
                    $id_cidade = $resultado['id'];
                }
                else {
                    try {
                        $query = "INSERT INTO cidade(nome, uf) VALUES(:nome, :uf);";
                        $stmt = $this->conexao->prepare($query);
                        $stmt->bindValue(':nome', $this->endereco->__get('cidade')->__get('nome'));
                        $stmt->bindValue(':uf', $this->endereco->__get('cidade')->__get('uf'));
                        $stmt->execute();
                        $id_cidade = $this->conexao->lastInsertId();
                    }
                    catch(PDOException $e) {
                        echo 'erro: '. $e->getMessage();
                    }
                }
                try {
                    $query = "UPDATE endereco 
                        SET 
                            logradouro = :logradouro,
                            numero = :numero,
                            complemento = :complemento,
                            bairro = :bairro,
                            cep = :cep,
                            id_cidade = :id_cidade        
                            WHERE id = :id_endereco;";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':logradouro', $this->endereco->__get('logradouro'));
                    $stmt->bindValue(':numero', $this->endereco->__get('numero'));
                    $stmt->bindValue(':complemento', $this->endereco->__get('complemento'));
                    $stmt->bindValue(':bairro', $this->endereco->__get('bairro'));
                    $stmt->bindValue(':cep', $this->endereco->__get('cep'));
                    $stmt->bindValue(':id_cidade', $id_cidade);
                    $stmt->bindValue(':id_endereco', $_POST['id_endereco']);
                    if($stmt->execute()) return true;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function apagarEnderecoById(int $id) {
            $status = 'status';
            try {
                $query = "UPDATE endereco 
                                    SET ". $status ." = :st
                                    WHERE (id = :id_endereco AND ". $status ." = 1);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':st', 0);
                $stmt->bindValue(':id_endereco', $id);
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }
        
    }