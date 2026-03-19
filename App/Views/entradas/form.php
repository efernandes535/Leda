<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Registrar Entrada de Mercadoria</h1>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= URL_BASE ?>/entradas/salvar" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Produto</label>
                    <select name="produto_id" class="form-select" required>
                        <option value="">Selecione o produto...</option>
                        <?php foreach ($produtos as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['sku'] ? '['.$p['sku'].'] ' : '' ?><?= $p['nome'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" name="quantidade" class="form-control" required min="1" placeholder="Ex: 10">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Preço Unitário de Compra (R$)</label>
                    <input type="number" step="0.01" name="preco_compra" class="form-control" required placeholder="Ex: 150,00">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Lote</label>
                    <input type="text" name="lote" class="form-control" placeholder="Ex: ABC123DEF">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Data de Validade</label>
                    <input type="date" name="data_validade" class="form-control">
                </div>

                <div class="col-12 mt-4">
                    <hr>
                    <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                    <a href="<?= URL_BASE ?>/entradas" class="btn btn-light">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
