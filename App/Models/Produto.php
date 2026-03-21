<?php

namespace App\Models;

class Produto extends Model {
    protected $table = 'produtos';

    public function create($data) {
        $sql = "INSERT INTO produtos (categoria_id, sku, nome, descricao, preco_compra, preco_venda, quantidade, estoque_minimo) 
                VALUES (:categoria_id, :sku, :nome, :descricao, :preco_compra, :preco_venda, :quantidade, :estoque_minimo)";
        
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
                estoque_minimo = :estoque_minimo 
                WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getWithCategoria() {
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getBaixoEstoqueCount() {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE quantidade <= estoque_minimo";
        $result = $this->db->query($sql)->fetch();
        return $result['total'] ?? 0;
    }

    public function getTopVendidos($limit = 5) {
        $sql = "SELECT p.*, SUM(iv.quantidade) as total_vendido 
                FROM produtos p 
                JOIN itens_venda iv ON p.id = iv.produto_id 
                GROUP BY p.id 
                ORDER BY total_vendido DESC 
                LIMIT " . (int)$limit;
        return $this->db->query($sql)->fetchAll();
    }
}
