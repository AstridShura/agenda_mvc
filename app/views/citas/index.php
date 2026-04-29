<?php
// Vista: Lista + Calendario de citas
$coloresEstado = [
    'Pendiente'  => 'warning',
    'Confirmada' => 'success',
    'Cancelada'  => 'danger',
];
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
        <i class="bi bi-calendar2-week-fill me-2 text-primary"></i><?= $titulo ?>
    </h2>
    <div class="d-flex gap-2 flex-wrap">

        <!-- Toggle Tabla / Calendario -->
        <div class="btn-group btn-group-sm" role="group">
            <button type="button"
                    class="btn btn-outline-primary active"
                    id="btnVistTabla"
                    onclick="mostrarVista('tabla')">
                <i class="bi bi-list-ul me-1"></i>Tabla
            </button>
            <button type="button"
                    class="btn btn-outline-primary"
                    id="btnVistaCalendario"
                    onclick="mostrarVista('calendario')">
                <i class="bi bi-calendar3 me-1"></i>Calendario
            </button>
        </div>

        <!-- Exportar Excel -->
        <a href="<?= BASE_URL ?>/citas/exportarexcel"
           class="btn btn-success btn-sm"
           title="Descargar listado en Excel">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>

        <!-- Exportar PDF -->
        <a href="<?= BASE_URL ?>/citas/exportarpdf"
           class="btn btn-danger btn-sm"
           title="Descargar listado en PDF">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>

        <!-- Exportar Word -->
        <a href="<?= BASE_URL ?>/citas/exportarword"
        class="btn btn-primary btn-sm"
        title="Descargar en Word">
            <i class="bi bi-file-earmark-word me-1"></i>Word
        </a>

        <!-- Exportar Imagen -->
        <a href="<?= BASE_URL ?>/citas/exportarimagen"
        class="btn btn-info btn-sm"
        title="Descargar Infografía JPG">
            <i class="bi bi-file-earmark-image me-1"></i>Imagen
        </a>

        <!-- Nueva Cita -->
        <a href="<?= BASE_URL ?>/citas/crear"
           class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Nueva Cita
        </a>
        <!-- Botón Kanban -->
        <a href="<?= BASE_URL ?>/citas/kanban"
        class="btn btn-outline-primary btn-sm">
            <i class="bi bi-kanban me-1"></i>Kanban
        </a>
    </div>
</div>

