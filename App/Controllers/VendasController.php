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

    public function novo($id = null) {
        $produtos = $this->produtoModel->getWithCategoria(true);
        foreach ($produtos as &$p) {
            $p['lotes'] = $this->produtoModel->getLotesDisponiveis($p['id']);
        }
        $clientes = $this->clienteModel->all();

        $orcamento = null;
        $itensOrcamento = [];
        if ($id) {
            $orcamentoModel = new \App\Models\Orcamento();
            $orcamento = $orcamentoModel->find($id);
            if ($orcamento) {
                $itensOrcamento = $orcamentoModel->getItens($id);
                // Adiciona o estoque atual de cada produto para conferência
                foreach ($itensOrcamento as &$item) {
                    $prod = $this->produtoModel->find($item['produto_id']);
                    $item['estoque_atual'] = $prod['quantidade'] ?? 0;
                }
            }
        }

        $this->view('vendas/form', [
            'title' => 'Nova Venda',
            'produtos' => $produtos,
            'clientes' => $clientes,
            'orcamento' => $orcamento,
            'itensPreCarregados' => $itensOrcamento
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
                        'preco_unitario' => $precos[$i],
                        'lote' => !empty($_POST['lote'][$i]) ? $_POST['lote'][$i] : null,
                        'data_validade' => !empty($_POST['data_validade'][$i]) ? $_POST['data_validade'][$i] : null
                    ];
                    $total += $quantidades[$i] * $precos[$i];
                }
            }
            $forma_pagamento = $_POST['forma_pagamento'] ?? 'avista';
            $status_pagamento = $_POST['status_pagamento'] ?? 'pago';
            $numero_parcelas = $_POST['numero_parcelas'] ?? 1;

            try {
                if ($this->vendaModel->create($cliente_id, $total, $itens, $forma_pagamento, $status_pagamento, $numero_parcelas)) {
                    // Lógica de Saldo de Orçamento (Backorder)
                    if (!empty($_POST['orcamento_id'])) {
                        $orcamento_id = $_POST['orcamento_id'];
                        $orcamentoModel = new \App\Models\Orcamento();
                        $orcamentoOriginal = $orcamentoModel->find($orcamento_id);

                        if ($orcamentoOriginal) {
                            $itensOriginais = $orcamentoModel->getItens($orcamento_id);
                            $itensBackorder = [];
                            $totalBackorder = 0;
                            $itensOrcamentoOriginalAtualizados = [];
                            $totalOrcamentoOriginalAtualizado = 0;

                            // Agrupar quantidades vendidas por produto_id (da venda atual)
                            $qtdVendidaPorProduto = [];
                            foreach ($itens as $it) {
                                $pid = $it['produto_id'];
                                $qtdVendidaPorProduto[$pid] = ($qtdVendidaPorProduto[$pid] ?? 0) + $it['quantidade'];
                            }

                            foreach ($itensOriginais as $itOrig) {
                                $pid = $itOrig['produto_id'];
                                $qtdVendida = $qtdVendidaPorProduto[$pid] ?? 0;
                                $saldo = $itOrig['quantidade'] - $qtdVendida;

                                // 1. O que foi vendido fica no Orçamento Original
                                if ($qtdVendida > 0) {
                                    $itensOrcamentoOriginalAtualizados[] = [
                                        'produto_id' => $pid,
                                        'quantidade' => $qtdVendida,
                                        'preco_unitario' => $itOrig['preco_unitario'],
                                        'lote' => $itOrig['lote'],
                                        'data_validade' => $itOrig['data_validade']
                                    ];
                                    $totalOrcamentoOriginalAtualizado += $qtdVendida * $itOrig['preco_unitario'];
                                }

                                // 2. O que sobrou vai para o Novo Orçamento (Backorder)
                                if ($saldo > 0 && isset($_POST['gerar_backorder'])) {
                                    $itensBackorder[] = [
                                        'produto_id' => $pid,
                                        'quantidade' => $saldo,
                                        'preco_unitario' => $itOrig['preco_unitario']
                                    ];
                                    $totalBackorder += $saldo * $itOrig['preco_unitario'];
                                }
                                // Remove do mapeamento para detectar apenas o que sobrou
                                unset($qtdVendidaPorProduto[$pid]);
                            }

                            // Atualizar orçamento original com as quantidades aprovadas
                            if (!empty($itensOrcamentoOriginalAtualizados)) {
                                $orcamentoModel->update(
                                    $orcamento_id,
                                    $orcamentoOriginal['cliente_id'],
                                    $totalOrcamentoOriginalAtualizado,
                                    $itensOrcamentoOriginalAtualizados,
                                    $orcamentoOriginal['forma_pagamento'],
                                    $orcamentoOriginal['status_pagamento'],
                                    $orcamentoOriginal['numero_parcelas']
                                );
                            }

                            // Criar novo orçamento para o saldo
                            if (!empty($itensBackorder)) {
                                $orcamentoModel->create(
                                    $orcamentoOriginal['cliente_id'],
                                    $totalBackorder,
                                    $itensBackorder,
                                    $orcamentoOriginal['forma_pagamento'],
                                    $orcamentoOriginal['status_pagamento'],
                                    $orcamentoOriginal['numero_parcelas']
                                );
                                $_SESSION['success_backorder'] = "Um novo orçamento foi gerado com os itens pendentes.";
                            }
                        }

                        $orcamentoModel->updateStatus($orcamento_id, 'aprovado');
                    }

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
            $data_pagamento = $_POST['data_pagamento'] ?? date('Y-m-d');
            $valor_recebido = !empty($_POST['valor_recebido']) ? (float)str_replace(',', '.', str_replace('.', '', $_POST['valor_recebido'])) : null;

            if ($this->vendaModel->pagarParcela($id, $data_pagamento, $valor_recebido)) {
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
