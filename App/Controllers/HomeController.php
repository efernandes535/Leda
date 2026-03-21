<?php

namespace App\Controllers;

use App\Models\Venda;
use App\Models\Produto;

class HomeController extends Controller {
    private $vendaModel;
    private $produtoModel;

    public function __construct() {
        parent::__construct();
        $this->vendaModel = new Venda();
        $this->produtoModel = new Produto();
    }

    public function index() {
        $stats = [
            'faturamento_mes' => $this->vendaModel->getFaturamentoMes(),
            'recebiveis_pendentes' => $this->vendaModel->getRecebiveisPendentes(),
            'baixo_estoque' => $this->produtoModel->getBaixoEstoqueCount(),
            'ultimas_vendas' => $this->vendaModel->getUltimasVendas(5),
            'top_vendidos' => $this->produtoModel->getTopVendidos(5)
        ];

        $this->view('home', [
            'title' => 'Dashboard de Indicadores',
            'stats' => $stats
        ]);
    }
}
