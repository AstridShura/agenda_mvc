<?php
// Vista: Lista de usuarios
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-person-square me-2 text-primary"></i><?= $titulo ?>
    </h2>
    <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
    </a>
</div>

<!-- ── Buscador dinámico ───────────────────────────────── -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="position-relative" style="max-width: 450px;">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input  type="text"
                        id="inputBuscarUsu"
                        class="form-control border-start-0"
                        placeholder="Buscar por nombre, apellido, email o usuario"
                        autocomplete="off">
                <button class="btn btn-outline-secondary"
                        id="btnLimpiarUsu"
                        style="display:none"
                        title="Limpiar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Resultados del autocompletado -->
            <div id="resultadosBusquedaUsu"
                 class="position-absolute w-100 bg-white border rounded-2 shadow-sm"
                 style="display:none; z-index:1000; top:100%; max-height:350px; overflow-y:auto">
            </div>
        </div>
        <small class="text-muted mt-1 d-block">
            Escribe al menos 2 caracteres para buscar
        </small>
    </div>
</div>

<!-- ── Tabla de usuarios ─────────────────────────────── -->

<?php if (empty($usuarios)): ?>
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-journal-x display-3 mb-3"></i>
        <h5>No hay usuarios aún</h5>
        <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary mt-2">
            Agregar primer usuario
        </a>
    </div>

<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">

            <thead class="table-dark">
                <tr>
                    <th>Nombre y Apellido</th>
                    <th>Email</th>
                    <th>Usuario</th>
                    <th class="text-center">Rol</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Fecha Alta</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['nombre']) ?>,
                        <?= htmlspecialchars($u['apellido']) ?></strong>
                    </td>
                    <td>
                        <?php if ($u['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($u['email']) ?>">
                                <?= htmlspecialchars($u['email']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><code><?= htmlspecialchars($u['usuario']) ?></code></td>
                    <td class="text-center">
                        <!-- Badge según el rol -->
                        <span class="badge <?= $u['rol'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                            <?= htmlspecialchars($u['rol']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <!-- Semáforo visual para activo/inactivo -->
                        <?php if ($u['activo']): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Activo
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="bi bi-x-circle me-1"></i>Inactivo
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?= date('d/m/Y', strtotime($u['fecha_alta'])) ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/usuarios/ver/<?= $u['id'] ?>"
                        class="btn btn-sm btn-outline-info btn-action me-1" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/usuarios/editar/<?= $u['id'] ?>"
                        class="btn btn-sm btn-outline-warning btn-action me-1" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="<?= BASE_URL ?>/usuarios/eliminar/<?= $u['id'] ?>"
                        class="btn btn-sm btn-outline-danger btn-action"
                        title="Eliminar"
                        onclick="return confirm('¿Eliminar a <?= htmlspecialchars($u['nombre']) ?> <?= htmlspecialchars($u['apellido']) ?>?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

            </table>
        </div>
    </div>
<?php endif; ?>