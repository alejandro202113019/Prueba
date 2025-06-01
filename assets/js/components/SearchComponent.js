// assets/js/components/SearchComponent.js - Versión corregida
class IncidenteSearchComponent {
    constructor() {
        this.init();
    }
    
    init() {
        // Verificar que estamos en la página correcta
        const table = document.getElementById('incidentes-table');
        if (!table) {
            console.log('Tabla incidentes-table no encontrada');
            return;
        }
        
        this.originalTable = table;
        this.createSearchField();
        this.bindEvents();
        console.log('✅ SearchComponent inicializado correctamente');
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
        this.originalTable.insertAdjacentHTML('beforebegin', searchHtml);
        
        // Actualizar referencias
        this.searchField = document.getElementById('incidente-search');
        this.resultsContainer = document.getElementById('search-results');
    }
    
    bindEvents() {
        // Búsqueda al presionar Enter
        this.searchField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.executeSearch();
            }
        });
        
        // Búsqueda al hacer click en botón
        document.getElementById('search-btn').addEventListener('click', () => {
            this.executeSearch();
        });
        
        // Limpiar búsqueda
        document.getElementById('clear-search-btn').addEventListener('click', () => {
            this.clearSearch();
        });
        
        // Limpiar cuando el campo esté vacío
        this.searchField.addEventListener('input', (e) => {
            if (e.target.value === '') {
                this.clearSearch();
            }
        });
    }
    
    async executeSearch() {
        const id = this.searchField.value.trim();
        
        if (!id) {
            this.showError('Por favor ingrese un ID');
            return;
        }
        
        if (!this.isValidId(id)) {
            this.showError('El ID debe ser un número mayor a 0');
            return;
        }
        
        this.showLoader();
        
        const url = `index.php?entity=incidente&action=buscar_por_id&id=${id}`;
        console.log('🔍 Buscando en:', url);
        
        try {
            const response = await fetch(url);
            console.log('📡 Respuesta recibida:', response.status, response.statusText);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            console.log('📋 Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('❌ Respuesta no es JSON:', text.substring(0, 200));
                throw new Error('El servidor no devolvió JSON válido');
            }
            
            const result = await response.json();
            console.log('✅ Resultado JSON:', result);
            
            if (result.success) {
                this.displayResult(result.data);
            } else {
                this.showError(result.message || 'Incidente no encontrado');
            }
        } catch (error) {
            console.error('❌ Error completo:', error);
            this.showError(`Error: ${error.message}`);
        }
    }
    
    isValidId(id) {
        return /^\d+$/.test(id) && parseInt(id) > 0;
    }
    
    displayResult(incidente) {
        this.originalTable.style.display = 'none';
        
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
            'Abierto': 'danger',
            'En Proceso': 'warning',
            'Resuelto': 'success',
            'Cerrado': 'secondary'
        };
        return classes[estado] || 'primary';
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
        this.originalTable.style.display = 'table';
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
        this.searchField.value = '';
        this.resultsContainer.innerHTML = '';
        this.originalTable.style.display = 'table';
        this.searchField.focus();
    }
}

// Inicializar solo en páginas de incidentes
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.href.includes('entity=incidente') || 
        document.getElementById('incidentes-table')) {
        console.log('🔍 Inicializando SearchComponent para incidentes...');
        new IncidenteSearchComponent();
    }
});