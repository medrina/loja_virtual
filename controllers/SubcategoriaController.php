<?php
    class SubcategoriaController {
        private $subcategoriaService;

        public function __construct(SubcategoriaService $subcategoriaService) {
            $this->subcategoriaService = $subcategoriaService;
        }

        public function index() {
            $subcategorias = $this->subcategoriaService->show();          
            $lista = array();
            foreach($subcategorias as $indice => $valor) {
                $lista[$indice] = [
                    'id' => $subcategorias[$indice]['id'],
                    'nome' => $subcategorias[$indice]['nome']
                ];
            }
            $lista = json_encode($lista);
            echo $lista;
        }

        public function show($id) {
            $conexao = new Connection();
            $produtos = new SubcategoriaService($conexao, new Subcategoria());
            $prod = $produtos->getProdutos($id);
            $prod = json_encode($prod);
            echo $prod;
        }

        public function showAdmin($id) {
            $conexao = new Connection();
            $produtos = new SubcategoriaService($conexao, new Subcategoria());
            $prod = $produtos->getProdutosAdmin($id);
            $prod = json_encode($prod);
            echo $prod;
        }

        public function salvarSubCategoria() {
            $id_categoria = $_POST['categoria'];
            $nomeSubcategoria = $_POST['nome'];
            $subCategoria = new Subcategoria();
            $subCategoria->__set('nome', $nomeSubcategoria);
            $conexao = new Connection();
            $subCategoriaService = new SubcategoriaService($conexao, $subCategoria);
            $resultado = $subCategoriaService->salvarSubCategoria();
            if($resultado == 1) {
                echo $resultado;
            }
            else if($resultado == 2) {
                $cat = new Categoria();
                $cat->__set('id', $id_categoria);
                $sub = new Subcategoria();
                $sub->__set('id', $_SESSION['id_subcategoria'] );
                $conexao = new Connection();
                require '../classes_aux/CategoriaSubcategoriaService.php';
                $cat_sub = new CategoriaSubcategoriaService($conexao, $cat, $sub);
                if($cat_sub->salvarCad_Sub()) echo $resultado;
                else echo 0;
            }
            else echo "else: $resultado";
        }

        public function atualizarSubCategoriaBanco() {
            $resultado = $this->subcategoriaService->atualizarSubCategoria();
            echo $resultado;
        }
    }