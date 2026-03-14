<?php

namespace App\Controllers;

abstract class Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function view($view, $data = []) {
        extract($data);
        $viewFile = "../App/Views/$view.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $view não encontrada.");
        }
    }

    protected function redirect($url) {
        header("Location: " . URL_BASE . $url);
        exit;
    }
}
