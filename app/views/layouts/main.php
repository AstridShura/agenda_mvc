<?php
// Lee el tema de la SESSION
// Si no hay SESSION activa usa 'claro'
$temaActual = $_SESSION['usuario_tema'] ?? 'claro';
?>
<!DOCTYPE html>
<HTML lang="es" data-tema="<?= $temaActual ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Agenda MVC' ?> | Agenda</title>

    <!-- Bootstrap 5 LOCAL -->
    <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons LOCAL -->
    <link href="<?= BASE_URL ?>/assets/css/bootstrap-icons.css" rel="stylesheet">

    <style>
    /* ══════════════════════════════════════════════════
       TEMA CLARO — Variables CSS
    ══════════════════════════════════════════════════ */
    :root {
        --bg-body         : #f8f9fa;
        --bg-card         : #ffffff;
        --bg-card-header  : #f8f9fa;
        --bg-table-head   : #212529;
        --bg-table-row    : #ffffff;
        --bg-table-alt    : #f8f9fa;
        --bg-table-border : #dee2e6;
        --bg-input        : #ffffff;
        --bg-navbar       : linear-gradient(135deg, #1a1a2e, #16213e);
        --bg-dropdown     : #ffffff;
        --bg-dropdown-hov : #f8f9fa;
        --bg-code         : #f1f3f5;

        --text-primary    : #212529;
        --text-secondary  : #6c757d;
        --text-heading    : #212529;
        --text-label      : #495057;
        --text-code       : #e83e8c;

        --border-color    : #dee2e6;
        --shadow          : 0 2px 10px rgba(0,0,0,0.08);
        --link-color      : #0d6efd;

        --input-bg        : #ffffff;
        --input-border    : #ced4da;
        --input-text      : #212529;
        --input-placeholder: #6c757d;

        --table-striped-bg : #f8f9fa;
        --table-hover-bg   : #e9ecef;
        --table-border     : #dee2e6;
        --table-text       : #212529;

        --page-link-bg    : #ffffff;
        --page-link-color : #0d6efd;
        --page-link-border: #dee2e6;

        --alert-info-bg   : #cff4fc;
        --alert-info-text : #055160;
        --alert-info-bord : #b6effb;
    }

    /* ══════════════════════════════════════════════════
       TEMA OSCURO — Variables CSS
    ══════════════════════════════════════════════════ */
    [data-tema="oscuro"] {
        --bg-body         : #0d1117;
        --bg-card         : #161b22;
        --bg-card-header  : #1c2128;
        --bg-table-head   : #1c2128;
        --bg-table-row    : #161b22;
        --bg-table-alt    : #1c2128;
        --bg-table-border : #30363d;
        --bg-input        : #21262d;
        --bg-navbar       : linear-gradient(135deg, #010409, #0d1117);
        --bg-dropdown     : #161b22;
        --bg-dropdown-hov : #1c2128;
        --bg-code         : #21262d;

        --text-primary    : #e6edf3;
        --text-secondary  : #8b949e;
        --text-heading    : #e6edf3;
        --text-label      : #c9d1d9;
        --text-code       : #ff7b7b;

        --border-color    : #30363d;
        --shadow          : 0 2px 10px rgba(0,0,0,0.5);
        --link-color      : #58a6ff;

        --input-bg        : #21262d;
        --input-border    : #30363d;
        --input-text      : #e6edf3;
        --input-placeholder: #8b949e;

        --table-striped-bg : #1c2128;
        --table-hover-bg   : #262c36;
        --table-border     : #30363d;
        --table-text       : #e6edf3;

        --page-link-bg    : #161b22;
        --page-link-color : #58a6ff;
        --page-link-border: #30363d;

        --alert-info-bg   : #0c2d48;
        --alert-info-text : #58a6ff;
        --alert-info-bord : #1a6a9a;
    }

    /* ══════════════════════════════════════════════════
       BODY Y BASE
    ══════════════════════════════════════════════════ */
    body {
        background-color : var(--bg-body);
        color            : var(--text-primary);
    }

    /* ══════════════════════════════════════════════════
       NAVBAR
    ══════════════════════════════════════════════════ */
    .navbar {
        background : var(--bg-navbar) !important;
    }

    /* ══════════════════════════════════════════════════
       CARDS — El problema principal de fondo blanco
    ══════════════════════════════════════════════════ */
    .card {
        background-color : var(--bg-card) !important;
        border-color     : var(--border-color) !important;
        box-shadow       : var(--shadow);
        color            : var(--text-primary);
    }

    .card-header {
        background-color : var(--bg-card-header) !important;
        border-color     : var(--border-color) !important;
        color            : var(--text-primary) !important;
    }

    .card-body {
        background-color : var(--bg-card) !important;
        color            : var(--text-primary);
    }

    .card-footer {
        background-color : var(--bg-card-header) !important;
        border-color     : var(--border-color) !important;
        color            : var(--text-secondary);
    }

    /* ══════════════════════════════════════════════════
       TABLAS — Fondo blanco corregido
    ══════════════════════════════════════════════════ */
    .table {
        color            : var(--table-text) !important;
        border-color     : var(--table-border) !important;
        --bs-table-color : var(--table-text);
        --bs-table-bg    : var(--bg-table-row);
        --bs-table-border-color: var(--table-border);
    }

    /* Encabezado oscuro de tabla */
    .table-dark {
        --bs-table-bg     : var(--bg-table-head) !important;
        --bs-table-color  : #ffffff !important;
        --bs-table-border-color: var(--table-border) !important;
        background-color  : var(--bg-table-head) !important;
        color             : #ffffff !important;
    }

    /* Filas impares */
    .table tbody tr {
        background-color : var(--bg-table-row);
        color            : var(--table-text);
        border-color     : var(--table-border);
    }

    /* Filas pares — striped */
    .table tbody tr:nth-child(even) {
        background-color : var(--table-striped-bg);
    }

    /* Hover */
    .table-hover tbody tr:hover {
        background-color : var(--table-hover-bg) !important;
        color            : var(--table-text) !important;
    }

    /* Celdas */
    .table td,
    .table th {
        border-color     : var(--table-border) !important;
        color            : var(--table-text);
    }

    /* Tabla sin bordes — usada en ver.php */
    .table-borderless td,
    .table-borderless th {
        border           : none !important;
        background-color : transparent !important;
        color            : var(--table-text);
    }

    /* ══════════════════════════════════════════════════
       FORMULARIOS — Texto invisible corregido
    ══════════════════════════════════════════════════ */
    .form-control {
        background-color : var(--input-bg) !important;
        color            : var(--input-text) !important;
        border-color     : var(--input-border) !important;
    }

    .form-control:focus {
        background-color : var(--input-bg) !important;
        color            : var(--input-text) !important;
        border-color     : var(--link-color) !important;
        box-shadow       : 0 0 0 .25rem rgba(88,166,255,.15) !important;
    }

    .form-control::placeholder {
        color            : var(--input-placeholder) !important;
        opacity          : 1;
    }

    .form-select {
        background-color : var(--input-bg) !important;
        color            : var(--input-text) !important;
        border-color     : var(--input-border) !important;
    }

    .form-select:focus {
        background-color : var(--input-bg) !important;
        color            : var(--input-text) !important;
        border-color     : var(--link-color) !important;
    }

    /* Input group */
    .input-group-text {
        background-color : var(--input-bg) !important;
        border-color     : var(--input-border) !important;
        color            : var(--text-secondary) !important;
    }

    /* Labels de formulario */
    .form-label {
        color            : var(--text-label) !important;
    }

    label {
        color            : var(--text-label);
    }

    /* Textarea */
    textarea.form-control {
        background-color : var(--input-bg) !important;
        color            : var(--input-text) !important;
    }

    /* Checkbox y switch */
    .form-check-label {
        color            : var(--text-primary) !important;
    }

    /* ══════════════════════════════════════════════════
       TEXTO Y TIPOGRAFÍA
    ══════════════════════════════════════════════════ */
    h1, h2, h3, h4, h5, h6 {
        color            : var(--text-heading) !important;
    }

    p, span, div, li {
        color            : inherit;
    }

    .text-muted {
        color            : var(--text-secondary) !important;
    }

    a {
        color            : var(--link-color);
    }

    a:hover {
        color            : var(--link-color);
        opacity          : .85;
    }

    /* Texto oscuro en badges warning */
    .badge.bg-warning {
        color            : #000 !important;
    }

    code {
        background-color : var(--bg-code);
        color            : var(--text-code);
        padding          : .1rem .3rem;
        border-radius    : .25rem;
    }

    /* ══════════════════════════════════════════════════
       PAGINADOR
    ══════════════════════════════════════════════════ */
    .page-link {
        background-color : var(--page-link-bg) !important;
        border-color     : var(--page-link-border) !important;
        color            : var(--page-link-color) !important;
    }

    .page-item.disabled .page-link {
        background-color : var(--page-link-bg) !important;
        border-color     : var(--page-link-border) !important;
        color            : var(--text-secondary) !important;
    }

    .page-item.active .page-link {
        background-color : var(--link-color) !important;
        border-color     : var(--link-color) !important;
        color            : #ffffff !important;
    }

    /* ══════════════════════════════════════════════════
       ALERTS
    ══════════════════════════════════════════════════ */
    [data-tema="oscuro"] .alert-info {
        background-color : var(--alert-info-bg)   !important;
        border-color     : var(--alert-info-bord)  !important;
        color            : var(--alert-info-text)  !important;
    }

    [data-tema="oscuro"] .alert-danger {
        background-color : #2d0c0c !important;
        border-color     : #9a1a1a !important;
        color            : #ff7b7b !important;
    }

    [data-tema="oscuro"] .alert-success {
        background-color : #0c2d1a !important;
        border-color     : #1a6a3a !important;
        color            : #56d364 !important;
    }

    [data-tema="oscuro"] .alert-warning {
        background-color : #2d220c !important;
        border-color     : #9a6a1a !important;
        color            : #e3b341 !important;
    }

    /* ══════════════════════════════════════════════════
       DROPDOWNS
    ══════════════════════════════════════════════════ */
    .dropdown-menu {
        background-color : var(--bg-dropdown) !important;
        border-color     : var(--border-color) !important;
    }

    .dropdown-item {
        color            : var(--text-primary) !important;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color : var(--bg-dropdown-hov) !important;
        color            : var(--text-primary) !important;
    }

    .dropdown-divider {
        border-color     : var(--border-color) !important;
    }

    .dropdown-header {
        color            : var(--text-secondary) !important;
    }

    /* ══════════════════════════════════════════════════
       BUSCADOR AJAX — Fondo blanco corregido
    ══════════════════════════════════════════════════ */
    #resultadosBusqueda,
    #resultadosBusquedaCat,
    #resultadosBusquedaUsu,
    #resultadosBusquedaCita {
        background-color : var(--bg-dropdown)   !important;
        border-color     : var(--border-color)  !important;
        color            : var(--text-primary)  !important;
    }

    #resultadosBusqueda a,
    #resultadosBusquedaCat a,
    #resultadosBusquedaUsu a,
    #resultadosBusquedaCita a {
        color            : var(--text-primary) !important;
        background-color : var(--bg-dropdown)  !important;
    }

    #resultadosBusqueda a:hover,
    #resultadosBusquedaCat a:hover,
    #resultadosBusquedaUsu a:hover,
    #resultadosBusquedaCita a:hover {
        background-color : var(--bg-dropdown-hov) !important;
    }

    /* ══════════════════════════════════════════════════
       TABS DE NAVEGACIÓN
    ══════════════════════════════════════════════════ */
    .nav-tabs {
        border-color     : var(--border-color) !important;
    }

    .nav-tabs .nav-link {
        color            : var(--text-secondary);
        border-color     : transparent;
    }

    .nav-tabs .nav-link:hover {
        border-color     : var(--border-color);
        color            : var(--text-primary);
    }

    .nav-tabs .nav-link.active {
        background-color : var(--bg-card)   !important;
        border-color     : var(--border-color)
                           var(--border-color)
                           var(--bg-card)   !important;
        color            : var(--text-primary) !important;
    }

    /* ══════════════════════════════════════════════════
       BORDES Y SEPARADORES
    ══════════════════════════════════════════════════ */
    .border {
        border-color     : var(--border-color) !important;
    }

    hr {
        border-color     : var(--border-color);
        opacity          : 1;
    }

    /* List group */
    .list-group-item {
        background-color : var(--bg-card)      !important;
        border-color     : var(--border-color) !important;
        color            : var(--text-primary) !important;
    }

    /* ══════════════════════════════════════════════════
       BOTONES OUTLINE EN TEMA OSCURO
    ══════════════════════════════════════════════════ */
    [data-tema="oscuro"] .btn-outline-secondary {
        color            : var(--text-secondary);
        border-color     : var(--border-color);
    }

    [data-tema="oscuro"] .btn-outline-secondary:hover {
        background-color : var(--bg-table-alt);
        color            : var(--text-primary);
        border-color     : var(--border-color);
    }

    [data-tema="oscuro"] .btn-outline-primary {
        color            : var(--link-color);
        border-color     : var(--link-color);
    }

    [data-tema="oscuro"] .btn-outline-primary:hover {
        background-color : var(--link-color);
        color            : #ffffff;
    }

    [data-tema="oscuro"] .btn-outline-info {
        color            : #58a6ff;
        border-color     : #58a6ff;
    }

    [data-tema="oscuro"] .btn-outline-warning {
        color            : #e3b341;
        border-color     : #e3b341;
    }

    [data-tema="oscuro"] .btn-outline-danger {
        color            : #ff7b7b;
        border-color     : #ff7b7b;
    }

    /* ══════════════════════════════════════════════════
       CLASES UTILITARIAS DE BOOTSTRAP
    ══════════════════════════════════════════════════ */
    [data-tema="oscuro"] .bg-white {
        background-color : var(--bg-card) !important;
    }

    [data-tema="oscuro"] .bg-light {
        background-color : var(--bg-card-header) !important;
    }

    [data-tema="oscuro"] .text-dark {
        color            : var(--text-primary) !important;
    }

    [data-tema="oscuro"] .border-bottom {
        border-color     : var(--border-color) !important;
    }

    /* ══════════════════════════════════════════════════
       TOGGLE TEMA — Botón en navbar
    ══════════════════════════════════════════════════ */
    .btn-tema {
        border-radius    : 20px !important;
        padding          : .25rem .75rem !important;
        font-size        : .8rem !important;
    }

    /* ══════════════════════════════════════════════════
       BOTONES DE ACCIÓN — Tamaño fijo
    ══════════════════════════════════════════════════ */
    .btn-action {
        width            : 32px;
        height           : 32px;
        padding          : 0;
        display          : inline-flex;
        align-items      : center;
        justify-content  : center;
    }

    /* ══════════════════════════════════════════════════
       TRANSICIÓN SUAVE AL CAMBIAR TEMA
    ══════════════════════════════════════════════════ */
    body,
    .card,
    .card-header,
    .card-body,
    .card-footer,
    .table,
    .table td,
    .table th,
    .form-control,
    .form-select,
    .input-group-text,
    .dropdown-menu,
    .list-group-item,
    .page-link,
    .nav-tabs .nav-link {
        transition       : background-color .25s ease,
                           border-color    .25s ease,
                           color           .25s ease !important;
    }

    /* ══════════════════════════════════════════════════
    TOM SELECT — Tema oscuro
    El select de contactos en Citas usa TomSelect
    que tiene sus propios estilos que hay que
    sobreescribir explícitamente
    ══════════════════════════════════════════════════ */

    /* Contenedor principal del select */
    [data-tema="oscuro"] .ts-wrapper .ts-control {
        background-color : var(--input-bg)     !important;
        border-color     : var(--input-border) !important;
        color            : var(--input-text)   !important;
    }

    /* Cuando está enfocado/activo */
    [data-tema="oscuro"] .ts-wrapper.focus .ts-control {
        background-color : var(--input-bg)   !important;
        border-color     : var(--link-color) !important;
        box-shadow       : 0 0 0 .25rem rgba(88,166,255,.15) !important;
    }

    /* Texto escrito en el input de búsqueda */
    [data-tema="oscuro"] .ts-wrapper .ts-control input {
        background-color : transparent       !important;
        color            : var(--input-text) !important;
    }

    /* Item seleccionado dentro del control */
    [data-tema="oscuro"] .ts-wrapper .ts-control .item {
        background-color : #30363d           !important;
        border-color     : var(--border-color) !important;
        color            : var(--input-text) !important;
    }

    /* Dropdown — lista desplegable */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown {
        background-color : var(--bg-dropdown)  !important;
        border-color     : var(--border-color) !important;
        color            : var(--text-primary) !important;
    }

    /* Cada opción en el dropdown */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .option {
        background-color : var(--bg-dropdown)  !important;
        color            : var(--text-primary) !important;
    }

    /* Opción al pasar el cursor — cursor invisible corregido */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .option:hover,
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .option.active {
        background-color : var(--link-color) !important;
        color            : #ffffff           !important;
        cursor           : pointer           !important;
    }

    /* Opción seleccionada actualmente */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .option.selected {
        background-color : #30363d           !important;
        color            : var(--link-color) !important;
    }

    /* Placeholder */
    [data-tema="oscuro"] .ts-wrapper .ts-control .placeholder {
        color            : var(--input-placeholder) !important;
    }

    /* Grupo de opciones — encabezado */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .optgroup-header {
        background-color : var(--bg-card-header) !important;
        color            : var(--text-secondary) !important;
    }

    /* Sin resultados */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .no-results {
        background-color : var(--bg-dropdown)  !important;
        color            : var(--text-secondary) !important;
    }

    /* Botón X para limpiar selección */
    [data-tema="oscuro"] .ts-wrapper .ts-control .clear-button {
        color            : var(--text-secondary) !important;
    }

    [data-tema="oscuro"] .ts-wrapper .ts-control .clear-button:hover {
        color            : var(--text-primary) !important;
    }

    /* Separador entre opciones */
    [data-tema="oscuro"] .ts-wrapper .ts-dropdown .divider {
        border-color     : var(--border-color) !important;
    }


    /* 29/04/26    KANBAN BOARD Drug and Drop (Arrastrar y soltar) */

    /* Tarjeta individual */
    .kanban-card {
        background-color : var(--bg-card);
        border           : 1px solid var(--border-color);
        border-radius    : 8px;
        cursor           : grab;
        transition       : transform .15s ease,
                        box-shadow .15s ease,
                        opacity .15s ease;
        user-select      : none;
    }

    /* Al pasar el mouse */
    .kanban-card:hover {
        transform        : translateY(-2px);
        box-shadow       : 0 6px 20px rgba(0,0,0,0.15);
    }

    /* Mientras se arrastra */
    .kanban-card.sortable-chosen {
        cursor           : grabbing;
        transform        : rotate(2deg) scale(1.02);
        box-shadow       : 0 12px 30px rgba(0,0,0,0.25);
        opacity          : .95;
        z-index          : 9999;
    }

    /* Fantasma — placeholder donde caerá la tarjeta */
    .kanban-card.sortable-ghost {
        opacity          : .3;
        background-color : var(--bg-table-alt);
        border           : 2px dashed var(--border-color);
    }

    /* Animación al soltar */
    .kanban-card.sortable-drag {
        opacity          : 1;
    }

    /* Zona de drop activa */
    .kanban-lista.sortable-over {
        background-color : var(--bg-table-alt);
        border-radius    : 8px;
        min-height       : 60px;
    }

    /* Placeholder vacío */
    .kanban-vacio {
        transition       : all .2s ease;
    }

    /* Indicador de guardando */
    .guardando {
        animation        : pulso 1s infinite;
    }

    @keyframes pulso {
        0%, 100% { opacity: 1; }
        50%       { opacity: .5; }
    }

    /* ══════════════════════════════════════════════════
       LEAFLET — Aislar popups y controles del tema oscuro
       Los popups de Leaflet usan divs propios que heredan
       el color del tema y se vuelven ilegibles.
       Forzamos siempre fondo blanco/texto oscuro en ellos.
       30/04/26
    ══════════════════════════════════════════════════ */

    /* Popup — fondo y texto siempre claros */
    .leaflet-popup-content-wrapper,
    .leaflet-popup-tip {
        background-color : #ffffff !important;
        color            : #212529 !important;
        box-shadow       : 0 3px 14px rgba(0,0,0,0.4) !important;
    }

    .leaflet-popup-content {
        color            : #212529 !important;
    }

    /* Links dentro del popup */
    .leaflet-popup-content a {
        color            : #0d6efd !important;
    }

    /* Botón cerrar popup */
    .leaflet-popup-close-button {
        color            : #666 !important;
    }

    .leaflet-popup-close-button:hover {
        color            : #000 !important;
    }

    /* Controles de zoom — siempre con fondo claro */
    .leaflet-control-zoom a {
        background-color : #ffffff !important;
        color            : #333333 !important;
        border-color     : #ccc    !important;
    }

    .leaflet-control-zoom a:hover {
        background-color : #f4f4f4 !important;
    }

    /* Barra de atribución OpenStreetMap */
    .leaflet-control-attribution {
        background-color : rgba(255,255,255,0.85) !important;
        color            : #333333 !important;
    }

    .leaflet-control-attribution a {
        color            : #0078a8 !important;
    }

    /* Contenedor del mapa — anular herencia de color del tema */
    .leaflet-container {
        background-color : #f0f0f0 !important;
        color            : #212529 !important;
    }

    /* Tooltip de Leaflet */
    .leaflet-tooltip {
        background-color : #ffffff !important;
        color            : #212529 !important;
        border-color     : #ccc    !important;
    }

    </style>

    <!-- Agregado para Citas -->
    <!-- Flatpickr — date/time picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- TomSelect — buscador de contactos en select -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css">
    <!-- FullCalendar CSS — corregido 30/04/26 (evita NS_ERROR_CORRUPTED_CONTENT en Firefox) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.10/main.min.css">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- SortableJS — Drag & Drop (Arrastrar y Soltar) 29/04/26 -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <!-- Leaflet CSS Para 30/04/26Geolocalización-->
    <link rel="stylesheet"  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

