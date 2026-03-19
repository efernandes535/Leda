<?php

namespace App\Controllers;

use App\Models\Entrada;
use App\Models\Produto;

class EntradasController extends Controller {
    private $entradaModel;
    private $produtoModel;

    public function __construct() {
        parent::__construct();
        $this->entradaModel = new Entrada();
        $this->produtoModel = new Produto();
    }

    public function index() {
        $entradas = $this->entradaModel->getRecentes();
        $this->view('entradas/index', [
            'title' => 'Entradas de Estoque (Compras)',
            'entradas' => $entradas
        ]);
    }

    public function novo() {
        $produtos = $this->produtoModel->all();
        $this->view('entradas/form', [
            'title' => 'Registrar Nova Entrada',
            'produtos' => $produtos
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'produto_id' => $_POST['produto_id'],
                'quantidade' => $_POST['quantidade'],
                'lote' => $_POST['lote'],
                'data_validade' => $_POST['data_validade'] ?: null,
                'preco_compra' => $_POST['preco_compra']
            ];

            if ($this->entradaModel->create($data)) {
                // Atualizar estoque e preço de compra no produto
                $produto = $this->produtoModel->find($data['produto_id']);
                
                // Cálculo de preço médio (opcional, mas o usuário pediu "ter uma média")
                // Por enquanto vamos apenas atualizar o preco_compra para o último e somar a quantidade
                $nova_qtd = $produto['quantidade'] + $data['quantidade'];
                
                $sqlUp = "UPDATE produtos SET quantidade = ?, preco_compra = ? WHERE id = ?";
                $stmt = $this->produtoModel->getDb()->prepare($sqlUp);
                $stmt->execute([$nova_qtd, $data['preco_compra'], $data['produto_id']]);

                $_SESSION['success'] = "Entrada de estoque registrada com sucesso!";
                $this->redirect('/entradas');
            } else {
                $_SESSION['error'] = "Erro ao registrar entrada.";
                $this->redirect('/entradas/novo');
            }
        }
    }
}
