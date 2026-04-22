<?php

/**
 * ============================================================
 * CLASE BASE — Controller
 * ============================================================
 * Todos los controllers de la app heredan de esta clase.
 * Provee métodos comunes como cargar vistas y modelos.
 * ============================================================
 */

class Controller
{

    public function __construct()
    {
        // Inicia sesión si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Carga y renderiza una Vista.
     *
     * Uso desde un Controller hijo:
     *   $this->view('contactos/index', ['contactos' => $data]);
     *
     * @param string $view   Ruta relativa a /app/views/
     * @param array  $data   Variables que estarán disponibles en la vista
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
     * Carga e instancia un Modelo.
     *
     * Uso desde un Controller hijo:
     *   $this->model('Contacto');
     *   → carga /app/models/Contacto.php
     *   → retorna instancia de la clase Contacto
     *
     * @param  string $model  Nombre de la clase Model
     * @return object         Instancia del modelo
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
     * Redirecciona a otra URL de la app.
     *
     * Uso:
     *   $this->redirect('contactos/index');
     *
     * @param string $url  Ruta relativa desde BASE_URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Guarda un mensaje flash en SESSION.
     *
     * Uso en cualquier Controller hijo:
     *   $this->flash('success', 'Contacto creado correctamente.');
     *   $this->flash('danger',  'Error al eliminar el contacto.');
     *   $this->flash('warning', 'No se encontraron resultados.');
     *   $this->flash('info',    'No hubo cambios que guardar.');
     *
     * Tipos Bootstrap válidos:
     *   success | danger | warning | info
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
     * Lee y borra el mensaje flash de SESSION.
     * Retorna null si no hay mensaje pendiente.
     *
     * Se llama SOLO desde el layout main.php
     */
    public static function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        // Lee el mensaje
        $flash = $_SESSION['flash'];

        // Lo borra inmediatamente — solo se muestra UNA vez
        unset($_SESSION['flash']);

        return $flash;
    }
}
?>