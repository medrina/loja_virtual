<?php
    class Cliente {
        private int $id;
        private string $nome;
        private string $email;
        private string $senha;

        public function __construct()
        {
            $this->nome = '';
            $this->email = '';
        }

        public function setId(int $id) {
            $this->id = $id;
        }

        public function setNome($nome) {
            $this->nome = $nome;
        }
        
        public function setEmail($email) {
            $this->email = $email;
        }

        public function setSenha($senha) {
            $this->senha = $senha;
        }

        public function getID() {
            return $this->id;
        }
        public function getNome() {
            return $this->nome;
        }

        public function getEmail() {
            return $this->email;
        }

        protected function getSenha() {
            return $this->senha;
        }

    }