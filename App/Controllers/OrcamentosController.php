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
        $produtos = $this->produtoModel->all();
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

            if ($this->orcamentoModel->create($cliente_id, $total, $itens)) {
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
            $itens = $this->orcamentoModel->getItens($id);
            
            $vendaModel = new Venda();
            try {
                $vendaModel->create($orcamento['cliente_id'], $orcamento['total'], $itens);
                $this->orcamentoModel->updateStatus($id, 'aprovado');
                $_SESSION['success'] = "Orçamento aprovado e venda gerada!";
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erro ao aprovar: " . $e->getMessage();
            }
        }
        $this->redirect('/orcamentos');
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
}
