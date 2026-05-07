<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Histórico de Notas de Entrada</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/entradas/novo" class="btn btn-primary text-white">
            <i class="bi bi-plus-lg"></i> Registrar Nota Fiscal
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nº Lançamento</th>
                        <th>Data Registro</th>
                        <th>Data Emissão</th>
                        <th>Nº Nota</th>
                        <th>Fornecedor</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entradas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Nenhuma nota de entrada registrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entradas as $e): ?>
                            <tr>
                                <td>#<?= str_pad($e['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($e['data_registro'])) ?></td>
                                <td><?= $e['data_emissao'] ? date('d/m/Y', strtotime($e['data_emissao'])) : '-' ?></td>
                                <td><span class="badge bg-secondary"><?= $e['numero_nota'] ?: 'S/N' ?></span></td>
                                <td><?= $e['fornecedor'] ?: 'Não Informado' ?></td>
                                <td><strong>R$ <?= number_format($e['valor_total'], 2, ',', '.') ?></strong></td>
                                <td>
                                    <a href="<?= URL_BASE ?>/entradas/detalhes/<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver Detalhes
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