</head>
<body>

<!-- ── NAVBAR ─────────────────────────────────── -->
<nav class="navbar navbar-dark navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/contactos">
            <i class="bi bi-journal-text me-2"></i>Agenda MVC
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <!-- Links izquierda -->
            <div class="navbar-nav me-auto">
                <a class="nav-link text-white" href="<?= BASE_URL ?>/contactos">
                    <i class="bi bi-people me-1"></i>Contactos
                </a>
                <a class="nav-link text-white" href="<?= BASE_URL ?>/categorias">
                    <i class="bi bi-tags me-1"></i>Categorías
                </a>
                <a class="nav-link text-white" href="<?= BASE_URL ?>/citas">
                    <i class="bi bi-calendar2-week me-1"></i>Citas
                </a>
                <!-- Kanban Drop and Drag 29/04/26-->
                <a class="nav-link text-white" href="<?= BASE_URL ?>/citas/kanban">
                    <i class="bi bi-kanban me-1"></i>Kanban
                </a>                
                <a class="nav-link text-white" href="<?= BASE_URL ?>/usuarios">
                    <i class="bi bi-people-fill me-1"></i>Usuarios
                </a>
            </div>

                <!-- ── Toggle Tema 28/04/26 ─────────────────────── -->
                <a href="<?= BASE_URL ?>/usuarios/cambiatema"
                   class="btn btn-sm btn-tema
                          <?= $temaActual === 'oscuro'
                              ? 'btn-warning'
                              : 'btn-outline-light' ?>"
                   title="<?= $temaActual === 'oscuro'
                              ? 'Cambiar a Modo Claro'
                              : 'Cambiar a Modo Oscuro' ?>">
                    <?php if ($temaActual === 'oscuro'): ?>
                        ☀️ <span class="d-none d-md-inline">Claro</span>
                    <?php else: ?>
                        🌙 <span class="d-none d-md-inline">Oscuro</span>
                    <?php endif; ?>
                </a>

            <!-- Usuario autenticado — derecha -->
            <div class="navbar-nav ms-auto">
                <?php
                // Muestra el nombre del usuario autenticado
                $nombreSesion = $_SESSION['usuario_nombre'] ?? 'Usuario';
                $rolSesion    = $_SESSION['usuario_rol']    ?? 'usuario';
                ?>
                <span class="nav-link text-white-50 d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle"></i>
                    <span>
                        <?= htmlspecialchars($nombreSesion) ?>
                        <small class="badge bg-secondary ms-1">
                            <?= htmlspecialchars($rolSesion) ?>
                        </small>
                    </span>
                </span>

                <!-- Botón Nuevo Contacto -->
                <a class="nav-link text-white" href="<?= BASE_URL ?>/contactos/crear">
                    <i class="bi bi-person-plus me-1"></i>Nuevo
                </a>
                <!-- 30/04/26 Para Mapas -->
                <a class="nav-link text-white" href="<?= BASE_URL ?>/contactos/mapa">
                    <i class="bi bi-geo-alt me-1"></i>Mapa
                </a>

                <!-- Logout -->
                <a class="nav-link text-danger fw-semibold"
                   href="<?= BASE_URL ?>/auth/logout"
                   onclick="return confirm('¿Cerrar sesión?')">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </div>
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

    <!-- ── Tema: aplicar inmediatamente sin flash 28/04/26 ───────────── -->
    <script>
    (function () {
        // Aplicar tema guardado ANTES de que el browser pinte
        // Evita el "flash" de tema incorrecto al cargar
        var tema = '<?= $_SESSION["usuario_tema"] ?? "claro" ?>';
        document.documentElement.setAttribute('data-tema', tema);
    })();
    </script>    

    <!-- 29/04/26 Drag and Drop/Arrastrar y soltar-->
    <!-- ── Kanban Board — Drag & Drop ────────── -->
    <script>
    (function () 
    {

        // Solo ejecutar en la página del Kanban
        var board = document.getElementById('kanbanBoard');
        if (!board) return;

        // URL del endpoint AJAX
        var urlActualizar = '<?= BASE_URL ?>/citas/actualizarestado';

        // ── Inicializar SortableJS en cada columna ────────────
        var listas = document.querySelectorAll('.kanban-lista');

        listas.forEach(function (lista) {
            new Sortable(lista, {

                // Permite arrastrar entre todas las listas
                group: 'citas',

                // Animación suave al reordenar
                animation: 200,

                // Clase CSS mientras se arrastra
                chosenClass:  'sortable-chosen',

                // Clase CSS del elemento fantasma
                ghostClass:   'sortable-ghost',

                // Clase CSS del elemento que se arrastra
                dragClass:    'sortable-drag',

                // Clase al pasar sobre una lista
                dragoverBubble: true,

                // Qué elementos son arrastrables
                draggable: '.kanban-card',

                // ── Al soltar la tarjeta ─────────────────────
                onEnd: function (evt) {
                    var tarjeta      = evt.item;
                    var listaDestino = evt.to;
                    var listaOrigen  = evt.from;

                    // Si no cambió de columna → no hacer nada
                    if (listaOrigen === listaDestino) {
                        actualizarContadores();
                        return;
                    }

                    var idCita       = tarjeta.getAttribute('data-id');
                    var estadoNuevo  = listaDestino.getAttribute('data-estado');
                    var estadoViejo  = listaOrigen.getAttribute('data-estado');

                    // Actualizar visualmente el atributo
                    tarjeta.setAttribute('data-estado', estadoNuevo);

                    // Actualizar barra de color superior
                    var barra = tarjeta.querySelector('div[style*="height:4px"]');
                    if (barra) {
                        var colores = {
                            'Pendiente'  : '#ffc107',
                            'Confirmada' : '#28a745',
                            'Cancelada'  : '#dc3545'
                        };
                        barra.style.background = colores[estadoNuevo] || '#6c757d';
                    }

                    // Actualizar contadores de columnas
                    actualizarContadores();

                    // Guardar en BD via AJAX
                    guardarEstado(idCita, estadoNuevo, estadoViejo, tarjeta, listaOrigen);
                }
            });
        });

        // ── Guardar estado en BD ──────────────────────────────
        function guardarEstado(idCita, estadoNuevo, estadoViejo, tarjeta, listaOrigen) {

            // Mostrar indicador visual en la tarjeta
            tarjeta.classList.add('guardando');

            fetch(urlActualizar, {
                method  : 'POST',
                headers : {
                    'Content-Type'     : 'application/json',
                    'X-Requested-With' : 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id_cita : parseInt(idCita),
                    estado  : estadoNuevo
                })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                tarjeta.classList.remove('guardando');

                if (data.ok) {
                    // ✅ Éxito — mostrar toast
                    mostrarToast(
                        '✅ "' + data.cita + '" → ' + estadoNuevo,
                        'success'
                    );
                } else {
                    // ❌ Error — revertir al estado original
                    mostrarToast(
                        '❌ Error al guardar. Revierte automáticamente.',
                        'danger'
                    );
                    // Devolver la tarjeta a la columna original
                    listaOrigen.appendChild(tarjeta);
                    tarjeta.setAttribute('data-estado', estadoViejo);
                    actualizarContadores();
                }
            })
            .catch(function (error) {
                tarjeta.classList.remove('guardando');
                console.error('Error:', error);
                mostrarToast('❌ Error de conexión. Intenta de nuevo.', 'danger');

                // Revertir
                listaOrigen.appendChild(tarjeta);
                tarjeta.setAttribute('data-estado', estadoViejo);
                actualizarContadores();
            });
        }

        // ── Actualizar contadores de cada columna ─────────────
        function actualizarContadores() {
            var listas = document.querySelectorAll('.kanban-lista');
            listas.forEach(function (lista) {
                var estado    = lista.getAttribute('data-estado');
                var tarjetas  = lista.querySelectorAll('.kanban-card');
                var contador  = document.getElementById('contador-' + estado);
                if (contador) {
                    contador.textContent = tarjetas.length;
                }

                // Mostrar/ocultar placeholder vacío
                var vacio    = lista.querySelector('.kanban-vacio');
                if (vacio) {
                    vacio.style.display = tarjetas.length === 0
                        ? 'block' : 'none';
                }
            });
        }

        // ── Toast de notificación ─────────────────────────────
        function mostrarToast(mensaje, tipo) {
            var toastEl  = document.getElementById('toastMsg');
            var toastTxt = document.getElementById('toastTexto');

            if (!toastEl || !toastTxt) return;

            toastTxt.textContent = mensaje;

            // Cambiar color según tipo
            toastEl.className = 'toast align-items-center text-white border-0 bg-' + tipo;

            // Mostrar el toast de Bootstrap
            var toast = new bootstrap.Toast(toastEl, {
                delay : 3000
            });
            toast.show();
        }

    })();
    </script>

    
    <!-- 30/04/26 Para Geolocalización-->    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- ── Leaflet: Mapa selector en crear/editar ─────────── -->
    <script>
    (function () {

        // Solo ejecutar si existe el mapa selector
        var contenedor = document.getElementById('mapaSelector');
        if (!contenedor) return;

        // ── Centro inicial: Latinoamérica ────────────────────
        var latInicial = -16.5000;  // Bolivia/Latinoamérica
        var lngInicial = -64.0000;
        var zoomInicial = 5;

        // Si ya tiene coordenadas → centrar en ellas
        var latGuardada = document.getElementById('inputLatitud').value;
        var lngGuardada = document.getElementById('inputLongitud').value;

        if (latGuardada && lngGuardada) {
            latInicial  = parseFloat(latGuardada);
            lngInicial  = parseFloat(lngGuardada);
            zoomInicial = 15;
        }

        // ── Inicializar mapa ─────────────────────────────────
        var mapa = L.map('mapaSelector').setView(
            [latInicial, lngInicial],
            zoomInicial
        );

        // Tiles OpenStreetMap (gratuito)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution : '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            maxZoom     : 19
        }).addTo(mapa);

        // ── Marcador arrastrable ─────────────────────────────
        var marcador = null;

        // Si ya tiene coordenadas → mostrar marcador
        if (latGuardada && lngGuardada) {
            marcador = L.marker(
                [parseFloat(latGuardada), parseFloat(lngGuardada)],
                { draggable: true }
            ).addTo(mapa);

            marcador.on('dragend', function () {
                var pos = marcador.getLatLng();
                actualizarCoordenadas(pos.lat, pos.lng);
            });
        }

        // ── Clic en el mapa → colocar/mover marcador ─────────
        mapa.on('click', function (e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marcador) {
                marcador.setLatLng([lat, lng]);
            } else {
                marcador = L.marker([lat, lng], { draggable: true })
                            .addTo(mapa);

                marcador.on('dragend', function () {
                    var pos = marcador.getLatLng();
                    actualizarCoordenadas(pos.lat, pos.lng);
                });
            }

            actualizarCoordenadas(lat, lng);
        });

        // ── Buscador de dirección via Nominatim ──────────────
        var btnBuscar = document.getElementById('btnBuscarDir');
        var inputDir  = document.getElementById('buscadorDireccion');

        if (btnBuscar && inputDir) {
            btnBuscar.addEventListener('click', function () {
                var query = inputDir.value.trim();
                if (!query) return;

                btnBuscar.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span>';
                btnBuscar.disabled = true;

                // Nominatim — geocoding gratuito de OpenStreetMap
                fetch('https://nominatim.openstreetmap.org/search' +
                    '?format=json&limit=1&q=' +
                    encodeURIComponent(query), {
                    headers: {
                        'Accept-Language': 'es',
                        'User-Agent'      : 'AgendaMVC/1.0'
                    }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    btnBuscar.innerHTML = '<i class="bi bi-search me-1"></i>Buscar';
                    btnBuscar.disabled  = false;

                    if (data.length === 0) {
                        alert('No se encontró la dirección. Intenta con más detalle.');
                        return;
                    }

                    var lat = parseFloat(data[0].lat);
                    var lng = parseFloat(data[0].lon);

                    // Mover mapa y colocar marcador
                    mapa.setView([lat, lng], 16);

                    if (marcador) {
                        marcador.setLatLng([lat, lng]);
                    } else {
                        marcador = L.marker([lat, lng], { draggable: true })
                                    .addTo(mapa);

                        marcador.on('dragend', function () {
                            var pos = marcador.getLatLng();
                            actualizarCoordenadas(pos.lat, pos.lng);
                        });
                    }

                    actualizarCoordenadas(lat, lng);

                    // Mostrar popup con la dirección encontrada
                    marcador.bindPopup(
                        '<strong>' + data[0].display_name + '</strong>'
                    ).openPopup();
                })
                .catch(function () {
                    btnBuscar.innerHTML = '<i class="bi bi-search me-1"></i>Buscar';
                    btnBuscar.disabled  = false;
                    alert('Error al buscar. Verifica tu conexión a internet.');
                });
            });

            // Buscar también al presionar Enter
            inputDir.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    btnBuscar.click();
                }
            });
        }

        // ── Limpiar ubicación ────────────────────────────────
        var btnLimpiar = document.getElementById('btnLimpiarUbic');
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function () {
                if (marcador) {
                    mapa.removeLayer(marcador);
                    marcador = null;
                }
                document.getElementById('inputLatitud').value  = '';
                document.getElementById('inputLongitud').value = '';

                var display = document.getElementById('coordsDisplay');
                if (display) display.style.display = 'none';
            });
        }

        // ── Actualizar inputs ocultos y display ──────────────
        function actualizarCoordenadas(lat, lng) {
            var latR = lat.toFixed(8);
            var lngR = lng.toFixed(8);

            document.getElementById('inputLatitud').value  = latR;
            document.getElementById('inputLongitud').value = lngR;

            var dispLat = document.getElementById('dispLat');
            var dispLng = document.getElementById('dispLng');
            var display = document.getElementById('coordsDisplay');

            if (dispLat) dispLat.textContent = latR;
            if (dispLng) dispLng.textContent = lngR;
            if (display) display.style.display = 'block';
        }

        // ── Forzar redibujado del mapa ───────────────────────
        // Necesario cuando el mapa está dentro de una card
        setTimeout(function () {
            mapa.invalidateSize();
        }, 100);

    })();
    </script>

    <!-- ── Leaflet: Mapa general de contactos ─────────────── -->
    <script>
    (function () {

        // Solo ejecutar en la página del mapa general
        var contenedor = document.getElementById('mapaGeneral');
        if (!contenedor || typeof contactosMapa === 'undefined') return;

        // ── Inicializar mapa centrado en Latinoamérica ───────
        var mapa = L.map('mapaGeneral').setView([-15, -65], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution : '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            maxZoom     : 19
        }).addTo(mapa);

        // ── Crear marcadores para cada contacto ──────────────
        var marcadores   = [];
        var grupoMarc    = L.featureGroup().addTo(mapa);

        contactosMapa.forEach(function (c) {
            if (!c.latitud || !c.longitud) return;

            // Color del marcador según categoría
            var color = c.categoria_color || '#0d6efd';

            // Ícono personalizado con color de categoría
            var icono = L.divIcon({
                className : 'marcador-contacto',
                html      :
                    '<div style="' +
                        'background:' + color + ';' +
                        'width:28px; height:28px;' +
                        'border-radius:50% 50% 50% 0;' +
                        'transform:rotate(-45deg);' +
                        'border:3px solid white;' +
                        'box-shadow:0 2px 8px rgba(0,0,0,0.3)' +
                    '"></div>',
                iconSize    : [28, 28],
                iconAnchor  : [14, 28],
                popupAnchor : [0, -30],
            });

            // Popup con info del contacto
            var popupHTML =
                '<div style="min-width:180px">' +
                '<strong style="font-size:1rem">' +
                    escH(c.apellido) + ', ' + escH(c.nombre) +
                '</strong>';

            if (c.alias) {
                popupHTML += '<br><small class="text-muted">(' +
                            escH(c.alias) + ')</small>';
            }

            if (c.categoria) {
                popupHTML +=
                    '<br><span style="' +
                        'background:' + color + ';' +
                        'color:white;' +
                        'padding:2px 8px;' +
                        'border-radius:10px;' +
                        'font-size:.75rem;' +
                        'display:inline-block;' +
                        'margin-top:4px' +
                    '">' + escH(c.categoria) + '</span>';
            }

            if (c.email) {
                popupHTML += '<br><small>📧 ' + escH(c.email) + '</small>';
            }

            if (c.direccion) {
                popupHTML += '<br><small>📍 ' +
                    escH(c.direccion.substring(0, 50)) +
                    (c.direccion.length > 50 ? '...' : '') +
                    '</small>';
            }

            popupHTML +=
                '<br><div style="margin-top:8px">' +
                '<a href="' + baseUrl + '/contactos/ver/' + c.id + '" ' +
                'style="' +
                        'background:#0d6efd;color:white;' +
                        'padding:4px 12px;border-radius:4px;' +
                        'text-decoration:none;font-size:.8rem' +
                '">' +
                '👁 Ver detalle' +
                '</a>' +
                '</div></div>';

            var marcador = L.marker(
                [parseFloat(c.latitud), parseFloat(c.longitud)],
                { icon: icono }
            )
            .bindPopup(popupHTML, { maxWidth: 250 });

            grupoMarc.addLayer(marcador);

            marcadores.push({
                id       : c.id,
                marcador : marcador,
                cat      : c.categoria || '',
            });
        });

        // Ajustar zoom para mostrar todos los marcadores
        if (marcadores.length > 0) {
            mapa.fitBounds(grupoMarc.getBounds().pad(0.1));
        }

        // ── Clic en lista lateral → centrar en marcador ──────
        var items = document.querySelectorAll('.item-contacto-mapa');
        items.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                var lat = parseFloat(item.getAttribute('data-lat'));
                var lng = parseFloat(item.getAttribute('data-lng'));

                mapa.setView([lat, lng], 16);

                // Abrir popup del marcador correspondiente
                var id = parseInt(item.getAttribute('data-id'));
                marcadores.forEach(function (m) {
                    if (m.id === id) {
                        m.marcador.openPopup();
                    }
                });

                // Resaltar item en la lista
                items.forEach(function (i) {
                    i.classList.remove('active');
                });
                item.classList.add('active');
            });
        });

        // ── Filtro por categoría ─────────────────────────────
        var filtroCat = document.getElementById('filtroCat');
        if (filtroCat) {
            filtroCat.addEventListener('change', function () {
                var catSelec = this.value;

                marcadores.forEach(function (m) {
                    if (!catSelec || m.cat === catSelec) {
                        grupoMarc.addLayer(m.marcador);
                    } else {
                        grupoMarc.removeLayer(m.marcador);
                    }
                });

                // Filtrar lista lateral
                items.forEach(function (item) {
                    var catItem = item.getAttribute('data-cat');
                    item.style.display =
                        (!catSelec || catItem === catSelec) ? '' : 'none';
                });
            });
        }

        // ── Helper escape HTML ───────────────────────────────
        function escH(s) {
            if (!s) return '';
            return String(s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

    })();
    </script>
    <!-- Fin Geolocalización-->

    <!-- 30/04/26 Para Mapas -->
    <!-- ── Leaflet: Mapa individual de contacto ───────────── -->
    <script>
    (function () {
        var contenedor = document.getElementById('mapaContacto');
        if (!contenedor ||
            typeof latContacto === 'undefined') return;

        // ── Fix iconos Leaflet desde CDN unpkg ───────────────
        // Sin esto, el marcador por defecto no encuentra sus
        // imágenes porque Leaflet busca en rutas relativas.
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
            iconRetinaUrl : 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            iconUrl       : 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            shadowUrl     : 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        });

        // ── Inicializar mapa ─────────────────────────────────
        var mapa = L.map('mapaContacto').setView(
            [latContacto, lngContacto], 15
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution : '© OpenStreetMap',
            maxZoom     : 19
        }).addTo(mapa);

        // Marcador con popup
        L.marker([latContacto, lngContacto])
        .addTo(mapa)
        .bindPopup('<strong>' + nomContacto + '</strong>')
        .openPopup();

        setTimeout(function () {
            mapa.invalidateSize();
        }, 100);
    })();
    </script>    
    <!-- fin mapas -->
</body>
</HTML>