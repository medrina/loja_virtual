<?php
    class ItensCarrinhoAux {
        private $conexao;
        private int $id;
        private $data_inclusao;
        private int $quantidade;
        private float $valor_total;
        private Carrinho $carrinho;
        private Produto $produto;
        private $arrayProdutos;

        public function __construct(Connection $conexao)
        {
            $this->conexao = $conexao->conectar();
            $this->arrayProdutos = [];
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function setDataInclusao($data_inclusao) {
            $this->data_inclusao = $data_inclusao;
        }

        public function adicionarProduto() {
            $quantProdutos = 0; $id_carrinho = 0; $isQuant = 0; $valor_produto = 0.0;
            $id_produto = $_POST['id'];
            session_start();
            $id_carrinho = $_SESSION['id_carrinho'];
            $isQuant = $this->verificarQuantidade($id_carrinho, $id_produto);
            if($isQuant) {
                try {
                    $quantProdutos = $this->recuperarQuantidade($id_carrinho, $id_produto);
                    $quantProdutos++;
                    $status = 'status';
                    $query = "UPDATE itens_carrinho 
                                SET quantidade = :quant,
                                    valor_total = (SELECT p.valor * :quant FROM produto p WHERE p.id = :id_produto)
                     WHERE (id_carrinho = :id_carrinho AND id_produto = :id_produto AND ". $status ." = 1);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':quant', $quantProdutos);
                    $stmt->bindValue(':id_carrinho', $id_carrinho);
                    $stmt->bindValue(':id_produto', $id_produto);
                    if($stmt->execute()) return true;
                }
                catch(PDOException $e) {
                    return false;
                }
            }
            else {
                try {
                    $status = 'status';
                    $query = "INSERT INTO itens_carrinho(quantidade, valor_total, ". $status .", id_carrinho, id_produto)
                                VALUES(1, (SELECT p.valor FROM produto p WHERE p.id = :id_produto), :st, :id_carrinho, :id_produto);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':st', 1);
                    $stmt->bindValue(':id_carrinho', $id_carrinho);
                    $stmt->bindValue(':id_produto', $id_produto);
                    if($stmt->execute()) return true;
                }
                catch(PDOException $e) {
                    return false;
                }
            }
        }

        public function alterarQuantidadeProdutoCarrinho() {
            try {
                $query = "UPDATE itens_carrinho 
                                    SET quantidade = :quant,
                                        valor_total = (SELECT p.valor * :quant FROM produto p WHERE p.id = :id_produto)
                        WHERE (id_carrinho = :id_carrinho AND id_produto = :id_produto);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':quant', $_GET['quant']);
                $stmt->bindValue(':id_carrinho', $_GET['car']);
                $stmt->bindValue(':id_produto', $_GET['prod']);
                if($stmt->execute()) {
                    $status = 'status';
                    try {
                        $query = "SELECT quantidade, valor_total AS total FROM itens_carrinho WHERE (id_carrinho = :id_carrinho AND id_produto = :id_produto AND ". $status ." = 1);";
                        $stmt = $this->conexao->prepare($query);
                        $stmt->bindValue(':id_carrinho', $_GET['car']);
                        $stmt->bindValue(':id_produto', $_GET['prod']);
                        $stmt->execute();
                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        return $resultado;
                    }
                    catch(PDOException $e) {
                        echo 'Erro: '. $e->getMessage();
                    }
                }
                else return 0;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getProdutos(int $id_carrinho) {
            $status = 'status';
            try {
                $query = "SELECT count(*) as 'quant' FROM itens_carrinho WHERE (id_carrinho = :id_carrinho AND ". $status ." = 1)";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                $stmt->execute();
                $quant = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            try {
                $query = "SELECT id_produto FROM itens_carrinho WHERE (id_carrinho = :id_carrinho AND ". $status ." = 1)";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            $primeiroID = '';
            $string = '';
            if(empty($resultado)) {
                $_SESSION['status_carrinho'] = false;
            }
            else {
                if($quant['quant'] < 2) {
                    $primeiroID .= $resultado['id_produto'];
                    $string = $primeiroID;
                }
                else {
                    $primeiroID = ', ';
                    $primeiroID .= $resultado['id_produto'];
                    $ids_produtos = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        array_push($ids_produtos, $row['id_produto']);
                    }
                    $array_produtos = array();
                    foreach($ids_produtos as $indice => $val) {
                        $array_produtos[] = $ids_produtos[$indice];
                    }
                    $string = implode(">", $array_produtos);
                    $string = str_replace('>', ', ', $string);
                    $string .= $primeiroID;
                }
                try {
                    $query = "SELECT id, nome, valor, imagem_path AS 'imagem', id_subcategoria, id_marca FROM produto WHERE id IN($string) ORDER BY (id);";
                    $stmt = $this->conexao->query($query);
                    $stmt->execute();
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'Erro: '. $e->getMessage();
                }
                try {
                    $query = "SELECT id, quantidade, valor_total FROM itens_carrinho WHERE (". $status ." = 1) AND id_carrinho = :id_carrinho ORDER BY (id_produto);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':id_carrinho', $id_carrinho);
                    $stmt->execute();
                    $itens_carrinhos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'Erro: '. $e->getMessage();
                }
                $conexao = new Connection();
                $i = 0;
                foreach($resultado as $indice => $produto) {
                    $produto = new Produto();
                    $produto->__set('id', $resultado[$indice]['id']);
                    $produto->__set('nome', $resultado[$indice]['nome']);
                    $produto->__set('valor', $resultado[$indice]['valor']);
                    $produto->__set('imagem_path', $resultado[$indice]['imagem']);
                    try {
                        $query = "SELECT id, nome FROM marca WHERE id = ". $resultado[$indice]['id_marca'];
                        $stmt = $this->conexao->query($query);
                        $stmt->execute();
                        $marca = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    catch(PDOException $e) {
                        echo 'Erro: '. $e->getMessage();
                    }
                    $objMarca = new Marca();
                    $objMarca->__set('id', $marca['id']);
                    $objMarca->__set('nome', $marca['nome']);
                    $produto->setMarca($objMarca);
                    try {
                        $query = 'select c.id as "id_categoria", c.nome as "categoria", s.id as "id_subcategoria", s.nome as "subcategoria"
                                    from categoria c
                                    JOIN aux_cat_sub cs
                                    ON c.id = cs.id_categoria
                                    JOIN subcategoria s
                                    ON s.id = cs.id_subcategoria
                                    WHERE s.id = '. $resultado[$indice]['id_subcategoria'];
                        $stmt = $this->conexao->query($query);
                        $stmt->execute();
                        $cat_sub = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    catch(PDOException $e) {
                        echo 'Erro: '. $e->getMessage();
                    }
                    $categoria = new Categoria();
                    $categoria->__set('nome', $cat_sub['categoria']);
                    $categoria->__set('id', $cat_sub['id_categoria']);
                    $subcategoria = new Subcategoria();
                    $subcategoria->__set('nome', $cat_sub['subcategoria']);
                    $subcategoria->__set('id', $cat_sub['id_subcategoria']);
                    $objCategoriaSubcategoria = new CategoriaSubcategoriaAux($categoria, $subcategoria);
                    $objItensCarrinho = new ItensCarrinhoAux($conexao);
                    $objItensCarrinho->id = $itens_carrinhos[$i]['id'];
                    $objItensCarrinho->quantidade = $itens_carrinhos[$i]['quantidade'];
                    $objItensCarrinho->valor_total = $itens_carrinhos[$i]['valor_total'];
                    $this->arrayProdutos[$i] = [
                        'itens_carrinho' => $objItensCarrinho,
                        'produto' => $produto,
                        'cat_sub' => $objCategoriaSubcategoria
                    ];
                    $i++;
                    $_SESSION['status_carrinho'] = true;
                }
            }
            require_once '../views/painel/painel.phtml';
        }

        public function getProdutosCarrinhoCliente(int $id_carrinho) {
            $status = 'status';
            try {
                $query = "SELECT count(*) as 'quant' FROM itens_carrinho WHERE (". $status ." = 1 AND id_carrinho = :id_carrinho);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                $stmt->execute();
                $quant = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            try {
                $query = "SELECT id_produto FROM itens_carrinho WHERE (". $status ." = 1 AND id_carrinho = :id_carrinho)";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            $primeiroID = '';
            $string = '';
            if(empty($resultado)) {
                $_SESSION['status_carrinho'] = false;
            }
            else {
                if($quant['quant'] < 2) {
                    $primeiroID .= $resultado['id_produto'];
                    $string = $primeiroID;
                }
                else {
                    $primeiroID = ', ';
                    $primeiroID .= $resultado['id_produto'];
                    $ids_produtos = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        array_push($ids_produtos, $row['id_produto']);
                    }
                    $array_produtos = array();
                    foreach($ids_produtos as $indice => $val) {
                        $array_produtos[] = $ids_produtos[$indice];
                    }
                    $string = implode(">", $array_produtos);
                    $string = str_replace('>', ', ', $string);
                    $string .= $primeiroID;
                }
                try {
                    $query = "SELECT id, nome, cor, valor, imagem_path AS 'imagem', nro_parcelas, valor_parcela, altura, largura, comprimento, peso, id_subcategoria, id_marca FROM produto WHERE id IN($string) ORDER BY (id);";
                    $stmt = $this->conexao->query($query);
                    $stmt->execute();
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'Erro: '. $e->getMessage();
                }
                try {
                    $query = "SELECT quantidade, valor_total FROM itens_carrinho WHERE (". $status ." = 1) AND id_carrinho = :id_carrinho ORDER BY (id_produto);";
                    $stmt = $this->conexao->prepare($query);
                    $stmt->bindValue(':id_carrinho', $id_carrinho);
                    $stmt->execute();
                    $itens_carrinhos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                catch(PDOException $e) {
                    echo 'Erro: '. $e->getMessage();
                }
                $conexao = new Connection();
                $i = 0;
                foreach($resultado as $indice => $produto) {
                    $produto = new Produto();
                    $produto->__set('id', $resultado[$indice]['id']);
                    $produto->__set('nome', $resultado[$indice]['nome']);
                    $produto->__set('cor', $resultado[$indice]['cor']);
                    $produto->__set('valor', $resultado[$indice]['valor']);
                    $produto->__set('imagem_path', $resultado[$indice]['imagem']);
                    $produto->__set('nro_parcelas', $resultado[$indice]['nro_parcelas']);
                    $produto->__set('valor_parcela', $resultado[$indice]['valor_parcela']);
                    $produto->__set('altura', $resultado[$indice]['altura']);
                    $produto->__set('largura', $resultado[$indice]['largura']);
                    $produto->__set('comprimento', $resultado[$indice]['comprimento']);
                    $produto->__set('peso', $resultado[$indice]['peso']);
                    try {
                        $query = "SELECT id, nome FROM marca WHERE id = ". $resultado[$indice]['id_marca'];
                        $stmt = $this->conexao->query($query);
                        $stmt->execute();
                        $marca = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    catch(PDOException $e) {
                        echo 'Erro: '. $e->getMessage();
                    }
                    $objMarca = new Marca();
                    $objMarca->__set('id', $marca['id']);
                    $objMarca->__set('nome', $marca['nome']);
                    $produto->setMarca($objMarca);
                    try {
                        $query = 'select c.id as "id_categoria", c.nome as "categoria", s.id as "id_subcategoria", s.nome as "subcategoria"
                                    from categoria c
                                    JOIN aux_cat_sub cs
                                    ON c.id = cs.id_categoria
                                    JOIN subcategoria s
                                    ON s.id = cs.id_subcategoria
                                    WHERE s.id = '. $resultado[$indice]['id_subcategoria'];
                        $stmt = $this->conexao->query($query);
                        $stmt->execute();
                        $cat_sub = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    catch(PDOException $e) {
                        echo 'Erro: '. $e->getMessage();
                    }
                    $categoria = new Categoria();
                    $categoria->__set('nome', $cat_sub['categoria']);
                    $categoria->__set('id', $cat_sub['id_categoria']);
                    $subcategoria = new Subcategoria();
                    $subcategoria->__set('nome', $cat_sub['subcategoria']);
                    $subcategoria->__set('id', $cat_sub['id_subcategoria']);
                    $objCategoriaSubcategoria = new CategoriaSubcategoriaAux($categoria, $subcategoria);
                    $objItensCarrinho = new ItensCarrinhoAux($conexao);
                    $objItensCarrinho->quantidade = $itens_carrinhos[$i]['quantidade'];
                    $objItensCarrinho->valor_total = $itens_carrinhos[$i]['valor_total'];
                    $this->arrayProdutos[$i] = [
                        'itens_carrinho' => $objItensCarrinho,
                        'produto' => $produto,
                        'cat_sub' => $objCategoriaSubcategoria
                    ];
                    $i++;
                }
            }
            return $this->arrayProdutos;
        }
        
        private function verificarQuantidade(int $id_carrinho, int $id_produto) {
            $status = 'status';
            try {
                $query = "SELECT count(*) AS 'quant' FROM itens_carrinho WHERE (id_carrinho = :id_carrinho AND id_produto = :id_produto AND ". $status ." = 1);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue('id_carrinho', $id_carrinho);
                $stmt->bindValue('id_produto', $id_produto);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if($resultado['quant'] == 1) return true;
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function recuperarQuantidade(int $id_carrinho, int $id_produto) {
            try {
                $query = "SELECT quantidade FROM itens_carrinho WHERE (id_carrinho = :id_carrinho AND id_produto = :id_produto);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue('id_carrinho', $id_carrinho);
                $stmt->bindValue('id_produto', $id_produto);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['quantidade'];
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getArrayProdutos() {
            return $this->arrayProdutos;
        }

        public function removerProdutoCarrinho(int $id){
            session_start();
            try {
                $query = "DELETE FROM itens_carrinho WHERE id = :id;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        protected function fecharCompraItensCarrinho(int $id_carrinho) {
            $status = 'status';
            try {
                $query = "UPDATE itens_carrinho 
                                    SET ". $status ." = :st
                                    WHERE (id_carrinho = :id_carrinho AND ". $status ." = 1);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':st', 0);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                if($stmt->execute()) return true;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        protected function getIdsItensCarrinho(int $id_carrinho) {
            $status = 'status';
            try {
                $query = "SELECT ic.id FROM itens_carrinho ic
                            JOIN produto p
                            ON p.id = ic.id_produto
                            JOIN carrinho c
                            ON c.id = ic.id_carrinho
                            WHERE (ic.". $status ." = :st) AND c.id = :id_carrinho;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':st', 1);
                $stmt->bindValue(':id_carrinho', $id_carrinho);
                if($stmt->execute()) {
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $resultado;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }
    }
