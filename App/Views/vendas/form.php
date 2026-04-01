<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Nova Venda de Perfumes</h1>
    </div>
</div>

<form action="<?= URL_BASE ?>/vendas/salvar" method="POST">
    <?php if (isset($orcamento)): ?>
        <input type="hidden" name="orcamento_id" value="<?= $orcamento['id'] ?>">
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dados Gerais</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select">
                            <option value="">Cliente Avulso</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (isset($orcamento) && $orcamento['cliente_id'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= $c['nome'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento" id="forma_pagamento" class="form-select">
                            <option value="avista" <?= (isset($orcamento) && $orcamento['forma_pagamento'] == 'avista') ? 'selected' : '' ?>>À Vista (Dinheiro/Pix)</option>
                            <option value="cartao" <?= (isset($orcamento) && $orcamento['forma_pagamento'] == 'cartao') ? 'selected' : '' ?>>Cartão (Débito/Crédito)</option>
                            <option value="parcelado" <?= (isset($orcamento) && $orcamento['forma_pagamento'] == 'parcelado') ? 'selected' : '' ?>>Parcelado / Carne</option>
                        </select>
                    </div>

                    <div class="mb-3 <?= (isset($orcamento) && $orcamento['forma_pagamento'] == 'parcelado') ? '' : 'd-none' ?>" id="div-parcelas">
                        <label class="form-label">Número de Parcelas</label>
                        <input type="number" name="numero_parcelas" class="form-control" value="<?= $orcamento['numero_parcelas'] ?? 1 ?>" min="1" max="12">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status do Pagamento</label>
                        <select name="status_pagamento" class="form-select">
                            <option value="pago" <?= (isset($orcamento) && $orcamento['status_pagamento'] == 'pago') ? 'selected' : '' ?>>Já Pago</option>
                            <option value="pendente" <?= (isset($orcamento) && $orcamento['status_pagamento'] == 'pendente') ? 'selected' : '' ?>>Pendente / Em Aberto</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Itens do Pedido</h5>
                    <?php if (isset($orcamento)): ?>
                        <span class="badge bg-info">Importado do Orçamento #<?= $orcamento['id'] ?></span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                        <i class="bi bi-plus-lg"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itens-container">
                        <?php 
                        $render_itens = !empty($itensPreCarregados) ? $itensPreCarregados : [['produto_id' => '', 'quantidade' => 1, 'preco_unitario' => '']];
                        foreach ($render_itens as $index => $itemVal): 
                        ?>
                        <div class="row g-2 mb-3 item-row">
                            <div class="col-md-4">
                                <label class="form-label">Produto</label>
                                <select name="produto_id[]" class="form-select produto-select" required>
                                    <option value="">Selecione o Perfume...</option>
                                    <?php foreach ($produtos as $p): ?>
                                        <option value="<?= $p['id'] ?>" 
                                                data-preco="<?= $p['preco_venda'] ?>"
                                                data-lotes='<?= json_encode($p['lotes']) ?>'
                                                data-estoque="<?= $p['quantidade'] ?>"
                                                <?= $itemVal['produto_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= $p['nome'] ?> (Qtd: <?= $p['quantidade'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Lote / Validade</label>
                                <select name="lote[]" class="form-select lote-select">
                                    <option value="">Selecione o Lote...</option>
                                </select>
                                <input type="hidden" name="data_validade[]" class="validade-input">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qtd.</label>
                                <input type="number" name="quantidade[]" class="form-control qtd-input" value="<?= $itemVal['quantidade'] ?>" min="1" required>
                                <small class="stock-check-label d-block mt-1"></small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Preço Unit.</label>
                                <input type="number" name="preco_unitario[]" step="0.01" class="form-control preco-input" value="<?= $itemVal['preco_unitario'] ?>" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-item w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <h4>Total: <span id="valor-total">R$ 0,00</span></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <?php if (isset($orcamento)): ?>
                    <input class="form-check-input" type="checkbox" name="gerar_backorder" id="gerar_backorder" value="1" checked>
                    <label class="form-check-label" for="gerar_backorder">
                        Gerar novo orçamento para itens pendentes (Saldo)
                    </label>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?= URL_BASE ?>/vendas" class="btn btn-light me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary px-5">Finalizar Venda</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itens-container');
    const btnAdd = document.getElementById('add-item');
    const totalSpan = document.getElementById('valor-total');
    const btnSubmit = document.querySelector('button[type="submit"]');

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qtd = row.querySelector('.qtd-input').value;
            const preco = row.querySelector('.preco-input').value;
            if (qtd && preco) {
                total += qtd * preco;
            }
        });
        totalSpan.textContent = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
    }

    function checkStock(row) {
        const select = row.querySelector('.produto-select');
        const qtdInput = row.querySelector('.qtd-input');
        const label = row.querySelector('.stock-check-label');
        
        const option = select.selectedOptions[0];
        if (!option || !option.value) {
            label.textContent = '';
            return true;
        }

        const estoque = parseInt(option.dataset.estoque) || 0;
        const qtdSolicitada = parseInt(qtdInput.value) || 0;

        if (qtdSolicitada > estoque) {
            label.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Insuficiente (Apenas ${estoque})</span>`;
            qtdInput.classList.add('is-invalid');
            return false;
        } else {
            label.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Disponível (${estoque})</span>`;
            qtdInput.classList.remove('is-invalid');
            return true;
        }
    }

    function validateAll() {
        let allOk = true;
        document.querySelectorAll('.item-row').forEach(row => {
            if (!checkStock(row)) allOk = false;
        });
        btnSubmit.disabled = !allOk;
    }

    // Lógica para mostrar parcelas
    const selectForma = document.getElementById('forma_pagamento');
    const divParcelas = document.getElementById('div-parcelas');
    if (selectForma) {
        selectForma.addEventListener('change', function() {
            if (this.value === 'parcelado') {
                divParcelas.classList.remove('d-none');
            } else {
                divParcelas.classList.add('d-none');
            }
        });
    }

    btnAdd.addEventListener('click', function() {
        const firstRow = document.querySelector('.item-row');
        const row = firstRow.cloneNode(true);
        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
            if (select.classList.contains('lote-select')) {
                select.innerHTML = '<option value="">Selecione o Lote...</option>';
            }
        });
        row.querySelector('.qtd-input').value = 1;
        row.querySelector('.stock-check-label').textContent = '';
        row.querySelector('.qtd-input').classList.remove('is-invalid');
        container.appendChild(row);
        validateAll();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotal();
                validateAll();
            }
        }
    });

    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('produto-select')) {
            updateLoteSelect(e.target);
            calculateTotal();
            validateAll();
        }

        if (e.target.classList.contains('lote-select')) {
            const row = e.target.closest('.item-row');
            const option = e.target.selectedOptions[0];
            row.querySelector('.validade-input').value = option.dataset.validade || '';
        }

        if (e.target.classList.contains('qtd-input') || e.target.classList.contains('preco-input')) {
            calculateTotal();
            validateAll();
        }
    });

    // Validar ao digitar para feedback imediato
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qtd-input')) {
            const row = e.target.closest('.item-row');
            checkStock(row);
            validateAll();
        }
    });

    function updateLoteSelect(produtoSelect) {
        const row = produtoSelect.closest('.item-row');
        const option = produtoSelect.selectedOptions[0];
        if (!option || !option.value) {
            row.querySelector('.lote-select').innerHTML = '<option value="">Selecione o Lote...</option>';
            return;
        }

        const preco = option.dataset.preco;
        const lotes = JSON.parse(option.dataset.lotes || '[]');
        
        row.querySelector('.preco-input').value = preco || 0;
        
        const loteSelect = row.querySelector('.lote-select');
        loteSelect.innerHTML = '<option value="">Selecione o Lote...</option>';
        
        lotes.forEach(l => {
            const label = l.lote + (l.data_validade ? ' (Venc: ' + l.data_validade + ')' : '');
            const opt = new Option(label, l.lote);
            opt.dataset.validade = l.data_validade || '';
            loteSelect.add(opt);
        });
    }

    // Inicializar linhas existentes
    document.querySelectorAll('.produto-select').forEach(select => {
        if (select.value) {
            updateLoteSelect(select);
        }
    });
    calculateTotal();
    validateAll();
});
</script>

<?php include '../App/Views/partials/footer.php'; ?>
