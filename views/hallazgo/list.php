<!-- views/hallazgo/list.php -->
<?php include 'views/layout/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Hallazgos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lista de Hallazgos</h1>
        <div>
            <a href="index.php?entity=hallazgo&action=create" class="btn btn-primary">Crear Hallazgo</a>
            <a href="index.php?entity=hallazgo&action=estadisticas_sedes" class="btn btn-info">📊 Estadísticas por Sede</a>
        </div>
    </div>

    <!-- Filtro por Sede -->
    <div class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label for="sede-filter">Filtrar por Sede:</label>
                <select class="form-control" id="sede-filter">
                    <option value="">Todas las sedes</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>" <?= (isset($_GET['sede_id']) && $_GET['sede_id'] == $sede['id']) ? 'selected' : '' ?>>
                            <?= $sede['nombre'] ?> (<?= $sede['ciudad'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-secondary" id="clear-sede-filter">
                    🗑️ Limpiar
                </button>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <?php if (isset($sedeSeleccionada) && $sedeSeleccionada): ?>
                    <div class="alert alert-info mb-0">
                        <small>Mostrando <?= count($hallazgos) ?> hallazgos de: <strong><?= $sedeSeleccionada['nombre'] ?></strong></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Leyenda de Estados -->
    <div class="mb-3">
        <div class="card">
            <div class="card-body py-2">
                <small class="text-muted">
                    <strong>Estados:</strong>
                    <span class="badge badge-danger">🚨 Abierto</span>
                    <span class="badge badge-warning">⚠️ En Proceso</span>
                    <span class="badge badge-success">✅ Resuelto</span>
                    <span class="badge badge-secondary">🔒 Cerrado</span>
                    <em class="ml-2">Haz clic en cualquier estado para cambiarlo</em>
                </small>
            </div>
        </div>
    </div>

    <!-- Tabla de Hallazgos -->
    <table class="table table-bordered table-hover" id="hallazgos-table">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado <small class="text-muted">(click para cambiar)</small></th>
                <th>Usuario</th>
                <th>Sede</th>
                <th>Procesos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hallazgos)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <em>No se encontraron hallazgos<?= isset($sedeSeleccionada) ? ' para esta sede' : '' ?></em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($hallazgos as $hallazgo): ?>
                <tr>
                    <td><strong><?= $hallazgo['id'] ?></strong></td>
                    <td><?= $hallazgo['titulo'] ?></td>
                    <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($hallazgo['descripcion']) ?>">
                        <?= strlen($hallazgo['descripcion']) > 50 ? substr($hallazgo['descripcion'], 0, 50) . '...' : $hallazgo['descripcion'] ?>
                    </td>
                    <td>
                        <!-- El EstadoManager convertirá esto en un dropdown interactivo -->
                        <span class="badge badge-<?= $hallazgo['estado_nombre'] === 'Abierto' ? 'danger' : ($hallazgo['estado_nombre'] === 'En Proceso' ? 'warning' : ($hallazgo['estado_nombre'] === 'Resuelto' ? 'success' : 'secondary')) ?>">
                            <?= $hallazgo['estado_nombre'] ?>
                        </span>
                    </td>
                    <td><?= $hallazgo['usuario_nombre'] ?></td>
                    <td>
                        <?php if ($hallazgo['sede_nombre']): ?>
                            <span class="badge badge-info"><?= $hallazgo['sede_nombre'] ?></span>
                        <?php else: ?>
                            <span class="text-muted"><em>Sin sede</em></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($hallazgo['procesos'])): ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach (array_slice($hallazgo['procesos'], 0, 2) as $proceso): ?>
                                    <li><small>• <?= $proceso['nombre'] ?></small></li>
                                <?php endforeach; ?>
                                <?php if (count($hallazgo['procesos']) > 2): ?>
                                    <li><small class="text-muted">... y <?= count($hallazgo['procesos']) - 2 ?> más</small></li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <small class="text-muted">Sin procesos</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group-vertical btn-group-sm" role="group">
                            <a href="index.php?entity=hallazgo&action=show&id=<?= $hallazgo['id'] ?>" 
                               class="btn btn-info btn-sm" title="Ver detalles">👁️</a>
                            <a href="index.php?entity=hallazgo&action=edit&id=<?= $hallazgo['id'] ?>" 
                               class="btn btn-warning btn-sm" title="Editar">✏️</a>
                            <a href="index.php?entity=hallazgo&action=delete&id=<?= $hallazgo['id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('¿Está seguro de eliminar este hallazgo?')" 
                               title="Eliminar">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">ℹ️ Información sobre cambio de estados</h6>
                    <p class="card-text small text-muted">
                        • <strong>Abierto → En Proceso/Resuelto:</strong> Comenzar trabajo o resolver directamente<br>
                        • <strong>En Proceso → Abierto/Resuelto/Cerrado:</strong> Regresar, resolver o cerrar<br>
                        • <strong>Resuelto → Cerrado/En Proceso:</strong> Cerrar definitivamente o reabrir<br>
                        • <strong>Cerrado → Abierto:</strong> Reabrir (requiere justificación)
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/components/SedeManager.js"></script>
<script src="assets/js/components/EstadoManager.js"></script>

<script>
// Mejorar el filtro de sedes
document.getElementById('sede-filter').addEventListener('change', function() {
    const sedeId = this.value;
    if (sedeId) {
        window.location.href = `index.php?entity=hallazgo&action=index&sede_id=${sedeId}`;
    } else {
        window.location.href = 'index.php?entity=hallazgo&action=index';
    }
});

document.getElementById('clear-sede-filter').addEventListener('click', function() {
    window.location.href = 'index.php?entity=hallazgo&action=index';
});
</script>
</body>
</html>