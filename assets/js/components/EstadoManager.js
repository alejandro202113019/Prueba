// assets/js/components/EstadoManager.js
class EstadoManager {
    constructor() {
        this.entity = this.detectEntity();
        this.init();
    }
    
    detectEntity() {
        const url = window.location.href;
        if (url.includes('entity=incidente')) return 'incidente';
        if (url.includes('entity=hallazgo')) return 'hallazgo';
        return 'hallazgo'; // default
    }
    
    init() {
        this.createEstadoButtons();
        this.bindEvents();
        console.log(`🔄 EstadoManager inicializado para: ${this.entity}`);
    }
    
    createEstadoButtons() {
        // Buscar todas las celdas de estado en la tabla
        const estadoCells = document.querySelectorAll('td:nth-child(4)'); // Columna Estado
        
        estadoCells.forEach((cell, index) => {
            // Saltar el header
            if (index === 0 || cell.closest('thead')) return;
            
            const row = cell.closest('tr');
            const recordId = this.extractRecordId(row);
            const currentEstado = cell.textContent.trim();
            
            if (recordId && currentEstado) {
                this.enhanceEstadoCell(cell, recordId, currentEstado);
            }
        });
    }
    
    extractRecordId(row) {
        // El ID está en la primera celda
        const firstCell = row.querySelector('td:first-child');
        return firstCell ? firstCell.textContent.trim() : null;
    }
    
    enhanceEstadoCell(cell, recordId, currentEstado) {
        const originalContent = cell.innerHTML;
        
        // Crear badge con dropdown
        const enhancedHtml = `
            <div class="estado-container" data-record-id="${recordId}" data-current-estado="${currentEstado}">
                <div class="dropdown">
                    <button class="btn btn-sm btn-${this.getEstadoColor(currentEstado)} dropdown-toggle estado-btn" 
                            type="button" 
                            data-toggle="dropdown" 
                            aria-haspopup="true" 
                            aria-expanded="false"
                            title="Cambiar estado">
                        ${this.getEstadoIcon(currentEstado)} ${currentEstado}
                    </button>
                    <div class="dropdown-menu estado-dropdown">
                        <h6 class="dropdown-header">Cambiar estado a:</h6>
                        <div class="estado-options" data-loading="false">
                            <!-- Se cargarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        cell.innerHTML = enhancedHtml;
    }
    
    bindEvents() {
        // Event delegation para botones de estado
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('estado-btn')) {
                this.handleEstadoClick(e.target);
            }
            
            if (e.target.classList.contains('estado-option')) {
                this.handleEstadoChange(e.target);
            }
        });
        
        // Cargar opciones cuando se abre el dropdown
        document.addEventListener('show.bs.dropdown', (e) => {
            if (e.target.querySelector('.estado-dropdown')) {
                this.loadEstadoOptions(e.target);
            }
        });
    }
    
    async handleEstadoClick(button) {
        const container = button.closest('.estado-container');
        const recordId = container.dataset.recordId;
        const currentEstado = container.dataset.currentEstado;
        
        console.log(`🔄 Cargando opciones para ${this.entity} #${recordId} (estado actual: ${currentEstado})`);
    }
    
