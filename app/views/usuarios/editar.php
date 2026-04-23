<?php // Vista: Formulario editar usuario ?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/usuarios/ver/<?= $usuario['id'] ?>"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card" style="max-width:700px; margin:auto">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i><?= $titulo ?></h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/usuarios/editar/<?= $usuario['id'] ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido *</label>
                    <input type="text" name="apellido" class="form-control"
                           value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email *</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Usuario *</label>
                <input type="text" name="usuario" class="form-control"
                       value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
            </div>

            <!-- Password NO aparece aquí — tiene su propio formulario -->
            <div class="alert alert-info py-2 mb-3">
                <i class="bi bi-info-circle me-2"></i>
                El password se cambia desde
                <a href="<?= BASE_URL ?>/usuarios/cambiarPassword/<?= $usuario['id'] ?>">
                    aquí
                </a>.
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol</label>
                    <select name="rol" class="form-select">
                        <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>
                            Usuario
                        </option>
                        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>
                            Administrador
                        </option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox"
                               name="activo" id="activoCheck"
                               <?= $usuario['activo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activoCheck">
                            Usuario activo
                        </label>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/usuarios/ver/<?= $usuario['id'] ?>"
                   class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>