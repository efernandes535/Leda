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
                            <tr onclick="abrirModalDetalhes(<?= $p['id'] ?>)" style="cursor: pointer;" class="produto-row">
                                <td><?= $p['id'] ?></td>
                                <td><small class="text-muted"><?= $p['sku'] ?: '-' ?></small></td>
                                <td class="text-uppercase"><?= $p['nome'] ?></td>
                                <td class="text-uppercase"><?= $p['categoria_nome'] ?? 'Sem categoria' ?></td>
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
                                        <a href="<?= URL_BASE ?>/estoque/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Editar" onclick="event.stopPropagation();">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($p['ativo'] == 1): ?>
                                            <a href="<?= URL_BASE ?>/estoque/excluir/<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning" title="Arquivar Produto" onclick="event.stopPropagation(); return confirm('Deseja arquivar este produto? Ele não aparecerá mais em novas vendas.')">
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

<!-- Modal de Detalhes do Produto -->
<div class="modal fade" id="modalDetalhesProduto" tabindex="-1" aria-labelledby="modalDetalhesProdutoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalhesProdutoLabel">Detalhes do Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>Nome:</strong> <span id="detalheNome" class="text-uppercase"></span></p>
            <p><strong>Referência/SKU:</strong> <span id="detalheSku"></span></p>
            <p><strong>Categoria:</strong> <span id="detalheCategoria" class="text-uppercase"></span></p>
            <p><strong>Descrição:</strong> <span id="detalheDescricao"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Preço de Compra:</strong> <span id="detalhePrecoCompra"></span></p>
            <p><strong>Preço de Venda:</strong> <span id="detalhePrecoVenda"></span></p>
            <p><strong>Quantidade Total:</strong> <span id="detalheQuantidade"></span></p>
            <p><strong>Estoque Mínimo:</strong> <span id="detalheMinimo"></span></p>
          </div>
        </div>
        
        <h6 class="border-bottom pb-2 mt-4">Quantidade por Lote</h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered">
            <thead class="table-light">
              <tr>
                <th>Lote</th>
                <th>Validade</th>
                <th>Quantidade Disponível</th>
              </tr>
            </thead>
            <tbody id="tabelaLotesBody">
              <!-- Lotes serão inseridos aqui -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
function abrirModalDetalhes(id) {
    document.getElementById('detalheNome').textContent = 'Carregando...';
    document.getElementById('detalheSku').textContent = '';
    document.getElementById('detalheCategoria').textContent = '';
    document.getElementById('detalheDescricao').textContent = '';
    document.getElementById('detalhePrecoCompra').textContent = '';
    document.getElementById('detalhePrecoVenda').textContent = '';
    document.getElementById('detalheQuantidade').textContent = '';
    document.getElementById('detalheMinimo').textContent = '';
    document.getElementById('tabelaLotesBody').innerHTML = '<tr><td colspan="3" class="text-center">Carregando lotes...</td></tr>';
    
    var modal = new bootstrap.Modal(document.getElementById('modalDetalhesProduto'));
    modal.show();
    
    fetch('<?= URL_BASE ?>/estoque/detalhes/' + id, { credentials: 'same-origin' })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.error) {
                alert(data.error);
                modal.hide();
                return;
            }
            
            const p = data.produto;
            document.getElementById('detalheNome').textContent = p.nome;
            document.getElementById('detalheSku').textContent = p.sku || '-';
            document.getElementById('detalheCategoria').textContent = p.categoria_nome || '-';
            document.getElementById('detalheDescricao').textContent = p.descricao || '-';
            
            const formatMoeda = valor => Number(valor).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
            
            document.getElementById('detalhePrecoCompra').textContent = formatMoeda(p.preco_compra);
            document.getElementById('detalhePrecoVenda').textContent = formatMoeda(p.preco_venda);
            
            document.getElementById('detalheQuantidade').textContent = p.quantidade;
            document.getElementById('detalheMinimo').textContent = p.estoque_minimo;
            
            const lotes = data.lotes;
            const tbody = document.getElementById('tabelaLotesBody');
            tbody.innerHTML = '';
            
            if(lotes && lotes.length > 0) {
                lotes.forEach(lote => {
                    const tr = document.createElement('tr');
                    
                    let dataFormatada = '-';
                    if(lote.data_validade) {
                        const partes = lote.data_validade.split('-');
                        if(partes.length === 3) {
                            dataFormatada = `${partes[2]}/${partes[1]}/${partes[0]}`;
                        } else {
                            dataFormatada = lote.data_validade;
                        }
                    }
                    
                    tr.innerHTML = `
                        <td>${lote.lote || '-'}</td>
                        <td>${dataFormatada}</td>
                        <td>${lote.quantidade_disponivel}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhum lote com quantidade disponível.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Erro ao buscar detalhes:', error);
            document.getElementById('detalheNome').textContent = 'Erro ao carregar dados.';
            document.getElementById('tabelaLotesBody').innerHTML = '';
        });
}
</script>

<style>
.produto-row:hover {
    background-color: rgba(0,0,0,0.05);
}
</style>

<?php include '../App/Views/partials/footer.php'; ?>
