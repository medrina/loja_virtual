<?php
    require_once '../autoload.php';
    $flag = false;
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    require_once '../config/Connection.php';
    $db = new Connection();
    require '../config/rotas.php';
    foreach($routes as $route => $action) {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>\w+)', $route);
        if (preg_match("~^$regex$~", $uri, $matches)) {
            list($controllerName, $metodo) = explode('@', $action);
            $resultado = preg_split('/(?=[A-Z])/', $controllerName);
            if($resultado[1] != 'Home') {
                $model = $resultado[1];
                $model = new $model();
                $resultado = $resultado[1].'Service';
                $service = new $resultado($db, $model);
                $controlador = new $controllerName($service);
                if(isset($matches[1])) {
                    $controlador->$metodo($matches[1]);
                }
                else {
                    $controlador->$metodo();
                }
            }
            else {
                $controlador = new $controllerName();
                $controlador->$metodo();
            }
            $flag = true;
            break;
        }
    }
    if(!$flag) echo '<b>Rota Inválida!</b>';