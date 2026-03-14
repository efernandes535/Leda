<?php

require_once '../config/config.php';
require_once '../App/Core/Database.php';

// Autoload simples para as classes
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = '../' . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Capturar a URL e processar a rota
$url = $_GET['url'] ?? 'home';
$url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerName = 'App\\Controllers\\' . ucfirst($url[0]) . 'Controller';
$methodName = $url[1] ?? 'index';

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        $params = array_slice($url, 2);
        call_user_func_array([$controller, $methodName], $params);
    } else {
        echo "Método $methodName não encontrado no controller $controllerName";
    }
} else {
    echo "Controller $controllerName não encontrado";
}
