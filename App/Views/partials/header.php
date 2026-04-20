<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= URL_BASE ?>/public/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= URL_BASE ?>">
            <img src="<?= URL_BASE ?>/public/img/logo.png" alt="Logo" height="30" class="me-2 rounded">
            <?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/estoque">Estoque</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/entradas">Entradas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/categorias">Categorias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/clientes">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/vendas">Vendas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= URL_BASE ?>/orcamentos">Orçamentos</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?= $_SESSION['usuario_nome'] ?? 'Usuário' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="<?= URL_BASE ?>/logout"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_backorder'])): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> <?= $_SESSION['success_backorder'] ?>
            <a href="<?= URL_BASE ?>/orcamentos" class="alert-link">Ver Orçamentos</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_backorder']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
