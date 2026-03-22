<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1><?= $title ?></h1>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= URL_BASE ?>/estoque/salvar" method="POST">
            <?php if (isset($produto)): ?>
                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
            <?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Referência (SKU)</label>
                    <input type="text" name="sku" class="form-control" placeholder="Ex: PERF-001" value="<?= $produto['sku'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" name="nome" class="form-control" required placeholder="Ex: Good Girl" value="<?= $produto['nome'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($produto) && $produto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= $cat['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3"><?= $produto['descricao'] ?? '' ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Preço Compra (R$)</label>
                    <input type="number" step="0.01" name="preco_compra" class="form-control" required value="<?= $produto['preco_compra'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Preço Venda (R$)</label>
                    <input type="number" step="0.01" name="preco_venda" class="form-control" required value="<?= $produto['preco_venda'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" name="quantidade" class="form-control" value="<?= $produto['quantidade'] ?? '0' ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estoque Mínimo</label>
                    <input type="number" name="estoque_minimo" class="form-control" value="<?= $produto['estoque_minimo'] ?? '5' ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="ativo" value="1" id="statusAtivo" <?= (!isset($produto) || $produto['ativo'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="statusAtivo">Produto Ativo</label>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <hr>
                    <button type="submit" class="btn btn-primary"><?= isset($produto) ? 'Atualizar' : 'Salvar' ?> Produto</button>
                    <?php if (isset($produto) && $produto['ativo'] == 1): ?>
                        <a href="<?= URL_BASE ?>/estoque/excluir/<?= $produto['id'] ?>" class="btn btn-outline-warning float-end" onclick="return confirm('Deseja arquivar este produto? Ele não aparecerá mais em novas vendas.')">
                            <i class="bi bi-archive"></i> Arquivar Produto
                        </a>
                    <?php endif; ?>
                    <a href="<?= URL_BASE ?>/estoque" class="btn btn-light">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
