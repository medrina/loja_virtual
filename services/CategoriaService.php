<?php
    class CategoriaService {
        private $conexao;
        private Categoria $categoria;

        public function __construct(Connection $conexao, Categoria $categoria)
        {
            $this->conexao = $conexao->conectar();
            $this->categoria = $categoria;
        }

        public function getCategorias() {
            try {
                $query = 'SELECT id, nome from categoria ORDER BY (nome);';
                $stmt = $this->conexao->query($query);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function getCategoriaPorID($id) {
            try {
                $query = "SELECT s.id AS 'id_subcategoria', s.nome AS 'subcategoria' FROM categoria c
                    JOIN aux_cat_sub ct
                    ON c.id = ct.id_categoria
                    JOIN subcategoria s
                    ON s.id = ct.id_subcategoria
                    WHERE ct.id_categoria = :id;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $i = 0;
                $cat = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $cat[$i]['id'] = $row['id_subcategoria'];
                    $cat[$i]['nome'] = $row['subcategoria'];
                    $i++;
                }
                return $cat;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }

        public function salvarCategoria() {
            try {
                $query = "SELECT count(*) AS 'quant' FROM categoria WHERE nome = :nome";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->categoria->__get('nome'));
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($resultado['quant']) return 1;
            else {
                try {
                    $query = "INSERT INTO categoria(nome) VALUES(:categoria);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':categoria', $this->categoria->__get('nome'));
                    if($stmt->execute()) return 2;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function atualizarCategoria() {
            try {
                $query = "UPDATE categoria SET nome = :nome WHERE id = :id_categoria;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $_POST['nome_categoria']);
                $stmt->bindValue(':id_categoria', $_POST['id_categoria']);
                if($stmt->execute()) return true;
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }
        
    }