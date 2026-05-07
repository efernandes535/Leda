<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Registrar Nota de Entrada</h1>
    </div>
</div>

<form action="<?= URL_BASE ?>/entradas/salvar" method="POST">
    <input type="hidden" name="id" value="<?= $entrada['id'] ?? '' ?>">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dados Gerais da Nota</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Fornecedor</label>
                        <input type="text" name="fornecedor" class="form-control" placeholder="Nome da empresa ou fornecedor" value="<?= htmlspecialchars($entrada['fornecedor'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nº da Nota Fiscal / Recibo</label>
                        <input type="text" name="numero_nota" class="form-control" placeholder="Ex: 123456" value="<?= htmlspecialchars($entrada['numero_nota'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data de Emissão</label>
                            <input type="date" name="data_emissao" class="form-control" value="<?= $entrada['data_emissao'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Forma de Pagamento</label>
                            <?php $forma = $entrada['forma_pagamento'] ?? ''; ?>
                            <select name="forma_pagamento" class="form-select">
                                <option value="Boleto" <?= $forma == 'Boleto' ? 'selected' : '' ?>>Boleto Bancário</option>
                                <option value="Pix" <?= $forma == 'Pix' ? 'selected' : '' ?>>Pix</option>
                                <option value="Transferência" <?= $forma == 'Transferência' ? 'selected' : '' ?>>Transferência (TED/DOC)</option>
                                <option value="Cartão de Crédito" <?= $forma == 'Cartão de Crédito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                                <option value="Cartão de Débito" <?= $forma == 'Cartão de Débito' ? 'selected' : '' ?>>Cartão de Débito</option>
                                <option value="Dinheiro" <?= $forma == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                                <option value="Outros" <?= $forma == 'Outros' ? 'selected' : '' ?>>Outros</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Qtd. Parcelas</label>
                            <input type="number" name="numero_parcelas" id="numero_parcelas" class="form-control" value="<?= $entrada['numero_parcelas'] ?? 1 ?>" min="1">
                        </div>
                    </div>

                    <div id="parcelas-container" class="mb-3 p-3 bg-light border rounded" style="<?= isset($parcelas) && count($parcelas) > 0 ? 'display:block;' : 'display:none;' ?>">
                        <h6 class="mb-3">Vencimentos das Parcelas</h6>
                        <div id="parcelas-list">
                            <?php if(isset($parcelas) && count($parcelas) > 0): ?>
                                <?php foreach($parcelas as $i => $p): ?>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label small">Data Venc. Parcela <?= $i+1 ?></label>
                                            <input type="date" name="data_vencimento_parcela[]" class="form-control form-control-sm" value="<?= $p['data_vencimento'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Valor Parcela <?= $i+1 ?> (R$)</label>
                                            <input type="number" step="0.01" name="valor_parcela_item[]" class="form-control form-control-sm" value="<?= $p['valor'] ?>" required>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valor do Frete (R$)</label>
                        <input type="number" step="0.01" name="valor_frete" id="valor_frete" class="form-control" placeholder="0.00" value="<?= $entrada['valor_frete'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="2" placeholder="Anotações adicionais..."><?= htmlspecialchars($entrada['observacoes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Itens da Nota</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                        <i class="bi bi-plus-lg"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itens-container">
                        <?php $itensLoop = (isset($itens) && !empty($itens)) ? $itens : [[]]; ?>
                        <?php foreach($itensLoop as $idx => $item): ?>
                        <div class="row g-2 mb-3 item-row">
                            <div class="col-md-4">
                                <label class="form-label">Produto</label>
                                <select name="produto_id[]" class="form-select produto-select" required>
                                    <option value="">Selecione o Produto...</option>
                                    <?php foreach ($produtos as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-preco="<?= $p['preco_compra'] ?>" <?= ($item['produto_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                            <?= $p['sku'] ? '['.$p['sku'].'] ' : '' ?><?= $p['nome'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qtd.</label>
                                <input type="number" name="quantidade[]" class="form-control qtd-input" value="<?= $item['quantidade'] ?? 1 ?>" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Preço Unit. (Compra)</label>
                                <input type="number" name="preco_compra[]" step="0.01" class="form-control preco-input" required placeholder="0.00" value="<?= $item['preco_unitario'] ?? '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lote</label>
                                <input type="text" name="lote[]" class="form-control" placeholder="Lote" value="<?= htmlspecialchars($item['lote'] ?? '') ?>">
                            </div>
                            <div class="col-md-2 d-flex gap-2 align-items-end">
                                <div>
                                    <label class="form-label">Validade</label>
                                    <input type="date" name="data_validade[]" class="form-control px-1" value="<?= $item['data_validade'] ?? '' ?>">
                                </div>
                                <button type="button" class="btn btn-outline-danger remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <h4>Total da Nota: <span id="valor-total">R$ 0,00</span></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-end">
            <a href="<?= URL_BASE ?>/entradas" class="btn btn-light me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary px-5">Registrar Nota de Entrada</button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itens-container');
    const btnAdd = document.getElementById('add-item');
    const totalSpan = document.getElementById('valor-total');

    // Template para novas linhas (antes de inicializar TomSelect)
    const firstRow = document.querySelector('.item-row');
    const rowTemplate = firstRow.cloneNode(true);
    
    function initProductSelect(element) {
        if (element.tomselect) return;
        new TomSelect(element, {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: "Selecione o Produto...",
            allowEmptyOption: true,
            maxOptions: 1000
        });
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qtd = row.querySelector('.qtd-input').value;
            const preco = row.querySelector('.preco-input').value;
            if (qtd && preco) {
                total += qtd * preco;
            }
        });
        
        const frete = parseFloat(document.getElementById('valor_frete').value) || 0;
        total += frete;
        
        totalSpan.textContent = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});

        renderParcelas(total);
    }

    function renderParcelas(total) {
        const parcelasList = document.getElementById('parcelas-list');
        const container = document.getElementById('parcelas-container');
        const numParcelas = parseInt(document.getElementById('numero_parcelas').value) || 1;
        const formaPagamento = document.querySelector('select[name="forma_pagamento"]').value;

        if (numParcelas > 1 || formaPagamento === 'Cartão de Crédito') {
            container.style.display = 'block';
            const valorPorParcela = total > 0 ? (total / numParcelas).toFixed(2) : '';
            
            // Reconstruir apenas se a quantidade mudou, senão só atualiza os valores se estiverem vazios
            if (parcelasList.children.length !== numParcelas) {
                parcelasList.innerHTML = '';
                let dataAtual = new Date();
                
                for (let i = 1; i <= numParcelas; i++) {
                    dataAtual.setMonth(dataAtual.getMonth() + 1);
                    const defaultDate = dataAtual.toISOString().split('T')[0];
                    
                    parcelasList.innerHTML += `
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label small">Data Venc. Parcela ${i}</label>
                                <input type="date" name="data_vencimento_parcela[]" class="form-control form-control-sm" value="${defaultDate}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Valor Parcela ${i} (R$)</label>
                                <input type="number" step="0.01" name="valor_parcela_item[]" class="form-control form-control-sm" value="${valorPorParcela}" required>
                            </div>
                        </div>
                    `;
                }
            } else {
                // Atualiza placeholders ou valores se mudar o total
                const inputsValor = parcelasList.querySelectorAll('input[name="valor_parcela_item[]"]');
                inputsValor.forEach(input => {
                    input.value = valorPorParcela;
                });
            }
        } else {
            container.style.display = 'none';
            parcelasList.innerHTML = '';
        }
    }

    // Inicializa o total se houver itens
    calculateTotal();
    
    document.getElementById('valor_frete').addEventListener('input', calculateTotal);
    document.getElementById('numero_parcelas').addEventListener('input', calculateTotal);
    document.querySelector('select[name="forma_pagamento"]').addEventListener('change', calculateTotal);

    btnAdd.addEventListener('click', function() {
        const row = rowTemplate.cloneNode(true);
        row.querySelectorAll('input').forEach(input => input.value = '');
        row.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        row.querySelector('.qtd-input').value = 1;
        container.appendChild(row);

        // Inicializar TomSelect na nova linha
        initProductSelect(row.querySelector('.produto-select'));
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotal();
            }
        }
    });

    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('produto-select')) {
            const row = e.target.closest('.item-row');
            const preco = e.target.selectedOptions[0].dataset.preco;
            row.querySelector('.preco-input').value = preco || 0;
            calculateTotal();
        }
        if (e.target.classList.contains('qtd-input') || e.target.classList.contains('preco-input')) {
            calculateTotal();
        }
    });

    // Inicializar linhas existentes
    document.querySelectorAll('.produto-select').forEach(select => {
        initProductSelect(select);
        if (select.value) {
            const row = select.closest('.item-row');
            const preco = select.selectedOptions[0].dataset.preco;
            if (!row.querySelector('.preco-input').value) {
                row.querySelector('.preco-input').value = preco || 0;
            }
        }
    });

    calculateTotal();
});
</script>

<?php include '../App/Views/partials/footer.php'; ?>
