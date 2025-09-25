<?php
    class PedidoService {
        private $conexao;
        private $pedido;

        public function __construct(Connection $conexao, Pedido $pedido)
        {
            $this->conexao = $conexao->conectar();
            $this->pedido = $pedido;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function getPedidos() {
            $arrayIDsItensCarrinho = array();
            try {
                $status = 'et.status';
                $query = "SELECT p.id, p.data_compra, p.hora_compra, p.valor, p.lista_itens_carrinho, ". $status .", en.logradouro, en.numero, en.complemento, en.bairro, en.cep, c.nome AS 'cidade', c.uf
                                FROM pedido p JOIN entrega et
                                ON p.id = et.id_pedido
                                JOIN endereco en
                                ON en.id = et.id_endereco
                                JOIN cidade c
                                ON c.id = en.id_cidade
                                WHERE id_carrinho = :id_carrinho;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_carrinho', $_SESSION['id_carrinho']);
                $stmt->execute();               
                if($stmt->execute()) {
                    $obj = '';
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($resultado as $indice => $valor) {
                        $obj = json_decode($resultado[$indice]['lista_itens_carrinho']);
                        $arrayIDsItensCarrinho[] = $obj;
                    }
                    $arrayItens = array();
                    for($i = 0; $i < count($arrayIDsItensCarrinho); $i++) {
                        for($j = 0; $j < count($arrayIDsItensCarrinho[$i]); $j++) {
                            try {
                                $query2 = "SELECT ic.id, m.nome AS 'Marca', pr.nome AS 'Produto', pr.imagem_path, ic.quantidade AS 'Quant', pr.valor AS 'Valor', ic.valor_total AS 'Total'
                                                FROM carrinho c
                                                JOIN itens_carrinho ic
                                                ON c.id = ic.id_carrinho
                                                JOIN produto pr
                                                ON pr.id = ic.id_produto
                                                JOIN marca m
                                                ON m.id = pr.id_marca
                                                WHERE ic.id = :id;";
                                $stmt = $this->conexao->prepare($query2);
                                $stmt->bindValue(':id', $arrayIDsItensCarrinho[$i][$j]->id);
                                if($stmt->execute()) {
                                    $item = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $arrayItens[$i][] = $item;
                                }
                            }
                            catch(PDOException $e) {
                                echo 'erro: '. $e->getMessage();
                            }
                        }
                    }
                    $listaPedidos = [
                        'pedidos' => $resultado,
                        'itens' => $arrayItens
                    ];
                    return $listaPedidos;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function getPedidoPorID(int $id) {
            $listaIDsItens = [];
            try {
                $status = 'status';
                $query1 = "SELECT cl.nome, cl.email, p.id AS 'id_pedido', p.data_compra, p.hora_compra, p.valor, p.lista_itens_carrinho,
                                t.nome AS 'transportadora', fe.nome AS 'forma_envio', f.valor AS 'preco_frete',
                                et.`status` AS 'status_entrega', et.tempo_entrega, et.data_entrega, et.valor_com_frete,
                                en.logradouro, en.numero, en.complemento, en.bairro, en.cep, c.nome AS 'cidade', c.uf,
                                pg.id AS 'id_pagto', pg.id_modalidade, pg.id_tipo_tabela
                                FROM pedido p JOIN entrega et
                                ON p.id = et.id_pedido
                                JOIN pagamento pg
                                ON pg.id_entrega = et.id
                                JOIN frete f
                                ON f.id = et.id_frete
                                JOIN forma_envio fe
                                ON fe.id = f.id_forma_envio
                                JOIN transportadora t
                                ON t.id = fe.id_transportadora
                                JOIN endereco en
                                ON en.id = et.id_endereco
                                JOIN cidade c
                                ON c.id = en.id_cidade
                                JOIN cliente cl
                                ON cl.id = pg.id_cliente
                                WHERE p.id = :id_pedido;";
                $stmt1 = $this->conexao->prepare($query1);
                $stmt1->bindValue(':id_pedido', $id);
                if($stmt1->execute()) {
                    $resultado1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                    $obj = '';
                    foreach($resultado1 as $indice => $valor) {
                        $obj = json_decode($resultado1[$indice]['lista_itens_carrinho']);
                        $listaIDsItens[] = $obj;
                    }
                    $arrayItens = [];
                    for($i = 0; $i < count($listaIDsItens[0]); $i++) {
                        $query2 = "SELECT ic.id, m.nome AS 'Marca', pr.nome AS 'Produto', pr.imagem_path, ic.quantidade AS 'Quant', ic.preco_unit, ic.valor_total AS 'Total'
                                            FROM carrinho c
                                            JOIN itens_carrinho ic
                                            ON c.id = ic.id_carrinho
                                            JOIN produto pr
                                            ON pr.id = ic.id_produto
                                            JOIN marca m
                                            ON m.id = pr.id_marca
                                            WHERE ic.id = :id;";
                        $stmt2 = $this->conexao->prepare($query2);
                        $stmt2->bindValue(':id', $listaIDsItens[0][$i]->id);
                        if($stmt2->execute()) {
                            $itens = $stmt2->fetch(PDO::FETCH_ASSOC);
                            $arrayItens[] = $itens;
                        }
                    }
                    $idTipoTabela = $resultado1[0]['id_tipo_tabela'];
                    $id_modalidade = $resultado1[0]['id_modalidade'];
                    $resultado3 = '';
                    switch($id_modalidade) {
                        case 1:
                                $status = 'status';
                                $query3 = "SELECT m.tipo, c.nro_parcelas, c.valor_parcela, b.id AS 'id_bandeira', b.nome AS 'bandeira', ". $status ."
                                                FROM pagamento pg
                                                JOIN modalidade m
                                                ON m.id = pg.id_modalidade
                                                JOIN cartao_credito c
                                                ON c.id = pg.id_tipo_tabela
                                                JOIN bandeira b
                                                ON b.id = c.id_bandeira
                                                WHERE (pg.id_tipo_tabela = :id_tipo_tabela AND pg.id_modalidade = :id_modalidade)";
                                $stmt3 = $this->conexao->prepare($query3);
                                $stmt3->bindValue(':id_tipo_tabela', $idTipoTabela);
                                $stmt3->bindValue(':id_modalidade', $id_modalidade);
                                if($stmt3->execute()) {
                                    $resultado3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                                }
                                break;
                        case 2:
                                $status = 'status';
                                $query4 = "SELECT m.tipo, b.nro_parcelas, b.data_vencimento, ". $status ."
                                                FROM pagamento pg
                                                JOIN modalidade m
                                                ON m.id = pg.id_modalidade
                                                JOIN boleto b
                                                ON b.id = pg.id_tipo_tabela
                                                WHERE (pg.id_tipo_tabela = :id_tipo_tabela AND pg.id_modalidade = :id_modalidade)";
                                $stmt4 = $this->conexao->prepare($query4);
                                $stmt4->bindValue(':id_tipo_tabela', $idTipoTabela);
                                $stmt4->bindValue('id_modalidade', $id_modalidade);
                                if($stmt4->execute()) {
                                    $resultado3 = $stmt4->fetch(PDO::FETCH_ASSOC);
                                }
                                break;
                        case 3:
                                $status = 'status';
                                $query = "SELECT m.tipo, p.valor, p.chave, `status`
                                                FROM pagamento pg
                                                JOIN modalidade m
                                                ON m.id = pg.id_modalidade
                                                JOIN pix p
                                                ON p.id = pg.id_tipo_tabela
                                                WHERE (pg.id_tipo_tabela = :id_tipo_tabela AND pg.id_modalidade = :id_modalidade);";
                                $stmt = $this->conexao->prepare($query);
                                $stmt->bindValue(':id_tipo_tabela', $idTipoTabela);
                                $stmt->bindValue(':id_modalidade', $id_modalidade);
                                if($stmt->execute()) $resultado3 = $stmt->fetch(PDO::FETCH_ASSOC);
                                break;
                    }
                    $pedidoDetalhado = [
                        'pedido' => $resultado1,
                        'item' => $arrayItens,
                        'pgto' => $resultado3
                    ];
                    return $pedidoDetalhado;
                }
            }
            catch(PDOException $e) {
                echo 'Errol: '. $e->getMessage();
            }
        }

        public function getPedidosPorDiaMesAno() {
            $data = explode('-', $_GET['data']);
            $arrayIDsItensCarrinho = array();
            try {
                $status = 'et.status';
                $statusPgto = 'pg.status';
                $query = "SELECT cl.nome AS 'cliente', cl.email, p.id AS 'id_pedido', p.data_compra, p.hora_compra, p.valor, ". $statusPgto ." AS 'status_pgto'
                                FROM pedido p JOIN entrega et
                                ON p.id = et.id_pedido
                                JOIN carrinho cr
                                ON p.id_carrinho = cr.id
                                JOIN cliente cl
                                ON cr.id_cliente = cl.id
                                JOIN pagamento pg
                                ON pg.id_entrega = et.id
                                WHERE p.dia = :dia AND p.mes = :mes AND p.ano = :ano ORDER BY p.id;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':dia', $data[2]);
                $stmt->bindValue(':mes', $data[1]);
                $stmt->bindValue(':ano', $data[0]);
                $stmt->execute();               
                if($stmt->execute()) {
                    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $listaPedidos = [
                        'pedidos' => $resultado
                    ];
                    return $listaPedidos;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        public function atualizarStatusPedidoEntrega(string $st, int $id_pedido) {
            $status = 'status';
            try {
                $query = "UPDATE entrega SET ". $status ." = :status WHERE id_pedido = :id_pedido;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':status', $st);
                $stmt->bindValue(':id_pedido', $id_pedido);
                if($stmt->execute()) return true;
                else return false;
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

    }