<?php

namespace App\Models;

class Venda extends Model {
    protected $table = 'vendas';

    public function create($cliente_id, $total, $itens) {
        try {
            $this->db->beginTransaction();

            $sqlVenda = "INSERT INTO vendas (cliente_id, total) VALUES (?, ?)";
            $stmtVenda = $this->db->prepare($sqlVenda);
            $stmtVenda->execute([$cliente_id, $total]);
            $venda_id = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
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

                $stmtItem->execute([$venda_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario']]);
                $stmtEstoque->execute([$item['quantidade'], $item['produto_id']]);
            }

            $this->db->commit();
            return $venda_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
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
}
