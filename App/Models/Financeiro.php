<?php
namespace App\Models;

class Financeiro extends Model {

    public function getResumoDashboard($mes = null, $ano = null) {
        if (!$mes) $mes = date('m');
        if (!$ano) $ano = date('Y');

        $resumo = [
            'receber_pendente' => 0,
            'pagar_pendente' => 0,
            'recebido_mes' => 0,
            'pago_mes' => 0,
        ];

        // Total a Receber Pendente (geral)
        $sql = "SELECT SUM(valor) as total FROM parcelas_venda WHERE status = 'pendente'";
        $res = $this->db->query($sql)->fetch();
        $resumo['receber_pendente'] = $res['total'] ?? 0;

        // Total a Pagar Pendente (geral)
        $sql = "SELECT SUM(valor) as total FROM entrada_parcelas WHERE status_pagamento = 'pendente'";
        $res = $this->db->query($sql)->fetch();
        $resumo['pagar_pendente'] = $res['total'] ?? 0;

        // Recebido no Mês Selecionado (usando valor)
        $sql = "SELECT SUM(valor) as total FROM parcelas_venda 
                WHERE status = 'pago' AND MONTH(data_pagamento) = ? AND YEAR(data_pagamento) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano]);
        $res = $stmt->fetch();
        $resumo['recebido_mes'] = $res['total'] ?? 0;

        // Pago no Mês Selecionado
        $sql = "SELECT SUM(valor) as total FROM entrada_parcelas 
                WHERE status_pagamento = 'pago' AND MONTH(data_pagamento) = ? AND YEAR(data_pagamento) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $ano]);
        $res = $stmt->fetch();
        $resumo['pago_mes'] = $res['total'] ?? 0;

        return $resumo;
    }

    public function getContasPagar($status = 'pendente') {
        $sql = "SELECT p.*, e.fornecedor, e.numero_nota, e.data_emissao, e.numero_parcelas as total_parcelas 
                FROM entrada_parcelas p 
                JOIN entradas e ON p.entrada_id = e.id ";
        
        if ($status !== 'todos') {
            $sql .= "WHERE p.status_pagamento = :status ";
        }
        $sql .= "ORDER BY p.data_vencimento ASC";
        
        $stmt = $this->db->prepare($sql);
        if ($status !== 'todos') {
            $stmt->execute([':status' => $status]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getContasReceber($status = 'pendente') {
        $sql = "SELECT p.*, v.id as venda_id, v.data_venda, v.numero_parcelas as total_parcelas, c.nome as cliente_nome 
                FROM parcelas_venda p 
                JOIN vendas v ON p.venda_id = v.id 
                LEFT JOIN clientes c ON v.cliente_id = c.id ";
        
        if ($status !== 'todos') {
            $sql .= "WHERE p.status = :status ";
        }
        $sql .= "ORDER BY p.data_vencimento ASC";
        
        $stmt = $this->db->prepare($sql);
        if ($status !== 'todos') {
            $stmt->execute([':status' => $status]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function baixarPagar($id, $data_pagamento) {
        $sql = "UPDATE entrada_parcelas SET status_pagamento = 'pago', data_pagamento = :data_pagamento WHERE id = :id";
        return $this->db->prepare($sql)->execute([':data_pagamento' => $data_pagamento, ':id' => $id]);
    }

    public function estornarPagar($id) {
        $sql = "UPDATE entrada_parcelas SET status_pagamento = 'pendente', data_pagamento = NULL WHERE id = :id";
        return $this->db->prepare($sql)->execute([':id' => $id]);
    }

    public function baixarReceber($id, $data_pagamento) {
        $sql = "UPDATE parcelas_venda SET status = 'pago', data_pagamento = :data_pagamento, valor_pago = valor WHERE id = :id";
        return $this->db->prepare($sql)->execute([':data_pagamento' => $data_pagamento, ':id' => $id]);
    }

    public function estornarReceber($id) {
        $sql = "UPDATE parcelas_venda SET status = 'pendente', data_pagamento = NULL, valor_pago = 0.00 WHERE id = :id";
        return $this->db->prepare($sql)->execute([':id' => $id]);
    }
}
