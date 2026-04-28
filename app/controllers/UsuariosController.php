<?php

/**
 * ============================================================
 * UsuariosController
 * ============================================================
 * Maneja todas las peticiones HTTP relacionadas a Usuarios.
 * Hereda de Controller: view(), model(), redirect(), flash()
 *
 * URLs manejadas:
 *   GET  /usuarios                    → index()
 *   GET  /usuarios/ver/5              → ver(5)
 *   GET  /usuarios/crear              → crear()
 *   POST /usuarios/crear              → crear()
 *   GET  /usuarios/editar/5           → editar(5)
 *   POST /usuarios/editar/5           → editar(5)
 *   GET  /usuarios/eliminar/5         → eliminar(5)
 *   GET  /usuarios/cambiarpassword/5  → cambiarpassword(5)
 *   POST /usuarios/cambiarpassword/5  → cambiarpassword(5)
 *   GET  /usuarios/buscadorusu        → buscadorusu() AJAX
 * ============================================================
 */

class UsuariosController extends Controller
{
    private object $usuarioModel;

    // ─────────────────────────────────────────────────────────
    /**
     * Constructor
     * Llama al padre para iniciar sesión (flash messages)
     * y carga el modelo de usuarios.
     */
    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = $this->model('Usuario');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todos los usuarios
     * URL: GET /usuarios
     * En fecha 27/04/26 se adiciono para soportar paginacion de Usuarios
     */
    public function index(): void
    {
        $opcionesValidas = [5, 15, 50];
        $porPagina       = (int) ($_GET['porpagina'] ?? 5);

        if (!in_array($porPagina, $opcionesValidas)) {
            $porPagina = 5;
        }

        $paginaActual = (int) ($_GET['pagina'] ?? 1);
        $total        = $this->usuarioModel->contarTodos();
        $paginador    = new Paginador($total, $porPagina, $paginaActual);

        $usuarios = $this->usuarioModel->getAllPaginado(
            $paginador->getPorPagina(),
            $paginador->getOffset()
        );

        $this->view('usuarios/index', [
            'titulo'    => 'Mis Usuarios',
            'usuarios'  => $usuarios,
            'paginador' => $paginador
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VER — Detalle completo de un usuario
     * URL: GET /usuarios/ver/5
     */
    public function ver(int $id): void
    {
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $this->flash('warning', 'El usuario no existe o fue eliminado.');
            $this->redirect('usuarios');
        }

        $this->view('usuarios/ver', [
            'titulo'  => $usuario['nombre'] . ' ' . $usuario['apellido'],
            'usuario' => $usuario
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento
     * GET : /usuarios/crear → formulario vacío
     * POST: /usuarios/crear → guarda usuario
     */
    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'nombre'   => trim($_POST['nombre']   ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'email'    => trim($_POST['email']    ?? ''),
                'usuario'  => trim($_POST['usuario']  ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'rol'      => trim($_POST['rol']      ?? 'usuario'),
                'activo'   => isset($_POST['activo']) ? 1 : 0
            ];

            if (empty($datos['nombre'])   || empty($datos['apellido']) ||
                empty($datos['email'])    || empty($datos['usuario'])   ||
                empty($datos['password'])) {
                $this->view('usuarios/crear', [
                    'titulo' => 'Nuevo Usuario',
                    'error'  => 'Todos los campos son obligatorios.',
                    'datos'  => $datos
                ]);
                return;
            }

            $this->usuarioModel->crear($datos);

            $this->flash('success',
                "Usuario <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 creado correctamente."
            );
            $this->redirect('usuarios');
        }

        $this->view('usuarios/crear', [
            'titulo' => 'Nuevo Usuario'
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento
     * GET : /usuarios/editar/5 → formulario con datos
     * POST: /usuarios/editar/5 → guarda cambios
     * Sin password — se cambia en formulario separado
     */
    public function editar(int $id): void
    {
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $this->flash('warning', 'El usuario no existe o fue eliminado.');
            $this->redirect('usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'nombre'   => trim($_POST['nombre']   ?? ''),
                'apellido' => trim($_POST['apellido'] ?? ''),
                'email'    => trim($_POST['email']    ?? ''),
                'usuario'  => trim($_POST['usuario']  ?? ''),
                'rol'      => trim($_POST['rol']      ?? 'usuario'),
                'activo'   => isset($_POST['activo']) ? 1 : 0
            ];

            if (empty($datos['nombre'])  || empty($datos['apellido']) ||
                empty($datos['email'])   || empty($datos['usuario'])) {
                $this->view('usuarios/editar', [
                    'titulo'  => 'Editar Usuario',
                    'usuario' => $usuario,
                    'error'   => 'Nombre, Apellido, Email y Usuario son obligatorios.'
                ]);
                return;
            }

            $this->usuarioModel->actualizar($id, $datos);

            $this->flash('info',
                "Usuario <strong>{$datos['nombre']} {$datos['apellido']}</strong>
                 actualizado correctamente."
            );
            $this->redirect('usuarios/ver/' . $id);
        }

        $this->view('usuarios/editar', [
            'titulo'  => 'Editar Usuario',
            'usuario' => $usuario
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR — Elimina un usuario por ID
     * URL: GET /usuarios/eliminar/5
     */
    public function eliminar(int $id): void
    {
        $usuario = $this->usuarioModel->getById($id);

        if ($usuario) {
            $this->usuarioModel->eliminar($id);
            $this->flash('danger',
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
     * CAMBIARPASSWORD — Formulario separado de password
     * ───────────────────────────────────────────────────
     * GET : /usuarios/cambiarpassword/5 → muestra formulario
     * POST: /usuarios/cambiarpassword/5 → valida y actualiza
     *
     * 🎓 NOTA: Nombre en minúscula porque el Router convierte
     * la URL a minúsculas antes de buscar el método.
     * URL /cambiarpassword → busca cambiarpassword() ✅
     *
     * Validaciones:
     *   1. Password actual correcto (password_verify)
     *   2. Password nuevo mínimo 8 caracteres
     *   3. Confirmación coincide con el nuevo
     */
    public function cambiarpassword(int $id): void
    {
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $this->flash('warning', 'El usuario no existe o fue eliminado.');
            $this->redirect('usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $passwordActual  = trim($_POST['password_actual']  ?? '');
            $passwordNuevo   = trim($_POST['password_nuevo']   ?? '');
            $passwordConfirm = trim($_POST['password_confirm'] ?? '');

            // ── Validación 1: password actual correcto ──────
            if (!password_verify($passwordActual, $usuario['password'])) {
                $this->view('usuarios/cambiar_password', [
                    'titulo'  => 'Cambiar Password',
                    'usuario' => $usuario,
                    'error'   => 'El password actual es incorrecto.'
                ]);
                return;
            }

            // ── Validación 2: mínimo 8 caracteres ───────────
            if (strlen($passwordNuevo) < 8) {
                $this->view('usuarios/cambiar_password', [
                    'titulo'  => 'Cambiar Password',
                    'usuario' => $usuario,
                    'error'   => 'El nuevo password debe tener al menos 8 caracteres.'
                ]);
                return;
            }

            // ── Validación 3: confirmación coincide ──────────
            if ($passwordNuevo !== $passwordConfirm) {
                $this->view('usuarios/cambiar_password', [
                    'titulo'  => 'Cambiar Password',
                    'usuario' => $usuario,
                    'error'   => 'El nuevo password y la confirmación no coinciden.'
                ]);
                return;
            }

            // ── Actualizar con nuevo hash BCRYPT ─────────────
            $this->usuarioModel->cambiarpassword($id, $passwordNuevo);

            $this->flash('success',
                "Password de <strong>{$usuario['nombre']} {$usuario['apellido']}</strong>
                 actualizado correctamente."
            );
            $this->redirect('usuarios/ver/' . $id);
        }

        // GET → mostrar formulario
        $this->view('usuarios/cambiar_password', [
            'titulo'  => 'Cambiar Password',
            'usuario' => $usuario
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * BUSCADORUSU — Endpoint AJAX para autocompletado
     * URL: GET /usuarios/buscadorusu?q=juan
     */
    public function buscadorusu(): void
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(403);
            exit();
        }

        $termino = trim($_GET['q'] ?? '');

        if (strlen($termino) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        $resultados = $this->usuarioModel->buscajax($termino);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }

    //28/04/26 Para Exportador 
    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR EXCEL — Todos los usuarios
     * ───────────────────────────────────────
     * URL: GET /usuarios/exportarexcel
     *
     * Exporta TODOS los usuarios sin paginación.
     * Incluye: nombre, apellido, alias, email,
     *          categoría, teléfonos y fecha de alta.
     */
    public function exportarexcel(): void
    {
        // Obtiene TODOS — sin paginación
        $usuarios = $this->usuarioModel->getAll();

        Exportador::excel(
            $usuarios,
            [
                'nombre'          => 'Nombre',            
                'apellido'        => 'Apellido',
                'email'           => 'Email',
                'usuario'         => 'Usuario',
                'rol'             => 'Rol',
            ],
            'Listado de Usuarios — Agenda MVC',
            'usuarios_' . date('Ymd_His')
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR PDF — Todos los usuarios
     * ────────────────────────────────────
     * URL: GET /usuarios/exportarpdf
     */
    public function exportarpdf(): void
    {
        $usuarios = $this->usuarioModel->getAll();

        Exportador::pdf(
            $usuarios,
            [
                'nombre'          => 'Nombre',            
                'apellido'        => 'Apellido',
                'email'           => 'Email',
                'usuario'         => 'Usuario',
                'rol'             => 'Rol',
            ],
            'Listado de Usuarios — Agenda MVC',
            'usuarios_' . date('Ymd_His')
        );
    }        

    // 28/04/26 Para Cambio de Tema
    // ──────────────────────────────────────────────────────────
    /**
     * CAMBIATEMA — Alterna entre tema claro y oscuro
     * ────────────────────────────────────────────────
     * URL: GET /usuarios/cambiatema
     *
     * 🎓 NOTA: nombre en minúscula por convención del Router.
     *
     * Flujo:
     *   1. Lee tema actual de SESSION
     *   2. Calcula el tema opuesto
     *   3. Guarda en BD para persistir entre sesiones
     *   4. Actualiza SESSION para efecto inmediato
     *   5. Redirige a la página anterior
     */
    public function cambiatema(): void
    {
        // Verificar que hay usuario autenticado
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth/login');
        }

        // ── Calcular tema opuesto ────────────────────────────
        $temaActual = $_SESSION['usuario_tema'] ?? 'claro';
        $temaNuevo  = $temaActual === 'claro' ? 'oscuro' : 'claro';

        // ── Guardar en BD ────────────────────────────────────
        // Así persiste aunque el usuario cierre el browser
        $this->usuarioModel->guardarTema(
            $_SESSION['usuario_id'],
            $temaNuevo
        );

        // ── Actualizar SESSION ───────────────────────────────
        // Para efecto inmediato sin necesidad de re-login
        $_SESSION['usuario_tema'] = $temaNuevo;

        // ── Redirigir a página anterior ──────────────────────
        // HTTP_REFERER contiene la URL de donde vino el usuario
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/contactos';
        header('Location: ' . $referer);
        exit();
    }    

}