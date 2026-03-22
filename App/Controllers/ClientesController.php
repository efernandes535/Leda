<?php

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Venda;

class ClientesController extends Controller {
    private $clienteModel;

    public function __construct() {
        parent::__construct();
        $this->clienteModel = new Cliente();
    }

    public function index() {
        $clientes = $this->clienteModel->all();
        // Para cada cliente, vamos anexar o saldo devedor (lógica simplificada para a lista)
        foreach ($clientes as &$c) {
            $c['saldo_devedor'] = $this->clienteModel->getSaldoDevedor($c['id']);
        }
        $this->view('clientes/index', [
            'title' => 'Gestão de Clientes',
            'clientes' => $clientes
        ]);
    }

    public function novo() {
        $this->view('clientes/form', ['title' => 'Novo Cliente']);
    }

    public function editar($id) {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            $this->redirect('/clientes');
        }
        $this->view('clientes/form', [
            'title' => 'Editar Cliente: ' . $cliente['nome'],
            'cliente' => $cliente
        ]);
    }

    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'telefone' => $_POST['telefone'],
                'endereco' => $_POST['endereco']
            ];

            if ($id) {
                if ($this->clienteModel->update($id, $data)) {
                    $this->redirect('/clientes');
                }
            } else {
                if ($this->clienteModel->create($data)) {
                    $this->redirect('/clientes');
                }
            }
        }
    }

    public function detalhes($id) {
        $cliente = $this->clienteModel->find($id);
        $historico = $this->clienteModel->getHistorico($id);
        $saldo = $this->clienteModel->getSaldoDevedor($id);

        $this->view('clientes/detalhes', [
            'title' => 'Perfil do Cliente: ' . $cliente['nome'],
            'cliente' => $cliente,
            'historico' => $historico,
            'saldo' => $saldo
        ]);
    }
}
