<?php

namespace App\Controllers;

use App\Models\Usuario;

class AuthController extends Controller {
    private $usuarioModel;

    public function __construct() {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/home');
        }
        $this->view('auth/login', ['title' => 'Login - Leda']);
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['usuario'];
            $pass = $_POST['senha'];

            $usuario = $this->usuarioModel->findByUsuario($user);

            if ($usuario && password_verify($pass, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $this->redirect('/home');
            } else {
                $_SESSION['error'] = "Usuário ou senha inválidos.";
                $this->redirect('/login');
            }
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
