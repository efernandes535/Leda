<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Histórico de Orçamentos</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/orcamentos/novo" class="btn btn-primary">
            <i class="bi bi-file-earmark-plus"></i> Novo Orçamento
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
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orcamentos)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhum orçamento registrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orcamentos as $o): ?>
                            <tr>
                                <td><?= $o['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($o['data_criacao'])) ?></td>
                                <td><?= $o['cliente_nome'] ?? 'Cliente Avulso' ?></td>
                                <td>R$ <?= number_format($o['total'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $o['status'] == 'pendente' ? 'warning' : ($o['status'] == 'aprovado' ? 'success' : 'danger') ?>">
                                        <?= ucfirst($o['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= URL_BASE ?>/orcamentos/detalhes/<?= $o['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver Detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($o['status'] === 'pendente'): ?>
                                        <a href="<?= URL_BASE ?>/orcamentos/editar/<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar Orçamento">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= URL_BASE ?>/orcamentos/aprovar/<?= $o['id'] ?>" class="btn btn-sm btn-outline-success" title="Aprovar e Gerar Venda" onclick="return confirm('Deseja aprovar este orçamento e gerar uma venda?')">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form action="<?= URL_BASE ?>/orcamentos/excluir/<?= $o['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este orçamento?')">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Orçamento">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
