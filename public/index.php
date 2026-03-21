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

// Capturar a URL de forma robusta
$routeParam = $_GET['url'] ?? '';

if (empty($routeParam)) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = $_SERVER['SCRIPT_NAME']; // Ex: /Leda/public/index.php
    $scriptDir = str_replace('\\', '/', dirname($scriptName)); // Ex: /Leda/public
    
    // O base path é o diretório do projeto, removendo /public se houver
    $basePath = str_replace('/public', '', $scriptDir);
    
    // Tentar remover o basePath do início da URI
    if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
    
    // Remover o /public do início se ele ainda estiver lá (caso de acesso direto à pasta public)
    if (strpos($requestUri, '/public') === 0) {
        $requestUri = substr($requestUri, 7);
    }
    
    $routeParam = $requestUri;
}

// Limpar e quebrar a URL
$url = explode('/', filter_var(trim($routeParam, '/'), FILTER_SANITIZE_URL));

// SE o primeiro elemento ainda for 'public' (caso de .htaccess na raiz passando public no parâmetro 'url')
if (isset($url[0]) && $url[0] === 'public') {
    array_shift($url);
}

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
