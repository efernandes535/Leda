<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <a href="<?= URL_BASE ?>/entradas" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <h1>Detalhes da Nota de Entrada #<?= str_pad($entrada['id'], 5, '0', STR_PAD_LEFT) ?></h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/entradas/editar/<?= $entrada['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar Nota
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Dados da Nota</h5>
            </div>
            <div class="card-body">
                <p><strong>Fornecedor:</strong> <?= $entrada['fornecedor'] ?: 'Não Informado' ?></p>
                <p><strong>Nº da Nota:</strong> <?= $entrada['numero_nota'] ?: 'S/N' ?></p>
                <p><strong>Data de Emissão:</strong> <?= $entrada['data_emissao'] ? date('d/m/Y', strtotime($entrada['data_emissao'])) : '-' ?></p>
                <p><strong>Forma de Pagto.:</strong> <?= $entrada['forma_pagamento'] ?: '-' ?></p>
                <p><strong>Qtd. Parcelas:</strong> <?= $entrada['numero_parcelas'] ?>x</p>
                <p><strong>Valor do Frete:</strong> R$ <?= number_format($entrada['valor_frete'], 2, ',', '.') ?></p>
                <p><strong>Valor Total da Nota:</strong> R$ <?= number_format($entrada['valor_total'], 2, ',', '.') ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="row g-4 mb-4">
            <?php if (!empty($parcelas)): ?>
            <div class="col-12">
                <div class="card shadow-sm h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Vencimentos (Contas a Pagar)</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Parcela</th>
                                    <th>Data de Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parcelas as $p): ?>
                                    <tr>
                                        <td class="align-middle"><?= $p['numero_parcela'] ?> / <?= $entrada['numero_parcelas'] ?></td>
                                        <td class="align-middle"><strong><?= date('d/m/Y', strtotime($p['data_vencimento'])) ?></strong></td>
                                        <td class="align-middle">R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
                                        <td class="align-middle">
                                            <?php if ($p['status_pagamento'] === 'pago'): ?>
                                                <form action="<?= URL_BASE ?>/entradas/baixarParcela/<?= $p['id'] ?>" method="POST" class="d-inline">
                                                    <span class="badge bg-success me-2">Pago em:</span>
                                                    <input type="date" name="data_pagamento" value="<?= $p['data_pagamento'] ?>" class="form-control form-control-sm d-inline-block w-auto" title="Data do Pagamento" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary ms-1" title="Atualizar Data"><i class="bi bi-save"></i></button>
                                                </form>
                                                <a href="<?= URL_BASE ?>/entradas/estornarParcela/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger ms-1" title="Desfazer Baixa" onclick="return confirm('Deseja cancelar o pagamento desta parcela?');">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Estornar
                                                </a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-success ms-1" data-bs-toggle="modal" data-bs-target="#modalPagarDetalhe<?= $p['id'] ?>" title="Marcar como Pago">
                                                    <i class="bi bi-check-lg"></i> Baixar
                                                </button>
                                                
                                                <div class="modal fade" id="modalPagarDetalhe<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content text-start border-success border-top border-4">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold text-success">Confirmar Pagamento</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="<?= URL_BASE ?>/entradas/baixarParcela/<?= $p['id'] ?>" method="POST">
                                                                <div class="modal-body">
                                                                    <p>Confirmando o pagamento da <strong><?= $p['numero_parcela'] ?>ª Parcela</strong> no valor de <strong>R$ <?= number_format($p['valor'], 2, ',', '.') ?></strong>.</p>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Data do Pagamento</label>
                                                                        <input type="date" name="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                                    </div>
                                                                    
                                                                    <div class="mb-1">
                                                                        <label class="form-label">Valor Pago (R$)</label>
                                                                        <input type="number" step="0.01" class="form-control" value="<?= $p['valor'] ?>" readonly>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Observações</h5>
                    </div>
                    <div class="card-body">
                        <p><?= nl2br($entrada['observacoes'] ?: 'Nenhuma observação.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Itens Recebidos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Cód. (SKU)</th>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Validade</th>
                        <th>Qtd.</th>
                        <th>Preço Unit. (Compra)</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><small class="text-muted"><?= $item['produto_sku'] ?: '-' ?></small></td>
                            <td><?= $item['produto_nome'] ?></td>
                            <td><?= $item['lote'] ?: '-' ?></td>
                            <td><?= $item['data_validade'] ? date('d/m/Y', strtotime($item['data_validade'])) : '-' ?></td>
                            <td><?= $item['quantidade'] ?></td>
                            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                            <td><strong>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end border-0 pb-1"><strong>Subtotal dos Itens:</strong></td>
                        <td class="border-0 pb-1"><strong>R$ <?= number_format($entrada['valor_total'] - $entrada['valor_frete'], 2, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end border-0 pb-1 text-danger"><strong>(+) Frete:</strong></td>
                        <td class="border-0 pb-1 text-danger"><strong>R$ <?= number_format($entrada['valor_frete'], 2, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end fs-5"><strong>Total da Nota:</strong></td>
                        <td class="fs-5 text-primary"><strong>R$ <?= number_format($entrada['valor_total'], 2, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
