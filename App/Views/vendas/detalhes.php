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
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Resumo da Venda</h5>
            </div>
            <div class="card-body">
                <p><strong>Status:</strong> <span class="badge bg-success text-white">Finalizada</span></p>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></p>
                <p><strong>Total:</strong> <span class="h4 text-primary d-block mt-2">R$ <?= number_format($venda['total'], 2, ',', '.') ?></span></p>
                <hr>
                <a href="<?= URL_BASE ?>/vendas/excluir/<?= $venda['id'] ?>" class="btn btn-danger w-100 mt-3" onclick="return confirm('Deseja realmente excluir esta venda? O estoque será estornado automaticamente.')">
                    <i class="bi bi-trash"></i> Excluir Venda
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
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
