<!-- views/incidente/list.php -->
<?php include 'views/layout/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Incidentes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lista de Incidentes</h1>
        <a href="index.php?entity=incidente&action=create" class="btn btn-primary">Crear Incidente</a>
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
    
    <!-- Campo de búsqueda (se insertará dinámicamente por SearchComponent) -->
    
    <table class="table table-bordered table-hover" id="incidentes-table">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Fecha de Ocurrencia</th>
                <th>Estado <small class="text-muted">(click para cambiar)</small></th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($incidentes)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <em>No se encontraron incidentes</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($incidentes as $incidente): ?>
                <tr>
                    <td><strong><?= $incidente['id'] ?></strong></td>
                    <td class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($incidente['descripcion']) ?>">
                        <?= strlen($incidente['descripcion']) > 80 ? substr($incidente['descripcion'], 0, 80) . '...' : $incidente['descripcion'] ?>
                    </td>
                    <td><?= $incidente['fecha_ocurrencia'] ?></td>
                    <td>
                        <!-- El EstadoManager convertirá esto en un dropdown interactivo -->
                        <span class="badge badge-<?= $incidente['estado_nombre'] === 'Abierto' ? 'danger' : ($incidente['estado_nombre'] === 'En Proceso' ? 'warning' : ($incidente['estado_nombre'] === 'Resuelto' ? 'success' : 'secondary')) ?>">
                            <?= $incidente['estado_nombre'] ?>
                        </span>
                    </td>
                    <td><?= $incidente['usuario_nombre'] ?></td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="index.php?entity=incidente&action=show&id=<?= $incidente['id'] ?>" 
                               class="btn btn-info btn-sm" title="Ver detalles">👁️</a>
                            <a href="index.php?entity=incidente&action=edit&id=<?= $incidente['id'] ?>" 
                               class="btn btn-warning btn-sm" title="Editar">✏️</a>
                            <a href="index.php?entity=incidente&action=delete&id=<?= $incidente['id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('¿Está seguro de eliminar este incidente?')" 
                               title="Eliminar">🗑️</a>
                            <a href="index.php?entity=incidente&action=planes_accion&id=<?= $incidente['id'] ?>" 
                               class="btn btn-secondary btn-sm" title="Planes de Acción">📋</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Estadísticas rápidas -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">📊 Resumen de Incidentes</h6>
                    <div class="row text-center">
                        <?php 
                        $total = count($incidentes);
                        $abiertos = count(array_filter($incidentes, fn($i) => $i['estado_nombre'] === 'Abierto'));
                        $enProceso = count(array_filter($incidentes, fn($i) => $i['estado_nombre'] === 'En Proceso'));
                        $resueltos = count(array_filter($incidentes, fn($i) => $i['estado_nombre'] === 'Resuelto'));
                        $cerrados = count(array_filter($incidentes, fn($i) => $i['estado_nombre'] === 'Cerrado'));
                        ?>
                        <div class="col-3">
                            <h4 class="text-primary"><?= $total ?></h4>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-danger"><?= $abiertos ?></h4>
                            <small class="text-muted">Abiertos</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-warning"><?= $enProceso ?></h4>
                            <small class="text-muted">En Proceso</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-success"><?= $resueltos + $cerrados ?></h4>
                            <small class="text-muted">Resueltos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
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

    <!-- Botones de acción adicionales -->
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <div class="btn-group" role="group" aria-label="Acciones adicionales">
                <button type="button" class="btn btn-outline-info" onclick="window.print()">
                    🖨️ Imprimir Lista
                </button>
                <button type="button" class="btn btn-outline-success" onclick="exportarCSV()">
                    📊 Exportar CSV
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                    🔄 Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/components/SearchComponent.js"></script>
<script src="assets/js/components/EstadoManager.js"></script>

<script>
// Función para exportar a CSV
function exportarCSV() {
    const tabla = document.getElementById('incidentes-table');
    let csv = [];
    
    // Headers
    const headers = [];
    tabla.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim().replace(/\n/g, ' '));
    });
    csv.push(headers.join(','));
    
    // Rows
    tabla.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach((td, index) => {
            if (index < 5) { // Solo las primeras 5 columnas (sin acciones)
                let texto = td.textContent.trim().replace(/,/g, ';').replace(/\n/g, ' ');
                row.push(`"${texto}"`);
            }
        });
        if (row.length > 0) {
            csv.push(row.join(','));
        }
    });
    
    // Descargar
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `incidentes_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl+N para nuevo incidente
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        window.location.href = 'index.php?entity=incidente&action=create';
    }
    
    // F5 para actualizar
    if (e.key === 'F5') {
        e.preventDefault();
        location.reload();
    }
});

// Tooltip para botones de acción
$(document).ready(function() {
    $('[title]').tooltip();
    
    // Mensaje de bienvenida si no hay incidentes
    <?php if (empty($incidentes)): ?>
    setTimeout(function() {
        const toast = `
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000" style="position: fixed; top: 20px; right: 20px; z-index: 1055;">
                <div class="toast-header">
                    <strong class="mr-auto">💡 Sugerencia</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    No hay incidentes registrados. ¿Quieres <a href="index.php?entity=incidente&action=create">crear el primero</a>?
                </div>
            </div>
        `;
        $('body').append(toast);
        $('.toast').toast('show');
    }, 1000);
    <?php endif; ?>
});

// Función para resaltar filas según estado crítico
$(document).ready(function() {
    $('#incidentes-table tbody tr').each(function() {
        const estadoBadge = $(this).find('.badge');
        if (estadoBadge.hasClass('badge-danger')) {
            $(this).addClass('table-warning'); // Resaltar incidentes abiertos
        }
    });
});
</script>

<style>
/* Estilos adicionales para la tabla */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-group-sm .btn {
    margin-right: 2px;
}

.badge {
    cursor: pointer;
    transition: all 0.2s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* Estilos para impresión */
@media print {
    .btn, .card:last-child, .btn-group {
        display: none !important;
    }
    
    .container {
        max-width: 100% !important;
    }
    
    .table {
        font-size: 12px;
    }
}

/* Animación para filas nuevas */
@keyframes highlightRow {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

.row-highlight {
    animation: highlightRow 2s ease-out;
}
</style>
</body>
</html>