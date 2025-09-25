<?php
    function testarLogin() {
        if(!isset($_SESSION)) {
            header('Location: /');
        }
    }

    function testarLoginAdmin(): bool {
        if($_SESSION['id'] == 1) return true;
        else return false;
    }

    function testarSessao() {
        session_start();
        if(empty($_SESSION)) {
            return false;
        }
        else return true;
    }

    function testarGET() {
        if(isset($_GET['erro']) == 3) return 'ATENÇÃO! Operação não permitida!';
    }

    function gerarNomesRandomicos() {
        $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $tamanho = 12;
        $letra = '';
        $resultado = '';
        for($i = 1; $i <= $tamanho; $i++) {
            $letra = substr($alfabeto, rand(0, strlen($alfabeto)-1), 1);
            $resultado .= $letra;
        }
        $agora = getdate();
        $codigo_ano = $agora['year'] .'_'. $agora['yday'];
        $codigo_data = $agora['hours'] . $agora['minutes'] . $agora['seconds'];
        $resultado .= '_' . $codigo_ano .'_'. $codigo_data;
        return $resultado;
    }

    function encriptarSenha($senha) {
        $senhaEncriptada = password_hash($senha, PASSWORD_BCRYPT);
        return $senhaEncriptada;
    }

    function desencriptarSenha($senha, $senhaBanco) {
        $senhaVerificada = password_verify($senha, $senhaBanco);
        return $senhaVerificada;
    }

    function aplicarMascara($val, $mask) {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i])) $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
    