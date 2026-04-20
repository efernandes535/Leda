<?php include '../App/Views/partials/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1><?= isset($orcamento) ? 'Editar Orçamento #' . $orcamento['id'] : 'Novo Orçamento de Perfumes' ?></h1>
    </div>
</div>

<form action="<?= URL_BASE ?>/orcamentos/salvar" method="POST">
    <?php if (isset($orcamento)): ?>
        <input type="hidden" name="id" value="<?= $orcamento['id'] ?>">
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
                        <label class="form-label">Previsão de Pagamento</label>
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
                        <label class="form-label">Status Pretendido</label>
                        <select name="status_pagamento" class="form-select">
                            <option value="pago" <?= (isset($orcamento) && $orcamento['status_pagamento'] == 'pago') ? 'selected' : '' ?>>Já Pago (na aprovação)</option>
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
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                        <i class="bi bi-plus-lg"></i> Adicionar Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itens-container">
                        <?php 
                        $render_itens = !empty($itensPreCarregados) ? $itensPreCarregados : [['produto_id' => '', 'quantidade' => 1, 'preco_unitario' => '']];
                        foreach ($render_itens as $itemVal): 
                        ?>
                        <div class="row g-2 mb-3 item-row">
                            <div class="col-md-6">
                                <label class="form-label">Produto</label>
                                <select name="produto_id[]" class="form-select produto-select" required>
                                    <option value="">Selecione o Perfume...</option>
                                    <?php foreach ($produtos as $p): ?>
                                        <option value="<?= $p['id'] ?>" data-preco="<?= $p['preco_venda'] ?>" <?= $itemVal['produto_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= $p['sku'] ? '['.$p['sku'].'] ' : '' ?><?= $p['nome'] ?> (Qtd: <?= $p['quantidade'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qtd.</label>
                                <input type="number" name="quantidade[]" class="form-control qtd-input" value="<?= $itemVal['quantidade'] ?>" min="1" required>
                            </div>
                            <div class="col-md-3">
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
        <div class="col-12 text-end">
            <hr>
            <a href="<?= URL_BASE ?>/orcamentos" class="btn btn-light me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary px-5">
                <?= isset($orcamento) ? 'Atualizar Orçamento' : 'Salvar Orçamento' ?>
            </button>
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
            placeholder: "Selecione o Perfume...",
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
        totalSpan.textContent = 'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
    }

    // Inicializa o total se houver itens
    calculateTotal();

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
            row.querySelector('.preco-input').value = preco || 0;
        }
    });

    calculateTotal();
});
</script>

<?php include '../App/Views/partials/footer.php'; ?>
