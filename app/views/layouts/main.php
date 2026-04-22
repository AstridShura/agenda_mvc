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
           <!-- Opcion adicionada 22/04/26-->
                <!-- ── Enlace nuevo ── -->
            <a class="nav-link text-white ms-2" href="<?= BASE_URL ?>/categorias">
                <i class="bi bi-tags me-1"></i>Categorías
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

</body>
</html>