<!-- ── Buscador ───────────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="position-relative" style="max-width:450px">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="inputBuscarCita"
                       class="form-control border-start-0"
                       placeholder="Buscar por título o contacto..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary"
                        id="btnLimpiarCita" style="display:none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="resultadosBusquedaCita"
                 class="position-absolute w-100 bg-white border rounded-2 shadow-sm"
                 style="display:none; z-index:1000; top:100%; max-height:300px; overflow-y:auto">
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- VISTA TABLA                                           -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="vistaTbla">

    <!-- Tabs por estado -->
    <ul class="nav nav-tabs mb-3" id="tabsEstado">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-estado="todas">
                Todas
                <span class="badge bg-secondary ms-1"><?= $conteos['todas'] ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-estado="Pendiente">
                Pendientes
                <span class="badge bg-warning text-dark ms-1">
                    <?= $conteos['Pendiente'] ?>
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-estado="Confirmada">
                Confirmadas
                <span class="badge bg-success ms-1">
                    <?= $conteos['Confirmada'] ?>
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-estado="Cancelada">
                Canceladas
                <span class="badge bg-danger ms-1">
                    <?= $conteos['Cancelada'] ?>
                </span>
            </a>
        </li>
    </ul>

    <?php if (empty($citas)): ?>
        <div class="card p-5 text-center text-muted">
            <i class="bi bi-calendar-x display-3 mb-3"></i>
            <h5>No hay citas registradas</h5>
            <a href="<?= BASE_URL ?>/citas/crear" class="btn btn-primary mt-2">
                Agendar primera cita
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0"
                       id="tablaCitas">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Contacto</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($citas as $c): ?>
                        <tr data-estado="<?= $c['estado'] ?>">
                            <td>
                                <strong><?= htmlspecialchars($c['titulo']) ?></strong>
                                <?php if ($c['titulo']): ?>
                                    <br><small class="text-muted">
                                        <?= htmlspecialchars(substr($c['titulo'], 0, 50)) ?>
                                        <?= strlen($c['titulo']) > 50 ? '...' : '' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/citas/ver/<?= $c['id_contacto'] ?>"
                                   class="text-decoration-none">
                                    <i class="bi bi-person me-1 text-muted"></i>
                                    <?= htmlspecialchars($c['contacto_apellido']) ?>,
                                    <?= htmlspecialchars($c['contacto_nombre']) ?>
                                </a>
                            </td>
                            <td>
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                <?= date('d/m/Y', strtotime($c['fecha_cita'])) ?>
                            </td>
                            <td>
                                <i class="bi bi-clock me-1 text-muted"></i>
                                <?= substr($c['hora_inicio'], 0, 5) ?>
                                <?php if ($c['hora_fin']): ?>
                                    → <?= substr($c['hora_fin'], 0, 5) ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <i class="bi <?= $iconosTipo[$c['tipo']] ?? 'bi-calendar-event' ?>
                                            text-primary"></i>
                                <small class="d-block text-muted">
                                    <?= htmlspecialchars($c['tipo']) ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <!-- Dropdown para cambiar estado directo -->
                                <div class="dropdown">
                                    <button class="badge border-0 dropdown-toggle
                                                   bg-<?= $coloresEstado[$c['estado']] ?? 'secondary' ?>
                                                   <?= $c['estado'] === 'Pendiente' ? 'text-dark' : '' ?>"
                                            data-bs-toggle="dropdown">
                                        <?= $c['estado'] ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><h6 class="dropdown-header">Cambiar estado</h6></li>
                                        <?php foreach (['Pendiente','Confirmada','Cancelada'] as $est): ?>
                                            <?php if ($est !== $c['estado']): ?>
                                            <li>
                                                <a class="dropdown-item"
                                                   href="<?= BASE_URL ?>/citas/cambiarEstado/<?= $c['id'] ?>?estado=<?= $est ?>">
                                                    <span class="badge bg-<?= $coloresEstado[$est] ?>
                                                                 <?= $est === 'Pendiente' ? 'text-dark' : '' ?> me-2">
                                                        <?= $est ?>
                                                    </span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>/citas/ver/<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-info btn-action me-1"
                                   title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/citas/editar/<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-warning btn-action me-1"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/citas/eliminar/<?= $c['id'] ?>"
                                   class="btn btn-sm btn-outline-danger btn-action"
                                   title="Eliminar"
                                   onclick="return confirm('¿Eliminar esta cita?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- 28/04/26 Se adiciono para mostrar el paginador -->
    <!-- ── Paginador ───────────────────────────────── -->
    <?= $paginador->renderizar(BASE_URL . '/citas') ?>  

    <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════ -->
<!-- VISTA CALENDARIO — FullCalendar                       -->
<!-- ══════════════════════════════════════════════════════ -->
<div id="vistaCalendario" style="display:none">
    <div class="card">
        <div class="card-body">
            <!-- Leyenda de colores -->
            <div class="d-flex gap-3 mb-3">
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-circle-fill me-1"></i>Pendiente
                </span>
                <span class="badge bg-success">
                    <i class="bi bi-circle-fill me-1"></i>Confirmada
                </span>
                <span class="badge bg-danger">
                    <i class="bi bi-circle-fill me-1"></i>Cancelada
                </span>
            </div>
            <!-- Contenedor del calendario -->
            <div id="calendarioCitas"></div>
        </div>
    </div>
</div>