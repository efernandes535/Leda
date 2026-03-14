<?php

namespace App\Models;

class Cliente extends Model {
    protected $table = 'clientes';

    public function create($data) {
        $sql = "INSERT INTO clientes (nome, email, telefone, endereco) VALUES (:nome, :email, :telefone, :endereco)";
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
        $sql = "SELECT SUM(total) as total_pendente FROM vendas WHERE cliente_id = ? AND status_pagamento = 'pendente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch()['total_pendente'] ?? 0;
    }
}
