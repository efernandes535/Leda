<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URL_BASE ?>/clientes">Clientes</a></li>
                <li class="breadcrumb-item active">Perfil do Cliente</li>
            </ol>
        </nav>
        <h1><?= $cliente['nome'] ?></h1>
    </div>
</div>

<div class="row g-4">
    <!-- Resumo do Cliente -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informações de Contato</h5>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> <?= $cliente['email'] ?></p>
                <p><strong>Telefone:</strong> <?= $cliente['telefone'] ?></p>
                <p><strong>Endereço:</strong><br><?= nl2br($cliente['endereco']) ?></p>
            </div>
        </div>

        <div class="card shadow-sm border-danger">
            <div class="card-body text-center py-4">
                <h6 class="text-muted mb-2">Saldo em Aberto</h6>
                <h2 class="<?= $saldo > 0 ? 'text-danger' : 'text-success' ?>">
                    R$ <?= number_format($saldo, 2, ',', '.') ?>
                </h2>
                <?php if ($saldo > 0): ?>
                    <button class="btn btn-sm btn-danger mt-2">Registrar Recebimento</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Histórico de Compras -->
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Histórico de Compras / Vendas</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Valor Total</th>
                                <th>Pagamento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historico)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Ainda não há vendas para este cliente.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historico as $v): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                                        <td>R$ <?= number_format($v['total'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $v['status_pagamento'] == 'pago' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($v['status_pagamento']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= URL_BASE ?>/vendas/detalhes/<?= $v['id'] ?>" class="btn btn-sm btn-outline-secondary">Ver Detalhes</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
