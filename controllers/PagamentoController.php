<?php
    class PagamentoController {
        private $pagamentoService;

        public function __construct(PagamentoService $pagamentoService)
        {
            $this->pagamentoService = $pagamentoService;
        }

        public function selecionarPgto() {
            switch($_POST['modalidade']) {
                case 1: $formaPgto = new stdClass();
                        $numeroCartao = str_replace(' ', '', $_POST['numero-cartao-credito-checkout']);
                        $formaPgto->numeroCartao = $numeroCartao;
                        $formaPgto->nomeCartao = $_POST['nome-cartao-credito-checkout'];
                        $formaPgto->dataValidadeCartao = $_POST['validade-cartao-credito-checkout'];
                        $formaPgto->numeroParcelas = $_POST['parcelas-cartao-credito-checkout'];
                        $formaPgto->codigoCartao = $_POST['codigo-cartao-credito-checkout'];
                        $cpf = str_replace('.', '', $_POST['cpf-cartao-credito-checkout']);
                        $cpf = str_replace('-', '', $cpf);
                        $formaPgto->cpfCartao = $cpf;
                        $formaPgto->valorTotalSemFrete = $_POST['valor-total-sem-frete-checkout'];
                        $formaPgto->valorTotalComFrete = $_POST['valor-total-com-frete-checkout'];
                        $formaPgto->valorParcela = $_POST['valor-parcela-checkout'];
                        $formaPgto->id_modalidade = $_POST['modalidade'];
                        $formaPgto->id_bandeira = $_POST['id_bandeira_checkout'];
                        break;
                case 2: $formaPgto = new stdClass();
                        $formaPgto->valorTotalSemFrete = $_POST['valor-total-sem-frete-checkout'];
                        $formaPgto->valorTotalComFrete = $_POST['boleto-checkout'];
                        $formaPgto->dataVencimento = $_POST['vencimento-boleto-checkout'];
                        $formaPgto->id_modalidade = $_POST['modalidade'];
                        break;
                case 3: $formaPgto = new stdClass();
                        $formaPgto->valorTotalSemFrete = $_POST['valor-total-sem-frete-checkout'];
                        $formaPgto->valorTotalComFrete = $_POST['pix-checkout'];
                        $formaPgto->codigoPix = $_POST['codigo-pix'];
                        $formaPgto->id_modalidade = $_POST['modalidade'];
                        break;
                default: echo false;
            }
            $objPgto = json_encode($formaPgto);
            session_start();
            $_SESSION['forma_pgto'] = $objPgto;
            if($_SESSION['forma_pgto']) echo true;
            else echo false;
        }

        public function pagar() {
            $dados = $_POST['dados'];
            $resposta = $this->pagamentoService->pagar($dados);
            echo $resposta;
        }

    }