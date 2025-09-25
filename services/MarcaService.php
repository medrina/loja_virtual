<?php
    class MarcaService {
        private $conexao;
        private $marca;

        public function __construct(Connection $conexao, Marca $marca)
        {
            $this->conexao = $conexao->conectar();
            $this->marca = $marca;
        }

        public function setMarca(Marca $marca)
        {
            $this->marca = $marca;
        }

        public function getMarca() {
            return $this->marca;
        }

        public function show() {
            try {
                $query = "SELECT id, nome FROM marca ORDER BY nome;";
                $stmt = $this->conexao->query($query);
                if($stmt->execute()) {
                    $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $lista;
                }
            }
            catch(PDOException $e) {
                return false;
            }
        }

        public function setMarcaBanco() {
            try {
                $query = "SELECT count(*) AS 'quant' FROM marca WHERE nome = :nome";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':nome', $this->getMarca()->__get('nome'));
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            if($resultado['quant']) return 1;
            else {
                try {
                    $query = "INSERT INTO marca(nome) VALUES(:marca);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue('marca', $this->getMarca()->__get('nome'));
                    if($stmt->execute()) return 2;
                }
                catch(PDOException $e) {
                    echo 'erro: '. $e->getMessage();
                }
            }
        }

        public function getMarcasPorSubcategoria() {
            try {
                $status = 'status';
                $query = "SELECT m.id, m.nome FROM marca m
                            JOIN produto p
                            ON m.id = p.id_marca
                            JOIN subcategoria s
                            ON p.id_subcategoria = s.id
                            WHERE p.id_subcategoria = :id_subcategoria AND ". $status ." = 1;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_subcategoria', $_GET['id']);
                if($stmt->execute()) {
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $resultado;
                }
                else return false;
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
        }
    }