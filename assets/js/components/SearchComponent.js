// assets/js/components/SearchComponent.js (Versión con debug)
class IncidenteSearchComponent {
    constructor() {
        this.searchField = document.getElementById('incidente-search');
        this.resultsContainer = document.getElementById('search-results');
        this.originalTable = document.getElementById('incidentes-table');
        this.init();
    }
    
    init() {
        this.createSearchField();
        this.bindEvents();
    }
    
    createSearchField() {
        const searchHtml = `
            <div class="search-container mb-3">
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           id="incidente-search" 
                           placeholder="Buscar por ID de incidente..."
                           min="1">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="search-btn">
                             Buscar
                        </button>
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="clear-search-btn">
                             Limpiar
                        </button>
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
    }
    
    async executeSearch() {
        const id = this.searchField.value.trim();
        
        if (!id) {
            this.showError('Por favor ingrese un ID');
            return;
        }
        
        if (!this.isValidId(id)) {
            this.showError('ID debe ser un número mayor a 0');
            return;
        }
        
        this.showLoader();
        
        // URL que se va a llamar (para debug)
        const url = `index.php?entity=incidente&action=buscar_por_id&id=${id}`;
        console.log('Llamando URL:', url);
        
        try {
            const response = await fetch(url);

            // Verificar si la respuesta es válida
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Verificar el content-type
            const contentType = response.headers.get('content-type');
            
            if (!contentType || !contentType.includes('application/json')) {
                // Si no es JSON, obtener el texto para debug
                const text = await response.text();
                throw new Error('El servidor no devolvió JSON válido');
            }
            
            const result = await response.json();
            console.log('Resultado JSON:', result);
            
            if (result.success) {
                this.displayResult(result.data);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error(' Error completo:', error);
            this.showError(`Error: ${error.message}`);
        }
    }
    
    isValidId(id) {
        return /^\d+$/.test(id) && parseInt(id) > 0;
    }
    
    displayResult(incidente) {
        this.originalTable.style.display = 'none';
        
        const resultHtml = `
            <div class="alert alert-success">
                <strong>Incidente encontrado:</strong> ID ${incidente.id}
            </div>
            <table class="table table-bordered">
                <thead>
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
                    <tr class="table-warning">
                        <td><strong>${incidente.id}</strong></td>
                        <td>${incidente.descripcion}</td>
                        <td>${incidente.fecha_ocurrencia}</td>
                        <td>${incidente.estado_nombre}</td>
                        <td>${incidente.usuario_nombre}</td>
                        <td>
                            <a href="index.php?entity=incidente&action=show&id=${incidente.id}" class="btn btn-info btn-sm">Ver</a>
                            <a href="index.php?entity=incidente&action=edit&id=${incidente.id}" class="btn btn-warning btn-sm">Editar</a>
                            <a href="index.php?entity=incidente&action=planes_accion&id=${incidente.id}" class="btn btn-secondary btn-sm">Planes de Acción</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        `;
        
        this.resultsContainer.innerHTML = resultHtml;
    }
    
    showError(message) {
        this.resultsContainer.innerHTML = `
            <div class="alert alert-danger">
                <strong>Error:</strong> ${message}
            </div>
        `;
        this.originalTable.style.display = 'table';
    }
    
    showLoader() {
        this.resultsContainer.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Buscando...</span>
                </div>
                <p>Buscando incidente...</p>
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

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('incidentes-table');
    if (table) {
        new IncidenteSearchComponent();
    } else {
        console.error('No se encontró la tabla incidentes-table');
    }
});