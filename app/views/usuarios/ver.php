<?php
// Vista: Detalle de un usuario
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/usuarios"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row g-4">

    <!-- ── Datos del usuario ── -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Datos del Usuario
                </h5>
                <a href="<?= BASE_URL ?>/usuarios/editar/<?= $usuario['id'] ?>"
                   class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">

                    <tr>
                        <th class="text-muted">Nombre</th>
                        <td><?= htmlspecialchars($usuario['nombre']) ?>
                            <?= htmlspecialchars($usuario['apellido']) ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email</th>
                        <td>
                            <?php if ($usuario['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($usuario['email']) ?>">
                                    <?= htmlspecialchars($usuario['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Usuario</th>
                        <td><code><?= htmlspecialchars($usuario['usuario']) ?></code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Password</th>
                        <!-- NUNCA mostrar el hash — solo indicar que existe -->
                        <td>
                            <span class="text-muted">••••••••</span>
                            <a href="<?= BASE_URL ?>/usuarios/cambiarPassword/<?= $usuario['id'] ?>"
                            class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="bi bi-key me-1"></i>Cambiar
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Rol</th>
                        <td>
                            <span class="badge <?= $usuario['rol'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                                <?= htmlspecialchars($usuario['rol']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Estado</th>
                        <td>
                            <?php if ($usuario['activo']): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Activo
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Fecha Reg.</th>
                        <td><?= date('d/m/Y', strtotime($usuario['fecha_alta'])) ?></td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- Botón eliminar -->
<div class="mt-4">
    <a href="<?= BASE_URL ?>/usuarios/eliminar/<?= $usuario['id'] ?>"
       class="btn btn-danger"
       onclick="return confirm('¿Eliminar este usuario?')">
        <i class="bi bi-trash me-1"></i>Eliminar Usuario
    </a> 
</div>