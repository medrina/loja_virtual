<?php
    class CategoriaSubcategoriaAux {
        private $categoria;
        private $subcategoria;

        public function __construct(Categoria $categoria, Subcategoria $subcategoria) {
            $this->categoria = $categoria;
            $this->subcategoria = $subcategoria;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }