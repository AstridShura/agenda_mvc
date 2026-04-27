<?php

/**
 * ============================================================
 * AuthController — Login y Logout 27/0426
 * ============================================================
 * Es una ruta PÚBLICA — no requiere sesión activa.
 * Declarado en $rutasPublicas de Controller base.
 *
 * URLs:
 *   GET  /auth/login  → muestra formulario
 *   POST /auth/login  → procesa credenciales
 *   GET  /auth/logout → cierra sesión
 * ============================================================
 */

class AuthController extends Controller
{
    private object $usuarioModel;

    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        // Llama al padre — inicia sesión y verifica auth
        // verificarSesion() detecta que es AuthController
        // y lo deja pasar sin redirigir ✅
        parent::__construct();
        $this->usuarioModel = $this->model('Usuario');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * LOGIN — Formulario y procesamiento
     * ────────────────────────────────────
     * GET : /auth/login → muestra formulario
     * POST: /auth/login → valida credenciales
     *
     * Flujo POST:
     *   1. Busca usuario por email en BD
     *   2. Verifica password con password_verify()
     *   3. Verifica que el usuario esté activo
     *   4. Guarda datos en SESSION
     *   5. Redirige a /contactos
     */
    public function login(): void
    {
        // Si ya está autenticado → redirigir directo
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('contactos');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            // ── Validación básica de campos ─────────────────
            if (empty($email) || empty($password)) {
                $this->viewLogin('Email y password son obligatorios.');
                return;
            }

            // ── Buscar usuario por email ────────────────────
            $usuario = $this->usuarioModel->getByEmail($email);

            // ── Verificar que existe ────────────────────────
            if (!$usuario) {
                // Mensaje genérico — no revelar si el email existe
                $this->viewLogin('Credenciales incorrectas.');
                return;
            }

            // ── Verificar password con hash BCRYPT ──────────
            if (!password_verify($password, $usuario['password'])) {
                $this->viewLogin('Credenciales incorrectas.');
                return;
            }

            // ── Verificar que el usuario esté activo ────────
            if (!$usuario['activo']) {
                $this->viewLogin('Tu cuenta está desactivada. Contacta al administrador.');
                return;
            }

            // ── Todo OK — crear sesión ───────────────────────
            // Regenerar ID de sesión previene ataques
            // de Session Fixation
            session_regenerate_id(true);

            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' .
                                          $usuario['apellido'];
            $_SESSION['usuario_rol']    = $usuario['rol'];
            $_SESSION['usuario_email']  = $usuario['email'];

            $this->flash(
                'success',
                "Bienvenido <strong>{$usuario['nombre']}</strong>. 👋"
            );
            $this->redirect('contactos');
        }

        // GET → mostrar formulario vacío
        $this->viewLogin();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * LOGOUT — Cierra la sesión
     * URL: GET /auth/logout
     *
     * Destruye completamente la sesión PHP
     * y redirige al login.
     */
    public function logout(): void
    {
        // Vaciar todas las variables de sesión
        $_SESSION = [];

        // Destruir la cookie de sesión
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'],   $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // Destruir la sesión en el servidor
        session_destroy();

        // Redirigir al login
        header('Location: ' . BASE_URL . '/auth/login');
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Helper privado para mostrar el login con o sin error.
     * Evita duplicar código entre GET y POST.
     *
     * @param string|null $error Mensaje de error opcional
     */
    private function viewLogin(?string $error = null): void
    {
        // El login usa su propio layout minimalista
        // sin navbar — para eso usamos viewSinLayout()
        $data = ['error' => $error];
        extract($data);

        $viewFile = ROOT_PATH . '/app/views/auth/login.php';
        require_once $viewFile;
        exit();
    }
}