<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Histórico de Vendas</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/vendas/novo" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Nova Venda
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Nenhuma venda registrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vendas as $v): ?>
                            <tr>
                                <td><?= $v['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                                <td><?= $v['cliente_nome'] ?? 'Cliente Avulso' ?></td>
                                <td>R$ <?= number_format($v['total'], 2, ',', '.') ?></td>
                                <td>
                                    <?php 
                                        $formas = ['avista' => 'À Vista', 'cartao' => 'Cartão', 'parcelado' => 'Parcelado'];
                                        echo $formas[$v['forma_pagamento']] ?? $v['forma_pagamento'];
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $v['status_pagamento'] == 'pago' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($v['status_pagamento']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= URL_BASE ?>/vendas/detalhes/<?= $v['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= URL_BASE ?>/vendas/excluir/<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir Venda" onclick="return confirm('Deseja excluir esta venda e estornar o estoque?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
