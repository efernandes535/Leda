<?php
require_once 'config/config.php';
require_once 'App/Core/Database.php';
use App\Core\Database;

$db = Database::getConnection();

// Vendas pendentes que NÃO estão na tabelas parcelas_venda
$sql = "SELECT v.id, v.total, v.numero_parcelas, v.data_vencimento_primeira, v.forma_pagamento 
        FROM vendas v 
        WHERE v.status_pagamento = 'pendente' 
        AND v.id NOT IN (SELECT venda_id FROM parcelas_venda)";

$vendas = $db->query($sql)->fetchAll();

foreach ($vendas as $v) {
    echo "Migrando Venda #{$v['id']}...\n";
    $venda_id = $v['id'];
    $total = $v['total'];
    $numero_parcelas = $v['numero_parcelas'] ?: 1;
    $data_vencimento_prim = $v['data_vencimento_primeira'] ?: date('Y-m-d', strtotime('+30 days'));
    
    $sqlParcela = "INSERT INTO parcelas_venda (venda_id, numero_parcela, data_vencimento, valor) VALUES (?, ?, ?, ?)";
    $stmtParcela = $db->prepare($sqlParcela);
    
    if ($v['forma_pagamento'] === 'parcelado') {
        $valor_parcela = $total / $numero_parcelas;
        $data_calc = $data_vencimento_prim;
        for ($i = 1; $i <= $numero_parcelas; $i++) {
            $stmtParcela->execute([$venda_id, $i, $data_calc, $valor_parcela]);
            $data_calc = date('Y-m-d', strtotime($data_calc . ' + 30 days'));
        }
    } else {
        $sqlSingle = "INSERT INTO parcelas_venda (venda_id, numero_parcela, data_vencimento, valor, status) VALUES (?, 1, ?, ?, 'pendente')";
        $db->prepare($sqlSingle)->execute([$venda_id, date('Y-m-d'), $total]);
    }
}
echo "Migração concluída!\n";
