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

// Capturar a URL de forma robusta, suportando Apache (.htaccess) e Servidor embutido do PHP (php -S)
$routeParam = $_GET['url'] ?? '';

if (empty($routeParam)) {
    // Detectar rota a partir da REQUEST_URI se for acesso direto sem parâmetro ?url=
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $routeParam = str_replace($scriptDir, '', $requestUri);
}

$url = explode('/', filter_var(trim($routeParam, '/'), FILTER_SANITIZE_URL));
if (empty($url[0])) {
    $url[0] = 'home';
}

// Iniciar sessão para verificar login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rotas públicas
$publicRoutes = ['login', 'autenticar'];

if (!isset($_SESSION['usuario_id']) && !in_array($url[0], $publicRoutes)) {
    header("Location: " . URL_BASE . "/login");
    exit;
}

// Mapa de controladores especiais
if ($url[0] === 'login') {
    $controllerName = 'App\\Controllers\\AuthController';
    $methodName = 'login';
} elseif ($url[0] === 'autenticar') {
    $controllerName = 'App\\Controllers\\AuthController';
    $methodName = 'autenticar';
} elseif ($url[0] === 'logout') {
    $controllerName = 'App\\Controllers\\AuthController';
    $methodName = 'logout';
} else {
    $controllerName = 'App\\Controllers\\' . ucfirst($url[0]) . 'Controller';
    $methodName = $url[1] ?? 'index';
}

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
