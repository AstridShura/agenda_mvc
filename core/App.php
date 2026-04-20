<?php

/**
 * ============================================================
 * CLASE APP — El Router
 * ============================================================
 * Lee la URL y determina:
 *   - Qué Controller instanciar
 *   - Qué método llamar
 *   - Qué parámetros pasarle
 *
 * Ejemplo:
 *   URL: /contactos/ver/5
 *        → Controller : ContactosController
 *        → Método     : ver()
 *        → Parámetro  : 5
 * ============================================================
 */

class App
{
    // Nombre del controller por defecto (string)
    protected string $controllerName = 'ContactosController';

    // Instancia del controller (objeto)
    protected object $controllerInstance;

    // Método por defecto
    protected string $method = 'index';

    // Parámetros opcionales en la URL
    protected array $params = [];

    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        // 1. Leer y limpiar la URL
        $url = $this->parseUrl();

        // 2. Determinar el Controller
        $this->resolveController($url);

        // 3. Determinar el Método
        $this->resolveMethod($url);

        // 4. Determinar los Parámetros
        $this->params = $url ? array_values($url) : [];

        // 5. Ejecutar: llama al método del controller
        call_user_func_array(
            [$this->controllerInstance, $this->method],
            $this->params
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Limpia y divide la URL en segmentos.
     *
     * /contactos/ver/5  →  ['contactos', 'ver', '5']
     */
    private function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Determina qué Controller cargar según el primer
     * segmento de la URL.
     */
    private function resolveController(array &$url): void
    {
        if (empty($url[0])) {
            // URL vacía → Controller por defecto
            $this->loadController($this->controllerName);
            return;
        }

        // 'contactos' → 'ContactosController'
        $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
        $controllerFile = ROOT_PATH . '/app/controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            $this->controllerName = $controllerName;
            require_once $controllerFile;
            $this->controllerInstance = new $controllerName();
        } else {
            $this->handle404();
            return;
        }

        // Remueve el primer segmento ya procesado
        array_shift($url);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Determina qué método del Controller llamar según
     * el segundo segmento de la URL.
     */
    private function resolveMethod(array &$url): void
    {
        if (!empty($url[0]) && method_exists($this->controllerInstance, $url[0])) {
            $this->method = $url[0];
            array_shift($url);
        }
        // Si no existe el método en la URL usa index() por defecto
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Carga el controller por defecto.
     */
    private function loadController(string $name): void
    {
        $file = ROOT_PATH . '/app/controllers/' . $name . '.php';

        if (file_exists($file)) {
            require_once $file;
            $this->controllerInstance = new $name();
        } else {
            $this->handle404();
        }
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Maneja rutas no encontradas.
     */
    private function handle404(): void
    {
        http_response_code(404);
        die('<h1>404 - Página no encontrada</h1>');
    }
}
?>