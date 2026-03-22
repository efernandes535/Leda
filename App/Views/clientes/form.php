<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Cadastro de Cliente</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/clientes/salvar" method="POST">
                    <?php if (isset($cliente)): ?>
                        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" required value="<?= $cliente['nome'] ?? '' ?>" placeholder="Ex: Maria Oliveira">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="<?= $cliente['email'] ?? '' ?>" placeholder="maria@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" class="form-control" value="<?= $cliente['telefone'] ?? '' ?>" placeholder="(11) 99999-9999">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço Residencial</label>
                        <textarea name="endereco" class="form-control" rows="2" placeholder="Rua, Número, Bairro, Cidade"><?= $cliente['endereco'] ?? '' ?></textarea>
                    </div>
                    
                    <div class="text-end mt-4">
                        <a href="<?= URL_BASE ?>/clientes" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <?= isset($cliente) ? 'Atualizar Cadastro' : 'Salvar Cadastro' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
