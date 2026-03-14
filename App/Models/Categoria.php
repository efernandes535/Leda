<?php

namespace App\Models;

class Categoria extends Model {
    protected $table = 'categorias';

    public function create($nome) {
        $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function update($id, $nome) {
        $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