    async loadEstadoOptions(dropdown) {
        const container = dropdown.closest('.estado-container');
        const recordId = container.dataset.recordId;
        const currentEstado = container.dataset.currentEstado;
        const optionsContainer = dropdown.querySelector('.estado-options');
        
        if (optionsContainer.dataset.loading === 'true') return;
        
        optionsContainer.dataset.loading = 'true';
        optionsContainer.innerHTML = '<div class="text-center"><small>Cargando...</small></div>';
        
        try {
            const response = await fetch(`index.php?entity=${this.entity}&action=obtener_estados_permitidos&record_id=${recordId}&estado_actual=${encodeURIComponent(currentEstado)}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                this.renderEstadoOptions(optionsContainer, result.data, recordId, currentEstado);
            } else {
                optionsContainer.innerHTML = '<small class="text-muted px-3">No hay cambios de estado disponibles</small>';
            }
        } catch (error) {
            console.error('❌ Error cargando opciones de estado:', error);
            optionsContainer.innerHTML = '<small class="text-danger px-3">Error cargando opciones</small>';
        } finally {
            optionsContainer.dataset.loading = 'false';
        }
    }
    
    renderEstadoOptions(container, options, recordId, currentEstado) {
        let html = '';
        
        options.forEach(option => {
            html += `
                <button class="dropdown-item estado-option" 
                        data-record-id="${recordId}"
                        data-estado-actual="${currentEstado}"
                        data-estado-nuevo="${option.estado}"
                        title="${option.descripcion}">
                    ${this.getEstadoIcon(option.estado)} ${option.estado}
                    <small class="text-muted d-block">${option.descripcion}</small>
                </button>
            `;
        });
        
        container.innerHTML = html;
    }
    
    async handleEstadoChange(button) {
        const recordId = button.dataset.recordId;
        const estadoActual = button.dataset.estadoActual;
        const estadoNuevo = button.dataset.estadoNuevo;
        
        // Confirmar el cambio
        const confirmMessage = `¿Confirma cambiar el estado de "${estadoActual}" a "${estadoNuevo}"?`;
        if (!confirm(confirmMessage)) return;
        
        // Mostrar loader
        this.showEstadoLoader(recordId);
        
        try {
            const response = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    entity: this.entity,
                    action: 'cambiar_estado',
                    record_id: recordId,
                    estado_actual: estadoActual,
                    estado_nuevo: estadoNuevo,
                    usuario_id: 1 // TODO: Obtener usuario actual
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.handleEstadoChangeSuccess(recordId, estadoNuevo, result.message);
            } else {
                this.handleEstadoChangeError(recordId, estadoActual, result.message);
            }
        } catch (error) {
            console.error('❌ Error cambiando estado:', error);
            this.handleEstadoChangeError(recordId, estadoActual, 'Error de conexión');
        }
    }
    
    showEstadoLoader(recordId) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector('.estado-btn');
        
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Cambiando...';
        button.disabled = true;
    }
    
    handleEstadoChangeSuccess(recordId, nuevoEstado, message) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector('.estado-btn');
        
        // Actualizar el botón
        button.className = `btn btn-sm btn-${this.getEstadoColor(nuevoEstado)} dropdown-toggle estado-btn`;
        button.innerHTML = `${this.getEstadoIcon(nuevoEstado)} ${nuevoEstado}`;
        button.disabled = false;
        
        // Actualizar dataset
        container.dataset.currentEstado = nuevoEstado;
        
        // Mostrar notificación de éxito
        this.showToast('success', '✅ Estado actualizado', message);
        
        // Cerrar dropdown
        const dropdown = container.querySelector('.dropdown');
        if (dropdown.classList.contains('show')) {
            dropdown.querySelector('.dropdown-toggle').click();
        }
    }
    
    handleEstadoChangeError(recordId, estadoOriginal, errorMessage) {
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector('.estado-btn');
        
        // Restaurar el botón original
        button.className = `btn btn-sm btn-${this.getEstadoColor(estadoOriginal)} dropdown-toggle estado-btn`;
        button.innerHTML = `${this.getEstadoIcon(estadoOriginal)} ${estadoOriginal}`;
        button.disabled = false;
        
        // Mostrar error
        this.showToast('error', '❌ Error', errorMessage);
    }
    
    getEstadoColor(estado) {
        const colors = {
            'Abierto': 'danger',
            'En Proceso': 'warning',
            'Resuelto': 'success',
            'Cerrado': 'secondary'
        };
        return colors[estado] || 'primary';
    }
    
    getEstadoIcon(estado) {
        const icons = {
            'Abierto': '🚨',
            'En Proceso': '⚠️',
            'Resuelto': '✅',
            'Cerrado': '🔒'
        };
        return icons[estado] || '📋';
    }
    
    showToast(type, title, message) {
        // Crear toast notification
        const toastHtml = `
            <div class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true" data-delay="4000">
                <div class="toast-header">
                    <strong class="mr-auto">${title}</strong>
                    <small>ahora</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        // Crear contenedor de toasts si no existe
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1055;
                max-width: 350px;
            `;
            document.body.appendChild(toastContainer);
        }
        
        // Agregar toast
        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml;
        toastContainer.appendChild(toastElement.firstElementChild);
        
        // Activar toast
        const toast = toastContainer.lastElementChild;
        $(toast).toast('show');
        
        // Remover después de que se oculte
        $(toast).on('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    // Método para obtener usuario actual (placeholder)
    getCurrentUserId() {
        // TODO: Implementar obtención de usuario actual
        // Por ahora retornamos 1 como default
        return 1;
    }
}

// CSS adicional para toasts
const toastStyles = `
<style>
.toast-success .toast-header {
    background-color: #d4edda;
    color: #155724;
}

.toast-error .toast-header {
    background-color: #f8d7da;
    color: #721c24;
}

.estado-container .dropdown-menu {
    min-width: 250px;
}

.estado-container .dropdown-item {
    padding: 8px 15px;
    border-bottom: 1px solid #f8f9fa;
}

.estado-container .dropdown-item:hover {
    background-color: #f8f9fa;
}

.estado-container .dropdown-item:last-child {
    border-bottom: none;
}

.estado-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.estado-options[data-loading="true"] {
    pointer-events: none;
}
</style>
`;

// Insertar estilos
document.head.insertAdjacentHTML('beforeend', toastStyles);

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en una página de listado
    const isListPage = window.location.href.includes('action=index') || 
                      (!window.location.href.includes('action=') && 
                       (window.location.href.includes('entity=hallazgo') || 
                        window.location.href.includes('entity=incidente')));
    
    if (isListPage) {
        console.log('🔄 Inicializando EstadoManager...');
        new EstadoManager();
    }
});