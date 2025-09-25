<?php
    class ProdutoService {
        private $conexao;
        private $produto;

        public function __construct(Connection $conexao, Produto $produto)
        {
            $this->conexao = $conexao->conectar();
            $this->produto = $produto;
        }

        public function getProduto($id) {
            $status = 'status';
            try {
                $query = "select m.id AS 'id_marca', m.nome AS 'marca', 
                            p.id AS 'id_produto', p.nome AS 'produto', p.descricao AS 'descricao', p.cor AS 'cor', p.valor AS 'valor', p.nro_parcelas AS 'nro_parcelas',
                            p.valor_parcela AS 'valor_parcela', p.altura, p.largura, p.comprimento, p.peso,
                            s.id as 'id_subcategoria', s.nome as 'subcategoria', p.imagem_path as 'imagem', p.".$status."
                                FROM produto p JOIN marca m
                                ON m.id = p.id_marca
                                JOIN subcategoria s
                                ON s.id = p.id_subcategoria
                                where p.id = :id;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $consulta = $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $consulta;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function setProdutoBanco() {
            $status1 = 'status';
            $status2 = ':status';
            try {
                $query = 'INSERT INTO
                            produto(
                                nome,
                                descricao,
                                cor,
                                imagem_path,
                                valor,
                                nro_parcelas,
                                valor_parcela,
                                altura,
                                largura,
                                comprimento,
                                peso,'.
                                $status1.',
                                id_subcategoria,
                                id_marca)
                            VALUES(
                                :nome,
                                :descricao,
                                :cor,
                                :imagem,
                                :valor,
                                :nro_parcelas,
                                :valor_parcela,
                                :altura,
                                :largura,
                                :comprimento,
                                :peso,'.
                                $status2.',
                                :id_subcategoria,
                                :id_marca
                            );';                
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->produto->__get('nome'));
                $stmt->bindValue(':descricao', $this->produto->__get('descricao'));
                $stmt->bindValue(':cor', $this->produto->__get('cor'));
                $stmt->bindValue(':imagem', $this->produto->__get('imagem_path'));
                $stmt->bindValue(':valor', $this->produto->__get('valor'));
                $stmt->bindValue(':nro_parcelas', $this->produto->__get('nro_parcelas'));
                $stmt->bindValue(':valor_parcela', $this->produto->__get('valor_parcela'));
                $stmt->bindValue(':altura', $this->produto->__get('altura'));
                $stmt->bindValue(':largura', $this->produto->__get('largura'));
                $stmt->bindValue(':comprimento', $this->produto->__get('comprimento'));
                $stmt->bindValue(':peso', $this->produto->__get('peso'));
                $stmt->bindValue($status2, true);
                $stmt->bindValue(':id_subcategoria', $_POST['subcat-selecionada']);
                $stmt->bindValue(':id_marca', $_POST['marca-selecionada']);
                if($stmt->execute()) return true;
            } catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }    
        }

        public function atualizarProdutoBanco() {
            try {
                $status = 'status';
                $query = 'UPDATE produto 
                            SET nome = :nome,
                                descricao = :descricao,
                                cor = :cor,
                                valor = :valor,
                                nro_parcelas = :nro_parcelas,
                                valor_parcela = :valor_parcela,
                                altura = :altura,
                                largura = :largura,
                                comprimento = :comprimento,
                                peso = :peso,'.
                                $status.' = :controle,
                                id_subcategoria = :id_subcategoria,
                                id_marca = :id_marca
                            WHERE id = :id_produto;';
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->produto->__get('nome'));
                $stmt->bindValue(':descricao', $this->produto->__get('descricao'));
                $stmt->bindValue(':cor', $this->produto->__get('cor'));
                $stmt->bindValue(':valor', $this->produto->__get('valor'));
                $stmt->bindValue(':nro_parcelas', $this->produto->__get('nro_parcelas'));
                $stmt->bindValue(':valor_parcela', $this->produto->__get('valor_parcela'));
                $stmt->bindValue(':altura', $this->produto->__get('altura'));
                $stmt->bindValue(':largura', $this->produto->__get('largura'));
                $stmt->bindValue(':comprimento', $this->produto->__get('comprimento'));
                $stmt->bindValue(':peso', $this->produto->__get('peso'));
                $stmt->bindValue(':controle', $_POST['status_produto_selecionado']);
                $stmt->bindValue(':id_subcategoria', $_POST['subcat-selecionada']);
                $stmt->bindValue(':id_marca', $_POST['marca-selecionada']);
                $stmt->bindValue(':id_produto', $_POST['id_produto']);
                if($stmt->execute()) return true;
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function atualizarImagemBanco($imagem, $id_produto) {
            try {
                $query = "UPDATE produto SET imagem_path = :imagem WHERE id = :id_produto";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':imagem', $imagem);
                $stmt->bindValue(':id_produto', $id_produto);
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getListaProdutosPorMarca() {
            try {
                $status = 'status';
                $sql = "SELECT p.id AS 'id_produto', imagem_path AS 'imagem', p.nome AS 'produto', nro_parcelas, valor_parcela, p.valor AS 'valor', m.nome AS 'marca'
                        FROM produto p
                        JOIN marca m
                        ON p.id_marca = m.id
                        JOIN subcategoria s
                        ON p.id_subcategoria = s.id
                        WHERE m.id = :id_marca AND p.". $status ." = 1 AND s.id = :id_subcategoria;";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id_marca', $_GET['id']);
            $stmt->bindValue(':id_subcategoria', $_GET['id_subcategoria']);
            if($stmt->execute()) return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            else return false;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getProdutosPorSubcatCores() {
            try {
                $status = 'status';
                $sql = "SELECT p.id AS 'id_produto', p.cor
                        FROM produto p
                        JOIN subcategoria s
                        ON p.id_subcategoria = s.id
                        WHERE p.". $status ." = 1 AND s.id = :id_subcategoria;";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id_subcategoria', $_GET['id']);
            if($stmt->execute()) return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            else return false;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function maiorMenorPreco($opcao) {
            try {
                $status = 'status';
                $query = "SELECT p.id AS 'id', imagem_path AS 'imagem', p.nome AS 'produto', p.descricao, p.cor, nro_parcelas, valor_parcela, p.valor AS 'valor', p.altura, p.largura, p.comprimento, p.peso, m.nome AS 'marca'
                            FROM produto p
                            JOIN subcategoria s
                            ON p.id_subcategoria = s.id
                            JOIN marca m
                            ON p.id_marca = m.id
                            WHERE (s.id = :subcategoria AND p.". $status ." = 1) ORDER BY p.valor ". $opcao;
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':subcategoria', $_GET['subcategoria']);
                if($stmt->execute()) return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                else return false;
            }
            catch(PDOException $e) {
                echo 'erro: '.$e->getMessage();
            }
        }
        
    }