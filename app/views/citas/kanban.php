<?php
// Vista: Kanban Board — Seguimiento de Citas

$iconosTipo = [
    'Reunion' => 'bi-people-fill',
    'Llamada' => 'bi-telephone-fill',
    'Visita'  => 'bi-geo-alt-fill',
    'Otro'    => 'bi-calendar-event-fill',
];
?>

<!-- ── Encabezado ──────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-kanban-fill me-2 text-primary"></i>
        <?= $titulo ?>
    </h2>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/citas"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list-ul me-1"></i>Ver Lista
        </a>
        <a href="<?= BASE_URL ?>/citas/crear"
           class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nueva Cita
        </a>
    </div>
</div>

<!-- ── Indicador de guardado ──────────────────────────── -->
<div id="toastGuardado"
     class="position-fixed bottom-0 end-0 p-3"
     style="z-index:9999">
    <div class="toast align-items-center text-white border-0"
         id="toastMsg" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastTexto">
                ✅ Estado actualizado
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast">
            </button>
        </div>
    </div>
</div>

<!-- ── Leyenda ─────────────────────────────────────────── -->
<div class="d-flex gap-3 mb-3 flex-wrap">
    <small class="text-muted">
        <i class="bi bi-info-circle me-1"></i>
        Arrastra las tarjetas entre columnas para cambiar el estado.
        Los cambios se guardan automáticamente.
    </small>
    <div class="ms-auto d-flex gap-2">
        <span class="badge bg-warning text-dark">
            🟡 <?= count($pendientes) ?> Pendientes
        </span>
        <span class="badge bg-success">
            🟢 <?= count($confirmadas) ?> Confirmadas
        </span>
        <span class="badge bg-danger">
            🔴 <?= count($canceladas) ?> Canceladas
        </span>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- KANBAN BOARD — 3 columnas                            -->
