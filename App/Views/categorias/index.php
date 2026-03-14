<?php include '../App/Views/partials/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h1>Categorias</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Nova Categoria</h5>
            </div>
            <div class="card-body">
                <form action="<?= URL_BASE ?>/categorias/salvar" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex: Ferramentas">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categorias)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Nenhuma categoria cadastrada.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categorias as $c): ?>
                                    <tr>
                                        <td><?= $c['id'] ?></td>
                                        <td><?= $c['nome'] ?></td>
                                        <td class="text-end">
                                            <a href="<?= URL_BASE ?>/categorias/excluir/<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza?')">
                                                <i class="bi bi-trash"></i>
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
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
