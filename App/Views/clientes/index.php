<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Gestão de Clientes</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/clientes/novo" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Novo Cliente
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Contato</th>
                        <th>Saldo em Aberto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Nenhum cliente cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td><strong><?= $c['nome'] ?></strong></td>
                                <td>
                                    <small class="d-block text-muted"><?= $c['email'] ?></small>
                                    <small class="d-block"><?= $c['telefone'] ?></small>
                                </td>
                                <td>
                                    <span class="<?= $c['saldo_devedor'] > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                                        R$ <?= number_format($c['saldo_devedor'], 2, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= URL_BASE ?>/clientes/detalhes/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person-vcard"></i> Ver Perfil
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
