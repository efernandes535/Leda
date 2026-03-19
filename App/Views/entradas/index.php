<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Histórico de Entradas de Estoque</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/entradas/novo" class="btn btn-primary text-white">
            <i class="bi bi-plus-lg"></i> Registrar Compra
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Ref.</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Validade</th>
                        <th>Qtd.</th>
                        <th>Custo Unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entradas)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Nenhuma entrada registrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entradas as $e): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($e['data_entrada'])) ?></td>
                                <td><small class="text-muted"><?= $e['produto_sku'] ?: '-' ?></small></td>
                                <td><?= $e['produto_nome'] ?></td>
                                <td><?= $e['lote'] ?: '-' ?></td>
                                <td><?= $e['data_validade'] ? date('d/m/Y', strtotime($e['data_validade'])) : '-' ?></td>
                                <td><?= $e['quantidade'] ?></td>
                                <td>R$ <?= number_format($e['preco_compra'], 2, ',', '.') ?></td>
                                <td><strong>R$ <?= number_format($e['quantidade'] * $e['preco_compra'], 2, ',', '.') ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
