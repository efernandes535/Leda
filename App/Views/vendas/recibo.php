<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Venda #<?= $venda['id'] ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.5; padding: 20px; background: #fff; }
        .receipt-container { max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 32px; font-weight: bold; color: #000; letter-spacing: 2px; }
        .receipt-title { text-align: right; }
        .receipt-title h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .receipt-title p { margin: 5px 0 0; color: #777; }
        
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box h3 { font-size: 14px; text-transform: uppercase; color: #777; margin-bottom: 10px; border-bottom: 1px solid #eee; }
        .info-box p { margin: 2px 0; font-size: 15px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f9f9f9; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 14px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 15px; }
        .text-right { text-align: right; }
        
        .totals { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total-row.grand-total { border-bottom: none; font-size: 20px; font-weight: bold; color: #000; }

        .payment-info { margin-top: 40px; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .payment-info h3 { margin-top: 0; font-size: 16px; margin-bottom: 10px; }

        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px; }
        
        @media print {
            body { padding: 0; background: none; }
            .receipt-container { box-shadow: none; border: none; max-width: 100%; }
            .no-print { display: none; }
        }
        
        .no-print-btn { background: #333; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
        .no-print-btn:hover { background: #000; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <a href="<?= URL_BASE ?>/vendas/detalhes/<?= $venda['id'] ?>" class="no-print-btn" style="background: #666; text-decoration: none; display: inline-block;">VOLTAR</a>
        <button onclick="window.print()" class="no-print-btn">IMPRIMIR / SALVAR PDF</button>
        <p style="font-size: 12px; color: #777;">Dica: Selecione "Salvar como PDF" no destino da impressora.</p>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="logo">LEDA</div>
            <div class="receipt-title">
                <h1>Recibo de Venda</h1>
                <p>#<?= str_pad($venda['id'], 5, '0', STR_PAD_LEFT) ?></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>Vendedor</h3>
                <p><strong>Leda Perfumaria Fina</strong></p>
                <p>Gestão de Estoque e Vendas</p>
            </div>
            <div class="info-box" style="text-align: right;">
                <h3>Cliente</h3>
                <p><strong><?= $venda['cliente_nome'] ?? 'Cliente não identificado' ?></strong></p>
                <p>Data: <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-right">Qtd</th>
                    <th class="text-right">Unitário</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= $item['produto_nome'] ?></td>
                        <td class="text-right"><?= $item['quantidade'] ?></td>
                        <td class="text-right">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                        <td class="text-right">R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>R$ <?= number_format($venda['total'], 2, ',', '.') ?></span>
            </div>
            <div class="total-row grand-total">
                <span>Total</span>
                <span>R$ <?= number_format($venda['total'], 2, ',', '.') ?></span>
            </div>
        </div>

        <div class="payment-info">
            <h3>Informações de Pagamento</h3>
            <p><strong>Forma:</strong> 
                <?php 
                    $formas = ['avista' => 'À Vista', 'cartao' => 'Cartão', 'parcelado' => 'Parcelado'];
                    echo $formas[$venda['forma_pagamento']] ?? $venda['forma_pagamento'];
                ?>
            </p>
            <?php if ($venda['forma_pagamento'] === 'parcelado' && !empty($parcelas)): ?>
                <p><strong>Detalhamento:</strong> <?= count($parcelas) ?> parcelas</p>
                <div style="font-size: 13px; margin-top: 10px;">
                    <?php foreach ($parcelas as $p): ?>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #eee; padding: 3px 0;">
                            <span><?= $p['numero_parcela'] ?>ª Parcela (<?= date('d/m/Y', strtotime($p['data_vencimento'])) ?>)</span>
                            <span>R$ <?= number_format($p['valor'], 2, ',', '.') ?> - <strong><?= strtoupper($p['status']) ?></strong></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Obrigado pela preferência! Este documento é um recibo de conferência.</p>
            <p>&copy; <?= date('Y') ?> Leda - Perfume Inventory Management</p>
        </div>
    </div>

    <script>
        // Auto trigger print if wanted
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
