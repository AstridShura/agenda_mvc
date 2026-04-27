<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Agenda MVC</title>

    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        .login-header {
            background: linear-gradient(135deg, #0f3460, #16213e);
            border-radius: 16px 16px 0 0;
            padding: 2rem;
            text-align: center;
        }

        .login-icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .form-control:focus {
            border-color: #0f3460;
            box-shadow: 0 0 0 .25rem rgba(15,52,96,.2);
        }

        .btn-login {
            background: linear-gradient(135deg, #0f3460, #16213e);
            border: none;
            padding: .75rem;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .5px;
            transition: opacity .2s;
        }

        .btn-login:hover { opacity: .9; }
    </style>
</head>
<body>

<div class="login-card card mx-3">

    <!-- Header -->
    <div class="login-header">
        <div class="login-icon">
            <i class="bi bi-journal-text text-white"></i>
        </div>
        <h4 class="text-white fw-bold mb-1">Agenda MVC</h4>
        <p class="text-white-50 mb-0" style="font-size:.9rem">
            Ingresa tus credenciales para continuar
        </p>
    </div>

    <!-- Body -->
    <div class="card-body p-4">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/auth/login">

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted">
                    <i class="bi bi-envelope me-1"></i>Email
                </label>
                <input  type="email"
                        name="email"
                        class="form-control form-control-lg"
                        placeholder="usuario@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autofocus
                        required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">
                    <i class="bi bi-lock me-1"></i>Password
                </label>
                <div class="input-group">
                    <input  type="password"
                            name="password"
                            id="loginPassword"
                            class="form-control form-control-lg"
                            placeholder="••••••••"
                            required>
                    <button class="btn btn-outline-secondary"
                            type="button"
                            onclick="toggleLoginPass()"
                            title="Mostrar/ocultar">
                        <i class="bi bi-eye" id="iconLoginPass"></i>
                    </button>
                </div>
            </div>

            <!-- Botón -->
            <div class="d-grid">
                <button type="submit" class="btn btn-login btn-primary text-white">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </button>
            </div>

        </form>
    </div>

    <!-- Footer -->
    <div class="card-footer text-center text-muted py-3"
         style="border-radius:0 0 16px 16px; font-size:.85rem">
        <i class="bi bi-shield-lock me-1"></i>
        Acceso restringido — Solo usuarios autorizados
    </div>

</div>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script>
function toggleLoginPass() {
    var input = document.getElementById('loginPassword');
    var icon  = document.getElementById('iconLoginPass');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

</body>
</html>