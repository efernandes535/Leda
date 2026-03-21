<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URL_BASE ?>/orcamentos">Orçamentos</a></li>
                <li class="breadcrumb-item active">Detalhes</li>
            </ol>
        </nav>
        <h1>Orçamento #<?= $orcamento['id'] ?></h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/orcamentos/recibo/<?= $orcamento['id'] ?>" class="btn btn-outline-primary me-2">
            <i class="bi bi-printer"></i> Gerar PDF / Orçamento
        </a>
        <?php if ($orcamento['status'] === 'pendente'): ?>
            <a href="<?= URL_BASE ?>/orcamentos/aprovar/<?= $orcamento['id'] ?>" class="btn btn-success">
                <i class="bi bi-check-lg"></i> Aprovar e Gerar Venda
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Resumo</h5>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= $orcamento['status'] == 'pendente' ? 'warning' : ($orcamento['status'] == 'aprovado' ? 'success' : 'danger') ?>">
                        <?= ucfirst($orcamento['status']) ?>
                    </span>
                </p>
                <p><strong>Pagamento:</strong> 
                    <span class="badge bg-<?= $orcamento['status_pagamento'] == 'pago' ? 'success' : 'warning' ?>">
                        <?= $orcamento['status_pagamento'] == 'pago' ? 'Recebido' : 'Pendente' ?>
                    </span>
                </p>
                <p><strong>Forma:</strong> 
                    <?php 
                        $formas = ['avista' => 'À Vista', 'cartao' => 'Cartão', 'parcelado' => 'Parcelado'];
                        echo $formas[$orcamento['forma_pagamento']] ?? $orcamento['forma_pagamento'];
                    ?>
                </p>
                <?php if ($orcamento['forma_pagamento'] === 'parcelado'): ?>
                    <p><strong>Parcelas:</strong> <?= $orcamento['numero_parcelas'] ?>x</p>
                <?php endif; ?>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($orcamento['data_criacao'])) ?></p>
                <p><strong>Total:</strong> <span class="h4 text-primary d-block mt-2">R$ <?= number_format($orcamento['total'], 2, ',', '.') ?></span></p>
                <hr>
                <form action="<?= URL_BASE ?>/orcamentos/excluir/<?= $orcamento['id'] ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este orçamento?')">
                    <button type="submit" class="btn btn-danger w-100 mt-3">
                        <i class="bi bi-trash"></i> Excluir Orçamento
                    </button>
                </form>
            </div>
        </div>

        <?php if ($orcamento['forma_pagamento'] === 'parcelado'): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Previsão do Carnê</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Parcela</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $valor_parcela = $orcamento['total'] / $orcamento['numero_parcelas'];
                            // Para o orçamento, simulamos o primeiro vencimento para 30 dias a partir de hoje
                            $data_atual = date('Y-m-d', strtotime('+30 days'));
                            for ($i = 1; $i <= $orcamento['numero_parcelas']; $i++): 
                            ?>
                                <tr>
                                    <td><?= $i ?>ª Parcela</td>
                                    <td><?= date('d/m/Y', strtotime($data_atual)) ?></td>
                                    <td>R$ <?= number_format($valor_parcela, 2, ',', '.') ?></td>
                                </tr>
                                <?php $data_atual = date('Y-m-d', strtotime($data_atual . ' + 30 days')); ?>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                    <div class="p-2 border-top">
                        <small class="text-muted text-center d-block">Simulação baseada em aprovação para hoje.</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Itens do Orçamento</h5>
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
                                    <td><?= $item['produto_nome'] ?></td>
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

<?php include '../App/Views/partials/footer.php'; ?>
