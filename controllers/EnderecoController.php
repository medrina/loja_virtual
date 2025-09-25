<?php
    class EnderecoController {
        private $enderecoService;

        public function __construct(EnderecoService $enderecoService)
        {
            $this->enderecoService = $enderecoService;
        }

        public function editarEndereco($id) {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $endereco = $this->enderecoService->getEnderecoById($id);
                include __DIR__ . '/../views/painel/editar_endereco.phtml';
            }
        }

        public function editarEnderecoBanco() {
            $cidade = new Cidade($_POST['cidade'], $_POST['uf']);
            $endereco = new Endereco();
            $endereco->__set('numero', $_POST['numero']);
            $endereco->__set('logradouro', $_POST['logradouro']);
            $endereco->__set('complemento', $_POST['complemento']);
            $endereco->__set('cep', $_POST['cep']);
            $endereco->__set('bairro', $_POST['bairro']);
            $endereco->__set('cidade', $cidade);
            $conexao = new Connection();
            $enderecoService = new EnderecoService($conexao, $endereco);
            $resultado = $enderecoService->atualizarEndereco();
            if($resultado) echo 1;
            else echo 0;
        }

        public function apagarEnderecoBancoById() {
            $resp = $this->enderecoService->apagarEnderecoById($_POST['id']);
            echo $resp;
        }

        public function selecaoFrete() {
            $objetoFrete = new stdClass();
            $entrega = $_POST['dados'];
            $dadosFrete = explode('&', $entrega);
            $scanf = explode('=', $dadosFrete[0]);
            $objetoFrete->idFreteTransportadora = $scanf[1];
            $scanf = explode('=', $dadosFrete[1]);
            $objetoFrete->valorFrete = $scanf[1];
            $scanf = explode('=', $dadosFrete[2]);
            $objetoFrete->tempoEntrega = $scanf[1];
            $scanf = explode('=', $dadosFrete[3]);
            $objetoFrete->transportadoraNome = $scanf[1];
            $scanf = explode('=', $dadosFrete[4]);
            $objetoFrete->tipoEntrega = $scanf[1];
            $obj = serialize($objetoFrete);
            session_start();
            $_SESSION['frete'] = $obj;
            if($_SESSION['frete']) echo true;
            else false;
        }

        public function confirmarEnderecoFrete() {
            session_start();
            $_SESSION['id_endereco'] = $_POST['id_endereco'];
            $dadosFrete = $_SESSION['frete'];
            $dados = unserialize($dadosFrete);
            $array = array();
            $_SESSION['id_endereco'] = $_POST['id_endereco'];
            $endereco = $this->enderecoService->getEnderecoById($_SESSION['id_endereco']);
            $array = [
                'endereco' => $endereco,
                'frete' => $dados
            ];
            $array = json_encode($array);
            echo $array;
        }

    }