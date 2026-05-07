<?php

namespace App\Models;

class Entrada extends Model {
    protected $table = 'entradas';

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Salvar Capa da Nota
            $sql = "INSERT INTO entradas (fornecedor, numero_nota, data_emissao, forma_pagamento, numero_parcelas, valor_parcela, valor_frete, valor_total, observacoes) 
                    VALUES (:fornecedor, :numero_nota, :data_emissao, :forma_pagamento, :numero_parcelas, :valor_parcela, :valor_frete, :valor_total, :observacoes)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':fornecedor' => $data['fornecedor'] ?: null,
                ':numero_nota' => $data['numero_nota'] ?: null,
                ':data_emissao' => $data['data_emissao'] ?: null,
                ':forma_pagamento' => $data['forma_pagamento'] ?: null,
                ':numero_parcelas' => $data['numero_parcelas'],
                ':valor_parcela' => $data['valor_parcela'],
                ':valor_frete' => $data['valor_frete'],
                ':valor_total' => $data['valor_total'],
                ':observacoes' => $data['observacoes'] ?: null
            ]);
            
            $entrada_id = $this->db->lastInsertId();

            // Salvar Itens
            $sqlItem = "INSERT INTO itens_entrada (entrada_id, produto_id, quantidade, preco_unitario, lote, data_validade) 
                        VALUES (:entrada_id, :produto_id, :quantidade, :preco_unitario, :lote, :data_validade)";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($data['itens'] as $item) {
                $stmtItem->execute([
                    ':entrada_id' => $entrada_id,
                    ':produto_id' => $item['produto_id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco_unitario' => $item['preco_unitario'],
                    ':lote' => $item['lote'] ?: null,
                    ':data_validade' => $item['data_validade'] ?: null
                ]);
            }
            // Salvar Parcelas (se houver)
            if (!empty($data['parcelas'])) {
                $sqlParcela = "INSERT INTO entrada_parcelas (entrada_id, numero_parcela, valor, data_vencimento) 
                               VALUES (:entrada_id, :numero_parcela, :valor, :data_vencimento)";
                $stmtParcela = $this->db->prepare($sqlParcela);
                
                $numParcela = 1;
                foreach ($data['parcelas'] as $parcela) {
                    $stmtParcela->execute([
                        ':entrada_id' => $entrada_id,
                        ':numero_parcela' => $numParcela++,
                        ':valor' => $parcela['valor'],
                        ':data_vencimento' => $parcela['data_vencimento']
                    ]);
                }
            }

            $this->db->commit();
            return $entrada_id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE entradas SET 
                    fornecedor = :fornecedor, 
                    numero_nota = :numero_nota, 
                    data_emissao = :data_emissao, 
                    forma_pagamento = :forma_pagamento, 
                    numero_parcelas = :numero_parcelas, 
                    valor_parcela = :valor_parcela, 
                    valor_frete = :valor_frete, 
                    valor_total = :valor_total, 
                    observacoes = :observacoes 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':fornecedor' => $data['fornecedor'] ?: null,
                ':numero_nota' => $data['numero_nota'] ?: null,
                ':data_emissao' => $data['data_emissao'] ?: null,
                ':forma_pagamento' => $data['forma_pagamento'] ?: null,
                ':numero_parcelas' => $data['numero_parcelas'],
                ':valor_parcela' => $data['valor_parcela'],
                ':valor_frete' => $data['valor_frete'],
                ':valor_total' => $data['valor_total'],
                ':observacoes' => $data['observacoes'] ?: null
            ]);

            // Limpar Itens e Parcelas antigos
            $this->db->prepare("DELETE FROM itens_entrada WHERE entrada_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM entrada_parcelas WHERE entrada_id = ?")->execute([$id]);

            // Inserir Novos Itens
            $sqlItem = "INSERT INTO itens_entrada (entrada_id, produto_id, quantidade, preco_unitario, lote, data_validade) 
                        VALUES (:entrada_id, :produto_id, :quantidade, :preco_unitario, :lote, :data_validade)";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($data['itens'] as $item) {
                $stmtItem->execute([
                    ':entrada_id' => $id,
                    ':produto_id' => $item['produto_id'],
                    ':quantidade' => $item['quantidade'],
                    ':preco_unitario' => $item['preco_unitario'],
                    ':lote' => $item['lote'] ?: null,
                    ':data_validade' => $item['data_validade'] ?: null
                ]);
            }

            // Inserir Novas Parcelas
            if (!empty($data['parcelas'])) {
                $sqlParcela = "INSERT INTO entrada_parcelas (entrada_id, numero_parcela, valor, data_vencimento) 
                               VALUES (:entrada_id, :numero_parcela, :valor, :data_vencimento)";
                $stmtParcela = $this->db->prepare($sqlParcela);
                
                $numParcela = 1;
                foreach ($data['parcelas'] as $parcela) {
                    $stmtParcela->execute([
                        ':entrada_id' => $id,
                        ':numero_parcela' => $numParcela++,
                        ':valor' => $parcela['valor'],
                        ':data_vencimento' => $parcela['data_vencimento']
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getRecentes() {
        $sql = "SELECT * FROM entradas ORDER BY data_registro DESC LIMIT 50";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM entradas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getItens($entrada_id) {
        $sql = "SELECT i.*, p.nome as produto_nome, p.sku as produto_sku 
                FROM itens_entrada i 
                JOIN produtos p ON i.produto_id = p.id 
                WHERE i.entrada_id = :entrada_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':entrada_id' => $entrada_id]);
        return $stmt->fetchAll();
    }

    public function getParcelas($entrada_id) {
        $sql = "SELECT * FROM entrada_parcelas WHERE entrada_id = :entrada_id ORDER BY numero_parcela ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':entrada_id' => $entrada_id]);
        return $stmt->fetchAll();
    }

    public function atualizarStatusParcela($parcela_id, $status, $data_pagamento = null) {
        $sql = "UPDATE entrada_parcelas SET status_pagamento = :status, data_pagamento = :data_pagamento WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':data_pagamento' => $data_pagamento,
            ':id' => $parcela_id
        ]);
    }
}
