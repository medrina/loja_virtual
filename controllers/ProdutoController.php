<?php

use Connection as GlobalConnection;

    class ProdutoController {
        private $produtoService;

        public function __construct(ProdutoService $produtoService) {
            $this->produtoService = $produtoService;
        }

        public function show($id) {
            $produto = $this->produtoService->getProduto($id);
            include __DIR__ . '/../views/layouts/produto.phtml';
        }

        public function cadastrarProduto() {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(!testarLoginAdmin()) header('Location: /?erro=4');
            else {
                include __DIR__ . '/../views/layouts/produto-cadastrar.phtml';
            }
        }

        public function buscarProduto() {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(!testarLoginAdmin()) header('Location: /?erro=4');
            else {
                $conexao = new GlobalConnection();
                $categoriaService = new CategoriaService($conexao, new Categoria());
                $cat = $categoriaService->getCategorias();
                include __DIR__ . '/../views/layouts/produto-buscar.phtml';
            }
        }

        public function editarProduto($id) {
            require_once './../helper/funcoes_adicionais.php';
            session_start();
            if(!testarLoginAdmin()) header('Location: /?erro=4');
            else {
                $produto = $this->produtoService->getProduto($id);
                include __DIR__ . '/../views/layouts/produto-editar.phtml';
            }
        }

        public function atualizarProduto() {            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $produto = new Produto();
                $produto->__set('id', $_POST['id_produto']);
                $produto->__set('nome', $_POST['produto_nome']);
                $produto->__set('descricao', $_POST['produto_descricao']);
                $produto->__set('cor', $_POST['produto_cor']);
                $produto->__set('valor', $_POST['produto_valor']);
                $produto->__set('nro_parcelas', $_POST['produto_nro_parcelas']);
                $produto->__set('valor_parcela', $_POST['produto_valor_parcela']);
                $produto->__set('altura', $_POST['produto_altura_valor'] * 100);
                $produto->__set('largura', $_POST['produto_largura_valor'] * 100);
                $produto->__set('comprimento', $_POST['produto_comprimento_valor'] * 100);
                $produto->__set('peso', $_POST['produto_peso_valor']);
                $conexao = new GlobalConnection();
                $produtoService = new ProdutoService($conexao, $produto);
                $resultado = $produtoService->atualizarProdutoBanco();
                echo $resultado;
            }
        }

        public function alterarImagem() {
            $array = array();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
                    $tmp = $_FILES['imagem']['tmp_name'];
                    $nomeImagem = basename($_FILES['imagem']['name']);
                    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (in_array($_FILES['imagem']['type'], $tiposPermitidos)) {
                        $imagemAtual = '../public/assets/img/';
                        $imagemAtual .= $_POST['imagem_atual'];
                        if(unlink($imagemAtual)) {
                            $resultado = $this->inserirImagem('imagem', $nomeImagem, $tmp);
                            if($resultado) {
                                $atualizacaoImagem = $this->produtoService->atualizarImagemBanco($resultado, $_POST['id_produto']);
                                if($atualizacaoImagem) { 
                                    $array = ['1' => $resultado]; // novo nome da imagem salva no banco com sucesso
                                }
                                else { 
                                    $array = ['5' => 5]; // não foi possível salvar o novo nome da imagem no banco
                                }
                            }
                        }
                        else { 
                            $array = ['4' => 4]; // não foi possível remover a imagem (imagem não se encontra mais na pasta img)
                        }
                    }
                    else { 
                        $array = ['3' => 3]; // formato inválido de arquivo (aceita somente arquivos do tipo imagem)
                    }
                }
                else { 
                    $array = ['2' => 2]; // sem imagem selecionada
                }
            }
            else { 
                $array = ['0' => 0]; // requisição HTTP desconhecida
            }
            $array = json_encode($array);
            echo $array;
        }

        public function cadastrarProdutoBanco () {            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
                    $tmp = $_FILES['imagem']['tmp_name'];
                    $nomeImagem = basename($_FILES['imagem']['name']);
                    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (in_array($_FILES['imagem']['type'], $tiposPermitidos)) {
                        require '../helper/funcoes_adicionais.php';
                        $novoNome = gerarNomesRandomicos();
                        $tipo = $_FILES['imagem']['type'];
                        $extensao = strrchr($nomeImagem, '.');
                        $novoNome = 'foto_'. $novoNome .'_'. $extensao;
                        $destino = './../public/assets/img/' . $novoNome;
                        if (move_uploaded_file($tmp, $destino)) {
                            $conexao = new GlobalConnection();
                            $produto = new Produto();
                            $produto->__set('nome', $_POST['produto_nome']);
                            $produto->__set('valor', $_POST['produto_valor']);
                            $produto->__set('altura', ($_POST['produto_altura_valor']) * 100 );
                            $produto->__set('largura', ($_POST['produto_largura_valor'] * 100));
                            $produto->__set('comprimento', ($_POST['produto_comprimento_valor'] * 100));
                            $produto->__set('peso', $_POST['produto_peso_valor']);
                            $produto->__set('descricao', empty($_POST['produto_descricao']) ? ' ' : $_POST['produto_descricao']);
                            $produto->__set('cor', empty($_POST['produto_cor']) ? ' ' : $_POST['produto_cor']);
                            $produto->__set('nro_parcelas', empty($_POST['produto_nro_parcelas']) ? 0 : $_POST['produto_nro_parcelas']);
                            $produto->__set('valor_parcela', empty($_POST['produto_valor_parcela']) ? 0 : $_POST['produto_valor_parcela']);
                            $produto->__set('imagem_path', $novoNome);
                            $produtoService = new ProdutoService($conexao, $produto);
                            $resultado = $produtoService->setProdutoBanco();
                            if($resultado) echo 1;
                            else echo 0;
                        }
                        else {
                            //echo "Erro ao salvar a imagem.";
                            echo 2;
                        }
                    }
                    else {
                        //echo "Tipo de imagem não permitido.";
                        echo 3;
                    }
                }
                else {
                    //echo "Erro ao receber a imagem.";
                    echo 4;
                }
            }
            else {
                //echo "Requisição inválida.";
                echo 5;
            }
        }

        public function alterarQuantidade() {
            $conexao = new GlobalConnection();
            require '../classes_aux/ItensCarrinhoAux.php';
            $itensCarrinho = new ItensCarrinhoAux($conexao);
            $resultado = $itensCarrinho->alterarQuantidadeProdutoCarrinho();
            $resultado = json_encode($resultado);
            echo $resultado;
        }

        private function inserirImagem($indice, $nomeImagem, $tmp) {
            require '../helper/funcoes_adicionais.php';
            $novoNome = gerarNomesRandomicos();
            $tipo = $_FILES[$indice]['type'];
            $extensao = strrchr($nomeImagem, '.');
            $novoNome = 'foto_'. $novoNome .'_'. $extensao;
            $destino = './../public/assets/img/' . $novoNome;
            if (move_uploaded_file($tmp, $destino)) return $novoNome;
            else return false;
        }

        public function getProdutosPorID_Marca() {
            $conexao = new GlobalConnection();
            $resultado = $this->produtoService->getListaProdutosPorMarca();
            echo json_encode($resultado);
        }

        public function buscarCoresPorSubcategoria() {
            $conexao = new GlobalConnection();
            $resultado = $this->produtoService->getProdutosPorSubcatCores();
            echo json_encode($resultado);
        }

        public function maiorMenorPreco() {
            $consulta = 0;
            switch($_GET['opcao']) {
                case 1: $consulta = $this->produtoService->maiorMenorPreco('ASC');
                        break;
                case 2: $consulta = $this->produtoService->maiorMenorPreco('DESC');
                        break;
            }
            echo json_encode($consulta);
        }
        
    }
