<?php

/**
 * ============================================================
 * CitasController — CRUD completo de Citas
 * ============================================================
 * URLs manejadas:
 *   GET  /citas                  → index()
 *   GET  /citas/ver/5            → ver(5)
 *   GET  /citas/crear            → crear()
 *   POST /citas/crear            → crear()
 *   GET  /citas/editar/5         → editar(5)
 *   POST /citas/editar/5         → editar(5)
 *   GET  /citas/eliminar/5       → eliminar(5)
 *   GET  /citas/cambiarEstado/5  → cambiarEstado(5)
 *   GET  /citas/calendario       → calendario() AJAX JSON
 *   GET  /citas/buscadorcitas    → buscadorcitas() AJAX
 * ============================================================
 */

class CitasController extends Controller
{
    private object $citaModel;
    private object $contactoModel;

    // ─────────────────────────────────────────────────────────
    public function __construct()
    {
        parent::__construct();
        $this->citaModel     = $this->model('Cita');
        $this->contactoModel = $this->model('Contacto');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * INDEX — Lista todos los Citas
     * ─────────────────────────────────
     * URL    : GET /citas
     * Vista  : app/views/citas/index.php
     *
     * Obtiene todos los citas con su categoría y
     * los pasa a la vista para
     * mostrarlos en una tabla con acciones CRUD.
     * Ademas en fecha 28/04/26 se agrego para el paginador
     */
    public function index(): void
    {
        // Lee porpagina de la URL — default 5
        // Valida que sea una opción permitida
        $opcionesValidas = [5, 15, 50];
        $porPagina       = (int) ($_GET['porpagina'] ?? 5);

        if (!in_array($porPagina, $opcionesValidas)) {
            $porPagina = 5;
        }

        $paginaActual = (int) ($_GET['pagina'] ?? 1);
        $total        = $this->citaModel->contarTodos();
        $paginador    = new Paginador($total, $porPagina, $paginaActual);

        $citas = $this->citaModel->getAllPaginado(
            $paginador->getPorPagina(),
            $paginador->getOffset()
        );

        // Conteo por estado para los badges del tab
        $conteos = [
            'todas'      => count($citas),
            'Pendiente'  => 0,
            'Confirmada' => 0,
            'Cancelada'  => 0,
        ];
        foreach ($citas as $c) {
            $conteos[$c['estado']]++;
        }
                
        $this->view('citas/index', [
            'titulo'    => 'Citas',
            'citas' => $citas,
            'paginador' => $paginador,
            'conteos'   => $conteos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VER — Detalle completo de una cita
     * URL: GET /citas/ver/5
     */
    public function ver(int $id): void
    {
        $cita = $this->citaModel->getById($id);

        if (!$cita) {
            $this->flash('warning', 'La cita no existe o fue eliminada.');
            $this->redirect('citas');
        }

        $this->view('citas/ver', [
            'titulo' => $cita['titulo'],
            'cita'   => $cita
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CREAR — Formulario y procesamiento
     * GET : /citas/crear → formulario con Flatpickr + TomSelect
     * POST: /citas/crear → valida y guarda
     */
    public function crear(): void
    {
        // Necesitamos la lista de contactos para el selector
        $contactos = $this->contactoModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'id_contacto' => (int) ($_POST['id_contacto'] ?? 0),
                'titulo'      => trim($_POST['titulo']      ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_cita'  => trim($_POST['fecha_cita']  ?? ''),
                'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
                'hora_fin'    => trim($_POST['hora_fin']    ?? ''),
                'tipo'        => trim($_POST['tipo']        ?? 'Reunion'),
                'estado'      => trim($_POST['estado']      ?? 'Pendiente'),
            ];

            // ── Validaciones ────────────────────────────────
            $error = $this->validar($datos);

            if ($error) {
                $this->view('citas/crear', [
                    'titulo'     => 'Nueva Cita',
                    'contactos'  => $contactos,
                    'error'      => $error,
                    'datos'      => $datos
                ]);
                return;
            }

            $this->citaModel->crear($datos);

            $this->flash('success',
                "Cita <strong>{$datos['titulo']}</strong> creada correctamente."
            );
            $this->redirect('citas');
        }

        $this->view('citas/crear', [
            'titulo'    => 'Nueva Cita',
            'contactos' => $contactos
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EDITAR — Formulario y procesamiento
     * GET : /citas/editar/5
     * POST: /citas/editar/5
     */
    public function editar(int $id): void
    {
        $cita      = $this->citaModel->getById($id);
        $contactos = $this->contactoModel->getAll();

        if (!$cita) {
            $this->flash('warning', 'La cita no existe o fue eliminada.');
            $this->redirect('citas');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'id_contacto' => (int) ($_POST['id_contacto'] ?? 0),
                'titulo'      => trim($_POST['titulo']      ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_cita'  => trim($_POST['fecha_cita']  ?? ''),
                'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
                'hora_fin'    => trim($_POST['hora_fin']    ?? ''),
                'tipo'        => trim($_POST['tipo']        ?? 'Reunion'),
                'estado'      => trim($_POST['estado']      ?? 'Pendiente'),
            ];

            $error = $this->validar($datos);

            if ($error) {
                $this->view('citas/editar', [
                    'titulo'    => 'Editar Cita',
                    'contactos' => $contactos,
                    'cita'      => $cita,
                    'error'     => $error
                ]);
                return;
            }

            $this->citaModel->actualizar($id, $datos);

            $this->flash('info',
                "Cita <strong>{$datos['titulo']}</strong> actualizada correctamente."
            );
            $this->redirect('citas/ver/' . $id);
        }

        $this->view('citas/editar', [
            'titulo'    => 'Editar Cita',
            'contactos' => $contactos,
            'cita'      => $cita
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ELIMINAR
     * URL: GET /citas/eliminar/5
     */
    public function eliminar(int $id): void
    {
        $cita = $this->citaModel->getById($id);

        if ($cita) {
            $this->citaModel->eliminar($id);
            $this->flash('danger',
                "Cita <strong>{$cita['titulo']}</strong> eliminada correctamente."
            );
        } else {
            $this->flash('warning', 'La cita no existe o ya fue eliminada.');
        }

        $this->redirect('citas');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CAMBIAR ESTADO — Cambia entre Pendiente/Confirmada/Cancelada
     * URL: GET /citas/cambiarEstado/5?estado=Confirmada
     *
     * Permite cambiar el estado directamente desde la lista
     * sin entrar al formulario de edición completo.
     */
    public function cambiarEstado(int $id): void
    {
        $cita   = $this->citaModel->getById($id);
        $estado = trim($_GET['estado'] ?? '');

        if ($cita && $estado) {
            $this->citaModel->cambiarEstado($id, $estado);
            $this->flash('info',
                "Cita <strong>{$cita['titulo']}</strong>
                 marcada como <strong>{$estado}</strong>."
            );
        }

        $this->redirect('citas');
    }

    // ─────────────────────────────────────────────────────────
    /**
     * CALENDARIO — Endpoint JSON para FullCalendar
     * URL: GET /citas/calendario
     *
     * NO valida X-Requested-With porque FullCalendar
     * no envía ese header en sus peticiones fetch().
     * Retorna array de eventos en formato FullCalendar.
     */
    public function calendario(): void
    {
        $eventos = $this->citaModel->getParaCalendario();

        header('Content-Type: application/json');
        echo json_encode($eventos);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * BUSCADORCITAS — Endpoint AJAX para autocompletado
     * URL: GET /citas/buscadorcitas?q=reunion
     */
    public function buscadorcitas(): void
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

        $resultados = $this->citaModel->buscar($termino);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit();
    }

    // ─────────────────────────────────────────────────────────
    /**
     * VALIDAR — Método privado compartido
     * Evita duplicar validaciones entre crear() y editar()
     *
     * @param  array       $datos
     * @return string|null Error o null si todo OK
     */
    private function validar(array $datos): ?string
    {
        if (empty($datos['titulo'])) {
            return 'El título de la cita es obligatorio.';
        }
        if ($datos['id_contacto'] <= 0) {
            return 'Debes seleccionar un contacto.';
        }
        if (empty($datos['fecha_cita'])) {
            return 'La fecha de la cita es obligatoria.';
        }
        if (empty($datos['hora_inicio'])) {
            return 'La hora de inicio es obligatoria.';
        }
        if ($datos['hora_fin'] && $datos['hora_fin'] <= $datos['hora_inicio']) {
            return 'La hora de fin debe ser posterior a la hora de inicio.';
        }
        return null;
    }

    //28/04/26 Metodos para Exportador
    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR EXCEL — Todas las citas
     * ─────────────────────────────────
     * URL: GET /citas/exportarexcel
     *
     * Exporta TODAS las citas con datos del contacto.
     * Incluye: título, contacto, fecha, horario,
     *          tipo, estado y descripción.
     */
    public function exportarexcel(): void
    {
        $citas = $this->citaModel->getAll();

        Exportador::excel(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Apellido Contacto',
                'contacto_nombre'   => 'Nombre Contacto',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Hora Inicio',
                'hora_fin'          => 'Hora Fin',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
                'descripcion'       => 'Descripción',
            ],
            'Listado de Citas — Agenda MVC',
            'citas_' . date('Ymd_His')
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR PDF — Todas las citas
     * ────────────────────────────────
     * URL: GET /citas/exportarpdf
     */
    public function exportarpdf(): void
    {
        $citas = $this->citaModel->getAll();

        Exportador::pdf(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Apellido',
                'contacto_nombre'   => 'Nombre',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Hora Ini',
                'hora_fin'          => 'Hora Fin',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
            ],
            'Listado de Citas — Agenda MVC',
            'citas_' . date('Ymd_His')
        );
    }
 
    //Para reportes WORD e Imagen 28/04/26 
    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR WORD — Todas las citas
     * URL: GET /citas/exportarword
     */
    public function exportarword(): void
    {
        $citas = $this->citaModel->getAll();

        Exportador::word(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Apellido',
                'contacto_nombre'   => 'Nombre',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Inicio',
                'hora_fin'          => 'Fin',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
            ],
            'Listado de Citas — Agenda MVC',
            'citas_' . date('Ymd_His')
        );
    }

    // ─────────────────────────────────────────────────────────
    /**
     * EXPORTAR IMAGEN — Infografía de citas
     * URL: GET /citas/exportarimagen
     */
    public function exportarimagen(): void
    {
        $citas = $this->citaModel->getAll();

        // ── Estadísticas ─────────────────────────────────────
        $total      = count($citas);
        $pendientes = count(array_filter($citas,
            fn($c) => $c['estado'] === 'Pendiente'));
        $confirmadas = count(array_filter($citas,
            fn($c) => $c['estado'] === 'Confirmada'));
        $canceladas = count(array_filter($citas,
            fn($c) => $c['estado'] === 'Cancelada'));
        $reuniones  = count(array_filter($citas,
            fn($c) => $c['tipo'] === 'Reunion'));

        $stats = [
            [
                'label' => 'Total Citas',
                'valor' => $total,
                'color' => '#0d6efd'
            ],
            [
                'label' => 'Pendientes',
                'valor' => $pendientes,
                'color' => '#ffc107'
            ],
            [
                'label' => 'Confirmadas',
                'valor' => $confirmadas,
                'color' => '#28a745'
            ],
            [
                'label' => 'Canceladas',
                'valor' => $canceladas,
                'color' => '#dc3545'
            ],
            [
                'label' => 'Reuniones',
                'valor' => $reuniones,
                'color' => '#6f42c1'
            ],
        ];

        Exportador::imagen(
            $citas,
            [
                'titulo'            => 'Título',
                'contacto_apellido' => 'Contacto',
                'fecha_cita'        => 'Fecha',
                'hora_inicio'       => 'Inicio',
                'tipo'              => 'Tipo',
                'estado'            => 'Estado',
            ],
            $stats,
            'Infografía Citas — Agenda MVC',
            'citas_infografia_' . date('Ymd_His')
        );
    }

    //29/04/26 PAra Drag and Drop (arrastrar y soltar)    
    // ─────────────────────────────────────────────────────────
    /**
     * KANBAN — Vista de seguimiento de citas
     * ───────────────────────────────────────
     * URL: GET /citas/kanban
     *
     * Muestra las citas agrupadas en 3 columnas:
     * Pendiente | Confirmada | Cancelada
     * Con Drag & Drop para cambiar estado.
     */
    public function kanban(): void
    {
        // Obtener citas agrupadas por estado
        $pendientes  = $this->citaModel->getPorEstado('Pendiente');
        $confirmadas = $this->citaModel->getPorEstado('Confirmada');
        $canceladas  = $this->citaModel->getPorEstado('Cancelada');

        $this->view('citas/kanban', [
            'titulo'      => 'Seguimiento de Citas',
            'pendientes'  => $pendientes,
            'confirmadas' => $confirmadas,
            'canceladas'  => $canceladas,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    /**
     * ACTUALIZARESTADO — Endpoint AJAX para Drag & Drop
     * ───────────────────────────────────────────────────
     * URL: POST /citas/actualizarestado
     *
     * Recibe via POST:
     *   id_cita : int    → ID de la cita arrastrada
     *   estado  : string → Nuevo estado (Pendiente|Confirmada|Cancelada)
     *
     * Retorna JSON:
     *   {'ok': true}  → guardado correctamente
     *   {'ok': false} → error
     *
     * 🎓 NOTA: Es POST porque modifica datos en BD.
     * No requiere header AJAX especial porque
     * lo enviamos con fetch() desde el JS.
     */
    public function actualizarestado(): void
    {
        header('Content-Type: application/json');

        // Verificar que sea petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit();
        }

        // Leer datos JSON del body de la petición
        $body    = file_get_contents('php://input');
        $datos   = json_decode($body, true);

        $idCita  = (int)  ($datos['id_cita'] ?? 0);
        $estado  = trim($datos['estado']     ?? '');

        // Validar
        $estadosValidos = ['Pendiente', 'Confirmada', 'Cancelada'];

        if ($idCita <= 0 || !in_array($estado, $estadosValidos)) {
            echo json_encode([
                'ok'    => false,
                'error' => 'Datos inválidos'
            ]);
            exit();
        }

        // Verificar que la cita existe
        $cita = $this->citaModel->getById($idCita);
        if (!$cita) {
            echo json_encode([
                'ok'    => false,
                'error' => 'Cita no encontrada'
            ]);
            exit();
        }

        // Guardar nuevo estado en BD
        $this->citaModel->cambiarEstado($idCita, $estado);

        echo json_encode([
            'ok'     => true,
            'estado' => $estado,
            'cita'   => $cita['titulo']
        ]);
        exit();
    }

}