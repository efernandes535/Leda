<?php

namespace App\Models;

class Venda extends Model {
    protected $table = 'vendas';

    public function create($cliente_id, $total, $itens, $forma_pagamento = 'avista', $status_pagamento = 'pago', $numero_parcelas = 1) {
        try {
            $this->db->beginTransaction();

            $data_vencimento = null;
            if ($forma_pagamento === 'parcelado') {
                $data_vencimento = date('Y-m-d', strtotime('+30 days'));
            }

            $sqlVenda = "INSERT INTO vendas (cliente_id, total, forma_pagamento, status_pagamento, numero_parcelas, data_vencimento_primeira) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtVenda = $this->db->prepare($sqlVenda);
            $stmtVenda->execute([$cliente_id, $total, $forma_pagamento, $status_pagamento, $numero_parcelas, $data_vencimento]);
            $venda_id = $this->db->lastInsertId();

            // Gerar Parcelas na tabela parcelas_venda
            $sqlParcela = "INSERT INTO parcelas_venda (venda_id, numero_parcela, data_vencimento, valor) VALUES (?, ?, ?, ?)";
            $stmtParcela = $this->db->prepare($sqlParcela);
            
            if ($forma_pagamento === 'parcelado') {
                $valor_parcela = $total / $numero_parcelas;
                $data_calc = $data_vencimento;
                for ($i = 1; $i <= $numero_parcelas; $i++) {
                    $stmtParcela->execute([$venda_id, $i, $data_calc, $valor_parcela]);
                    $data_calc = date('Y-m-d', strtotime($data_calc . ' + 30 days'));
                }
            } else {
                // Para à vista ou cartão, gera 1 parcela já paga ou pendente conforme status
                $status_parc = ($status_pagamento === 'pago') ? 'pago' : 'pendente';
                $data_pagto = ($status_parc === 'pago') ? date('Y-m-d') : null;
                $sqlSingle = "INSERT INTO parcelas_venda (venda_id, numero_parcela, data_vencimento, valor, status, data_pagamento) VALUES (?, 1, ?, ?, ?, ?)";
                $this->db->prepare($sqlSingle)->execute([$venda_id, date('Y-m-d'), $total, $status_parc, $data_pagto]);
            }

            $sqlItem = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario, lote, data_validade) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtItem = $this->db->prepare($sqlItem);

            $sqlEstoque = "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?";
            $stmtEstoque = $this->db->prepare($sqlEstoque);

            $sqlCheck = "SELECT nome, quantidade FROM produtos WHERE id = ?";
            $stmtCheck = $this->db->prepare($sqlCheck);

            foreach ($itens as $item) {
                // Verificar se há estoque suficiente
                $stmtCheck->execute([$item['produto_id']]);
                $produto = $stmtCheck->fetch();

                if ($produto['quantidade'] < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto: " . $produto['nome'] . " (Disponível: " . $produto['quantidade'] . ")");
                }

                $lote = $item['lote'] ?? null;
                $data_validade = $item['data_validade'] ?? null;

                $stmtItem->execute([$venda_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario'], $lote, $data_validade]);
                $stmtEstoque->execute([$item['quantidade'], $item['produto_id']]);
            }

            $this->db->commit();
            return $venda_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findWithCliente($id) {
        $sql = "SELECT v.*, c.nome as cliente_nome 
                FROM vendas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getFullVendas() {
        $sql = "SELECT v.*, c.nome as cliente_nome 
                FROM vendas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                ORDER BY v.data_venda DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getItens($id) {
        $sql = "SELECT iv.*, p.nome as produto_nome 
                FROM itens_venda iv 
                JOIN produtos p ON iv.produto_id = p.id 
                WHERE iv.venda_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Buscar itens para estornar o estoque
            $itens = $this->getItens($id);
            $sqlEstoque = "UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?";
            $stmtEstoque = $this->db->prepare($sqlEstoque);
            
            foreach ($itens as $item) {
                $stmtEstoque->execute([$item['quantidade'], $item['produto_id']]);
            }

            // Deletar itens e a venda
            $this->db->prepare("DELETE FROM itens_venda WHERE venda_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM vendas WHERE id = ?")->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getParcelas($venda_id) {
        $sql = "SELECT * FROM parcelas_venda WHERE venda_id = ? ORDER BY numero_parcela ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$venda_id]);
        return $stmt->fetchAll();
    }

    public function pagarParcela($parcela_id, $data_pagamento = null, $valor_recebido = null) {
        if (!$data_pagamento) $data_pagamento = date('Y-m-d');
        
        try {
            $this->db->beginTransaction();
            
            // Buscar dados da parcela
            $sqlParc = "SELECT * FROM parcelas_venda WHERE id = ?";
            $stmtParc = $this->db->prepare($sqlParc);
            $stmtParc->execute([$parcela_id]);
            $parcela = $stmtParc->fetch();
            
            if (!$parcela || $parcela['status'] === 'pago') {
                $this->db->rollBack();
                return false;
            }
            
            $valor_original = $parcela['valor'];
            if ($valor_recebido === null) $valor_recebido = $valor_original;
            
            // 1. Marcar a parcela atual como paga
            $sqlUp = "UPDATE parcelas_venda SET status = 'pago', data_pagamento = ? WHERE id = ?";
            $this->db->prepare($sqlUp)->execute([$data_pagamento, $parcela_id]);
            
            // 2. Lógica de Abatimento (se o valor recebido for maior)
            $excedente = $valor_recebido - $valor_original;
            
            if ($excedente > 0) {
                // Abater das últimas parcelas pendentes (ordem decrescente)
                while ($excedente > 0.01) { // Usando 0.01 para evitar problemas de precisão de float
                    $sqlLast = "SELECT * FROM parcelas_venda 
                                WHERE venda_id = ? AND status = 'pendente' AND id != ? 
                                ORDER BY numero_parcela DESC LIMIT 1";
                    $stmtLast = $this->db->prepare($sqlLast);
                    $stmtLast->execute([$parcela['venda_id'], $parcela_id]);
                    $last = $stmtLast->fetch();
                    
                    if (!$last) break; // Não há mais parcelas pendentes
                    
                    if ($last['valor'] > $excedente) {
                        // Abate parcial da última
                        $novo_valor = $last['valor'] - $excedente;
                        $sqlAbate = "UPDATE parcelas_venda SET valor = ? WHERE id = ?";
                        $this->db->prepare($sqlAbate)->execute([$novo_valor, $last['id']]);
                        $excedente = 0;
                    } else {
                        // Quita a última integralmente e continua com o que sobrou
                        $excedente -= $last['valor'];
                        $sqlQuita = "UPDATE parcelas_venda SET valor = 0, status = 'pago', data_pagamento = ? WHERE id = ?";
                        $this->db->prepare($sqlQuita)->execute([$data_pagamento, $last['id']]);
                    }
                }
            }
            
            // 3. Verificar se todas as parcelas da venda foram pagas
            $sqlCheck = "SELECT COUNT(*) as pendentes FROM parcelas_venda WHERE venda_id = ? AND status = 'pendente'";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([$parcela['venda_id']]);
            $result = $stmtCheck->fetch();

            if ($result['pendentes'] == 0) {
                $this->db->prepare("UPDATE vendas SET status_pagamento = 'pago' WHERE id = ?")->execute([$parcela['venda_id']]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getVendaIdByParcela($parcela_id) {
        $sql = "SELECT venda_id FROM parcelas_venda WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$parcela_id]);
        $result = $stmt->fetch();
        return $result ? $result['venda_id'] : null;
    }

    public function getFaturamentoMes() {
        $sql = "SELECT SUM(total) as faturamento FROM vendas WHERE DATE_FORMAT(data_venda, '%Y-%m') = DATE_FORMAT(CURRENT_DATE, '%Y-%m')";
        $result = $this->db->query($sql)->fetch();
        return $result['faturamento'] ?? 0;
    }

    public function getRecebiveisPendentes() {
        $sql = "SELECT SUM(valor) as total FROM parcelas_venda WHERE status = 'pendente'";
        $result = $this->db->query($sql)->fetch();
        return $result['total'] ?? 0;
    }

    public function getUltimasVendas($limit = 5) {
        $sql = "SELECT v.*, c.nome as cliente_nome 
                FROM vendas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                ORDER BY v.data_venda DESC 
                LIMIT " . (int)$limit;
        return $this->db->query($sql)->fetchAll();
    }
}
