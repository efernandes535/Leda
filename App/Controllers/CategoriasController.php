<?php

namespace App\Controllers;

use App\Models\Categoria;

class CategoriasController extends Controller {
    private $categoriaModel;

    public function __construct() {
        parent::__construct();
        $this->categoriaModel = new Categoria();
    }

    public function index() {
        $categorias = $this->categoriaModel->all();
        $this->view('categorias/index', [
            'title' => 'Gerenciar Categorias',
            'categorias' => $categorias
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            if ($this->categoriaModel->create($nome)) {
                $this->redirect('/categorias');
            }
        }
    }

    public function excluir($id) {
        if ($this->categoriaModel->delete($id)) {
            $this->redirect('/categorias');
        }
    }
}
