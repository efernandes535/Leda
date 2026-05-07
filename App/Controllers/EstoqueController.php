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
            $id = $_POST['id'] ?? null;
            $sku = trim($_POST['sku'] ?? '');

            // Verificar se SKU já existe (evitar duplicidade)
            if (!empty($sku) && $this->produtoModel->skuExists($sku, $id)) {
                $_SESSION['error'] = "Já existe um produto cadastrado com a Referência/SKU: {$sku}";
                if ($id) {
                    $this->redirect("/estoque/editar/{$id}");
                } else {
                    $this->redirect('/estoque/novo');
                }
                return;
            }

            $data = [
                'categoria_id' => $_POST['categoria_id'] ?: null,
                'sku' => $sku,
                'nome' => trim($_POST['nome']),
                'descricao' => trim($_POST['descricao']),
                'preco_compra' => $_POST['preco_compra'],
                'preco_venda' => $_POST['preco_venda'],
                'quantidade' => $_POST['quantidade'],
                'estoque_minimo' => $_POST['estoque_minimo'],
                'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
            ];

            if ($id && !empty($id)) {
                if ($this->produtoModel->update($id, $data)) {
                    $_SESSION['success'] = "Produto atualizado com sucesso!";
                    $this->redirect('/estoque');
                }
            } else {
                if ($this->produtoModel->create($data)) {
                    $_SESSION['success'] = "Produto cadastrado com sucesso!";
                    $this->redirect('/estoque');
                }
            }
        }
    }

    public function excluir($id) {
        if ($this->produtoModel->inativar($id)) {
            $this->redirect('/estoque');
        }
    }
}
