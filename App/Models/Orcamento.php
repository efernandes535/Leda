<?php

namespace App\Models;

class Orcamento extends Model {
    protected $table = 'orcamentos';

    public function create($cliente_id, $total, $itens, $forma_pagamento = 'avista', $status_pagamento = 'pago', $numero_parcelas = 1) {
        try {
            $this->db->beginTransaction();

            $sqlOrc = "INSERT INTO orcamentos (cliente_id, total, status, forma_pagamento, status_pagamento, numero_parcelas) VALUES (?, ?, 'pendente', ?, ?, ?)";
            $stmtOrc = $this->db->prepare($sqlOrc);
            $stmtOrc->execute([$cliente_id, $total, $forma_pagamento, $status_pagamento, $numero_parcelas]);
            $orcamento_id = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO itens_orcamento (orcamento_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($itens as $item) {
                $stmtItem->execute([$orcamento_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario']]);
            }

            $this->db->commit();
            return $orcamento_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getFullOrcamentos() {
        $sql = "SELECT o.*, c.nome as cliente_nome 
                FROM orcamentos o 
                LEFT JOIN clientes c ON o.cliente_id = c.id 
                ORDER BY o.data_criacao DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getItens($id) {
        $sql = "SELECT io.*, p.nome as produto_nome 
                FROM itens_orcamento io 
                JOIN produtos p ON io.produto_id = p.id 
                WHERE io.orcamento_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE orcamentos SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();
            // Itens_orcamento tem FK com ON DELETE CASCADE? Se não, deletamos manualmente.
            $this->db->prepare("DELETE FROM itens_orcamento WHERE orcamento_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM orcamentos WHERE id = ?")->execute([$id]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
