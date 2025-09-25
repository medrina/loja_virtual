<?php
    class CategoriaController {
        private $categoriaService;

        public function __construct(CategoriaService $categoriaService) {
            $this->categoriaService = $categoriaService;
        }

        public function show($id) {
            $conexao = new Connection();
            $categoria = new Categoria();
            $sub = new CategoriaService($conexao, $categoria);
            $sub = $sub->getCategoriaPorID($id);
            $sub = json_encode($sub);
            echo $sub;
        }

        public function salvarCategoria() {
            $nomeCategoria = ucwords(strtolower($_POST['categoria']));
            $categoria = new Categoria();
            $categoria->__set('nome', $nomeCategoria);
            $conexao = new Connection();
            $categoriaService = new CategoriaService($conexao, $categoria);
            $resultado = $categoriaService->salvarCategoria();
            if($resultado) echo $resultado;
            else echo $resultado;
        }

        public function getListCategorias() {
            $conexao = new Connection();
            $cat = new CategoriaService($conexao, new Categoria);
            $cat = $cat->getCategorias();
            $listaCategorias = array();
            foreach($cat as $indice => $valor) {
                $listaCategorias[$indice] = [
                    'id' => $cat[$indice]['id'],
                    'nome' => $cat[$indice]['nome']
                ];
            }
            $texto = json_encode($listaCategorias);
            echo $texto;
        }

        public function atualizarCategoriaBanco() {
            $resultado = $this->categoriaService->atualizarCategoria();
            echo $resultado;
        }
    }