<?php include '../App/Views/partials/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1>Bem-vindo ao Sistema Leda</h1>
        <p class="lead">Dashboard Inicial</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Estoque</h5>
                <p class="card-text">Gerencie seus produtos e categorias.</p>
                <a href="<?= URL_BASE ?>/estoque" class="btn btn-outline-light">Acessar</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Vendas</h5>
                <p class="card-text">Registre novas vendas e veja o histórico.</p>
                <a href="<?= URL_BASE ?>/vendas" class="btn btn-outline-light">Acessar</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Orçamentos</h5>
                <p class="card-text">Crie e envie orçamentos para seus clientes.</p>
                <a href="<?= URL_BASE ?>/orcamentos" class="btn btn-outline-light">Acessar</a>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>

