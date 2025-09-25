<?php
    class MarcaController {
        private $marcaService;

        public function __construct(MarcaService $marcaService)
        {
            $this->marcaService = $marcaService;
        }

        public function getMarcasHome() {
            $conexao = new Connection();
            $marcaService = new MarcaService($conexao, new Marca());
            $resultado = $marcaService->getMarcasPorSubcategoria();
            echo json_encode($resultado);
        }
    }