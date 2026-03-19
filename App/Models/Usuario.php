<?php

namespace App\Models;

class Usuario extends Model {
    protected $table = 'usuarios';

    public function findByUsuario($usuario) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario = :usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetch();
    }
}
