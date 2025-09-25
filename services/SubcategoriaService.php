<?php
    class SubcategoriaService {
        private $conexao;
        private SubCategoria $subCategoria;

        public function __construct(Connection $conexao, Subcategoria $subCategoria)
        {
            $this->conexao = $conexao->conectar();
            $this->subCategoria = $subCategoria;
        }

        public function show() {
            try {
                $query = 'select id, nome from subcategoria ORDER BY nome;';
                $stmt = $this->conexao->query($query);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getProdutosAdmin($id) {
            try {
                $sql = "SELECT p.id AS 'id', imagem_path AS 'imagem', p.nome AS 'produto', p.descricao, p.cor, nro_parcelas, valor_parcela, p.valor AS 'valor', p.altura, p.largura, p.comprimento, p.peso, m.nome AS 'marca'
                            FROM produto p
                            JOIN subcategoria s
                            ON p.id_subcategoria = s.id
                            JOIN marca m
                            ON p.id_marca = m.id
                            WHERE s.id = :id;";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $i = 0;
                $prod = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $prod[$i]['id'] = $row['id'];
                    $prod[$i]['nome'] = $row['produto'];
                    $prod[$i]['descricao'] = $row['descricao'];
                    $prod[$i]['cor'] = $row['cor'];
                    $prod[$i]['altura'] = $row['altura'];
                    $prod[$i]['largura'] = $row['largura'];
                    $prod[$i]['comprimento'] = $row['comprimento'];
                    $prod[$i]['peso'] = $row['peso'];
                    $prod[$i]['valor'] = $row['valor'];
                    $prod[$i]['imagem'] = $row['imagem'];
                    $prod[$i]['nro_parcelas'] = $row['nro_parcelas'];
                    $prod[$i]['valor_parcela'] = $row['valor_parcela'];
                    $prod[$i]['marca'] = $row['marca'];
                    $i++;
                }
                return $prod;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getProdutos($id) {
            $status = 'status';
            try {
                $sql = "SELECT p.id AS 'id', imagem_path AS 'imagem', p.nome AS 'produto', p.descricao, p.cor, nro_parcelas, valor_parcela, p.valor AS 'valor', p.altura, p.largura, p.comprimento, p.peso, m.nome AS 'marca'
                            FROM produto p
                            JOIN subcategoria s
                            ON p.id_subcategoria = s.id
                            JOIN marca m
                            ON p.id_marca = m.id
                            WHERE s.id = :id AND p.".$status." = 1;";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $i = 0;
                $prod = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $prod[$i]['id'] = $row['id'];
                    $prod[$i]['nome'] = $row['produto'];
                    $prod[$i]['descricao'] = $row['descricao'];
                    $prod[$i]['cor'] = $row['cor'];
                    $prod[$i]['altura'] = $row['altura'];
                    $prod[$i]['largura'] = $row['largura'];
                    $prod[$i]['comprimento'] = $row['comprimento'];
                    $prod[$i]['peso'] = $row['peso'];
                    $prod[$i]['valor'] = $row['valor'];
                    $prod[$i]['imagem'] = $row['imagem'];
                    $prod[$i]['nro_parcelas'] = $row['nro_parcelas'];
                    $prod[$i]['valor_parcela'] = $row['valor_parcela'];
                    $prod[$i]['marca'] = $row['marca'];
                    $i++;
                }
                return $prod;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            catch(Exception $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function salvarSubCategoria() {
            try {
                $query = "SELECT count(*) AS 'quant' FROM subcategoria WHERE nome = :nome";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->subCategoria->__get('nome'));
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($resultado['quant']) return 1;
            else {
                try {
                    $query = "INSERT INTO subcategoria(nome) VALUES(:subcategoria);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':subcategoria', $this->subCategoria->__get('nome'));
                    if($stmt->execute()) {
                        session_start();
                        $_SESSION['id_subcategoria'] = $this->conexao->lastInsertId();
                        return 2;
                    }
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function atualizarSubCategoria() {
            try {
                $query = "UPDATE subcategoria SET nome = :nome WHERE id = :id_subcategoria;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $_POST['nome_subcategoria']);
                $stmt->bindValue(':id_subcategoria', $_POST['id_subcategoria']);
                if($stmt->execute()) return true;
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getObjSubategoria(): Subcategoria {
            return $this->subCategoria;
        }
    }