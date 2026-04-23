<?php // Vista: Formulario crear usuario ?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card" style="max-width:700px; margin:auto">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i><?= $titulo ?></h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/usuarios/crear">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido *</label>
                    <input type="text" name="apellido" class="form-control"
                           value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email *</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($datos['email'] ?? '') ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Usuario *</label>
                    <input type="text" name="usuario" class="form-control"
                           value="<?= htmlspecialchars($datos['usuario'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password *</label>
                    <!-- type="password" oculta el texto al escribir -->
                    <input type="password" name="password" class="form-control"
                           placeholder="Mínimo 8 caracteres" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol</label>
                    <!-- Select en lugar de input libre -->
                    <select name="rol" class="form-select">
                        <option value="usuario" <?= ($datos['rol'] ?? '') === 'usuario' ? 'selected' : '' ?>>
                            Usuario
                        </option>
                        <option value="admin" <?= ($datos['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            Administrador
                        </option>
                        <option value="sistemas" <?= ($datos['rol'] ?? '') === 'sistemas' ? 'selected' : '' ?>>
                            Sistemas
                        </option>                        
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
                    <!-- Checkbox en lugar de input numérico -->
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox"
                               name="activo" id="activoCheck"
                               <?= !isset($datos['activo']) || $datos['activo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activoCheck">
                            Usuario activo
                        </label>
                    </div>
                </div>
            </div>

            <!-- fecha_alta NO aparece — la genera SQL Server automáticamente -->

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Guardar Usuario
                </button>
            </div>

        </form>
    </div>
</div>