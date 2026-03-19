<?php

namespace App\Models;

class Entrada extends Model {
    protected $table = 'entradas_estoque';

    public function create($data) {
        $sql = "INSERT INTO entradas_estoque (produto_id, quantidade, lote, data_validade, preco_compra) 
                VALUES (:produto_id, :quantidade, :lote, :data_validade, :preco_compra)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getRecentes() {
        $sql = "SELECT e.*, p.nome as produto_nome, p.sku as produto_sku 
                FROM entradas_estoque e 
                JOIN produtos p ON e.produto_id = p.id 
                ORDER BY e.data_entrada DESC 
                LIMIT 50";
        return $this->db->query($sql)->fetchAll();
    }
}
