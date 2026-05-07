<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4 align-items-center">
    <div class="col">
        <h1>Dashboard Financeiro</h1>
    </div>
    <div class="col-auto">
        <form class="d-flex gap-2" method="GET">
            <select name="mes" class="form-select w-auto">
                <?php 
                $mesesPtBr = [
                    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                    '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                    '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                ];
                foreach($mesesPtBr as $num => $nome): 
                ?>
                    <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>>
                        <?= $nome ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="ano" class="form-select w-auto">
                <?php for($a = date('Y') - 2; $a <= date('Y') + 2; $a++): ?>
                    <option value="<?= $a ?>" <?= $ano == $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card bg-success text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 text-white-50">Recebido no Mês</h6>
                    <i class="bi bi-graph-up-arrow fs-3 text-white-50"></i>
                </div>
                <h3 class="mb-0">R$ <?= number_format($resumo['recebido_mes'], 2, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 text-white-50">Pago no Mês</h6>
                    <i class="bi bi-graph-down-arrow fs-3 text-white-50"></i>
                </div>
                <h3 class="mb-0">R$ <?= number_format($resumo['pago_mes'], 2, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 text-white-50">A Receber (Geral Pendente)</h6>
                    <i class="bi bi-wallet2 fs-3 text-white-50"></i>
                </div>
                <h3 class="mb-0">R$ <?= number_format($resumo['receber_pendente'], 2, ',', '.') ?></h3>
                <a href="<?= URL_BASE ?>/financeiro/receber" class="text-white text-decoration-none small mt-2 d-block">Ver detalhes <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-dark shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0 text-dark-50">A Pagar (Geral Pendente)</h6>
                    <i class="bi bi-receipt fs-3 text-dark-50"></i>
                </div>
                <h3 class="mb-0">R$ <?= number_format($resumo['pagar_pendente'], 2, ',', '.') ?></h3>
                <a href="<?= URL_BASE ?>/financeiro/pagar" class="text-dark text-decoration-none small mt-2 d-block">Ver detalhes <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Balanço do Mês (<?= $mes ?>/<?= $ano ?>)</h5>
            </div>
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <?php 
                    $saldo = $resumo['recebido_mes'] - $resumo['pago_mes']; 
                    $cor_saldo = $saldo >= 0 ? 'text-success' : 'text-danger';
                ?>
                <h6 class="text-muted mb-2">Saldo Realizado</h6>
                <h1 class="<?= $cor_saldo ?> fw-bold">R$ <?= number_format($saldo, 2, ',', '.') ?></h1>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-primary">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <i class="bi bi-arrow-left-right text-primary mb-3" style="font-size: 3rem;"></i>
                <h4 class="mb-3">Acesso Rápido</h4>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= URL_BASE ?>/financeiro/receber" class="btn btn-outline-success px-4 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Contas a Receber
                    </a>
                    <a href="<?= URL_BASE ?>/financeiro/pagar" class="btn btn-outline-danger px-4 py-2">
                        <i class="bi bi-box-arrow-up-right"></i> Contas a Pagar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/partials/footer.php'; ?>
