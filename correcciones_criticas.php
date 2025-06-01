<?php
// correcciones_criticas.php - Archivo para aplicar correcciones automáticas

echo "<h1>🔧 Aplicando Correcciones Críticas</h1>";

// ===== CORRECCIÓN 1: Vista de Hallazgo/Create =====
echo "<h2>1. Corrigiendo views/hallazgo/create.php</h2>";

$createHallazgoContent = '<!-- views/hallazgo/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Hallazgo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include \'views/layout/header.php\'; ?>
<div class="container mt-4">
    <h1>Crear Hallazgo</h1>
    <form action="index.php?entity=hallazgo&action=create" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="id_estado">Estado</label>
                    <select class="form-control" id="id_estado" name="id_estado" required>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado[\'id\'] ?>"><?= $estado[\'nombre\'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_usuario">Usuario Responsable</label>
                    <select class="form-control" id="id_usuario" name="id_usuario" required>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario[\'id\'] ?>"><?= $usuario[\'nombre\'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- CAMPO SEDE CORREGIDO -->
                <div class="form-group">
                    <label for="sede_id">Sede 🏢</label>
                    <select class="form-control" id="sede_id" name="sede_id">
                        <option value="">Sin sede asignada</option>
                        <?php if (isset($sedes) && !empty($sedes)): ?>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede[\'id\'] ?>">
                                    <?= $sede[\'nombre\'] ?> (<?= $sede[\'ciudad\'] ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted">Selecciona la sede donde ocurrió el hallazgo</small>
                </div>
                
                <div class="form-group">
                    <label for="procesos">Procesos Relacionados</label>
                    <select multiple class="form-control" id="procesos" name="procesos[]" size="6">
                        <?php foreach ($procesos as $proceso): ?>
                            <option value="<?= $proceso[\'id\'] ?>"><?= $proceso[\'nombre\'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples procesos</small>
                </div>
            </div>
        </div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Guardar Hallazgo</button>
            <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>';

if (file_put_contents('views/hallazgo/create.php', $createHallazgoContent)) {
    echo "✅ views/hallazgo/create.php corregido<br>";
} else {
    echo "❌ Error escribiendo views/hallazgo/create.php<br>";
}

// ===== CORRECCIÓN 2: Vista de Hallazgo/Edit =====
echo "<h2>2. Corrigiendo views/hallazgo/edit.php</h2>";

$editHallazgoContent = '<!-- views/hallazgo/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Hallazgo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include \'views/layout/header.php\'; ?>
<div class="container mt-4">
    <h1>Editar Hallazgo</h1>
    <form action="index.php?entity=hallazgo&action=edit&id=<?= $hallazgo[\'id\'] ?>" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $hallazgo[\'titulo\'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= $hallazgo[\'descripcion\'] ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="id_estado">Estado</label>
                    <select class="form-control" id="id_estado" name="id_estado" required>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?= $estado[\'id\'] ?>" <?= ($estado[\'id\'] == $hallazgo[\'id_estado\']) ? \'selected\' : \'\' ?>>
                                <?= $estado[\'nombre\'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_usuario">Usuario Responsable</label>
                    <select class="form-control" id="id_usuario" name="id_usuario" required>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario[\'id\'] ?>" <?= ($usuario[\'id\'] == $hallazgo[\'id_usuario\']) ? \'selected\' : \'\' ?>>
                                <?= $usuario[\'nombre\'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- CAMPO SEDE CORREGIDO -->
                <div class="form-group">
                    <label for="sede_id">Sede 🏢</label>
                    <select class="form-control" id="sede_id" name="sede_id">
                        <option value="">Sin sede asignada</option>
                        <?php if (isset($sedes) && !empty($sedes)): ?>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede[\'id\'] ?>" <?= ($sede[\'id\'] == $hallazgo[\'sede_id\']) ? \'selected\' : \'\' ?>>
                                    <?= $sede[\'nombre\'] ?> (<?= $sede[\'ciudad\'] ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted">
                        <?php if ($hallazgo[\'sede_nombre\']): ?>
                            Actualmente asignado a: <strong><?= $hallazgo[\'sede_nombre\'] ?></strong>
                        <?php else: ?>
                            Este hallazgo no tiene sede asignada
                        <?php endif; ?>
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="procesos">Procesos Relacionados</label>
                    <select multiple class="form-control" id="procesos" name="procesos[]" size="6">
                        <?php foreach ($procesos as $proceso): ?>
                            <option value="<?= $proceso[\'id\'] ?>" <?= in_array($proceso[\'id\'], $selectedProcesoIds) ? \'selected\' : \'\' ?>>
                                <?= $proceso[\'nombre\'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Mantén presionada la tecla Ctrl para seleccionar múltiples procesos</small>
                </div>
            </div>
        </div>
        
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php?entity=hallazgo&action=index" class="btn btn-secondary">Cancelar</a>
            <a href="index.php?entity=hallazgo&action=show&id=<?= $hallazgo[\'id\'] ?>" class="btn btn-info">Ver Detalles</a>
        </div>
    </form>
</div>
</body>
</html>';

if (file_put_contents('views/hallazgo/edit.php', $editHallazgoContent)) {
    echo "✅ views/hallazgo/edit.php corregido<br>";
} else {
    echo "❌ Error escribiendo views/hallazgo/edit.php<br>";
}

// ===== CORRECCIÓN 3: Vista de Incidente/Create =====
echo "<h2>3. Corrigiendo views/incidente/create.php</h2>";

$createIncidenteContent = '<!-- views/incidente/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Incidente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include \'views/layout/header.php\'; ?>
<div class="container mt-4">
    <h1>Crear Incidente</h1>
    <form action="index.php?entity=incidente&action=create" method="POST">
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_ocurrencia">Fecha de Ocurrencia</label>
            <input type="date" class="form-control" id="fecha_ocurrencia" name="fecha_ocurrencia" required>
        </div>
        <div class="form-group">
            <label for="id_estado">Estado</label>
            <select class="form-control" id="id_estado" name="id_estado" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado[\'id\'] ?>"><?= $estado[\'nombre\'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Responsable</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario[\'id\'] ?>"><?= $usuario[\'nombre\'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php?entity=incidente&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>';

if (file_put_contents('views/incidente/create.php', $createIncidenteContent)) {
    echo "✅ views/incidente/create.php corregido<br>";
} else {
    echo "❌ Error escribiendo views/incidente/create.php<br>";
}

// ===== CORRECCIÓN 4: Vista de Incidente/Edit =====
echo "<h2>4. Corrigiendo views/incidente/edit.php</h2>";

$editIncidenteContent = '<!-- views/incidente/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Incidente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include \'views/layout/header.php\'; ?>
<div class="container mt-4">
    <h1>Editar Incidente</h1>
    <form action="index.php?entity=incidente&action=edit&id=<?= $incidente[\'id\'] ?>" method="POST">
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?= $incidente[\'descripcion\'] ?></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_ocurrencia">Fecha de Ocurrencia</label>
            <input type="date" class="form-control" id="fecha_ocurrencia" name="fecha_ocurrencia" value="<?= $incidente[\'fecha_ocurrencia\'] ?>" required>
        </div>
        <div class="form-group">
            <label for="id_estado">Estado</label>
            <select class="form-control" id="id_estado" name="id_estado" required>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado[\'id\'] ?>" <?= ($estado[\'id\'] == $incidente[\'id_estado\']) ? \'selected\' : \'\' ?>>
                        <?= $estado[\'nombre\'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_usuario">Usuario Responsable</label>
            <select class="form-control" id="id_usuario" name="id_usuario" required>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario[\'id\'] ?>" <?= ($usuario[\'id\'] == $incidente[\'id_usuario\']) ? \'selected\' : \'\' ?>>
                        <?= $usuario[\'nombre\'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index.php?entity=incidente&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>';

if (file_put_contents('views/incidente/edit.php', $editIncidenteContent)) {
    echo "✅ views/incidente/edit.php corregido<br>";
} else {
    echo "❌ Error escribiendo views/incidente/edit.php<br>";
}

// ===== CORRECCIÓN 5: SearchComponent.js mejorado =====
echo "<h2>5. Corrigiendo SearchComponent.js</h2>";

$searchComponentContent = '// assets/js/components/SearchComponent.js - Versión corregida
class IncidenteSearchComponent {
    constructor() {
        this.init();
    }
    
    init() {
        // Verificar que estamos en la página correcta
        const table = document.getElementById(\'incidentes-table\');
        if (!table) {
            console.log(\'Tabla incidentes-table no encontrada\');
            return;
        }
        
        this.originalTable = table;
        this.createSearchField();
        this.bindEvents();
        console.log(\'✅ SearchComponent inicializado correctamente\');
    }
    
    createSearchField() {
        const searchHtml = `
            <div class="search-container mb-3">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">🔍 Búsqueda de Incidentes por ID</h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   id="incidente-search" 
                                   placeholder="Ingresa el ID del incidente..."
                                   min="1">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" 
                                        type="button" 
                                        id="search-btn">
                                    🔍 Buscar
                                </button>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="clear-search-btn">
                                    🗑️ Limpiar
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Presiona Enter o haz clic en Buscar para encontrar un incidente específico
                        </small>
                    </div>
                </div>
            </div>
            <div id="search-results"></div>
        `;
        
        // Insertar antes de la tabla
        this.originalTable.insertAdjacentHTML(\'beforebegin\', searchHtml);
        
        // Actualizar referencias
        this.searchField = document.getElementById(\'incidente-search\');
        this.resultsContainer = document.getElementById(\'search-results\');
    }
    
    bindEvents() {
        // Búsqueda al presionar Enter
        this.searchField.addEventListener(\'keypress\', (e) => {
            if (e.key === \'Enter\') {
                e.preventDefault();
                this.executeSearch();
            }
        });
        
        // Búsqueda al hacer click en botón
        document.getElementById(\'search-btn\').addEventListener(\'click\', () => {
            this.executeSearch();
        });
        
        // Limpiar búsqueda
        document.getElementById(\'clear-search-btn\').addEventListener(\'click\', () => {
            this.clearSearch();
        });
        
        // Limpiar cuando el campo esté vacío
        this.searchField.addEventListener(\'input\', (e) => {
            if (e.target.value === \'\') {
                this.clearSearch();
            }
        });
    }
    
    async executeSearch() {
        const id = this.searchField.value.trim();
        
        if (!id) {
            this.showError(\'Por favor ingrese un ID\');
            return;
        }
        
        if (!this.isValidId(id)) {
            this.showError(\'El ID debe ser un número mayor a 0\');
            return;
        }
        
        this.showLoader();
        
        const url = `index.php?entity=incidente&action=buscar_por_id&id=${id}`;
        console.log(\'🔍 Buscando en:\', url);
        
        try {
            const response = await fetch(url);
            console.log(\'📡 Respuesta recibida:\', response.status, response.statusText);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get(\'content-type\');
            console.log(\'📋 Content-Type:\', contentType);
            
            if (!contentType || !contentType.includes(\'application/json\')) {
                const text = await response.text();
                console.error(\'❌ Respuesta no es JSON:\', text.substring(0, 200));
                throw new Error(\'El servidor no devolvió JSON válido\');
            }
            
            const result = await response.json();
            console.log(\'✅ Resultado JSON:\', result);
            
            if (result.success) {
                this.displayResult(result.data);
            } else {
                this.showError(result.message || \'Incidente no encontrado\');
            }
        } catch (error) {
            console.error(\'❌ Error completo:\', error);
            this.showError(`Error: ${error.message}`);
        }
    }
    
    isValidId(id) {
        return /^\\d+$/.test(id) && parseInt(id) > 0;
    }
    
    displayResult(incidente) {
        this.originalTable.style.display = \'none\';
        
        const resultHtml = `
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✅ Incidente Encontrado: ID ${incidente.id}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Ocurrencia</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-success">
                                    <td><strong>${incidente.id}</strong></td>
                                    <td>${incidente.descripcion}</td>
                                    <td>${incidente.fecha_ocurrencia}</td>
                                    <td>
                                        <span class="badge badge-${this.getEstadoBadgeClass(incidente.estado_nombre)}">
                                            ${incidente.estado_nombre}
                                        </span>
                                    </td>
                                    <td>${incidente.usuario_nombre}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?entity=incidente&action=show&id=${incidente.id}" 
                                               class="btn btn-info btn-sm">👁️ Ver</a>
                                            <a href="index.php?entity=incidente&action=edit&id=${incidente.id}" 
                                               class="btn btn-warning btn-sm">✏️ Editar</a>
                                            <a href="index.php?entity=incidente&action=planes_accion&id=${incidente.id}" 
                                               class="btn btn-secondary btn-sm">📋 Planes</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        
        this.resultsContainer.innerHTML = resultHtml;
    }
    
    getEstadoBadgeClass(estado) {
        const classes = {
            \'Abierto\': \'danger\',
            \'En Proceso\': \'warning\',
            \'Resuelto\': \'success\',
            \'Cerrado\': \'secondary\'
        };
        return classes[estado] || \'primary\';
    }
    
    showError(message) {
        this.resultsContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>❌ Error:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        this.originalTable.style.display = \'table\';
    }
    
    showLoader() {
        this.resultsContainer.innerHTML = `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Buscando...</span>
                </div>
                <p class="mt-2">🔍 Buscando incidente...</p>
            </div>
        `;
    }
    
    clearSearch() {
        this.searchField.value = \'\';
        this.resultsContainer.innerHTML = \'\';
        this.originalTable.style.display = \'table\';
        this.searchField.focus();
    }
}

// Inicializar solo en páginas de incidentes
document.addEventListener(\'DOMContentLoaded\', function() {
    if (window.location.href.includes(\'entity=incidente\') || 
        document.getElementById(\'incidentes-table\')) {
        console.log(\'🔍 Inicializando SearchComponent para incidentes...\');
        new IncidenteSearchComponent();
    }
});';

if (!is_dir('assets/js/components')) {
    mkdir('assets/js/components', 0755, true);
}

if (file_put_contents('assets/js/components/SearchComponent.js', $searchComponentContent)) {
    echo "✅ SearchComponent.js corregido<br>";
} else {
    echo "❌ Error escribiendo SearchComponent.js<br>";
}

// ===== CORRECCIÓN 6: EstadoManager.js mejorado =====
echo "<h2>6. Corrigiendo EstadoManager.js</h2>";

$estadoManagerContent = '// assets/js/components/EstadoManager.js - Versión corregida
class EstadoManager {
    constructor() {
        this.entity = this.detectEntity();
        console.log(`🔄 Iniciando EstadoManager para: ${this.entity}`);
        this.init();
    }
    
    detectEntity() {
        const url = window.location.href;
        if (url.includes(\'entity=incidente\')) return \'incidente\';
        if (url.includes(\'entity=hallazgo\')) return \'hallazgo\';
        
        // Detectar por tabla presente
        if (document.getElementById(\'incidentes-table\')) return \'incidente\';
        if (document.getElementById(\'hallazgos-table\')) return \'hallazgo\';
        
        return \'hallazgo\'; // default
    }
    
    init() {
        // Esperar a que la página esté completamente cargada
        if (document.readyState === \'loading\') {
            document.addEventListener(\'DOMContentLoaded\', () => this.setupEstadoButtons());
        } else {
            this.setupEstadoButtons();
        }
    }
    
    setupEstadoButtons() {
        this.createEstadoButtons();
        this.bindEvents();
        console.log(`✅ EstadoManager configurado para ${this.entity}`);
    }
    
    createEstadoButtons() {
        // Buscar badges de estado en la tabla
        const estadoBadges = document.querySelectorAll(\'.badge\');
        
        estadoBadges.forEach((badge, index) => {
            const row = badge.closest(\'tr\');
            if (!row || row.closest(\'thead\')) return; // Saltar headers
            
            const recordId = this.extractRecordId(row);
            const currentEstado = badge.textContent.trim();
            
            if (recordId && currentEstado && this.isValidEstado(currentEstado)) {
                this.enhanceEstadoBadge(badge, recordId, currentEstado);
            }
        });
    }
    
    isValidEstado(estado) {
        const validEstados = [\'Abierto\', \'En Proceso\', \'Resuelto\', \'Cerrado\'];
        return validEstados.includes(estado);
    }
    
    extractRecordId(row) {
        const firstCell = row.querySelector(\'td:first-child\');
        return firstCell ? firstCell.textContent.trim() : null;
    }
    
    enhanceEstadoBadge(badge, recordId, currentEstado) {
        // Crear contenedor con dropdown
        const container = document.createElement(\'div\');
        container.className = \'estado-container\';
        container.dataset.recordId = recordId;
        container.dataset.currentEstado = currentEstado;
        
        container.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-sm btn-${this.getEstadoColor(currentEstado)} dropdown-toggle estado-btn" 
                        type="button" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        title="Haz clic para cambiar estado">
                    ${this.getEstadoIcon(currentEstado)} ${currentEstado}
                </button>
                <div class="dropdown-menu estado-dropdown">
                    <h6 class="dropdown-header">Cambiar estado a:</h6>
                    <div class="estado-options" data-loading="false">
                        <div class="text-center p-2">
                            <small class="text-muted">Cargando opciones...</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Reemplazar el badge original
        badge.parentNode.replaceChild(container, badge);
    }
    
    bindEvents() {
        // Event delegation para manejar clicks
        document.addEventListener(\'click\', (e) => {
            if (e.target.classList.contains(\'estado-option\')) {
                this.handleEstadoChange(e.target);
            }
        });
        
        // Cargar opciones cuando se abre el dropdown
        document.addEventListener(\'show.bs.dropdown\', (e) => {
            const dropdown = e.target.querySelector(\'.estado-dropdown\');
            if (dropdown) {
                this.loadEstadoOptions(e.target);
            }
        });
    }
    
    async loadEstadoOptions(dropdownButton) {
        const container = dropdownButton.closest(\'.estado-container\');
        const recordId = container.dataset.recordId;
        const currentEstado = container.dataset.currentEstado;
        const optionsContainer = container.querySelector(\'.estado-options\');
        
        if (optionsContainer.dataset.loading === \'true\') return;
        
        optionsContainer.dataset.loading = \'true\';
        optionsContainer.innerHTML = \'<div class="text-center p-2"><small>⏳ Cargando...</small></div>\';
        
        try {
            const url = `index.php?entity=${this.entity}&action=obtener_estados_permitidos&record_id=${recordId}&estado_actual=${encodeURIComponent(currentEstado)}`;
            console.log(\'📡 Cargando estados desde:\', url);
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const result = await response.json();
            console.log(\'📋 Estados recibidos:\', result);
            
            if (result.success && result.data.length > 0) {
                this.renderEstadoOptions(optionsContainer, result.data, recordId, currentEstado);
            } else {
                optionsContainer.innerHTML = \'<small class="text-muted px-3 py-2 d-block">No hay transiciones disponibles</small>\';
            }
        } catch (error) {
            console.error(\'❌ Error cargando opciones:\', error);
            optionsContainer.innerHTML = \'<small class="text-danger px-3 py-2 d-block">Error cargando opciones</small>\';
        } finally {
            optionsContainer.dataset.loading = \'false\';
        }
    }
    
    renderEstadoOptions(container, options, recordId, currentEstado) {
        let html = \'\';
        
        options.forEach(option => {
            html += `
                <button class="dropdown-item estado-option" 
                        data-record-id="${recordId}"
                        data-estado-actual="${currentEstado}"
                        data-estado-nuevo="${option.estado}"
                        title="${option.descripcion}">
                    <div class="d-flex align-items-center">
                        <span class="mr-2">${this.getEstadoIcon(option.estado)}</span>
                        <div>
                            <div class="fw-bold">${option.estado}</div>
                            <small class="text-muted">${option.descripcion}</small>
                        </div>
                    </div>
                </button>
            `;
        });
        
        if (html) {
            container.innerHTML = html;
        } else {
            container.innerHTML = \'<small class="text-muted px-3 py-2 d-block">Sin opciones disponibles</small>\';
        }
    }
    
    async handleEstadoChange(button) {
        const recordId = button.dataset.recordId;
        const estadoActual = button.dataset.estadoActual;
        const estadoNuevo = button.dataset.estadoNuevo;
        
        const confirmMessage = `¿Confirma cambiar el estado de "${estadoActual}" a "${estadoNuevo}"?`;
        if (!confirm(confirmMessage)) return;
        
        this.showEstadoLoader(recordId);
        
        try {
            const response = await fetch(\'index.php\', {
                method: \'POST\',
                headers: {
                    \'Content-Type\': \'application/x-www-form-urlencoded\',
                },
                body: new URLSearchParams({
                    entity: this.entity,
                    action: \'cambiar_estado\',
                    record_id: recordId,
                    estado_actual: estadoActual,
                    estado_nuevo: estadoNuevo,
                    usuario_id: 1 // TODO: Obtener usuario actual
                })
            });
            
            const result = await response.json();
            console.log(\'📋 Resultado cambio estado:\', result);
            
            if (result.success) {
                this.handleEstadoChangeSuccess(recordId, estadoNuevo, result.message);
            } else {
                this.handleEstadoChangeError(recordId, estadoActual, result.message);
            }
        } catch (error) {
            console.error(\'❌ Error cambiando estado:\', error);
            this.handleEstadoChangeError(recordId, estadoActual, \'Error de conexión\');
        }
    }
    
    showEstadoLoader(recordId) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector(\'.estado-btn\');
        
        button.innerHTML = \'<span class="spinner-border spinner-border-sm mr-1"></span>Cambiando...\';
        button.disabled = true;
    }
    
    handleEstadoChangeSuccess(recordId, nuevoEstado, message) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector(\'.estado-btn\');
        
        // Actualizar el botón
        button.className = `btn btn-sm btn-${this.getEstadoColor(nuevoEstado)} dropdown-toggle estado-btn`;
        button.innerHTML = `${this.getEstadoIcon(nuevoEstado)} ${nuevoEstado}`;
        button.disabled = false;
        
        // Actualizar dataset
        container.dataset.currentEstado = nuevoEstado;
        
        // Mostrar notificación de éxito
        this.showToast(\'success\', \'✅ Estado actualizado\', message);
        
        // Cerrar dropdown
        const dropdown = container.querySelector(\'.dropdown-toggle\');
        if ($(dropdown).parent().hasClass(\'show\')) {
            $(dropdown).dropdown(\'hide\');
        }
    }
    
    handleEstadoChangeError(recordId, estadoOriginal, errorMessage) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector(\'.estado-btn\');
        
        // Restaurar el botón original
        button.className = `btn btn-sm btn-${this.getEstadoColor(estadoOriginal)} dropdown-toggle estado-btn`;
        button.innerHTML = `${this.getEstadoIcon(estadoOriginal)} ${estadoOriginal}`;
        button.disabled = false;
        
        // Mostrar error
        this.showToast(\'error\', \'❌ Error\', errorMessage);
    }
    
    getEstadoColor(estado) {
        const colors = {
            \'Abierto\': \'danger\',
            \'En Proceso\': \'warning\',
            \'Resuelto\': \'success\',
            \'Cerrado\': \'secondary\'
        };
        return colors[estado] || \'primary\';
    }
    
    getEstadoIcon(estado) {
        const icons = {
            \'Abierto\': \'🚨\',
            \'En Proceso\': \'⚠️\',
            \'Resuelto\': \'✅\',
            \'Cerrado\': \'🔒\'
        };
        return icons[estado] || \'📋\';
    }
    
    showToast(type, title, message) {
        // Crear toast usando Bootstrap
        const toastHtml = `
            <div class="toast toast-${type}" role="alert" data-delay="4000" style="position: fixed; top: 20px; right: 20px; z-index: 1055; min-width: 300px;">
                <div class="toast-header bg-${type === \'success\' ? \'success\' : \'danger\'} text-white">
                    <strong class="mr-auto">${title}</strong>
                    <small>ahora</small>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        const toastElement = document.createElement(\'div\');
        toastElement.innerHTML = toastHtml;
        document.body.appendChild(toastElement.firstElementChild);
        
        // Activar toast
        const toast = document.body.lastElementChild;
        $(toast).toast(\'show\');
        
        // Remover después de que se oculte
        $(toast).on(\'hidden.bs.toast\', function() {
            this.remove();
        });
    }
}

// Inicializar automáticamente
document.addEventListener(\'DOMContentLoaded\', function() {
    // Verificar si estamos en una página de listado relevante
    const isListPage = document.getElementById(\'hallazgos-table\') || 
                      document.getElementById(\'incidentes-table\') ||
                      window.location.href.includes(\'action=index\');
    
    if (isListPage) {
        console.log(\'🔄 Inicializando EstadoManager...\');
        setTimeout(() => new EstadoManager(), 100); // Pequeño delay para asegurar que DOM esté listo
    }
});';

if (file_put_contents('assets/js/components/EstadoManager.js', $estadoManagerContent)) {
    echo "✅ EstadoManager.js corregido<br>";
} else {
    echo "❌ Error escribiendo EstadoManager.js<br>";
}

echo "<h2>✅ Correcciones Aplicadas</h2>";
echo "<p>Se han aplicado las siguientes correcciones críticas:</p>";
echo "<ul>";
echo "<li>✅ Corregidas vistas de hallazgos (create/edit) con campo sede</li>";
echo "<li>✅ Corregidas vistas de incidentes (create/edit)</li>";
echo "<li>✅ Mejorado SearchComponent.js con mejor manejo de errores</li>";
echo "<li>✅ Mejorado EstadoManager.js con detección automática</li>";
echo "</ul>";

echo "<h3>🚀 Próximos Pasos:</h3>";
echo "<ol>";
echo "<li>Ejecutar el <strong>diagnostico_completo.php</strong> para verificar estado</li>";
echo "<li>Probar cada historia de usuario manualmente</li>";
echo "<li>Verificar consola del navegador para errores JavaScript</li>";
echo "<li>Revisar logs de errores PHP</li>";
echo "</ol>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>⚠️ Importante:</h4>";
echo "<p>Si aún tienes errores específicos, compárteme:</p>";
echo "<ul>";
echo "<li>La salida del diagnostico_completo.php</li>";
echo "<li>Errores específicos que ves en el navegador</li>";
echo "<li>Errores en la consola JavaScript (F12)</li>";
echo "<li>Qué funcionalidad específica no está funcionando</li>";
echo "</ul>";
echo "</div>";
?>