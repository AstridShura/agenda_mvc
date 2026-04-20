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

</body>
</html>