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
        </div>
    </div>
</nav>

<!-- ── CONTENIDO PRINCIPAL ────────────────────── -->
<main class="container pb-5">
    <?= $contenido ?>
</main>

<!-- Bootstrap JS LOCAL -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>

<!-- Teléfonos dinámicos -->
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

    <!-- 21/04/codigo JS para buscador Ajax de Contactos -->     
    <!-- Buscador dinámico con autocompletado -->
    <script>
    (function () {

        var input      = document.getElementById('inputBuscar');
        var resultados = document.getElementById('resultadosBusqueda');
        var btnLimpiar = document.getElementById('btnLimpiar');
        var tabla      = document.getElementById('tablaContactos');
        var timer      = null;

        if (!input) return; // Solo activo en páginas con buscador

        // ── Escucha escritura ──────────────────────────────────
        input.addEventListener('keyup', function () {
            var q = input.value.trim();

            clearTimeout(timer);
            btnLimpiar.style.display = q.length > 0 ? 'block' : 'none';

            // Menos de 2 chars → oculta resultados
            if (q.length < 2) {
                ocultarResultados();
                mostrarTabla();
                return;
            }

            // Espera 300ms después de que el usuario deja de escribir
            timer = setTimeout(function () {
                buscarajax(q);
            }, 300);
        });

        // ── Limpiar búsqueda ───────────────────────────────────
        btnLimpiar.addEventListener('click', function () {
            input.value = '';
            btnLimpiar.style.display = 'none';
            ocultarResultados();
            mostrarTabla();
            input.focus();
        });

        // ── Cerrar al hacer clic afuera ────────────────────────
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultados.contains(e.target)) {
                ocultarResultados();
            }
        });

        // ── Función principal de búsqueda ──────────────────────
        function buscarajax(q) {
            // Muestra spinner mientras carga
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

        // ── Pinta los resultados en el dropdown ────────────────
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
                var alias    = c.alias ? ' <span class="text-muted">(' + escHtml(c.alias) + ')</span>' : '';
                var email    = c.email ? '<small class="text-muted d-block">' + escHtml(c.email) + '</small>' : '';
                var catBadge = c.categoria
                    ? '<span class="badge ms-2" style="background:' + c.categoria_color + ';font-size:.7rem">' +
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
                        alias + catBadge +
                        email +
                    '    </div>' +
                    '  </div>' +
                    '</a>';
            });

            // Pie del dropdown con total
            html += '<div class="px-3 py-2 bg-light text-muted" style="font-size:.8rem">' +
                    '<i class="bi bi-info-circle me-1"></i>' +
                    data.length + ' resultado(s) encontrado(s)' +
                    '</div>';

            resultados.innerHTML = html;
            resultados.style.display = 'block';
            ocultarTabla();
        }

        // ── Resalta el término buscado en el texto ─────────────
        function resaltar(texto, q) {
            var regex = new RegExp('(' + escRegex(q) + ')', 'gi');
            return texto.replace(regex,
                '<mark class="p-0" style="background:#fff3cd">$1</mark>');
        }

        // ── Helpers ────────────────────────────────────────────
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
</body>
</html>