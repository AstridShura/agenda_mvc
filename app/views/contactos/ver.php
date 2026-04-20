<?php
// Vista: Detalle de un contacto
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/contactos"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row g-4">

    <!-- ── Datos del contacto ── -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Datos del Contacto
                </h5>
                <a href="<?= BASE_URL ?>/contactos/editar/<?= $contacto['id'] ?>"
                   class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="35%" class="text-muted">Nombre</th>
                        <td><?= htmlspecialchars($contacto['nombre']) ?>
                            <?= htmlspecialchars($contacto['apellido']) ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td>
                            <?php if ($contacto['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($contacto['email']) ?>">
                                    <?= htmlspecialchars($contacto['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Dirección</th>
                        <td><?= htmlspecialchars($contacto['direccion'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Categoría</th>
                        <td>
                            <?php if ($contacto['categoria']): ?>
                                <span class="badge"
                                      style="background:<?= $contacto['categoria_color'] ?>">
                                    <?= htmlspecialchars($contacto['categoria']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Registrado</th>
                        <td><?= date('d/m/Y', strtotime($contacto['fecha_alta'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- ── Teléfonos ── -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-telephone me-2"></i>Teléfonos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($telefonos)): ?>
                    <p class="text-muted text-center py-3">
                        <i class="bi bi-telephone-x display-6 d-block mb-2"></i>
                        Sin teléfonos registrados
                    </p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                    <?php foreach ($telefonos as $t): ?>
                        <li class="list-group-item d-flex
                                   justify-content-between align-items-center px-0">
                            <span>
                                <i class="bi bi-telephone-fill text-success me-2"></i>
                                <?= htmlspecialchars($t['numero']) ?>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars($t['tipo']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Botón eliminar -->
<div class="mt-4">
    <a href="<?= BASE_URL ?>/contactos/eliminar/<?= $contacto['id'] ?>"
       class="btn btn-danger"
       onclick="return confirm('¿Eliminar este contacto y todos sus teléfonos?')">
        <i class="bi bi-trash me-1"></i>Eliminar Contacto
    </a>
</div>