<?php

namespace App\Controllers;

use App\Models\Produto;
use App\Models\Categoria;

class EstoqueController extends Controller {
    private $produtoModel;
    private $categoriaModel;

    public function __construct() {
        parent::__construct();
        $this->produtoModel = new Produto();
        $this->categoriaModel = new Categoria();
    }

    public function index() {
        $produtos = $this->produtoModel->getWithCategoria();
        $this->view('estoque/index', [
            'title' => 'Gerenciar Estoque',
            'produtos' => $produtos
        ]);
    }

    public function novo() {
        $categorias = $this->categoriaModel->all();
        $this->view('estoque/form', [
            'title' => 'Novo Produto',
            'categorias' => $categorias
        ]);
    }

    public function editar($id) {
        $produto = $this->produtoModel->find($id);
        $categorias = $this->categoriaModel->all();
        $this->view('estoque/form', [
            'title' => 'Editar Produto',
            'produto' => $produto,
            'categorias' => $categorias
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'categoria_id' => $_POST['categoria_id'] ?: null,
                'nome' => $_POST['nome'],
                'descricao' => $_POST['descricao'],
                'preco_compra' => $_POST['preco_compra'],
                'preco_venda' => $_POST['preco_venda'],
                'quantidade' => $_POST['quantidade'],
                'estoque_minimo' => $_POST['estoque_minimo']
            ];

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                if ($this->produtoModel->update($_POST['id'], $data)) {
                    $this->redirect('/estoque');
                }
            } else {
                if ($this->produtoModel->create($data)) {
                    $this->redirect('/estoque');
                }
            }
        }
    }

    public function excluir($id) {
        if ($this->produtoModel->delete($id)) {
            $this->redirect('/estoque');
        }
    }
}
