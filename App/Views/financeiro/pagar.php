<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <a href="<?= URL_BASE ?>/financeiro" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
        </a>
        <h1><i class="bi bi-box-arrow-up-right text-danger"></i> Contas a Pagar</h1>
    </div>
    <div class="col-auto">
        <form class="d-flex gap-2" method="GET" action="<?= URL_BASE ?>/financeiro/pagar">
            <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                <option value="todos" <?= $statusFiltro == 'todos' ? 'selected' : '' ?>>Todos os Status</option>
                <option value="pendente" <?= $statusFiltro == 'pendente' ? 'selected' : '' ?>>Apenas Pendentes</option>
                <option value="pago" <?= $statusFiltro == 'pago' ? 'selected' : '' ?>>Apenas Pagos</option>
            </select>
        </form>
    </div>
</div>

<div class="card shadow-sm border-danger border-top-0 border-end-0 border-bottom-0 border-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vencimento</th>
                        <th>Fornecedor</th>
                        <th>Nº Nota</th>
                        <th>Parcela</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-end">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">Nenhuma conta encontrada neste filtro.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contas as $conta): ?>
                            <?php 
                            $vencido = false;
                            if ($conta['status_pagamento'] == 'pendente' && strtotime($conta['data_vencimento']) < strtotime('today')) {
                                $vencido = true;
                            }
                            ?>
                            <tr class="<?= $vencido ? 'table-danger' : '' ?>">
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($conta['data_vencimento'])) ?></strong>
                                    <?php if ($vencido): ?>
                                        <span class="badge bg-danger ms-1">Atrasado</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $conta['fornecedor'] ?: 'Não Informado' ?></td>
                                <td><?= $conta['numero_nota'] ?: 'S/N' ?></td>
                                <td><?= $conta['numero_parcela'] ?>/<?= $conta['total_parcelas'] ?></td>
                                <td>R$ <?= number_format($conta['valor'], 2, ',', '.') ?></td>
                                <td>
                                    <?php if ($conta['status_pagamento'] === 'pago'): ?>
                                        <span class="badge bg-success">Pago</span><br>
                                        <small class="text-muted">em <?= date('d/m/Y', strtotime($conta['data_pagamento'])) ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= URL_BASE ?>/entradas/detalhes/<?= $conta['entrada_id'] ?>" class="btn btn-sm btn-outline-primary" title="Ver Nota Original">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if ($conta['status_pagamento'] === 'pago'): ?>
                                        <a href="<?= URL_BASE ?>/financeiro/estornarPagar/<?= $conta['id'] ?>" class="btn btn-sm btn-outline-danger ms-1" title="Estornar Pagamento" onclick="return confirm('Desfazer baixa?');">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-success ms-1" data-bs-toggle="modal" data-bs-target="#modalPagar<?= $conta['id'] ?>" title="Dar Baixa">
                                            <i class="bi bi-check-lg"></i> Pagar
                                        </button>
                                        
                                        <!-- Modal de Pagamento -->
                                        <div class="modal fade" id="modalPagar<?= $conta['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start border-success border-top border-4">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-success">Confirmar Pagamento</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= URL_BASE ?>/financeiro/baixarPagar/<?= $conta['id'] ?>" method="POST">
                                                        <div class="modal-body">
                                                            <p>Confirmando o pagamento da <strong><?= $conta['numero_parcela'] ?>ª Parcela</strong> no valor de <strong>R$ <?= number_format($conta['valor'], 2, ',', '.') ?></strong>.</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Data do Pagamento</label>
                                                                <input type="date" name="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-1">
                                                                <label class="form-label">Valor Pago (R$)</label>
                                                                <input type="number" step="0.01" class="form-control" value="<?= $conta['valor'] ?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
