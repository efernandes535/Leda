<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1 class="h3 mb-0 text-gray-800">Painel de Indicadores</h1>
        <p class="text-muted small">Bem-vindo de volta, <?= $_SESSION['usuario_nome'] ?>!</p>
    </div>
    <div class="col-auto">
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y') ?>
        </div>
    </div>
</div>

<!-- KPIs Row -->
<div class="row g-4 mb-4">
    <!-- Faturamento do Mês -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 small">Faturamento (Mês)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($stats['faturamento_mes'], 2, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contas a Receber -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1 small">A Receber (Pendentes)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($stats['recebiveis_pendentes'], 2, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-wallet2 fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baixo Estoque -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-danger">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1 small">Baixo Estoque</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['baixo_estoque'] ?> Produtos</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lucro Estimado (Apenas exemplo, pode ser calculado depois) -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-info">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1 small">Novas Vendas (Mês)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($stats['ultimas_vendas']) ?> Registros</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cart-check fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Vendas Recentes -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-clock-history me-2"></i>Vendas Recentes</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['ultimas_vendas'] as $v): ?>
                                <tr>
                                    <td class="small"><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                                    <td><?= $v['cliente_nome'] ?></td>
                                    <td><strong>R$ <?= number_format($v['total'], 2, ',', '.') ?></strong></td>
                                    <td>
                                        <a href="<?= URL_BASE ?>/vendas/detalhes/<?= $v['id'] ?>" class="btn btn-sm btn-light">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($stats['ultimas_vendas'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Nenhuma venda este mês.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Vendidos -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-star me-2"></i>Mais Vendidos</h6>
            </div>
            <div class="card-body">
                <?php foreach ($stats['top_vendidos'] as $index => $p): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <span class="badge bg-<?= $index == 0 ? 'warning' : 'light text-dark border' ?> rounded-circle p-2" style="width: 32px; height: 32px; display: inline-block; text-align: center;">
                                <?= $index + 1 ?>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small fw-bold"><?= $p['nome'] ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= $p['total_vendido'] ?> unidades vendidas</div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($stats['top_vendidos'])): ?>
                    <p class="text-center text-muted py-4">Sem dados de venda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
