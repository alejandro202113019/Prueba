// assets/js/components/SedeManager.js
class SedeManager {
    constructor() {
        this.sedeFilter = document.getElementById('sede-filter');
        this.hallazgosTable = document.getElementById('hallazgos-table');
        this.init();
    }
    
    init() {
        this.createSedeFilter();
        this.bindEvents();
    }
    
    createSedeFilter() {
        // Verificar si ya existe el filtro
        if (document.getElementById('sede-filter-container')) {
            return;
        }
        
        const filterHtml = `
            <div id="sede-filter-container" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="sede-filter">Filtrar por Sede:</label>
                        <select class="form-control" id="sede-filter">
                            <option value="">Todas las sedes</option>
                            <!-- Las opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary" id="clear-sede-filter">
                            🗑️ Limpiar
                        </button>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="alert alert-info" id="filter-status" style="display: none; margin: 0;">
                            <small id="filter-message"></small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Insertar antes de la tabla
        this.hallazgosTable.insertAdjacentHTML('beforebegin', filterHtml);
        
        // Actualizar referencias
        this.sedeFilter = document.getElementById('sede-filter');
        this.filterStatus = document.getElementById('filter-status');
        this.filterMessage = document.getElementById('filter-message');
        
        // Cargar sedes en el select
        this.loadSedes();
    }
    
    async loadSedes() {
        try {
            // En este caso, las sedes ya están disponibles en el PHP
            // Pero podríamos hacer una llamada AJAX si fuera necesario
            console.log('✅ Filtro de sedes cargado');
        } catch (error) {
            console.error('❌ Error cargando sedes:', error);
        }
    }
    
    bindEvents() {
        // Filtrar al cambiar sede
        document.addEventListener('change', (e) => {
            if (e.target && e.target.id === 'sede-filter') {
                this.filterBySede();
            }
        });
        
        // Limpiar filtro
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'clear-sede-filter') {
                this.clearFilter();
            }
        });
    }
    
    async filterBySede() {
        const sedeId = this.sedeFilter.value;
        const selectedSede = this.sedeFilter.options[this.sedeFilter.selectedIndex].text;
        
        this.showLoader();
        
        try {
            let url = 'index.php?entity=hallazgo&action=filtrar_por_sede';
            if (sedeId) {
                url += `&sede_id=${sedeId}`;
            }
            
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.updateTable(result.data);
                this.showFilterStatus(sedeId ? selectedSede : 'Todas las sedes', result.data.length);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('❌ Error en filtro:', error);
            this.showError('Error al filtrar hallazgos');
        }
    }
    
    updateTable(hallazgos) {
        const tbody = this.hallazgosTable.querySelector('tbody');
        
        if (hallazgos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <em>No se encontraron hallazgos para esta sede</em>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        hallazgos.forEach(hallazgo => {
            const procesosHtml = hallazgo.procesos.map(p => `<li>${p.nombre}</li>`).join('');
            
            html += `
                <tr>
                    <td>${hallazgo.id}</td>
                    <td>${hallazgo.titulo}</td>
                    <td>${hallazgo.descripcion}</td>
                    <td>${hallazgo.estado_nombre}</td>
                    <td>${hallazgo.usuario_nombre}</td>
                    <td><span class="badge badge-secondary">${hallazgo.sede_nombre || 'Sin sede'}</span></td>
                    <td>
                        <ul style="margin: 0; padding-left: 15px;">
                            ${procesosHtml}
                        </ul>
                    </td>
                    <td>
                        <a href="index.php?entity=hallazgo&action=show&id=${hallazgo.id}" class="btn btn-info btn-sm">Ver</a>
                        <a href="index.php?entity=hallazgo&action=edit&id=${hallazgo.id}" class="btn btn-warning btn-sm">Editar</a>
                        <a href="index.php?entity=hallazgo&action=delete&id=${hallazgo.id}" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro?')">Eliminar</a>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    showLoader() {
        const tbody = this.hallazgosTable.querySelector('tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <span class="ml-2">Filtrando hallazgos...</span>
                </td>
            </tr>
        `;
    }
    
    showFilterStatus(sedeName, count) {
        this.filterMessage.textContent = `Mostrando ${count} hallazgos de: ${sedeName}`;
        this.filterStatus.style.display = 'block';
    }
    
    showError(message) {
        this.filterMessage.textContent = `Error: ${message}`;
        this.filterStatus.className = 'alert alert-danger';
        this.filterStatus.style.display = 'block';
        
        // Revertir color después de 3 segundos
        setTimeout(() => {
            this.filterStatus.className = 'alert alert-info';
        }, 3000);
    }
    
    clearFilter() {
        this.sedeFilter.value = '';
        this.filterStatus.style.display = 'none';
        
        // Recargar página para mostrar todos los hallazgos
        window.location.href = 'index.php?entity=hallazgo&action=index';
    }
}

// Inicializar cuando se carga la página de hallazgos
document.addEventListener('DOMContentLoaded', function() {
    const hallazgosTable = document.getElementById('hallazgos-table');
    if (hallazgosTable && window.location.href.includes('entity=hallazgo')) {
        console.log('🏢 Inicializando SedeManager...');
        new SedeManager();
    }
});