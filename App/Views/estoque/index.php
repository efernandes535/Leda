<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Estoque de Produtos</h1>
    </div>
    <div class="col-auto">
        <a href="<?= URL_BASE ?>/estoque/novo" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Produto
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
                        <th>Ref.</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço Venda</th>
                        <th>Qtd.</th>
                        <th>Mín.</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Nenhum produto cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><small class="text-muted"><?= $p['sku'] ?: '-' ?></small></td>
                                <td><?= $p['nome'] ?></td>
                                <td><?= $p['categoria_nome'] ?? 'Sem categoria' ?></td>
                                <td>R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?></td>
                                <td><?= $p['quantidade'] ?></td>
                                <td><?= $p['estoque_minimo'] ?></td>
                                <td>
                                    <?php if ($p['ativo'] == 0): ?>
                                        <span class="badge bg-secondary text-white">Arquivado</span>
                                    <?php elseif ($p['quantidade'] <= $p['estoque_minimo']): ?>
                                        <span class="badge bg-danger">Baixo Estoque</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= URL_BASE ?>/estoque/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($p['ativo'] == 1): ?>
                                            <a href="<?= URL_BASE ?>/estoque/excluir/<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning" title="Arquivar Produto" onclick="return confirm('Deseja arquivar este produto? Ele não aparecerá mais em novas vendas.')">
                                                <i class="bi bi-archive"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
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
