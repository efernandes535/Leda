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
}
