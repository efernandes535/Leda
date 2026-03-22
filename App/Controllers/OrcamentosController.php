<?php

namespace App\Controllers;

use App\Models\Orcamento;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Venda;

class OrcamentosController extends Controller {
    private $orcamentoModel;
    private $produtoModel;
    private $clienteModel;

    public function __construct() {
        parent::__construct();
        $this->orcamentoModel = new Orcamento();
        $this->produtoModel = new Produto();
        $this->clienteModel = new Cliente();
    }

    public function index() {
        $orcamentos = $this->orcamentoModel->getFullOrcamentos();
        $this->view('orcamentos/index', [
            'title' => 'Gestão de Orçamentos',
            'orcamentos' => $orcamentos
        ]);
    }

    public function novo() {
        $produtos = $this->produtoModel->getWithCategoria(true);
        foreach ($produtos as &$p) {
            $p['lotes'] = $this->produtoModel->getLotesDisponiveis($p['id']);
        }
        $clientes = $this->clienteModel->all();
        $this->view('orcamentos/form', [
            'title' => 'Novo Orçamento',
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

            if ($this->orcamentoModel->create($cliente_id, $total, $itens, $forma_pagamento, $status_pagamento, $numero_parcelas)) {
                $this->redirect('/orcamentos');
            }
        }
    }

    public function detalhes($id) {
        $orcamento = $this->orcamentoModel->find($id);
        $itens = $this->orcamentoModel->getItens($id);
        
        $this->view('orcamentos/detalhes', [
            'title' => 'Detalhes do Orçamento #' . $id,
            'orcamento' => $orcamento,
            'itens' => $itens
        ]);
    }

    public function aprovar($id) {
        $orcamento = $this->orcamentoModel->find($id);
        if ($orcamento && $orcamento['status'] === 'pendente') {
            // Em vez de criar a venda silenciosamente, redirecionamos para o form de venda
            // pré-carregado com os dados do orçamento para que o usuário possa escolher os lotes.
            $this->redirect('/vendas/novo/' . $id);
        } else {
            $_SESSION['error'] = "Orçamento não encontrado ou já processado.";
            $this->redirect('/orcamentos');
        }
    }

    public function excluir($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->orcamentoModel->delete($id)) {
                $_SESSION['success'] = "Orçamento excluído com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao excluir o orçamento.";
            }
        }
        $this->redirect('/orcamentos');
    }

    public function recibo($id) {
        $orcamento = $this->orcamentoModel->findWithCliente($id);
        $itens = $this->orcamentoModel->getItens($id);
        
        $this->view('orcamentos/recibo', [
            'orcamento' => $orcamento,
            'itens' => $itens
        ]);
    }
}
