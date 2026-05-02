<?php
// Vista: Mapa general de todos los contactos
?>

<!-- ── Encabezado ──────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
        <?= $titulo ?>
    </h2>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/contactos"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        <a href="<?= BASE_URL ?>/contactos/crear"
           class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>Nuevo Contacto
        </a>
    </div>
</div>

<!-- ── Estadísticas rápidas ───────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3">
            <h3 class="fw-bold text-primary mb-0"><?= $total ?></h3>
            <small class="text-muted">Contactos en el mapa</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <h3 class="fw-bold text-secondary mb-0">
                <?= $totalGeneral - $total ?>
            </h3>
            <small class="text-muted">Sin ubicación asignada</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <h3 class="fw-bold text-success mb-0"><?= $totalGeneral ?></h3>
            <small class="text-muted">Total de contactos</small>
        </div>
    </div>
</div>

<?php if (empty($contactos)): ?>
    <!-- Sin ubicaciones -->
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-geo-alt display-3 mb-3"></i>
        <h5>Ningún contacto tiene ubicación asignada</h5>
        <p>Edita un contacto y marca su ubicación en el mapa.</p>
        <a href="<?= BASE_URL ?>/contactos"
           class="btn btn-primary mt-2">
            Ir a Contactos
        </a>
    </div>

<?php else: ?>

    <div class="row g-3">

        <!-- ── Mapa principal ─────────────────────────── -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex
                            justify-content-between
                            align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-map me-2"></i>
                        Mapa de Contactos
                    </h6>
                    <!-- Filtro por categoría -->
                    <select id="filtroCat" class="form-select form-select-sm"
                            style="max-width:180px">
                        <option value="">Todas las categorías</option>
                        <?php
                        // Obtener categorías únicas
                        $cats = array_unique(
                            array_filter(array_column($contactos, 'categoria'))
                        );
                        sort($cats);
                        foreach ($cats as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>">
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="card-body p-0">
                    <div id="mapaGeneral"
                         style="height:520px; z-index:1;">
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Lista de contactos en el mapa ─────────── -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Contactos ubicados
                    </h6>
                </div>
                <div class="card-body p-0"
                     style="overflow-y:auto; max-height:520px">
                    <div class="list-group list-group-flush"
                         id="listaContactosMapa">
                        <?php foreach ($contactos as $c): ?>
                        <a href="#"
                           class="list-group-item list-group-item-action
                                  item-contacto-mapa"
                           data-id="<?= $c['id'] ?>"
                           data-lat="<?= $c['latitud'] ?>"
                           data-lng="<?= $c['longitud'] ?>"
                           data-cat="<?= htmlspecialchars($c['categoria'] ?? '') ?>">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Punto de color de categoría -->
                                <span style="
                                    width:10px; height:10px;
                                    background:<?= htmlspecialchars($c['categoria_color'] ?? '#6c757d') ?>;
                                    border-radius:50%;
                                    flex-shrink:0">
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold"
                                         style="font-size:.9rem">
                                        <?= htmlspecialchars($c['apellido']) ?>,
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </div>
                                    <?php if ($c['direccion']): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?= htmlspecialchars(
                                            substr($c['direccion'], 0, 40)
                                        ) ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>

<!-- Datos de contactos para JS -->
<script>
var contactosMapa = <?= json_encode($contactos) ?>;
var baseUrl       = '<?= BASE_URL ?>';
</script>