<!-- ══════════════════════════════════════════════════════ -->
<div class="row g-3" id="kanbanBoard">

    <?php
    // Definición de columnas
    $columnas = [
        [
            'id'       => 'col-pendiente',
            'estado'   => 'Pendiente',
            'titulo'   => '🟡 Pendiente',
            'color'    => 'warning',
            'bgHeader' => '#856404',
            'datos'    => $pendientes,
        ],
        [
            'id'       => 'col-confirmada',
            'estado'   => 'Confirmada',
            'titulo'   => '🟢 Confirmada',
            'color'    => 'success',
            'bgHeader' => '#0a5c2b',
            'datos'    => $confirmadas,
        ],
        [
            'id'       => 'col-cancelada',
            'estado'   => 'Cancelada',
            'titulo'   => '🔴 Cancelada',
            'color'    => 'danger',
            'bgHeader' => '#842029',
            'datos'    => $canceladas,
        ],
    ];
    ?>

    <?php foreach ($columnas as $col): ?>
    <div class="col-md-4">
        <div class="card h-100">

            <!-- Encabezado de columna -->
            <div class="card-header d-flex
                        justify-content-between
                        align-items-center py-2"
                 style="background:<?= $col['bgHeader'] ?>;
                        border-bottom:none;">
                <span class="fw-bold text-white">
                    <?= $col['titulo'] ?>
                </span>
                <span class="badge bg-white
                             text-<?= $col['color'] ?>
                             fw-bold"
                      id="contador-<?= $col['estado'] ?>">
                    <?= count($col['datos']) ?>
                </span>
            </div>

            <!-- Zona de drop — donde caen las tarjetas -->
            <div class="card-body p-2"
                 style="min-height:400px;
                        overflow-y:auto;
                        max-height:70vh;">

                <div class="kanban-lista d-flex flex-column gap-2"
                     id="<?= $col['id'] ?>"
                     data-estado="<?= $col['estado'] ?>">

                    <?php if (empty($col['datos'])): ?>
                        <!-- Placeholder cuando no hay tarjetas -->
                        <div class="kanban-vacio text-center
                                    text-muted py-5"
                             style="border:2px dashed var(--border-color);
                                    border-radius:8px;">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            <small>Sin citas <?= strtolower($col['estado'] === 'Cancelada' ? 'canceladas' : ($col['estado'] === 'Confirmada' ? 'confirmadas' : 'pendientes')) ?></small>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($col['datos'] as $cita): ?>
                    <!-- ── TARJETA DE CITA ───────────────── -->
                    <div class="kanban-card"
                         data-id="<?= $cita['id'] ?>"
                         data-estado="<?= $cita['estado'] ?>">

                        <!-- Barra de color superior -->
                        <div style="height:4px;
                                    background:<?= match($cita['estado']) {
                                        'Pendiente'  => '#ffc107',
                                        'Confirmada' => '#28a745',
                                        'Cancelada'  => '#dc3545',
                                        default      => '#6c757d'
                                    } ?>;
                                    border-radius:8px 8px 0 0;">
                        </div>

                        <div class="p-3">

                            <!-- Título e ícono de tipo -->
                            <div class="d-flex
                                        justify-content-between
                                        align-items-start mb-2">
                                <h6 class="mb-0 fw-bold"
                                    style="font-size:.9rem;
                                           line-height:1.3">
                                    <?= htmlspecialchars($cita['titulo']) ?>
                                </h6>
                                <i class="bi <?= $iconosTipo[$cita['tipo']] ?? 'bi-calendar-event' ?>
                                           text-<?= $col['color'] ?>
                                           ms-2 flex-shrink-0"
                                   title="<?= $cita['tipo'] ?>">
                                </i>
                            </div>

                            <!-- Contacto -->
                            <div class="d-flex align-items-center
                                        gap-1 mb-2">
                                <i class="bi bi-person text-muted"
                                   style="font-size:.8rem"></i>
                                <small class="text-muted">
                                    <?= htmlspecialchars(
                                        $cita['contacto_nombre'] . ' ' .
                                        $cita['contacto_apellido']
                                    ) ?>
                                </small>
                            </div>

                            <!-- Fecha y hora -->
                            <div class="d-flex align-items-center
                                        gap-1 mb-2">
                                <i class="bi bi-calendar3 text-muted"
                                   style="font-size:.8rem"></i>
                                <small class="text-muted">
                                    <?= date('d/m/Y',
                                        strtotime($cita['fecha_cita'])) ?>
                                </small>
                                <i class="bi bi-clock text-muted ms-2"
                                   style="font-size:.8rem"></i>
                                <small class="text-muted">
                                    <?= substr($cita['hora_inicio'], 0, 5) ?>
                                    <?php if ($cita['hora_fin']): ?>
                                        → <?= substr($cita['hora_fin'], 0, 5) ?>
                                    <?php endif; ?>
                                </small>
                            </div>

                            <!-- Descripción (si tiene) -->
                            <?php if (!empty($cita['descripcion'])): ?>
                                <p class="text-muted mb-2"
                                   style="font-size:.78rem;
                                          line-height:1.4">
                                    <?= htmlspecialchars(
                                        substr($cita['descripcion'], 0, 80)
                                    ) ?>
                                    <?= strlen($cita['descripcion']) > 80
                                        ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <!-- Footer de la tarjeta -->
                            <div class="d-flex
                                        justify-content-between
                                        align-items-center
                                        border-top pt-2 mt-1"
                                 style="border-color:
                                        var(--border-color)!important">
                                <span class="badge bg-<?= $col['color'] ?>
                                             <?= $cita['estado'] === 'Pendiente'
                                                 ? 'text-dark' : '' ?>"
                                      style="font-size:.7rem">
                                    <?= $cita['tipo'] ?>
                                </span>
                                <div class="d-flex gap-1">
                                    <a href="<?= BASE_URL ?>/citas/ver/<?= $cita['id'] ?>"
                                       class="btn btn-sm btn-outline-secondary
                                              btn-action"
                                       title="Ver detalle"
                                       style="width:24px;height:24px;
                                              font-size:.7rem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/citas/editar/<?= $cita['id'] ?>"
                                       class="btn btn-sm btn-outline-warning
                                              btn-action"
                                       title="Editar"
                                       style="width:24px;height:24px;
                                              font-size:.7rem">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- ── FIN TARJETA ──────────────────── -->
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

</div>