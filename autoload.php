<?php
function meuAutoload($classe) {
    $caminho = '../models/' . str_replace('\\', '/', $classe) . '.php';
    if (file_exists($caminho)) {
        require_once $caminho;
    }
    $caminho = '../controllers/' . str_replace('\\', '/', $classe) . '.php';
    if (file_exists($caminho)) {
        require_once $caminho;
    }
    $caminho = '../services/' . str_replace('\\', '/', $classe) . '.php';
    if (file_exists($caminho)) {
        require_once $caminho;
    }
}

spl_autoload_register('meuAutoload');
