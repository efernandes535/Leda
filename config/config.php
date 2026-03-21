<?php

// Definir a URL base de forma inteligente, lidando com subdiretórios e diferentes servidores (Apache, PHP -S)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$urlBase = $protocol . '://' . $host . rtrim($scriptDir, '/');
// Se o script estiver em /public, extraímos o caminho para ter a base correta do site
if (substr($urlBase, -7) === '/public') {
    $urlBase = substr($urlBase, 0, -7);
}
define('URL_BASE', $urlBase);

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_estoque');
define('DB_USER', 'caverna');
define('DB_PASS', 'dsiders');

// Outras configurações
define('APP_NAME', 'Sistema Leda - Gestão de Estoque');
