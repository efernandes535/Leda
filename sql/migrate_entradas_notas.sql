CREATE TABLE IF NOT EXISTS entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fornecedor VARCHAR(255) NULL,
    numero_nota VARCHAR(100) NULL,
    data_emissao DATE NULL,
    valor_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    observacoes TEXT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS itens_entrada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entrada_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    lote VARCHAR(50) NULL,
    data_validade DATE NULL,
    FOREIGN KEY (entrada_id) REFERENCES entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
);

-- Migração de dados antigos
-- Cria uma "Nota" genérica para agrupar as entradas antigas, se a tabela existir e tiver registros
INSERT INTO entradas (fornecedor, numero_nota, data_emissao, valor_total, observacoes)
SELECT 'Migração Sistema Antigo', 'S/N', CURDATE(), COALESCE(SUM(quantidade * preco_compra), 0), 'Entradas registradas individualmente antes da atualização do sistema.'
FROM entradas_estoque
HAVING COUNT(*) > 0;

-- Pega o ID dessa nova entrada genérica (só haverá uma se tivermos inserido)
SET @last_id = LAST_INSERT_ID();

-- Insere os itens vinculados a essa nova entrada
INSERT INTO itens_entrada (entrada_id, produto_id, quantidade, preco_unitario, lote, data_validade)
SELECT @last_id, produto_id, quantidade, COALESCE(preco_compra, 0), lote, data_validade 
FROM entradas_estoque
WHERE @last_id > 0;

-- Renomeia a tabela antiga para não confundir e manter backup
RENAME TABLE entradas_estoque TO entradas_estoque_legacy;
