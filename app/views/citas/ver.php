<?php
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= BASE_URL ?>/citas" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/citas/editar/<?= $cita['id'] ?>"
           class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="<?= BASE_URL ?>/citas/eliminar/<?= $cita['id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('¿Eliminar esta cita?')">
            <i class="bi bi-trash me-1"></i>Eliminar
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi <?= $iconosTipo[$cita['tipo']] ?? 'bi-calendar-event' ?>
                            text-primary fs-5"></i>
                <h5 class="mb-0"><?= htmlspecialchars($cita['titulo']) ?></h5>
                <span class="badge bg-<?= $coloresEstado[$cita['estado']] ?>
                             <?= $cita['estado'] === 'Pendiente' ? 'text-dark' : '' ?> ms-auto">
                    <?= $cita['estado'] ?>
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Contacto</th>
                        <td>
                            <a href="<?= BASE_URL ?>/contactos/ver/<?= $cita['id_contacto'] ?>">
                                <i class="bi bi-person me-1"></i>
                                <?= htmlspecialchars($cita['contacto_nombre']) ?>
                                <?= htmlspecialchars($cita['contacto_apellido']) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Fecha</th>
                        <td>
                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                            <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?>
                            — <?= date('l', strtotime($cita['fecha_cita'])) ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Horario</th>
                        <td>
                            <i class="bi bi-clock me-1 text-primary"></i>
                            <?= substr($cita['hora_inicio'], 0, 5) ?>
                            <?php if ($cita['hora_fin']): ?>
                                → <?= substr($cita['hora_fin'], 0, 5) ?>
                                <small class="text-muted ms-2">
                                    (<?php
                                        $ini = strtotime($cita['hora_inicio']);
                                        $fin = strtotime($cita['hora_fin']);
                                        $dif = ($fin - $ini) / 60;
                                        echo $dif . ' min';
                                    ?>)
                                </small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tipo</th>
                        <td>
                            <i class="bi <?= $iconosTipo[$cita['tipo']] ?? 'bi-calendar-event' ?>
                                        me-1 text-primary"></i>
                            <?= htmlspecialchars($cita['tipo']) ?>
                        </td>
                    </tr>
                    <?php if ($cita['descripcion']): ?>
                    <tr>
                        <th class="text-muted">Descripción</th>
                        <td><?= nl2br(htmlspecialchars($cita['descripcion'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th class="text-muted">Registrada</th>
                        <td><?= date('d/m/Y H:i', strtotime($cita['fecha_alta'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Cambio rápido de estado -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado
                </h6>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <?php foreach (['Pendiente','Confirmada','Cancelada'] as $est): ?>
                    <a href="<?= BASE_URL ?>/citas/cambiarEstado/<?= $cita['id'] ?>?estado=<?= $est ?>"
                       class="btn btn-<?= $coloresEstado[$est] ?>
                              <?= $est === 'Pendiente' ? '' : '' ?>
                              <?= $cita['estado'] === $est ? 'disabled' : '' ?>">
                        <?php if ($cita['estado'] === $est): ?>
                            <i class="bi bi-check-circle me-1"></i>
                        <?php endif; ?>
                        <?= $est ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>