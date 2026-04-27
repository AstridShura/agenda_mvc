<?php

class Controller
{
    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        //Habilitacion para control de sesiones y autenticacion 27/04/26 
        $this->verificarSesion();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VERIFICAR SESIÓN
     * ──────────────────
     * Se ejecuta en cada petición.
     * Usa get_class($this) para saber qué controller
     * está siendo instanciado en este momento.
     *
     * Si es AuthController → dejar pasar sin verificar.
     * Si no hay SESSION   → redirigir al login.
     */
    private function verificarSesion(): void
    {
        // Controllers que NO requieren autenticación
        $rutasPublicas = ['AuthController'];

        // get_class($this) retorna el nombre real
        // del controller hijo que está ejecutándose
        // Ej: 'ContactosController', 'AuthController'
        $controllerActual = get_class($this);

        // Si es ruta pública → dejar pasar
        if (in_array($controllerActual, $rutasPublicas)) {
            return;
        }

        // Si no hay sesión activa → redirect al login
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Retorna datos del usuario autenticado desde SESSION.
     * Disponible en todos los controllers hijos.
     */
    protected function usuarioActual(): array
    {
        return [
            'id'     => $_SESSION['usuario_id']     ?? null,
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'rol'    => $_SESSION['usuario_rol']    ?? 'usuario',
        ];
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Carga y renderiza una Vista dentro del layout.
     * ob_start() captura el HTML de la vista
     * y lo inyecta en $contenido del layout.
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("<h3>Vista no encontrada: <code>{$view}.php</code></h3>");
        }

        ob_start();
        require_once $viewFile;
        $contenido = ob_get_clean();

        $layoutFile = ROOT_PATH . '/app/views/layouts/main.php';
        require_once $layoutFile;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Carga e instancia un Model.
     */
    protected function model(string $model): object
    {
        $modelFile = ROOT_PATH . '/app/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        die("<h3>Modelo no encontrado: <code>{$model}.php</code></h3>");
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Redirecciona a una URL relativa a BASE_URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Guarda un mensaje flash en SESSION.
     * tipos: success | danger | warning | info
     */
    protected function flash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = [
            'tipo'    => $tipo,
            'mensaje' => $mensaje
        ];
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Lee y borra el flash de SESSION.
     * Solo se muestra UNA vez.
     */
    public static function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
}