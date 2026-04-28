<?php
// Vista: Lista de contactos
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-people-fill me-2 text-primary"></i><?= $titulo ?>
    </h2>
    <div class="d-flex gap-2 flex-wrap">

        <!-- Exportar Excel -->
        <a href="<?= BASE_URL ?>/contactos/exportarexcel"
           class="btn btn-success btn-sm"
           title="Descargar listado en Excel">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>

        <!-- Exportar PDF -->
        <a href="<?= BASE_URL ?>/contactos/exportarpdf"
           class="btn btn-danger btn-sm"
           title="Descargar listado en PDF">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>

        <!-- Nuevo Contacto -->
        <a href="<?= BASE_URL ?>/contactos/crear"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>Nuevo Contacto
        </a>

    </div>
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
                        id="inputBuscar"
                        class="form-control border-start-0"
                        placeholder="Buscar por nombre, apellido, alias o email..."
                        autocomplete="off">
                <button class="btn btn-outline-secondary"
                        id="btnLimpiar"
                        style="display:none"
                        title="Limpiar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Resultados del autocompletado -->
            <div id="resultadosBusqueda"
                 class="position-absolute w-100 bg-white border rounded-2 shadow-sm"
                 style="display:none; z-index:1000; top:100%; max-height:350px; overflow-y:auto">
            </div>
        </div>
        <small class="text-muted mt-1 d-block">
            Escribe al menos 2 caracteres para buscar
        </small>
    </div>
</div>

<!-- ── Tabla de contactos ─────────────────────────────── -->

<?php if (empty($contactos)): ?>
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-journal-x display-3 mb-3"></i>
        <h5>No hay contactos aún</h5>
        <a href="<?= BASE_URL ?>/contactos/crear" class="btn btn-primary mt-2">
            Agregar primer contacto
        </a>
    </div>

<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Categoría</th>
                        <th class="text-center">Teléfonos</th>
                        <th class="text-center">Alias</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contactos as $c): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($c['apellido']) ?>, <?= htmlspecialchars($c['nombre']) ?></strong>
                        </td>
                        <td>
                            <?php if ($c['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($c['email']) ?>">
                                    <?= htmlspecialchars($c['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['categoria']): ?>
                                <span class="badge"
                                      style="background:<?= $c['categoria_color'] ?>">
                                    <?= htmlspecialchars($c['categoria']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                <i class="bi bi-telephone me-1"></i>
                                <?= $c['total_telefonos'] ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($c['alias'] ?? "") ?: " "; ?></strong>
                        </td>                        
                        <td class="text-center">
                            <!-- Ver -->
                            <a href="<?= BASE_URL ?>/contactos/ver/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-info btn-action me-1"
                               title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <!-- Editar -->
                            <a href="<?= BASE_URL ?>/contactos/editar/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-warning btn-action me-1"
                               title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Eliminar -->
                            <a href="<?= BASE_URL ?>/contactos/eliminar/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-danger btn-action"
                               title="Eliminar"
                               onclick="return confirm('¿ Esta seguro de eliminar este contacto <?= htmlspecialchars($c['apellido']) ?>, <?= htmlspecialchars($c['nombre']) ?> ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- 27/04/26 Se adiciono para mostrar el paginador -->
    <!-- ── Paginador ───────────────────────────────── -->
    <?= $paginador->renderizar(BASE_URL . '/contactos') ?>
    
<?php endif; ?>