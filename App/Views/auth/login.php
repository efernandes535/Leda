<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-container {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo i {
            font-size: 50px;
            background: -webkit-linear-gradient(#fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-logo h1 {
            font-weight: 700;
            letter-spacing: -1px;
            margin-top: 10px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 12px;
            padding: 12px 15px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-login {
            background: linear-gradient(90deg, #ff00cc, #3333ff);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .bg-shapes div {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
        }

        .shape-1 { width: 300px; height: 300px; top: -150px; right: -150px; }
        .shape-2 { width: 200px; height: 200px; bottom: -100px; left: -100px; }
        .shape-3 { width: 100px; height: 100px; top: 20%; left: 10%; }
    </style>
</head>
<body>

    <div class="bg-shapes">
        <div class="shape-1"></div>
        <div class="shape-2"></div>
        <div class="shape-3"></div>
    </div>

    <div class="login-container">
        <div class="glass-card">
            <div class="login-logo">
                <i class="bi bi-gem"></i>
                <h1>Leda</h1>
                <p class="text-white-50 small">Perfume Inventory Management</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger border-0 bg-danger text-white py-2 small" role="alert">
                    <?= $_SESSION['error'] ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="<?= URL_BASE ?>/autenticar" method="POST">
                <div class="mb-3">
                    <label class="form-label small">Usuário ou E-mail</label>
                    <input type="text" name="usuario" class="form-control" placeholder="admin" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login">Entrar no Sistema</button>
            </form>
            
            <div class="text-center mt-4">
                <p class="small text-white-50">© 2026 Leda - Todos os direitos reservados</p>
            </div>
        </div>
    </div>

</body>
</html>
