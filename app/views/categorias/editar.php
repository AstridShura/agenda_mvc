<?php
// Vista: Formulario editar categoría
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/categorias"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card" style="max-width:500px; margin:auto">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i><?= $titulo ?>
        </h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= BASE_URL ?>/categorias/editar/<?= $categoria['id'] ?>">

            <!-- Nombre -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nombre de la categoría *
                </label>
                <input  type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= htmlspecialchars($categoria['nombre']) ?>"
                        required>
            </div>

            <!-- Color -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Color</label>
                <div class="d-flex align-items-center gap-3">
                    <input  type="color"
                            name="color"
                            id="colorPicker"
                            class="form-control form-control-color"
                            value="<?= htmlspecialchars($categoria['color']) ?>"
                            title="Elige un color">
                    <input  type="text"
                            id="colorHex"
                            class="form-control"
                            value="<?= htmlspecialchars($categoria['color']) ?>"
                            readonly
                            style="max-width:120px; font-family:monospace">
                    <span   id="previewBadge"
                            class="badge fs-6 px-3 py-2"
                            style="background:<?= htmlspecialchars($categoria['color']) ?>">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </span>
                </div>
                <small class="text-muted">
                    El color se aplicará en todos los contactos de esta categoría.
                </small>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/categorias"
                   class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>