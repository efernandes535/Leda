<?php

namespace App\Models;

class Cliente extends Model {
    protected $table = 'clientes';

    public function create($data) {
        $sql = "INSERT INTO clientes (nome, email, telefone, endereco) VALUES (:nome, :email, :telefone, :endereco)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getHistorico($id) {
        $sql = "SELECT v.* FROM vendas v WHERE v.cliente_id = ? ORDER BY v.data_venda DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getSaldoDevedor($id) {
        $sql = "SELECT SUM(p.valor) as total_pendente 
                FROM parcelas_venda p 
                JOIN vendas v ON p.venda_id = v.id 
                WHERE v.cliente_id = ? AND p.status = 'pendente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch()['total_pendente'] ?? 0;
    }
}
