<?php

use Connection as GlobalConnection;

    class HomeController {

        public function index() {
            $conexao = new GlobalConnection();
            require '../classes_aux/CategoriaSubcategoriaService.php';
            $cat_subService = new CategoriaSubcategoriaService($conexao, new Categoria(), new Subcategoria());
            $cat = $cat_subService->show();
            include __DIR__ .'/../views/layouts/home.phtml';
        }

        public function adicionarCategoriaSubcategoria() {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(testarLoginAdmin()) include __DIR__ .'/../views/layouts/add_cat_sub.phtml';
            else header('Location: /?erro=4');
        }

        public function editarCategoriaSubcategoria() {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(!testarLoginAdmin()) header('Location: /?erro=4');
            else {
                $conexao = new GlobalConnection();
                $subcategoriaService = new CategoriaService($conexao, new Categoria());
                $listaCategorias = $subcategoriaService->getCategorias();
                $subcategoriaService = new SubcategoriaService($conexao, new Subcategoria());
                $listaSubcategorias = $subcategoriaService->show();
                include __DIR__ .'/../views/layouts/editar_cat_sub.phtml';
            }
        }

        public function login() {
            session_start();
            if(isset($_SESSION['id'])) header('Location: /cliente/painel');
            else include __DIR__ .'/../views/layouts/login.phtml';
        }

        public function validarLogin() {
            if(empty($_POST)) header('Location: /login?erro=3');
            else {
                $cliente = new Cliente();
                $cliente->setEmail($_POST['email']);
                $cliente->setSenha($_POST['senha']);
                $conexao = new GlobalConnection();
                $verificacaoLogin = new ClienteService($conexao, $cliente);
                $verificacaoLogin = $verificacaoLogin->getCliente();
                if(!$verificacaoLogin) {
                    session_start();
                    session_destroy();
                    header('Location: /login?erro=2');
                }
            }
        }

        public function criarLogin() {
            include __DIR__ .'/../views/layouts/cadastro.phtml';
        }
        
        public function enviarLogin() {
            if(empty($_POST['nome']) || 
                empty($_POST['email']) || 
                empty($_POST['senha'])) {
                    header('Location: /login/criar-login?erro=1');
                }
            else {
                require_once './../helper/funcoes_adicionais.php';
                $senhaEncriptada = encriptarSenha($_POST['senha']);
                $cliente = new Cliente();
                $cliente->setNome($_POST['nome']);
                $cliente->setEmail($_POST['email']);
                $cliente->setSenha($senhaEncriptada);
                $conexao = new GlobalConnection();
                $clienteService = new ClienteService($conexao, $cliente);
                $resultado = $clienteService->salvarCliente();
                if($resultado) header('Location: /login?msg=1');
                else header('Location: /login/criar-login?err=2');
            }
        }

        public function validarLoginModal() {
            $cliente = new Cliente();
            $cliente->setEmail($_POST['email']);
            $cliente->setSenha($_POST['senha']);
            $conexao = new GlobalConnection();
            $verificacaoLogin = new ClienteService($conexao, $cliente);
            $verificacaoLogin = $verificacaoLogin->getCliente();
            if(!$verificacaoLogin) {
                session_start();
                session_destroy();
                echo 'erro';
            }
            else  echo 'OK';
        }

        public function logoff() {
            session_start();
            session_destroy();
            header('Location: /');
        }

        public function painel() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $cliente = new Cliente();
                $cliente->setNome($_SESSION['nome']);
                $cliente->setId($_SESSION['id']);
                $cliente->setEmail($_SESSION['email']);
                $conexao = new GlobalConnection();
                $getProdutos = new ClienteService($conexao, $cliente);
                $getProdutos->getClienteModal();
            }
        }

        public function adicionarProduto() {
            $conexao = new GlobalConnection();
            require '../classes_aux/ItensCarrinhoAux.php';
            $itens_carrinho = new ItensCarrinhoAux($conexao);
            $resultado = $itens_carrinho->adicionarProduto();
            echo $resultado;
        }

        public function remover() {
            $conexao = new GlobalConnection();
            require '../classes_aux/ItensCarrinhoAux.php';
            $itens_carrinho = new ItensCarrinhoAux($conexao);
            $resultado = $itens_carrinho->removerProdutoCarrinho($_POST['id']);
            echo $resultado;
        }

        public function gerarPedido() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else if(array_keys($_POST) == null && isset($_SESSION)) {
                header('Location: /cliente/painel?erro=3');
            }
            else {
                $conexao = new GlobalConnection();
                $clienteService = new ClienteService($conexao, new Cliente());
                $clienteService->getClienteByID($_SESSION['id']);
                $clienteAsaasService = new ClienteAsaasService($conexao, new ClienteAsaas());
                $clienteAsaas = $clienteAsaasService->getClienteAsaas();
                $enderecoService = new EnderecoService($conexao, new Endereco());
                $listaEnderecosCliente = $enderecoService->getEnderecoByCliente2($clienteService->__get('cliente'));
                foreach($listaEnderecosCliente as $indice => $valor) {
                    $id_cidade = $listaEnderecosCliente[$indice]['endereco']->__get('id_cidade');
                    $cidade = $enderecoService->getCidadeByID_2($id_cidade);
                    $listaEnderecosCliente[$indice]['endereco']->__set('cidade', $cidade);
                }
                require '../classes_aux/ItensCarrinhoAux.php';
                $itens_carrinho = new ItensCarrinhoAux($conexao);
                $listaProdutosCarrinho = $itens_carrinho->getProdutosCarrinhoCliente($_SESSION['id_carrinho']);
                $pedidoService = new PedidoService($conexao, new Pedido($_POST['valor_total'], $listaProdutosCarrinho));
                include __DIR__ .'/../views/painel/checkout.phtml';
            }
        }

        public function getListMarcas() {
            $conexao = new GlobalConnection();
            $marcaService = new MarcaService($conexao, new Marca());
            $lista = $marcaService->show();
            $lista = json_encode($lista);
            echo $lista;
        }

        public function setMarca() {
            $conexao = new GlobalConnection();
            $marca = new Marca();
            $marca->__set('nome', $_POST['marca']);
            $marcaService = new MarcaService($conexao, $marca);
            $resultado = $marcaService->setMarcaBanco();
            echo $resultado;
        }

        public function listaEnderecos() {
            require_once './../helper/funcoes_adicionais.php';
            if(testarSessao()) {
                $conexao = new GlobalConnection();
                $cliente = new Cliente();
                $cliente->setId($_SESSION['id']);
                $endereco = new Endereco();
                $endereco->__set('cliente', $cliente);
                $enderecoService = new EnderecoService($conexao, $endereco);
                $listaEnderecos = $enderecoService->getListaEnderecos();
                if(!$listaEnderecos) $listaEnderecos = 0;
                include __DIR__ .'/../views/painel/lista_enderecos.phtml';
            }
            else header('Location: /?erro=3');
        }

        public function adicionarEndereco() {
            require_once './../helper/funcoes_adicionais.php';
            if(testarSessao()) include __DIR__ .'/../views/painel/adicionar_endereco.phtml';
            else header('Location: /?erro=3');
        }

        public function criarEnderecoBanco() {
            if(empty($_POST['numero']) ||
                empty($_POST['cep']) ||
                empty($_POST['logradouro']) ||
                empty($_POST['bairro']) ||
                empty($_POST['cidade']) ||
                empty($_POST['uf'])) {
                    header(('Location: /cliente/painel/add-endereco?erro=1'));
            }
            else {
                session_start();
                $cliente = new Cliente();
                $cliente->setId($_SESSION['id']);
                $cidade = new Cidade($_POST['cidade'], $_POST['uf']);
                $endereco = new Endereco();
                $endereco->__set('logradouro', $_POST['logradouro']);
                $endereco->__set('numero', $_POST['numero']);
                $endereco->__set('complemento', $_POST['complemento']);
                $endereco->__set('bairro', $_POST['bairro']);
                $endereco->__set('cep', $_POST['cep']);
                $endereco->__set('cliente', $cliente);
                $endereco->__set('cidade', $cidade);
                $conexao = new GlobalConnection();
                $enderecoService = new EnderecoService($conexao, $endereco);
                $resultado = $enderecoService->criarEndereco();
                if($resultado) header('Location: /cliente/painel/add-endereco?msg=1');
                else header('Location: /cliente/painel/add-endereco?msg=0');
            }
        }

        public function dadosPessoais() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $cliente = new Cliente();
                $cliente->setId($_SESSION['id']);
                $conexao = new GlobalConnection();
                $clienteService = new ClienteService($conexao, $cliente);
                $dados = $clienteService->getClienteByID($_SESSION['id']);
                if($dados) {
                    $clienteService = new ClienteAsaasService($conexao, new ClienteAsaas());
                    $dadosClienteAsaas = $clienteService->getClienteAsaas();
                }
                $dadosSerializados = json_encode($dadosClienteAsaas);
                $_SESSION['dados_pessoais'] = $dadosSerializados;
                include __DIR__ .'/../views/painel/dados_pessoais.phtml';
            }
        }

        public function editarDadosPessoais() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $cliente = new Cliente();
                $cliente->setId($_SESSION['id']);
                $conexao = new GlobalConnection();
                $clienteService = new ClienteService($conexao, $cliente);
                $dados = $clienteService->getClienteByID($_SESSION['id']);
                if($dados) {
                    $clienteService = new ClienteAsaasService($conexao, new ClienteAsaas());
                    $dadosClienteAsaas = $clienteService->getClienteAsaas();
                }
                include __DIR__ .'/../views/painel/editar_dados_pessoais.phtml';
            }
        }

        public function salvarDadosPessoaisBanco() {
            $flag = true;
            if(isset($_POST)) {
                $cliente = new Cliente();
                if(isset($_POST['senha']) && isset($_POST['senha-confirmar'])) {
                    if($_POST['senha'] != $_POST['senha-confirmar']) {
                        $flag = false;
                        echo '0';
                    }
                    else $flag = true;
                }
                if($flag) {
                    $cliente->setNome($_POST['nome']);
                    $clienteAsaas = new ClienteAsaas();
                    $clienteAsaas->__set('telefone', $_POST['fone']);
                    $clienteAsaas->__set('cpf', $_POST['cpf']);
                    $clienteAsaas->__set('cliente', $cliente);
                    $conexao = new GlobalConnection();
                    $clienteAsaasService = new ClienteAsaasService($conexao, $clienteAsaas);
                    session_start();
                    $resultadoClienteAsaas = $clienteAsaasService->atualizarDados();
                    if($resultadoClienteAsaas) {
                        $clienteService = new ClienteService($conexao, $cliente);
                        $resultadoCliente = $clienteService->atualizarDados();
                        echo $resultadoCliente;
                    }
                }
            }
        }

        public function editarDadosAdmin() {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(!testarLoginAdmin()) header('Location: /?erro=4');
            else {
                $conexao = new GlobalConnection();
                $clienteService = new ClienteService($conexao, new Cliente());
                $dados = $clienteService->getUserAdmin();
                include __DIR__ .'/../views/painel/editar_dados_pessoais_admin.phtml';
            }
        }

        public function salvarDadosPessoaisAdminBanco() {
            $flag = true;
            if(isset($_POST)) {
                $cliente = new Cliente();
                if(isset($_POST['senha-alterar-admin']) && isset($_POST['senha-confirmar-admin'])) {
                    if($_POST['senha-alterar-admin'] != $_POST['senha-confirmar-admin']) {
                        $flag = false;
                        echo '0';
                    }
                    else $flag = true;
                }
                if($flag) {
                    $cliente->setNome($_POST['nome']);
                    $conexao = new GlobalConnection();
                    $clienteService = new ClienteService($conexao, $cliente);
                    $resultadoCliente = $clienteService->atualizarDadosAdmin();
                    echo $resultadoCliente;
                }
            }
        }

        public function pedidos() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $conexao = new GlobalConnection();
                $pedidoService = new PedidoService($conexao, new Pedido(0, []));
                $listaPedidos = $pedidoService->getPedidos();
                include '../views/painel/pedidos.phtml';
            }
        }

        public function getPedido() {
            session_start();
            if(empty($_SESSION)) {
                header('Location: /?erro=3');
            }
            else {
                $id_pedido = $_GET['id_pedido'];
                $conexao = new GlobalConnection();
                $pedidoService = new PedidoService($conexao, new Pedido(0, []));
                $resultado = $pedidoService->getPedidoPorID($id_pedido);
                include '../views/painel/pedido.phtml';
            }
        }

        public function getPedidosAdm() {
            $conexao = new GlobalConnection();
            $pedidoService = new PedidoService($conexao, new Pedido(0, []));
            $listaPedidosPorData = $pedidoService->getPedidosPorDiaMesAno();
            if($listaPedidosPorData) echo json_encode($listaPedidosPorData);
        }

        public function getPedidoAdm() {
            session_start();
            if($_SESSION['email'] != 'admin@email.com' && $_SESSION['id'] != 1) header('Location: /?erro=4');
            else {
                $conexao = new GlobalConnection();
                $pedidoService = new PedidoService($conexao, new Pedido(0, []));
                $resultado = $pedidoService->getPedidoPorID($_GET['id']);
                include '../views/painel/pedido_adm.phtml';
            }
        }

        public function alterarStatusPgtoPedido() {
            $statusEntrega = $_POST['alterar-status-entrega'];
            $status = '';
            $conexao = new GlobalConnection();
            $pedidoService = new PedidoService($conexao, new Pedido(0, []));
            switch($statusEntrega) {
                case 1: $status = 'PENDENTE';
                        break;
                case 2: $status = 'EM TRÂNSITO';
                        break;
                case 3: $status = 'ENVIADO';
                        break;
                case 4: $status = 'ENTREGUE';
                        break;
                case 0: $status = 'CANCELADO';
                        break;
                default: $status = 'INVÁLIDO';
            }
            $resultado = $pedidoService->atualizarStatusPedidoEntrega($status, $_POST['id_pedido']);
            echo $resultado;
        }

    }