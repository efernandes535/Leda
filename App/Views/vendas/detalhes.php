<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URL_BASE ?>/vendas">Vendas</a></li>
                <li class="breadcrumb-item active">Detalhes</li>
            </ol>
        </nav>
        <h1>Venda #<?= $venda['id'] ?></h1>
    </div>
</div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Resumo da Venda</h5>
            </div>
            <div class="card-body">
                <p><strong>Pagamento:</strong> 
                    <span class="badge bg-<?= $venda['status_pagamento'] == 'pago' ? 'success' : 'warning' ?>">
                        <?= $venda['status_pagamento'] == 'pago' ? 'Recebido' : 'Pendente' ?>
                    </span>
                </p>
                <p><strong>Forma:</strong> 
                    <?php 
                        $formas = ['avista' => 'À Vista', 'cartao' => 'Cartão', 'parcelado' => 'Parcelado'];
                        echo $formas[$venda['forma_pagamento']] ?? $venda['forma_pagamento'];
                    ?>
                </p>
                <?php if ($venda['forma_pagamento'] === 'parcelado'): ?>
                    <p><strong>Parcelas:</strong> <?= $venda['numero_parcelas'] ?>x</p>
                    <p><strong>Vencimento 1ª:</strong> <?= date('d/m/Y', strtotime($venda['data_vencimento_primeira'])) ?></p>
                <?php endif; ?>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></p>
                <p><strong>Total:</strong> <span class="h4 text-primary d-block mt-2">R$ <?= number_format($venda['total'], 2, ',', '.') ?></span></p>
                <hr>
                <a href="<?= URL_BASE ?>/vendas/excluir/<?= $venda['id'] ?>" class="btn btn-danger w-100 mt-3" onclick="return confirm('Deseja realmente excluir esta venda? O estoque será estornado automaticamente.')">
                    <i class="bi bi-trash"></i> Excluir Venda
                </a>
            </div>
        </div>

        <?php if ($venda['forma_pagamento'] === 'parcelado'): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Detalhamento do Carnê</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Parcela</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parcelas as $p): ?>
                                <tr>
                                    <td><?= $p['numero_parcela'] ?>ª Parcela</td>
                                    <td><?= date('d/m/Y', strtotime($p['data_vencimento'])) ?></td>
                                    <td>R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $p['status'] === 'pago' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($p['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($p['status'] === 'pendente'): ?>
                                            <a href="<?= URL_BASE ?>/vendas/pagarParcela/<?= $p['id'] ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Confirmar o recebimento desta parcela?')">
                                                <i class="bi bi-cash-stack"></i> Baixar
                                            </a>
                                        <?php else: ?>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($p['data_pagamento'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Itens Vendidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Qtd.</th>
                                <th>Preço Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?= $item['produto_nome'] ?></td>
                                    <td><?= $item['quantidade'] ?></td>
                                    <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
