<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Agenda MVC' ?> | Agenda</title>

    <!-- Bootstrap 5 LOCAL -->
    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons LOCAL -->
    <link href="<?= BASE_URL ?>/assets/css/bootstrap-icons.css" rel="stylesheet">

    <style>
        body        { background-color: #f8f9fa; }
        .navbar     { background: linear-gradient(135deg, #1a1a2e, #16213e); }
        .card       { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .badge-tipo { font-size: .75rem; }
        .btn-action { width: 32px; height: 32px; padding: 0;
                      display:inline-flex; align-items:center;
                      justify-content:center; }
    </style>

    <!-- Agregado para Citas -->
    <!-- Flatpickr — date/time picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- TomSelect — buscador de contactos en select -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css">
    <!-- FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- TomSelect JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────── -->
<nav class="navbar navbar-dark navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/contactos">
            <i class="bi bi-journal-text me-2"></i>Agenda MVC
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white" href="<?= BASE_URL ?>/contactos">
                <i class="bi bi-people me-1"></i>Contactos
            </a>
            <a class="nav-link text-white ms-2" href="<?= BASE_URL ?>/contactos/crear">
                <i class="bi bi-person-plus me-1"></i>Nuevo
            </a>
           <!-- Opcion adicionada 22/04/26-->
                <!-- ── Enlace nuevo ── -->
            <a class="nav-link text-white ms-2" href="<?= BASE_URL ?>/categorias">
                <i class="bi bi-tags me-1"></i>Categorías
            </a>
           <!-- Opcion adicionada 23/04/26-->
                <!-- ── Enlace nuevo ── -->
            <a class="nav-link text-white ms-2" href="<?= BASE_URL ?>/usuarios">
                <i class="bi bi-tags me-1"></i>Usuarios
            </a>
            <!-- Agregado 27/04/26 para Citas-->            
            <a class="nav-link text-white ms-2" href="<?= BASE_URL ?>/citas">
                <i class="bi bi-calendar2-week me-1"></i>Citas
            </a>
        </div>
    </div>
</nav>

<!-- ── CONTENIDO PRINCIPAL ────────────────────── -->
<main class="container pb-5">

    <?php
    // ── Mensaje Flash ─────────────────────────────────────
    // Inicia sesión si no está activa (necesario en el layout)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $flash = Controller::getFlash();
    if ($flash):
    ?>
    <div class="alert alert-<?= $flash['tipo'] ?> alert-dismissible fade show
                shadow-sm d-flex align-items-center gap-2"
         role="alert"
         id="flashMsg">

        <?php
        $iconos = [
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-trash-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'info'    => 'bi-pencil-fill',
        ];
        $icono = $iconos[$flash['tipo']] ?? 'bi-info-circle-fill';
        ?>

        <i class="bi <?= $icono ?> fs-5"></i>
        <span><?= $flash['mensaje'] ?></span>

        <button type="button"
                class="btn-close ms-auto"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
        </button>
    </div>
    <?php endif; ?>

    <?= $contenido ?>

</main>

<!-- Bootstrap JS LOCAL -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>

<!-- ── Teléfonos dinámicos ────────────────────── -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    var contenedor    = document.getElementById('contenedorTelefonos');
    var btnAgregarTel = document.getElementById('btnAgregarTel');

    if (btnAgregarTel && contenedor) {

        btnAgregarTel.addEventListener('click', function () {
            var filaBase  = contenedor.querySelector('.fila-telefono');
            var nuevaFila = filaBase.cloneNode(true);
            nuevaFila.querySelector('input[name="numeros[]"]').value = '';
            nuevaFila.querySelector('select[name="tipos[]"]').selectedIndex = 0;
            contenedor.appendChild(nuevaFila);
        });

        contenedor.addEventListener('click', function (e) {
            var btn = e.target.closest('.btnEliminarTel');
            if (!btn) return;
            var filas = contenedor.querySelectorAll('.fila-telefono');
            if (filas.length > 1) {
                btn.closest('.fila-telefono').remove();
            } else {
                contenedor.querySelector('input[name="numeros[]"]').value = '';
            }
        });
    }
});
</script>

<!-- ── Buscador dinámico con autocompletado 21/04/26 ── -->

<script>
(function () {

    var input      = document.getElementById('inputBuscar');
    var resultados = document.getElementById('resultadosBusqueda');
    var btnLimpiar = document.getElementById('btnLimpiar');
    var tabla      = document.getElementById('tablaContactos');
    var timer      = null;

    if (!input) return;

    input.addEventListener('keyup', function () {
        var q = input.value.trim();

        clearTimeout(timer);
        btnLimpiar.style.display = q.length > 0 ? 'block' : 'none';

        if (q.length < 2) {
            ocultarResultados();
            mostrarTabla();
            return;
        }

        timer = setTimeout(function () {
            buscarajax(q);
        }, 300);
    });

    btnLimpiar.addEventListener('click', function () {
        input.value = '';
        btnLimpiar.style.display = 'none';
        ocultarResultados();
        mostrarTabla();
        input.focus();
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !resultados.contains(e.target)) {
            ocultarResultados();
        }
    });

    function buscarajax(q) {
        resultados.style.display = 'block';
        resultados.innerHTML =
            '<div class="p-3 text-center text-muted">' +
            '<div class="spinner-border spinner-border-sm me-2"></div>' +
            'Buscando...</div>';

        fetch('<?= BASE_URL ?>/contactos/buscadorajax?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { pintarResultados(data, q); })
        .catch(function ()   { ocultarResultados(); });
    }

    function pintarResultados(data, q) {
        if (data.length === 0) {
            resultados.style.display = 'block';
            resultados.innerHTML =
                '<div class="p-3 text-center text-muted">' +
                '<i class="bi bi-person-x me-2"></i>' +
                'No se encontraron contactos para <strong>' +
                escHtml(q) + '</strong></div>';
            ocultarTabla();
            return;
        }

        var html = '';
        data.forEach(function (c) {
            var nombreCompleto = escHtml(c.apellido) + ', ' + escHtml(c.nombre);
            var email    = c.email
                ? '<small class="text-muted d-block">' + escHtml(c.email) + '</small>'
                : '';
            var alias    = c.alias
                ? ' <span class="text-muted">(' + escHtml(c.alias) + ')</span>'
                : '';                
            var catBadge = c.categoria
                ? '<span class="badge ms-2" style="background:' +
                  c.categoria_color + ';font-size:.7rem">' +
                  escHtml(c.categoria) + '</span>'
                : '';

            html +=
                '<a href="<?= BASE_URL ?>/contactos/ver/' + c.id + '" ' +
                '   class="d-block px-3 py-2 text-decoration-none text-dark ' +
                '          border-bottom item-resultado">' +
                '  <div class="d-flex align-items-center">' +
                '    <i class="bi bi-person-circle me-2 text-primary fs-5"></i>' +
                '    <div>' +
                '      <span class="fw-semibold">' + resaltar(nombreCompleto, q) + '</span>' +
                       alias + catBadge + email +
                '    </div>' +
                '  </div>' +
                '</a>';
        });

        html += '<div class="px-3 py-2 bg-light text-muted" style="font-size:.8rem">' +
                '<i class="bi bi-info-circle me-1"></i>' +
                data.length + ' resultado(s) encontrado(s)</div>';

        resultados.innerHTML = html;
        resultados.style.display = 'block';
        ocultarTabla();
    }

    function resaltar(texto, q) {
        var regex = new RegExp('(' + escRegex(q) + ')', 'gi');
        return texto.replace(regex,
            '<mark class="p-0" style="background:#fff3cd">$1</mark>');
    }

    function ocultarResultados() {
        resultados.style.display = 'none';
        resultados.innerHTML = '';
    }

    function ocultarTabla() {
        if (tabla) tabla.style.display = 'none';
    }

    function mostrarTabla() {
        if (tabla) tabla.style.display = 'block';
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function escRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

})();
</script>

<!-- ── Auto-cierre del mensaje flash ─────────── -->
<script>
(function () {
    var flash = document.getElementById('flashMsg');
    if (!flash) return;

    setTimeout(function () {
        flash.classList.remove('show');
        flash.classList.add('fade');
        setTimeout(function () {
            flash.style.display = 'none';
        }, 300);
    }, 4000);
})();
</script>

<!-- 22/04/26 Adicionado para controlar Color Picker -->
<!-- Color picker — preview en tiempo real -->
    <script>
    (function () {
        var picker  = document.getElementById('colorPicker');
        var hex     = document.getElementById('colorHex');
        var preview = document.getElementById('previewBadge');
        var nombre  = document.getElementById('nombreInput');

        if (!picker) return; // Solo activo en páginas de categorías

        // Actualiza el HEX y el preview cuando cambia el color
        picker.addEventListener('input', function () {
            var color = picker.value;
            if (hex)     hex.value              = color;
            if (preview) preview.style.background = color;
        });
    })();
    </script>

    <!-- 22/04/26-->
    <!-- Buscador dinámico de Categorias con Autocompletado -->
    <script>
    (function () {

        var input      = document.getElementById('inputBuscarCat');
        var resultados = document.getElementById('resultadosBusquedaCat');
        var btnLimpiar = document.getElementById('btnLimpiarCat');
        var tabla      = document.getElementById('tablaCategorias');
        var timer      = null;

        if (!input) return; // Solo activo en la página de categorías

        input.addEventListener('keyup', function () {
            var q = input.value.trim();

            clearTimeout(timer);
            btnLimpiar.style.display = q.length > 0 ? 'block' : 'none';

            if (q.length < 2) {
                ocultarResultados();
                mostrarTabla();
                return;
            }

            timer = setTimeout(function () {
                buscar(q);
            }, 300);
        });

        btnLimpiar.addEventListener('click', function () {
            input.value = '';
            btnLimpiar.style.display = 'none';
            ocultarResultados();
            mostrarTabla();
            input.focus();
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultados.contains(e.target)) {
                ocultarResultados();
            }
        });

        function buscar(q) {
            resultados.style.display = 'block';
            resultados.innerHTML =
                '<div class="p-3 text-center text-muted">' +
                '<div class="spinner-border spinner-border-sm me-2"></div>' +
                'Buscando...</div>';

            fetch('<?= BASE_URL ?>/categorias/buscadorcat?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) { pintarResultados(data, q); })
            .catch(function () { ocultarResultados(); });
        }

        function pintarResultados(data, q) {
            if (data.length === 0) {
                resultados.innerHTML =
                    '<div class="p-3 text-center text-muted">' +
                    '<i class="bi bi-tags me-2"></i>' +
                    'No se encontraron categorías para <strong>' +
                    escHtml(q) + '</strong></div>';
                resultados.style.display = 'block';
                ocultarTabla();
                return;
            }

            var html = '';
            data.forEach(function (c) {
                html +=
                    '<a href="<?= BASE_URL ?>/categorias/editar/' + c.id + '" ' +
                    '   class="d-block px-3 py-2 text-decoration-none text-dark border-bottom">' +
                    '  <div class="d-flex align-items-center gap-2">' +
                    '    <span style="display:inline-block;width:20px;height:20px;' +
                    '          background:' + escHtml(c.color) + ';border-radius:50%;' +
                    '          border:1px solid #dee2e6;flex-shrink:0"></span>' +
                    '    <span class="fw-semibold">' + resaltar(escHtml(c.nombre), q) + '</span>' +
                    '    <code class="ms-auto text-muted" style="font-size:.75rem">' +
                        escHtml(c.color) + '</code>' +
                    '  </div>' +
                    '</a>';
            });

            html += '<div class="px-3 py-2 bg-light text-muted" style="font-size:.8rem">' +
                    '<i class="bi bi-info-circle me-1"></i>' +
                    data.length + ' resultado(s) encontrado(s)</div>';

            resultados.innerHTML = html;
            resultados.style.display = 'block';
            ocultarTabla();
        }

        function resaltar(texto, q) {
            var regex = new RegExp('(' + escRegex(q) + ')', 'gi');
            return texto.replace(regex,
                '<mark class="p-0" style="background:#fff3cd">$1</mark>');
        }

        function ocultarResultados() {
            resultados.style.display = 'none';
            resultados.innerHTML = '';
        }

        function ocultarTabla() {
            if (tabla) tabla.style.display = 'none';
        }

        function mostrarTabla() {
            if (tabla) tabla.style.display = 'block';
        }

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function escRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

    })();
    </script>

    <!-- 23/04/26 Buscador dinámico de Usuarios con Autocompletado -->
    <script>
    (function () {

        var input      = document.getElementById('inputBuscarUsu');
        var resultados = document.getElementById('resultadosBusquedaUsu');
        var btnLimpiar = document.getElementById('btnLimpiarUsu');
        var tabla      = document.getElementById('tablaUsuarios');
        var timer      = null;

        if (!input) return; // Solo activo en la página de categorías

        input.addEventListener('keyup', function () {
            var q = input.value.trim();

            clearTimeout(timer);
            btnLimpiar.style.display = q.length > 0 ? 'block' : 'none';

            if (q.length < 2) {
                ocultarResultados();
                mostrarTabla();
                return;
            }

            timer = setTimeout(function () {
                buscar(q);
            }, 300);
        });

        btnLimpiar.addEventListener('click', function () {
            input.value = '';
            btnLimpiar.style.display = 'none';
            ocultarResultados();
            mostrarTabla();
            input.focus();
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultados.contains(e.target)) {
                ocultarResultados();
            }
        });

        function buscar(q) {
            resultados.style.display = 'block';
            resultados.innerHTML =
                '<div class="p-3 text-center text-muted">' +
                '<div class="spinner-border spinner-border-sm me-2"></div>' +
                'Buscando...</div>';

            fetch('<?= BASE_URL ?>/usuarios/buscadorusu?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) { pintarResultados(data, q); })
            .catch(function () { ocultarResultados(); });
        }

        function pintarResultados(data, q) {
            if (data.length === 0) {
                resultados.innerHTML =
                    '<div class="p-3 text-center text-muted">' +
                    '<i class="bi bi-tags me-2"></i>' +
                    'No se encontraron usuarios para <strong>' +
                    escHtml(q) + '</strong></div>';
                resultados.style.display = 'block';
                ocultarTabla();
                return;
            }

            var html = '';
            data.forEach(function (c) {
                const tono = Math.floor(Math.random() * 360);
                const colorAleatorio = `hsl(${tono}, 70%, 60%)`; 
                html +=
                    '<a href="<?= BASE_URL ?>/usuarios/editar/' + c.id + '" ' +
                    '   class="d-block px-3 py-2 text-decoration-none text-dark border-bottom">' +
                    '  <div class="d-flex align-items-center gap-2">' +
                    '    <span style="display:inline-block;width:20px;height:20px;' +
                    '          background:' + colorAleatorio + ';border-radius:50%;' +
                    '          border:1px solid #dee2e6;flex-shrink:0"></span>' +
                    '    <span class="fw-semibold">' + resaltar(escHtml(c.nombre), q)+ ', '+ resaltar(escHtml(c.apellido), q) + '</span>' +
                    '    <code class="ms-auto text-muted" style="font-size:.75rem">' +
                        escHtml(c.email) + '</code>' +
                    '  </div>' +
                    '</a>';
            });

            html += '<div class="px-3 py-2 bg-light text-muted" style="font-size:.8rem">' +
                    '<i class="bi bi-info-circle me-1"></i>' +
                    data.length + ' resultado(s) encontrado(s)</div>';

            resultados.innerHTML = html;
            resultados.style.display = 'block';
            ocultarTabla();
        }

        function resaltar(texto, q) {
            var regex = new RegExp('(' + escRegex(q) + ')', 'gi');
            return texto.replace(regex,
                '<mark class="p-0" style="background:#fff3cd">$1</mark>');
        }

        function ocultarResultados() {
            resultados.style.display = 'none';
            resultados.innerHTML = '';
        }

        function ocultarTabla() {
            if (tabla) tabla.style.display = 'none';
        }

        function mostrarTabla() {
            if (tabla) tabla.style.display = 'block';
        }

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function escRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

    })();
    </script>
    
    <!-- Para Citas 27/04/26-->
    <!-- ── Citas: Flatpickr + TomSelect + FullCalendar ── -->
    <script>
    (function () {

        // ── Flatpickr — selector de fecha ─────────────────────
        var fechas = document.querySelectorAll('.flatpickr-fecha');
        fechas.forEach(function (el) {
            flatpickr(el, {
                locale      : 'es',
                dateFormat  : 'Y-m-d',
                altInput    : true,
                altFormat   : 'd/m/Y',
                minDate     : 'today',
                allowInput  : false
            });
        });

        // ── Flatpickr — selector de hora ──────────────────────
        var horas = document.querySelectorAll('.flatpickr-hora');
        horas.forEach(function (el) {
            flatpickr(el, {
                enableTime  : true,
                noCalendar  : true,
                dateFormat  : 'H:i',
                time_24hr   : true,
                minuteIncrement: 15,
                allowInput  : false
            });
        });

        // ── TomSelect — selector de contactos ─────────────────
        var selectContacto = document.getElementById('selectContacto');
        if (selectContacto) {
            new TomSelect(selectContacto, {
                placeholder : 'Buscar contacto...',
                allowEmptyOption: true
            });
        }

        // ── Toggle Tabla / Calendario ─────────────────────────
        window.mostrarVista = function (vista) {
            var tabla      = document.getElementById('vistaTbla');
            var calendario = document.getElementById('vistaCalendario');
            var btnTabla   = document.getElementById('btnVistTabla');
            var btnCal     = document.getElementById('btnVistaCalendario');

            if (!tabla) return;

            if (vista === 'tabla') {
                tabla.style.display      = 'block';
                calendario.style.display = 'none';
                btnTabla.classList.add('active');
                btnCal.classList.remove('active');
            } else {
                tabla.style.display      = 'none';
                calendario.style.display = 'block';
                btnTabla.classList.remove('active');
                btnCal.classList.add('active');
                iniciarCalendario();
            }
        };

        // ── FullCalendar ──────────────────────────────────────
        var calendarObj = null;

        function iniciarCalendario() {
            var el = document.getElementById('calendarioCitas');
            if (!el) return;

            // Solo inicializar una vez
            if (calendarObj) {
                calendarObj.render();
                return;
            }

            calendarObj = new FullCalendar.Calendar(el, {
                initialView  : 'dayGridMonth',
                locale       : 'es',
                headerToolbar: {
                    left  : 'prev,next today',
                    center: 'title',
                    right : 'dayGridMonth,timeGridWeek,listWeek'
                },
                height       : 650,
                // Carga eventos via AJAX desde /citas/calendario
                events: {
                    url    : '<?= BASE_URL ?>/citas/calendario',
                    method : 'GET',
                    extraParams: {},
                    failure: function () {
                        alert('Error cargando el calendario.');
                    },
                    // Agrega el header AJAX requerido
                    success: function (data) { return data; }
                },
                // Al hacer clic en un evento → ver la cita
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    var url = info.event.extendedProps.url;
                    if (url) window.location.href = '<?= BASE_URL ?>' + url;
                },
                // Tooltip al pasar el mouse
                eventMouseEnter: function (info) {
                    var props = info.event.extendedProps;
                    info.el.setAttribute('title',
                        props.contacto + ' | ' + props.tipo + ' | ' + props.estado
                    );
                }
            });

            calendarObj.render();
        }

        // ── Tabs de filtro por estado ─────────────────────────
        var tabs = document.querySelectorAll('#tabsEstado .nav-link');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                tabs.forEach(function (t) { t.classList.remove('active'); });
                this.classList.add('active');

                var estado = this.getAttribute('data-estado');
                var filas  = document.querySelectorAll('#tablaCitas tbody tr');

                filas.forEach(function (fila) {
                    if (estado === 'todas' || fila.getAttribute('data-estado') === estado) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        });

        // ── Buscador AJAX de citas ────────────────────────────
        var inputCita  = document.getElementById('inputBuscarCita');
        var resCita    = document.getElementById('resultadosBusquedaCita');
        var btnLimCita = document.getElementById('btnLimpiarCita');
        var timerCita  = null;

        if (!inputCita) return;

        inputCita.addEventListener('keyup', function () {
            var q = inputCita.value.trim();
            clearTimeout(timerCita);
            btnLimCita.style.display = q.length > 0 ? 'block' : 'none';

            if (q.length < 2) {
                resCita.style.display = 'none';
                return;
            }

            timerCita = setTimeout(function () {
                buscarCita(q);
            }, 300);
        });

        btnLimCita.addEventListener('click', function () {
            inputCita.value = '';
            btnLimCita.style.display = 'none';
            resCita.style.display    = 'none';
            resCita.innerHTML        = '';
            inputCita.focus();
        });

        document.addEventListener('click', function (e) {
            if (inputCita && !inputCita.contains(e.target) &&
                resCita   && !resCita.contains(e.target)) {
                resCita.style.display = 'none';
            }
        });

        function buscarCita(q) {
            resCita.style.display = 'block';
            resCita.innerHTML =
                '<div class="p-3 text-center text-muted">' +
                '<div class="spinner-border spinner-border-sm me-2"></div>' +
                'Buscando...</div>';

            fetch('<?= BASE_URL ?>/citas/buscadorcitas?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) { pintarCitas(data, q); })
            .catch(function () { resCita.style.display = 'none'; });
        }

        function pintarCitas(data, q) {
            if (data.length === 0) {
                resCita.innerHTML =
                    '<div class="p-3 text-center text-muted">' +
                    '<i class="bi bi-calendar-x me-2"></i>' +
                    'No se encontraron citas para <strong>' + escH(q) + '</strong></div>';
                resCita.style.display = 'block';
                return;
            }

            var colores = {Pendiente:'warning', Confirmada:'success', Cancelada:'danger'};
            var html = '';

            data.forEach(function (c) {
                var fecha = c.fecha_cita ? c.fecha_cita.substring(0,10) : '';
                html +=
                    '<a href="<?= BASE_URL ?>/citas/ver/' + c.id + '" ' +
                    '   class="d-block px-3 py-2 text-decoration-none text-dark border-bottom">' +
                    '  <div class="d-flex align-items-center gap-2">' +
                    '    <i class="bi bi-calendar3 text-primary"></i>' +
                    '    <div class="flex-grow-1">' +
                    '      <span class="fw-semibold">' + resH(escH(c.titulo), q) + '</span>' +
                    '      <small class="text-muted d-block">' +
                            escH(c.contacto_nombre) + ' ' + escH(c.contacto_apellido) +
                    '        · ' + fecha +
                    '      </small>' +
                    '    </div>' +
                    '    <span class="badge bg-' + (colores[c.estado] || 'secondary') + '">' +
                        escH(c.estado) +
                    '    </span>' +
                    '  </div>' +
                    '</a>';
            });

            html += '<div class="px-3 py-2 bg-light text-muted" style="font-size:.8rem">' +
                    data.length + ' resultado(s)</div>';

            resCita.innerHTML     = html;
            resCita.style.display = 'block';
        }

        function resH(t, q) {
            return t.replace(new RegExp('(' + escR(q) + ')', 'gi'),
                '<mark class="p-0" style="background:#fff3cd">$1</mark>');
        }

        function escH(s) {
            if (!s) return '';
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function escR(s) {
            return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

    })();
    </script>
</body>
</html>