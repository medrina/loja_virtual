<?php
    class ClienteController {
        private $clienteService;

        public function __construct(ClienteService $clienteService) {
            $this->clienteService = $clienteService;
        }

        public function dadosPessoaisCheckout() {
            $array = [];
            if(isset($_POST)) {
                session_start();
                $cliente = $this->clienteService->getClienteByID($_SESSION['id']);
                $conexao = new Connection();
                $clienteAsaasService = new ClienteAsaasService($conexao, new ClienteAsaas());
                $clienteAsaas = $clienteAsaasService->getTelefoneClienteAsaas();
                $array = [
                    'cliente' => $cliente,
                    'cliente_asaas' => $clienteAsaas
                ];
                $array = json_encode($array);
                echo $array;
            }
            else echo false;
        }

    }