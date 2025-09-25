<?php
    require '../classes_aux/ItensCarrinhoAux.php';
    class PagamentoService extends ItensCarrinhoAux {
        private $conexao;
        private $pagamento;

        public function __construct(Connection $conexao, Pagamento $pagamento)
        {
            $this->conexao = $conexao->conectar();
            $this->pagamento = $pagamento;
        }

        public function pagar(bool $flag) {
            $resposta = -1;
            if(!$flag) return false;
            else {
                session_start();
                $formaPgto = json_decode($_SESSION['forma_pgto']);
                $resposta = $this->salvarCompraBanco($formaPgto);
            }
            return $resposta;
        }

        private function salvarCompraBanco(stdClass $objPgto) {
            date_default_timezone_set('America/Sao_Paulo');
            $id_tipo_tabela = 0;
            try {
                $id_frete = $this->setFrete();
                $id_pedido = $this->setPedido();
                $id_entrega = $this->setEntrega($id_frete, $id_pedido);
                if($objPgto->id_modalidade == 1) $id_tipo_tabela = $this->setCartaoCredito();
                else if($objPgto->id_modalidade == 2) {
                    $dataVencimento = explode('/', $objPgto->dataVencimento);
                    $id_tipo_tabela = $this->setBoleto($dataVencimento);
                }
                else if($objPgto->id_modalidade == 3) $id_tipo_tabela = $this->setPix();
                $id_pagamento = $this->setPagamento($id_tipo_tabela, $id_entrega);
                $dadosFatura = $this->gerarFatura($id_pagamento, $id_entrega, $id_pedido);
                if($dadosFatura) {
                    return $id_pedido;
                }
                else return false;
            }
            catch(Exception $e) {
                echo 'ERRO: '. $e->getMessage();
            }
        }

        private function setFrete() {
            $objFrete = $_SESSION['frete'];
            $objFrete = unserialize($objFrete);
            try {
                $query = "INSERT INTO frete(valor, id_forma_envio) VALUES(:valor, (SELECT id FROM forma_envio WHERE nome = :nome));";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':valor', $objFrete->valorFrete);
                $stmt->bindValue(':nome', $objFrete->tipoEntrega);
                if($stmt->execute()) {
                    $id_frete = $this->conexao->lastInsertId();
                    return $id_frete;
                }
            }
            catch(PDOException $e) {
                echo 'ERRO: '. $e->getMessage();
            }
        }

        private function setPedido() {
            $conexao = new Connection();
            $itensCarrinho = new ItensCarrinhoAux($conexao);
            $listaIDS = $itensCarrinho->getIdsItensCarrinho($_SESSION['id_carrinho']);
            $listaIDS = json_encode($listaIDS);
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            try {
                $query = "INSERT INTO pedido(data_compra, dia, mes, ano, hora_compra, lista_itens_carrinho, valor, id_carrinho) VALUES(:data_compra, :dia, :mes, :ano, :hora_compra, :lista_itens_carrinho, :valor, :id_carrinho)";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':data_compra', date("Y-m-d"));
                $data = date("Y-m-d");
                $dataFiltroAdm = explode('-', $data);
                $stmt->bindValue(':dia', (int) $dataFiltroAdm[2]);
                $stmt->bindValue(':mes', (int) $dataFiltroAdm[1]);
                $stmt->bindValue(':ano', (int) $dataFiltroAdm[0]);
                $stmt->bindValue(':hora_compra', date('H:i:s'));
                $stmt->bindValue(':lista_itens_carrinho', $listaIDS);
                $stmt->bindValue(':valor', $formaPgto->valorTotalSemFrete);
                $stmt->bindValue(':id_carrinho', $_SESSION['id_carrinho']);
                if($stmt->execute()) {
                    $id_pedido = $this->conexao->lastInsertId();
                    return $id_pedido;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function setEntrega(int $id_frete, int $id_pedido) {
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            $frete = $_SESSION['frete'];
            $frete = unserialize($frete);
            $dataAtual = date("Y-m-d");
            $dataEntrega = date('Y-m-d', strtotime('+'. $frete->tempoEntrega .' days', strtotime($dataAtual)));
            $status = 'status';
            try {
                $query = "INSERT INTO entrega
                                        (tempo_entrega, ". $status .", data_entrega, valor_com_frete, id_endereco, id_cliente, id_pedido, id_frete)
                                        VALUES(:tempo_entrega, :st, :data_entrega, :valor_com_frete, :id_endereco, :id_cliente, :id_pedido, :id_frete);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':tempo_entrega', $frete->tempoEntrega);
                $stmt->bindValue(':st', 'EM TRÂNSITO');
                $stmt->bindValue(':data_entrega', $dataEntrega);
                $stmt->bindValue(':valor_com_frete', $formaPgto->valorTotalComFrete);
                $stmt->bindValue(':id_endereco', $_SESSION['id_endereco']);
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                $stmt->bindValue(':id_pedido', $id_pedido);
                $stmt->bindValue(':id_frete', $id_frete);
                if($stmt->execute()) {
                    $id_entrega = $this->conexao->lastInsertId();
                    return $id_entrega;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function setCartaoCredito() {
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            try {
                $query = "INSERT INTO cartao_credito(valor_parcela, nro_parcelas, data_validade, id_bandeira)
	                        VALUES(:valor_parcela, :nro_parcelas, :data_validade, :id_bandeira);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':valor_parcela', $formaPgto->valorParcela);
                $stmt->bindValue(':nro_parcelas', $formaPgto->numeroParcelas);
                $stmt->bindValue(':data_validade', $formaPgto->dataValidadeCartao);
                $stmt->bindValue(':id_bandeira', $formaPgto->id_bandeira);
                if($stmt->execute()) {
                    $id_cartao_credito = $this->conexao->lastInsertId();
                    return $id_cartao_credito;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function setBoleto($dataVenc) {
            $data = $dataVenc[2] .'-'. $dataVenc[1] .'-'. $dataVenc[0];
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            try {
                $query = "INSERT INTO boleto(valor, valor_taxa, nro_parcelas, data_vencimento)
	                        VALUES(:valor, :valor_taxa, :nro_parcelas, :data_vencimento);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':valor', $formaPgto->valorTotalSemFrete);
                $stmt->bindValue(':valor_taxa', 1.95);
                $stmt->bindValue(':nro_parcelas', '1');
                $stmt->bindValue(':data_vencimento', $data);
                if($stmt->execute()) {
                    $id_boleto = $this->conexao->lastInsertId();
                    return $id_boleto;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function setPix() {
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            try {
                $query = "INSERT INTO pix(valor, chave) VALUES(:valor, :chave)";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':valor', $formaPgto->valorTotalSemFrete);
                $stmt->bindValue(':chave', $formaPgto->codigoPix);
                if($stmt->execute()) {
                    $id_pix = $this->conexao->lastInsertId();
                    return $id_pix;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function setPagamento(int $id_tipo_tabela, int $id_entrega) {
            $formaPgto = $_SESSION['forma_pgto'];
            $formaPgto = json_decode($formaPgto);
            $status = 'status';
            try {
                $query = "INSERT INTO pagamento(". $status .", id_cliente, id_modalidade, id_tipo_tabela, id_entrega)
	                        VALUES(:st, :id_cliente, :id_modalidade, :id_tipo_tabela, :id_entrega);";
                $stmt = $this->conexao->prepare($query);
                if($formaPgto->id_modalidade == 1) $stmt->bindValue(':st', 'APROVADO');
                else if($formaPgto->id_modalidade == 2 || $formaPgto->id_modalidade == 3) $stmt->bindValue(':st', 'PENDENTE');
                $stmt->bindValue(':id_cliente', $_SESSION['id']);
                $stmt->bindValue(':id_modalidade', $formaPgto->id_modalidade);
                $stmt->bindValue(':id_tipo_tabela', $id_tipo_tabela);
                $stmt->bindValue(':id_entrega', $id_entrega);
                if($stmt->execute()) {
                    $id_pagamento = $this->conexao->lastInsertId();
                    return $id_pagamento;
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
        }

        private function gerarFatura(int $id_pagamento, int $id_entrega, int $id_pedido) {
            $array = [];
            try {
                $query = "SELECT 
                            cl.nome AS 'cliente', cl.email,
                            e.logradouro, e.numero, e.complemento, e.bairro, e.cep,
                            c.nome AS 'cidade', c.uf,
                            tr.nome AS 'transportadora',
                            fe.nome AS 'forma-envio',
                            fr.valor AS 'valor-frete'
                            FROM cliente cl JOIN endereco e
                            ON cl.id = e.id_cliente
                            JOIN cidade c
                            ON e.id_cidade = c.id
                            JOIN entrega ent
                            ON ent.id_endereco = e.id
                            JOIN frete fr
                            ON ent.id_frete = fr.id
                            JOIN forma_envio fe
                            ON fr.id_forma_envio = fe.id
                            JOIN transportadora tr
                            ON fe.id_transportadora = tr.id
                            WHERE ent.id = :id_entrega;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_entrega', $id_entrega);
                if($stmt->execute()) $clienteEnderecoFrete = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            try {
                $status = 'ic.status';
                $query = "SELECT ic.id AS 'id_itens_carrinho', m.nome AS 'Marca', pr.id AS 'id_produto', pr.nome AS 'Produto', ic.quantidade AS 'Quant', pr.valor AS 'Valor', ic.valor_total AS 'Total'
                            FROM pedido pd
                            JOIN carrinho c
                            ON pd.id_carrinho = c.id
                            JOIN itens_carrinho ic
                            ON c.id = ic.id_carrinho
                            JOIN produto pr
                            ON pr.id = ic.id_produto
                            JOIN marca m
                            ON m.id = pr.id_marca
                            WHERE (pd.id = :id_pedido AND ". $status ." = 1);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_pedido', $id_pedido);
                $stmt->execute();
                $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            try {
                $query = "SELECT valor AS 'total' FROM pedido WHERE id = :id_pedido;";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':id_pedido', $id_pedido);
                if($stmt->execute()) {
                    $valorPedido = $stmt->fetch(PDO::FETCH_ASSOC);
                    $array = [
                        'cliente_endereco_frete' => $clienteEnderecoFrete,
                        'produtos' => $produtos,
                        'valor_pedido' => $valorPedido
                    ];
                }
            }
            catch(PDOException $e) {
                echo 'erro: '. $e->getMessage();
            }
            date_default_timezone_set('America/Sao_Paulo');
            $hora = date('H:i:s');
            $data = date("Y-m-d");
            $frete = $_SESSION['frete'];
            $frete = unserialize($frete);
            $frete = $frete->valorFrete;
            $obj = $_SESSION['forma_pgto'];
            $objPgto = json_decode($obj);
            $valor_icms = $objPgto->valorTotalComFrete * 0.17;
            $valorTotal = $objPgto->valorTotalSemFrete;       
            try {
                $query = "INSERT INTO fatura(data_emissao, hora_emissao, valor_total, valor_icms, valor_frete, id_pagamento)
                                VALUES(:data_emissao, :hora_emissao, :valor_total, :valor_icms, :valor_frete, :id_pagamento);";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindValue(':data_emissao', $data);
                $stmt->bindValue(':hora_emissao', $hora);
                $stmt->bindValue(':valor_total', $valorTotal);
                $stmt->bindValue(':valor_icms', $valor_icms);
                $stmt->bindValue(':valor_frete', $frete);
                $stmt->bindValue(':id_pagamento', $id_pagamento);
                if($stmt->execute()) {
                    foreach($produtos as $indice => $valor) {
                        try {
                            $status = 'status';
                            $query = "UPDATE itens_carrinho 
                                            SET preco_unit = :preco_unit
                                            WHERE (id = :id AND ". $status ." = 1);";
                            $stmt = $this->conexao->prepare($query);
                            $stmt->bindValue(':preco_unit', $produtos[$indice]['Valor']);
                            $stmt->bindValue(':id', $produtos[$indice]['id_itens_carrinho']);
                            $stmt->execute();
                        }
                        catch(PDOException $e) {
                            echo 'Erro: '. $e->getMessage();
                        }
                    }
                    $conexao = new Connection();
                    $itensCarrinho = new ItensCarrinhoAux($conexao);
                    $itensCarrinho->fecharCompraItensCarrinho($_SESSION['id_carrinho']);
                }
            }
            catch(PDOException $e) {
                echo 'Erro: '. $e->getMessage();
            }
            return $array;
        }

    }