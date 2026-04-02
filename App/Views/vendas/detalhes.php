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
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/vendas/recibo/<?= $venda['id'] ?>" class="btn btn-primary">
            <i class="bi bi-printer"></i> Gerar Recibo / PDF
        </a>
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
                <?php if ($venda['forma_pagamento'] === 'parcelado'): ?>
                    <?php 
                        $saldoDevedor = array_reduce($parcelas, function($carry, $item) {
                            return $carry + ($item['status'] === 'pendente' ? $item['valor'] : 0);
                        }, 0);
                    ?>
                    <p class="mb-0"><strong>Saldo Devedor:</strong></p>
                    <span class="h5 text-danger d-block">R$ <?= number_format($saldoDevedor, 2, ',', '.') ?></span>
                <?php endif; ?>
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
                                <th>Vlr. Recebido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parcelas as $p): ?>
                                <tr>
                                    <td><?= $p['numero_parcela'] ?>ª Parcela</td>
                                    <td><?= date('d/m/Y', strtotime($p['data_vencimento'])) ?></td>
                                    <td>R$ <?= number_format($p['valor'], 2, ',', '.') ?></td>
                                    <td class="text-success"><?= ($p['valor_pago'] > 0) ? 'R$ ' . number_format($p['valor_pago'], 2, ',', '.') : '-' ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'pago' || $p['valor'] <= 0.001): ?>
                                            <span class="badge bg-success">Pago</span>
                                        <?php elseif ($p['valor_pago'] > 0): ?>
                                            <span class="badge bg-info">Parcial</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($p['status'] === 'pendente'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success btn-baixar" 
                                                    data-id="<?= $p['id'] ?>" 
                                                    data-valor="<?= $p['valor'] ?>"
                                                    data-numero="<?= $p['numero_parcela'] ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalPagamento">
                                                <i class="bi bi-cash-stack"></i> Baixar
                                            </button>
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
        <!-- Itens da Venda (já existente) -->
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
                                    <td>
                                        <?= $item['produto_nome'] ?>
                                        <?php if (!empty($item['lote'])): ?>
                                            <div class="small text-muted">
                                                Lote: <?= $item['lote'] ?> 
                                                <?php if (!empty($item['data_validade'])): ?>
                                                    | Val: <?= date('d/m/Y', strtotime($item['data_validade'])) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
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

<!-- Modal de Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formPagamento" method="POST" action="">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Confirmar Recebimento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modal-info-parcela"></p>
                    <div class="mb-3">
                        <label class="form-label">Data do Recebimento</label>
                        <input type="date" name="data_pagamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor Recebido (R$)</label>
                        <input type="text" name="valor_recebido" id="valor_recebido" class="form-control" required>
                        <div class="form-text text-muted">
                            Se o valor for maior que a parcela, o saldo será abatido das últimas parcelas.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnBaixar = document.querySelectorAll('.btn-baixar');
    const formPagamento = document.getElementById('formPagamento');
    const modalInfo = document.getElementById('modal-info-parcela');
    const inputValor = document.getElementById('valor_recebido');

    btnBaixar.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const valor = this.getAttribute('data-valor');
            const numero = this.getAttribute('data-numero');

            formPagamento.action = '<?= URL_BASE ?>/vendas/pagarParcela/' + id;
            modalInfo.innerHTML = `Confirmando o recebimento da <strong>${numero}ª Parcela</strong> no valor de <strong>R$ ${parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong>.`;
            inputValor.value = parseFloat(valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        });
    });

    // Máscara básica para o valor
    inputValor.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2) + '';
        value = value.replace(".", ",");
        value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
        value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
        e.target.value = value;
    });
});
</script>

<?php include '../App/Views/partials/footer.php'; ?>
