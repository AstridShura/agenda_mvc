<?php // Vista: Formulario editar cita ?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/citas/ver/<?= $cita['id'] ?>"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="card" style="max-width:700px; margin:auto">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i><?= $titulo ?>
        </h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= BASE_URL ?>/citas/editar/<?= $cita['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Título *</label>
                <input type="text" name="titulo" class="form-control"
                       value="<?= htmlspecialchars($cita['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Contacto *</label>
                <select name="id_contacto" id="selectContacto" class="form-select">
                    <option value="">— Selecciona un contacto —</option>
                    <?php foreach ($contactos as $con): ?>
                        <option value="<?= $con['id'] ?>"
                            <?= $cita['id_contacto'] == $con['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($con['apellido']) ?>,
                            <?= htmlspecialchars($con['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Fecha *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    <input type="text" name="fecha_cita" id="fechaCita"
                           class="form-control flatpickr-fecha"
                           value="<?= htmlspecialchars($cita['fecha_cita']) ?>"
                           required readonly>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hora inicio *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="text" name="hora_inicio" id="horaInicio"
                               class="form-control flatpickr-hora"
                               value="<?= substr($cita['hora_inicio'], 0, 5) ?>"
                               required readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hora fin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                        <input type="text" name="hora_fin" id="horaFin"
                               class="form-control flatpickr-hora"
                               value="<?= $cita['hora_fin'] ? substr($cita['hora_fin'], 0, 5) : '' ?>"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="tipo" class="form-select">
                        <?php foreach (['Reunion','Llamada','Visita','Otro'] as $tipo): ?>
                            <option value="<?= $tipo ?>"
                                <?= $cita['tipo'] === $tipo ? 'selected' : '' ?>>
                                <?= $tipo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <?php foreach (['Pendiente','Confirmada','Cancelada'] as $est): ?>
                            <option value="<?= $est ?>"
                                <?= $cita['estado'] === $est ? 'selected' : '' ?>>
                                <?= $est ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"
                    ><?= htmlspecialchars($cita['descripcion'] ?? '') ?></textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/citas/ver/<?= $cita['id'] ?>"
                   class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>