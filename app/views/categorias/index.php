<?php
// Vista: Lista de categorías
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-tags-fill me-2 text-primary"></i><?= $titulo ?>
    </h2>
    <a href="<?= BASE_URL ?>/categorias/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
    </a>
</div>

<!-- Adicionaremos el buscador dinamico Autocompletable -->
<!-- Buscador dinámico de Categorias, con IDs propios  -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="position-relative" style="max-width: 450px;">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input  type="text"
                        id="inputBuscarCat"
                        class="form-control border-start-0"
                        placeholder="Buscar por nombre, apellido, alias o email..."
                        autocomplete="off">
                <button class="btn btn-outline-secondary"
                        id="btnLimpiarCat"
                        style="display:none"
                        title="Limpiar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Resultados del autocompletado -->
            <div id="resultadosBusquedaCat"
                 class="position-absolute w-100 bg-white border rounded-2 shadow-sm"
                 style="display:none; z-index:1000; top:100%; max-height:350px; overflow-y:auto">
            </div>
        </div>
        <small class="text-muted mt-1 d-block">
            Escribe al menos 2 caracteres para buscar
        </small>
    </div>
</div>
<!-- Fin buscador dinamico Autocompletable -->

<?php if (empty($categorias)): ?>

    <div class="card p-5 text-center text-muted">
        <i class="bi bi-tags display-3 mb-3"></i>
        <h5>No hay categorías aún</h5>
        <a href="<?= BASE_URL ?>/categorias/crear"
           class="btn btn-primary mt-2">
            Agregar primera categoría
        </a>
    </div>

<?php else: ?>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="60">Color</th>
                        <th>Nombre</th>
                        <th>Código HEX</th>
                        <th class="text-center">Contactos</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <!-- Muestra el color como círculo visual -->
                        <td>
                            <span style="
                                display:inline-block;
                                width:32px; height:32px;
                                background:<?= htmlspecialchars($cat['color']) ?>;
                                border-radius:50%;
                                border:2px solid #dee2e6;
                                vertical-align:middle;">
                            </span>
                        </td>
                        <td>
                            <span class="badge fs-6 fw-semibold px-3 py-2"
                                  style="background:<?= htmlspecialchars($cat['color']) ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </span>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($cat['color']) ?></code>
                        </td>
                        <td class="text-center">
                            <?php if ($cat['total_contactos'] > 0): ?>
                                <a href="<?= BASE_URL ?>/contactos"
                                   class="badge bg-primary text-decoration-none">
                                    <i class="bi bi-people me-1"></i>
                                    <?= $cat['total_contactos'] ?>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Editar -->
                            <a href="<?= BASE_URL ?>/categorias/editar/<?= $cat['id'] ?>"
                               class="btn btn-sm btn-outline-warning btn-action me-1"
                               title="Editar categoría">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Eliminar -->
                            <?php if ($cat['total_contactos'] == 0): ?>
                                <a href="<?= BASE_URL ?>/categorias/eliminar/<?= $cat['id'] ?>"
                                   class="btn btn-sm btn-outline-danger btn-action"
                                   title="Eliminar categoría"
                                   onclick="return confirm(
                                       '¿Eliminar la categoría <?= htmlspecialchars($cat['nombre']) ?>?'
                                   )">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <!-- Botón deshabilitado si tiene contactos -->
                                <button class="btn btn-sm btn-outline-danger btn-action"
                                        title="No se puede eliminar: tiene <?= $cat['total_contactos'] ?> contacto(s)"
                                        disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen estadístico -->
    <div class="mt-3 text-muted" style="font-size:.85rem">
        <i class="bi bi-info-circle me-1"></i>
        <?= count($categorias) ?> categoría(s) registrada(s).
        El botón eliminar se deshabilita si la categoría tiene contactos asociados.
    </div>

<?php endif; ?>