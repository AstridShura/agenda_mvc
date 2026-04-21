<?php
// Vista: Formulario crear contacto
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/contactos"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card" style="max-width:700px; margin:auto">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-person-plus me-2"></i><?= $titulo ?>
        </h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/contactos/crear">

            <!-- Datos personales -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Apellido *</label>
                    <input type="text" name="apellido" class="form-control"
                           value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($datos['email'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Dirección</label>
                <input type="text" name="direccion" class="form-control"
                       value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Alias</label>
                <input type="text" name="alias" class="form-control"
                       value="<?= htmlspecialchars($datos['alias'] ?? '') ?>">
            </div>            

            <div class="mb-4">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="id_categoria" class="form-select">
                    <option value="">— Sin categoría —</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (isset($datos['id_categoria']) &&
                                $datos['id_categoria'] == $cat['id'])
                                ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ── Teléfonos dinámicos ── -->
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="bi bi-telephone me-1"></i>Teléfonos
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary"
                        id="btnAgregarTel">
                    <i class="bi bi-plus-circle me-1"></i>Agregar teléfono
                </button>
            </div>

            <div id="contenedorTelefonos">
                <!-- Fila inicial de teléfono -->
                <div class="row g-2 mb-2 fila-telefono">
                    <div class="col-7">
                        <input type="text" name="numeros[]"
                               class="form-control" placeholder="Número">
                    </div>
                    <div class="col-4">
                        <select name="tipos[]" class="form-select">
                            <option>Personal</option>
                            <option>Trabajo</option>
                            <option>Casa</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div class="col-1 d-flex align-items-center">
                        <button type="button"
                                class="btn btn-sm btn-outline-danger btnEliminarTel"
                                title="Quitar">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>

            <hr class="mt-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/contactos"
                   class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Guardar Contacto
                </button>
            </div>

        </form>
    </div>
</div>
