<?php
    class CategoriaSubcategoriaService {
        private $conexao;
        private $categoria;
        private $subcategoria;

        public function __construct(Connection $conexao, Categoria $categoria, Subcategoria $subcategoria)
        {
            $this->conexao = $conexao->conectar();
            $this->categoria = $categoria;
            $this->subcategoria = $subcategoria;
        }

        public function show() {
            try {
                $query = "select c.id as 'id_categoria', c.nome as 'categoria', s.id as 'id_subcategoria', s.nome as 'subcategoria'
                            from aux_cat_sub a
                            INNER JOIN categoria c
                            on c.id = a.id_categoria
                            INNER JOIN subcategoria s
                            on s.id = a.id_subcategoria
                            ORDER BY c.id DESC;";
                $stmt = $this->conexao->query($query);
                $stmt->execute();
                $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            try {
                $query = "SELECT MAX(id) AS 'max' FROM categoria;";
                $stmt = $this->conexao->query($query);
                $stmt->execute();
                $max_id_categoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $max = $max_id_categoria[0]['max'];
                return $lista;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getByIdCategoria(int $id) {
            try {
                $query = "SELECT s.id AS 'id_subcategoria', s.nome AS 'subcategoria' FROM categoria c
                            JOIN aux_cat_sub ct
                            ON c.id = ct.id_categoria
                            JOIN subcategoria s
                            ON s.id = ct.id_subcategoria
                            WHERE c.id = :id";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $consulta = $stmt->fetch(PDO::FETCH_ASSOC);
                print_r($consulta);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function salvarCad_Sub() {
            try {
                $query = "INSERT INTO aux_cat_sub(id_categoria, id_subcategoria) VALUES(:categoria, :subcategoria);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':categoria', $this->categoria->__get('id'));
                $stmt->bindValue(':subcategoria', $this->subcategoria->__get('id'));
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'ERROR: '. $e->getMessage();
            }
        }

    }