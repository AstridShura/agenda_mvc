<?php

/**
 * ============================================================
 * UsuariosController
 * ============================================================
 * Maneja todas las peticiones HTTP relacionadas a Usuarios.
 * Hereda de Controller: view(), model(), redirect(), flash()
 *
 * URLs manejadas:
 *   GET  /usuarios              → index()
 *   GET  /usuarios/ver/5        → ver(5)
 *   GET  /usuarios/crear        → crear()
 *   POST /usuarios/crear        → crear()
 *   GET  /usuarios/editar/5     → editar(5)
 *   POST /usuarios/editar/5     → editar(5)
 *   GET  /usuarios/eliminar/5   → eliminar(5)
 *   GET  /usuarios/buscadorusu  → buscadorusu() ← AJAX
 * ============================================================
 */

class UsuariosController extends Controller
{
    private object $usuarioModel;

    // ─────────────────────────────────────────────────────────
    /**
     * Constructor
     * Se ejecuta automáticamente al instanciar el Controller.
     * - Llama al padre para iniciar la sesión (flash messages)
     * - Carga los 3 modelos que usará este controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel  = $this->model('Usuario');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todos los usuarios
     * ─────────────────────────────────
     * URL    : GET /usuarios
     * Vista  : app/views/usuarios/index.php
     *
     * Obtiene todos los usuarios, los pasa a la vista para
     * mostrarlos en una tabla con acciones CRUD.
     */
    public function index(): void
    {
        $usuarios = $this->usuarioModel->getAll();

        $this->view('usuarios/index', [
            'titulo'    => 'Mis usuarios',
            'usuarios' => $usuarios
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VER — Detalle completo de un usuario
     * ───────────────────────────────────────
     * URL    : GET /usuarios/ver/5
     * Vista  : app/views/usuarios/ver.php
     * Param  : $id → ID del usuario en la BD
     *
     * Carga el usuario. Si el ID no existe redirige con mensaje warning.
     */
    public function ver(int $id): void
    {
        // Busca el usuario por ID
        $usuario  = $this->usuarioModel->getById($id);

        // Si no existe → flash warning y vuelve al listado
        if (!$usuario) {
            $this->flash('warning', 'El usuario no existe o fue eliminado.');
            $this->redirect('usuarios');
        }

        $this->view('usuarios/ver', [
            'titulo'    => $usuario['nombre'] . ' ' . $usuario['apellido'],
            'usuario' => $usuario
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento de nuevo usuario
     * ──────────────────────────────────────────────────────
     * URL GET : /usuarios/crear  → muestra formulario vacío
     * URL POST: /usuarios/crear  → procesa y guarda datos
     * Vista   : app/views/usuarios/crear.php
     *
     * Flujo POST:
     *   1. Recoge y sanitiza datos del formulario
     *   2. Valida campos obligatorios
     *   3. Guarda el usuario → obtiene nuevo ID
     *   4. Flash success + redirect al listado
     */
    public function crear(): void
    {
        // Siempre necesitamos los usuarios para el select
        $usuarios = $this->usuarioModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── 1. Recoger y sanitizar ──────────────────────
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'apellido'     => trim($_POST['apellido']     ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'usuario'      => trim($_POST['usuario']      ?? ''),
                'password'     => trim($_POST['password']     ?? ''),           
                'rol'          => trim($_POST['rol']          ?? 'usuario'),
                'activo'       => isset($_POST['activo']) ? 1 : 0
                // Sin fecha_alta, ya que SQL Server lo pone automaticamente
            ];

            // ── 2. Validar campos obligatorios ──────────────
            if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email'])|| empty($datos['usuario'])|| empty($datos['password'])) {
                // Recarga el formulario con el error y los datos
                // que ya había escrito el usuario
                $this->view('usuarios/crear', [
                    'titulo'     => 'Nuevo Usuario',
                    'error'      => 'Todos los campos son obligatorios.',
                    'datos'      => $datos
                ]);
                return;
            }

            // ── 3. Guardar usuario → obtener nuevo ID ──────
            $this->usuarioModel->crear($datos);

            // ── 4. Flash + redirect ─────────────────────────
            $this->flash(
                'success',
                "Usuario <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 creado correctamente."
            );
            $this->redirect('usuarios');
        }

        // ── GET: mostrar formulario vacío ───────────────────
        $this->view('usuarios/crear', [
            'titulo'     => 'Nuevo Usuario'
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento de edición
     * ────────────────────────────────────────────────
     * URL GET : /usuarios/editar/5  → muestra formulario
     * URL POST: /usuarios/editar/5  → procesa y guarda
     * Vista   : app/views/usuarios/editar.php
     * Param   : $id → ID del usuario a editar
     *
     * Flujo POST:
     *   1. Recoge y sanitiza datos del formulario
     *   2. Valida campos obligatorios
     *   3. Flash info + redirect al detalle
     */
    public function editar(int $id): void
    {
        // Verifica que el usuario existe
        $usuario   = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $this->flash('warning', 'El usuario no existe o fue eliminado.');
            $this->redirect('usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── 1. Recoger y sanitizar ──────────────────────
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'apellido'     => trim($_POST['apellido']     ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'usuario'      => trim($_POST['usuario']      ?? ''),
                'rol'          => trim($_POST['rol']          ?? 'usuario'),
                'activo'       => isset($_POST['activo']) ? 1 : 0
                // Sin password — se cambia aparte
                // Sin fecha_alta — no debe cambiar
            ];

            // ── 2. Validar ──────────────────────────────────
            if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || empty($datos['usuario']) ) {
                $this->view('usuarios/editar', [
                    'titulo'     => 'Editar Usuario',
                    'usuario'    => $usuario,
                    'error'      => 'Los campos Nombre, Apellido, Email y Usuario son obligatorios.'
                ]);
                return;
            }

            // ── 3. Actualizar usuario ──────────────────────
            $this->usuarioModel->actualizar($id, $datos);

            // ── 4. Flash + redirect al detalle ──────────────
            $this->flash(
                'info',
                "Usuario <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 actualizado correctamente."
            );
            $this->redirect('usuarios/ver/' . $id);
        }

        // ── GET: mostrar formulario con datos actuales ──────
        $this->view('usuarios/editar', [
            'titulo'     => 'Editar Usuario',
            'usuario'    => $usuario
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR — Elimina un contacto y sus teléfonos
     * ────────────────────────────────────────────────
     * URL   : GET /usuarios/eliminar/5
     * Param : $id → ID del contacto a eliminar
     *
     * Los teléfonos se eliminan automáticamente por la
     * restricción ON DELETE CASCADE definida en SQL Server.
     * Siempre redirige al listado con mensaje flash.
     */
    public function eliminar(int $id): void
    {
        // Obtiene el usuario antes de borrarlo
        // para poder usar su nombre en el flash
        $usuario = $this->usuarioModel->getById($id);

        if ($usuario) {
            $this->usuarioModel->eliminar($id);

            $this->flash(
                'danger',
                "Usuario <strong>{$usuario['nombre']} {$usuario['apellido']}</strong>
                 eliminado correctamente."
            );
        } else {
            $this->flash('warning', 'El usuario no existe o ya fue eliminado.');
        }

        $this->redirect('usuarios');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * BUSCADORAJAX — Endpoint AJAX para autocompletado
     * ──────────────────────────────────────────────────
     * URL    : GET /usuarios/buscadorajax?q=juan
     * Retorna: JSON — NO carga ninguna vista
     *
     * Seguridad:
     *   - Solo acepta peticiones con header X-Requested-With
     *   - Mínimo 2 caracteres para ejecutar la búsqueda
     *   - Los parámetros van con LIKE en PDO (sin SQL injection)
     *
     * Flujo:
     *   JS escribe → fetch() con header AJAX →
     *   este método → Model::buscar() → SQL Server →
     *   json_encode() → JS pinta dropdown
     */
    public function buscadorusu(): void
    {
        // ── Seguridad: solo peticiones AJAX ─────────────────
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit();
        }

        // ── Obtener y validar término de búsqueda ───────────
        $termino = trim($_GET['q'] ?? '');

        // Mínimo 2 caracteres para evitar consultas masivas
        if (strlen($termino) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        // ── Ejecutar búsqueda en el Model ───────────────────
        // Busca en: nombre, apellido, alias, email
        $resultados = $this->usuarioModel->buscajax($termino);

        // ── Retornar JSON al JavaScript ─────────────────────
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }
}
?>