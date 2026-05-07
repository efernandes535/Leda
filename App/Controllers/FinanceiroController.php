<?php
namespace App\Controllers;

use App\Models\Financeiro;

class FinanceiroController extends Controller {
    private $financeiroModel;

    public function __construct() {
        parent::__construct();
        $this->financeiroModel = new Financeiro();
    }

    public function index() {
        $mes = $_GET['mes'] ?? date('m');
        $ano = $_GET['ano'] ?? date('Y');
        
        $resumo = $this->financeiroModel->getResumoDashboard($mes, $ano);
        
        $this->view('financeiro/dashboard', [
            'title' => 'Dashboard Financeiro',
            'resumo' => $resumo,
            'mes' => $mes,
            'ano' => $ano
        ]);
    }

    public function pagar() {
        $status = $_GET['status'] ?? 'pendente';
        $contas = $this->financeiroModel->getContasPagar($status);

        $this->view('financeiro/pagar', [
            'title' => 'Contas a Pagar',
            'contas' => $contas,
            'statusFiltro' => $status
        ]);
    }

    public function receber() {
        $status = $_GET['status'] ?? 'pendente';
        $contas = $this->financeiroModel->getContasReceber($status);

        $this->view('financeiro/receber', [
            'title' => 'Contas a Receber',
            'contas' => $contas,
            'statusFiltro' => $status
        ]);
    }

    // Ações para Contas a Pagar
    public function baixarPagar($id) {
        $data_pagamento = $_POST['data_pagamento'] ?? date('Y-m-d');
        if ($this->financeiroModel->baixarPagar($id, $data_pagamento)) {
            $_SESSION['success'] = "Conta Paga registrada com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao registrar pagamento.";
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? URL_BASE . '/financeiro/pagar'));
        exit;
    }

    public function estornarPagar($id) {
        if ($this->financeiroModel->estornarPagar($id)) {
            $_SESSION['success'] = "Baixa estornada com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao estornar.";
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? URL_BASE . '/financeiro/pagar'));
        exit;
    }

    // Ações para Contas a Receber
    public function baixarReceber($id) {
        $data_pagamento = $_POST['data_pagamento'] ?? date('Y-m-d');
        if ($this->financeiroModel->baixarReceber($id, $data_pagamento)) {
            $_SESSION['success'] = "Recebimento registrado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao registrar recebimento.";
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? URL_BASE . '/financeiro/receber'));
        exit;
    }

    public function estornarReceber($id) {
        if ($this->financeiroModel->estornarReceber($id)) {
            $_SESSION['success'] = "Recebimento estornado com sucesso!";
        } else {
            $_SESSION['error'] = "Erro ao estornar.";
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? URL_BASE . '/financeiro/receber'));
        exit;
    }
}
