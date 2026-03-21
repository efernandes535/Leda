<?php

namespace App\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Cliente;

class VendasController extends Controller {
    private $vendaModel;
    private $produtoModel;
    private $clienteModel;

    public function __construct() {
        parent::__construct();
        $this->vendaModel = new Venda();
        $this->produtoModel = new Produto();
        $this->clienteModel = new Cliente();
    }

    public function index() {
        $vendas = $this->vendaModel->getFullVendas();
        $this->view('vendas/index', [
            'title' => 'Gestão de Vendas',
            'vendas' => $vendas
        ]);
    }

    public function novo() {
        $produtos = $this->produtoModel->all();
        $clientes = $this->clienteModel->all();
        $this->view('vendas/form', [
            'title' => 'Nova Venda',
            'produtos' => $produtos,
            'clientes' => $clientes
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cliente_id = $_POST['cliente_id'] ?: null;
            $produtos_ids = $_POST['produto_id'];
            $quantidades = $_POST['quantidade'];
            $precos = $_POST['preco_unitario'];
            
            $itens = [];
            $total = 0;

            for ($i = 0; $i < count($produtos_ids); $i++) {
                if (!empty($produtos_ids[$i])) {
                    $itens[] = [
                        'produto_id' => $produtos_ids[$i],
                        'quantidade' => $quantidades[$i],
                        'preco_unitario' => $precos[$i]
                    ];
                    $total += $quantidades[$i] * $precos[$i];
                }
            }

            $forma_pagamento = $_POST['forma_pagamento'] ?? 'avista';
            $status_pagamento = $_POST['status_pagamento'] ?? 'pago';
            $numero_parcelas = $_POST['numero_parcelas'] ?? 1;

            try {
                if ($this->vendaModel->create($cliente_id, $total, $itens, $forma_pagamento, $status_pagamento, $numero_parcelas)) {
                    $_SESSION['success'] = "Venda finalizada com sucesso!";
                    $this->redirect('/vendas');
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/vendas/novo');
            }
        }
    }

    public function detalhes($id) {
        $venda = $this->vendaModel->find($id);
        $itens = $this->vendaModel->getItens($id);
        $parcelas = $this->vendaModel->getParcelas($id);
        
        $this->view('vendas/detalhes', [
            'title' => 'Detalhes da Venda #' . $id,
            'venda' => $venda,
            'itens' => $itens,
            'parcelas' => $parcelas
        ]);
    }

    public function excluir($id) {
        $this->vendaModel->delete($id);
        $this->redirect('/vendas');
    }

    public function pagarParcela($id) {
        try {
            if ($this->vendaModel->pagarParcela($id)) {
                $_SESSION['success'] = "Pagamento da parcela registrado com sucesso!";
            } else {
                $_SESSION['error'] = "Esta parcela já foi paga ou não foi encontrada.";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erro: " . $e->getMessage();
        }
        
        // Redirecionar de volta para os detalhes da venda
        $venda_id = $this->vendaModel->getVendaIdByParcela($id);
        
        if ($venda_id) {
            $this->redirect('/vendas/detalhes/' . $venda_id);
        } else {
            $this->redirect('/vendas');
        }
    }
    public function recibo($id) {
        $venda = $this->vendaModel->findWithCliente($id);
        $itens = $this->vendaModel->getItens($id);
        $parcelas = $this->vendaModel->getParcelas($id);
        
        $this->view('vendas/recibo', [
            'venda' => $venda,
            'itens' => $itens,
            'parcelas' => $parcelas
        ]);
    }
}
