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
        $sql = "SELECT lote, data_validade FROM entradas_estoque 
                WHERE produto_id = ? AND lote IS NOT NULL AND lote != '' 
                GROUP BY lote, data_validade 
                ORDER BY data_validade ASC, lote ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}
