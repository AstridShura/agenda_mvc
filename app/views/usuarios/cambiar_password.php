<?php
// Vista: Formulario cambiar password de usuario
?>

<div class="mb-4">
    <a href="<?= BASE_URL ?>/usuarios/ver/<?= $usuario['id'] ?>"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver al perfil
    </a>
</div>

<div class="card" style="max-width:500px; margin:auto">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-key-fill text-warning fs-5"></i>
        <h5 class="mb-0">
            <?= $titulo ?> —
            <span class="text-muted fw-normal">
                <?= htmlspecialchars($usuario['nombre']) ?>
                <?= htmlspecialchars($usuario['apellido']) ?>
            </span>
        </h5>
    </div>
    <div class="card-body">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Información de seguridad -->
        <div class="alert alert-info py-2 mb-4">
            <i class="bi bi-shield-lock me-2"></i>
            El password se guarda encriptado.
            Nadie puede verlo, solo verificarlo.
        </div>

        <form method="POST"
              action="<?= BASE_URL ?>/usuarios/cambiarpassword/<?= $usuario['id'] ?>">

            <!-- Password actual -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i>Password actual *
                </label>
                <div class="input-group">
                    <input  type="password"
                            name="password_actual"
                            id="passActual"
                            class="form-control"
                            placeholder="Ingresa tu password actual"
                            required>
                    <button class="btn btn-outline-secondary"
                            type="button"
                            onclick="togglePass('passActual', this)"
                            title="Mostrar/ocultar">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <hr class="my-3">

            <!-- Password nuevo -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-lock-fill me-1"></i>Nuevo password *
                </label>
                <div class="input-group">
                    <input  type="password"
                            name="password_nuevo"
                            id="passNuevo"
                            class="form-control"
                            placeholder="Mínimo 8 caracteres"
                            required
                            oninput="verificarFuerza(this.value)">
                    <button class="btn btn-outline-secondary"
                            type="button"
                            onclick="togglePass('passNuevo', this)"
                            title="Mostrar/ocultar">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <!-- Barra de fuerza del password -->
                <div class="mt-2">
                    <div class="progress" style="height:6px">
                        <div id="fuerzaBarra"
                             class="progress-bar"
                             style="width:0%; transition:all .3s">
                        </div>
                    </div>
                    <small id="fuerzaTexto" class="text-muted"></small>
                </div>
            </div>

            <!-- Confirmar password -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-lock-fill me-1"></i>Confirmar nuevo password *
                </label>
                <div class="input-group">
                    <input  type="password"
                            name="password_confirm"
                            id="passConfirm"
                            class="form-control"
                            placeholder="Repite el nuevo password"
                            required
                            oninput="verificarCoincidencia()">
                    <button class="btn btn-outline-secondary"
                            type="button"
                            onclick="togglePass('passConfirm', this)"
                            title="Mostrar/ocultar">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small id="coincidenciaTexto" class="mt-1 d-block"></small>
            </div>

            <hr>
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/usuarios/ver/<?= $usuario['id'] ?>"
                   class="btn btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-warning" id="btnGuardar">
                    <i class="bi bi-key me-1"></i>Cambiar Password
                </button>
            </div>

        </form>
    </div>
</div>