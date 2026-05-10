<?php

namespace App\Models;

class Produto extends Model {
    protected $table = 'produtos';

    public function create($data) {
        $sql = "INSERT INTO produtos (categoria_id, sku, nome, descricao, preco_compra, preco_venda, quantidade, estoque_minimo, ativo) 
                VALUES (:categoria_id, :sku, :nome, :descricao, :preco_compra, :preco_venda, :quantidade, :estoque_minimo, :ativo)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE produtos SET 
                categoria_id = :categoria_id, 
                sku = :sku, 
                nome = :nome, 
                descricao = :descricao, 
                preco_compra = :preco_compra, 
                preco_venda = :preco_venda, 
                quantidade = :quantidade, 
                estoque_minimo = :estoque_minimo,
                ativo = :ativo
                WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getWithCategoria($onlyActive = false) {
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id";
        
        if ($onlyActive) {
            $sql .= " WHERE p.ativo = 1";
        }

        $sql .= " ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithCategoria($id) {
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function inativar($id) {
        $sql = "UPDATE produtos SET ativo = 0 WHERE id = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }

    public function getBaixoEstoqueCount() {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE quantidade <= estoque_minimo AND ativo = 1";
        $result = $this->db->query($sql)->fetch();
        return $result['total'] ?? 0;
    }

    public function getTopVendidos($limit = 5) {
        $sql = "SELECT p.*, SUM(iv.quantidade) as total_vendido 
                FROM produtos p 
                JOIN itens_venda iv ON p.id = iv.produto_id 
                WHERE p.ativo = 1
                GROUP BY p.id 
                ORDER BY total_vendido DESC 
                LIMIT " . (int)$limit;
        return $this->db->query($sql)->fetchAll();
    }

    public function getLotesDisponiveis($id) {
        $sql = "SELECT lote, data_validade FROM itens_entrada 
                WHERE produto_id = ? AND lote IS NOT NULL AND lote != '' 
                GROUP BY lote, data_validade 
                ORDER BY data_validade ASC, lote ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getLotesComQuantidadeDisponivel($id) {
        $sql = "SELECT 
                    lote, 
                    data_validade, 
                    SUM(qtd_entrada) - SUM(qtd_saida) as quantidade_disponivel
                FROM (
                    SELECT lote, data_validade, quantidade as qtd_entrada, 0 as qtd_saida 
                    FROM itens_entrada 
                    WHERE produto_id = :id1 AND lote IS NOT NULL AND lote != ''
                    UNION ALL
                    SELECT lote, data_validade, 0 as qtd_entrada, quantidade as qtd_saida 
                    FROM itens_venda 
                    WHERE produto_id = :id2 AND lote IS NOT NULL AND lote != ''
                ) as lotes_movimentacao
                GROUP BY lote, data_validade
                HAVING quantidade_disponivel > 0
                ORDER BY data_validade ASC, lote ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id1' => $id, 'id2' => $id]);
        return $stmt->fetchAll();
    }

    public function skuExists($sku, $excludeId = null) {
        if (empty(trim($sku))) return false;
        
        $sql = "SELECT id FROM produtos WHERE sku = :sku";
        $params = [':sku' => trim($sku)];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
}
