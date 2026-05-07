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

    public function editar($id) {
        $entrada = $this->entradaModel->getById($id);
        if (!$entrada) {
            $_SESSION['error'] = "Entrada não encontrada.";
            $this->redirect('/entradas');
            return;
        }

        $itens = $this->entradaModel->getItens($id);
        $parcelas = $this->entradaModel->getParcelas($id);
        $produtos = $this->produtoModel->all();

        $this->view('entradas/form', [
            'title' => 'Editar Nota de Entrada',
            'entrada' => $entrada,
            'itens' => $itens,
            'parcelas' => $parcelas,
            'produtos' => $produtos
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'fornecedor' => $_POST['fornecedor'] ?? '',
                'numero_nota' => $_POST['numero_nota'] ?? '',
                'data_emissao' => $_POST['data_emissao'] ?: null,
                'forma_pagamento' => $_POST['forma_pagamento'] ?? '',
                'numero_parcelas' => isset($_POST['numero_parcelas']) ? (int)$_POST['numero_parcelas'] : 1,
                'valor_parcela' => isset($_POST['valor_parcela']) ? (float)str_replace(',', '.', $_POST['valor_parcela']) : 0.00,
                'valor_frete' => isset($_POST['valor_frete']) ? (float)str_replace(',', '.', $_POST['valor_frete']) : 0.00,
                'observacoes' => $_POST['observacoes'] ?? '',
                'valor_total' => 0,
                'itens' => [],
                'parcelas' => []
            ];

            // Capturar Parcelas Dinâmicas
            $vencimentos = $_POST['data_vencimento_parcela'] ?? [];
            $valores_parcela = $_POST['valor_parcela_item'] ?? [];
            
            for ($i = 0; $i < count($vencimentos); $i++) {
                if (!empty($vencimentos[$i]) && !empty($valores_parcela[$i])) {
                    $data['parcelas'][] = [
                        'data_vencimento' => $vencimentos[$i],
                        'valor' => (float)str_replace(',', '.', $valores_parcela[$i])
                    ];
                }
            }

            $produtos = $_POST['produto_id'] ?? [];
            $quantidades = $_POST['quantidade'] ?? [];
            $precos = $_POST['preco_compra'] ?? [];
            $lotes = $_POST['lote'] ?? [];
            $validades = $_POST['data_validade'] ?? [];

            for ($i = 0; $i < count($produtos); $i++) {
                if (!empty($produtos[$i]) && !empty($quantidades[$i]) && !empty($precos[$i])) {
                    $qtd = (int)$quantidades[$i];
                    $preco = (float)str_replace(',', '.', $precos[$i]);
                    
                    $data['itens'][] = [
                        'produto_id' => $produtos[$i],
                        'quantidade' => $qtd,
                        'preco_unitario' => $preco,
                        'lote' => $lotes[$i] ?? null,
                        'data_validade' => $validades[$i] ?: null
                    ];
                    $data['valor_total'] += ($qtd * $preco);
                }
            }

            // Soma o frete ao total da nota
            $data['valor_total'] += $data['valor_frete'];

            if (empty($data['itens'])) {
                $_SESSION['error'] = "Nenhum item válido foi adicionado à nota.";
                $this->redirect('/entradas/novo');
                return;
            }

            if ($id && !empty($id)) {
                // Reverter estoque antigo
                $old_itens = $this->entradaModel->getItens($id);
                foreach ($old_itens as $old_item) {
                    $produto = $this->produtoModel->find($old_item['produto_id']);
                    if ($produto) {
                        $nova_qtd_revertida = max(0, $produto['quantidade'] - $old_item['quantidade']);
                        $sqlRev = "UPDATE produtos SET quantidade = ? WHERE id = ?";
                        $this->produtoModel->getDb()->prepare($sqlRev)->execute([$nova_qtd_revertida, $old_item['produto_id']]);
                    }
                }

                if ($this->entradaModel->update($id, $data)) {
                    // Atualizar estoque e custo novos
                    foreach ($data['itens'] as $item) {
                        $produto = $this->produtoModel->find($item['produto_id']);
                        $nova_qtd = $produto['quantidade'] + $item['quantidade'];
                        
                        $sqlUp = "UPDATE produtos SET quantidade = ?, preco_compra = ? WHERE id = ?";
                        $stmt = $this->produtoModel->getDb()->prepare($sqlUp);
                        $stmt->execute([$nova_qtd, $item['preco_unitario'], $item['produto_id']]);
                    }
                    $_SESSION['success'] = "Nota de Entrada atualizada com sucesso!";
                    $this->redirect('/entradas/detalhes/' . $id);
                } else {
                    $_SESSION['error'] = "Erro ao atualizar a nota de entrada.";
                    $this->redirect('/entradas/editar/' . $id);
                }
            } else {
                $entrada_id = $this->entradaModel->create($data);

                if ($entrada_id) {
                    // Atualizar estoque e preço de compra no produto
                    foreach ($data['itens'] as $item) {
                        $produto = $this->produtoModel->find($item['produto_id']);
                        $nova_qtd = $produto['quantidade'] + $item['quantidade'];
                        
                        $sqlUp = "UPDATE produtos SET quantidade = ?, preco_compra = ? WHERE id = ?";
                        $stmt = $this->produtoModel->getDb()->prepare($sqlUp);
                        $stmt->execute([$nova_qtd, $item['preco_unitario'], $item['produto_id']]);
                    }

                    $_SESSION['success'] = "Nota de Entrada registrada com sucesso!";
                    $this->redirect('/entradas');
                } else {
                    $_SESSION['error'] = "Erro ao registrar nota de entrada.";
                    $this->redirect('/entradas/novo');
                }
            }
        }
    }

    public function detalhes($id) {
        $entrada = $this->entradaModel->getById($id);
        
        if (!$entrada) {
            $_SESSION['error'] = "Entrada não encontrada.";
            $this->redirect('/entradas');
            return;
        }

        $itens = $this->entradaModel->getItens($id);
        $parcelas = $this->entradaModel->getParcelas($id);

        $this->view('entradas/detalhes', [
            'title' => 'Detalhes da Nota de Entrada',
            'entrada' => $entrada,
            'itens' => $itens,
            'parcelas' => $parcelas
        ]);
    }

    public function baixarParcela($parcela_id) {
        // Precisamos descobrir qual a entrada_id para redirecionar de volta
        $sql = "SELECT entrada_id FROM entrada_parcelas WHERE id = ?";
        $stmt = $this->entradaModel->getDb()->prepare($sql);
        $stmt->execute([$parcela_id]);
        $parcela = $stmt->fetch();

        if ($parcela) {
            $data_pagamento = $_POST['data_pagamento'] ?? date('Y-m-d');
            if ($this->entradaModel->atualizarStatusParcela($parcela_id, 'pago', $data_pagamento)) {
                $_SESSION['success'] = "Baixa da parcela atualizada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao baixar parcela.";
            }
            $this->redirect('/entradas/detalhes/' . $parcela['entrada_id']);
        } else {
            $this->redirect('/entradas');
        }
    }

    public function estornarParcela($parcela_id) {
        $sql = "SELECT entrada_id FROM entrada_parcelas WHERE id = ?";
        $stmt = $this->entradaModel->getDb()->prepare($sql);
        $stmt->execute([$parcela_id]);
        $parcela = $stmt->fetch();

        if ($parcela) {
            if ($this->entradaModel->atualizarStatusParcela($parcela_id, 'pendente', null)) {
                $_SESSION['success'] = "Baixa estornada com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao estornar parcela.";
            }
            $this->redirect('/entradas/detalhes/' . $parcela['entrada_id']);
        } else {
            $this->redirect('/entradas');
        }
    }
}
