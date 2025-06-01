// assets/js/components/EstadoManager.js - Usando API dedicada
class EstadoManager {
    constructor() {
        this.entity = this.detectEntity();
        this.init();
        console.log(`🔄 EstadoManager inicializado para: ${this.entity}`);
    }
    
    detectEntity() {
        const url = window.location.href;
        if (url.includes('entity=incidente')) return 'incidente';
        if (url.includes('entity=hallazgo')) return 'hallazgo';
        
        // Detectar por tabla presente
        if (document.getElementById('incidentes-table')) return 'incidente';
        if (document.getElementById('hallazgos-table')) return 'hallazgo';
        
        return 'hallazgo'; // default
    }
    
    init() {
        // Esperar a que DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEstadoButtons());
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
        // Buscar badges con data-record-id (más específico)
        const estadoBadges = document.querySelectorAll('.badge[data-record-id]');
        
        console.log(`🔍 Encontrados ${estadoBadges.length} badges de estado`);
        
        estadoBadges.forEach((badge, index) => {
            const recordId = badge.dataset.recordId;
            const currentEstado = badge.dataset.currentEstado || badge.textContent.trim();
            
            if (recordId && currentEstado && this.isValidEstado(currentEstado)) {
                // Verificar que no esté ya procesado
                if (!badge.closest('.estado-container')) {
                    this.enhanceEstadoBadge(badge, recordId, currentEstado);
                    console.log(`✅ Badge ${recordId} procesado`);
                } else {
                    console.log(`⚠️ Badge ${recordId} ya estaba procesado`);
                }
            }
        });
    }
    
    isValidEstado(estado) {
        const validEstados = ['Abierto', 'En Proceso', 'Resuelto', 'Cerrado'];
        return validEstados.includes(estado);
    }
    
    enhanceEstadoBadge(badge, recordId, currentEstado) {
        // Crear contenedor único
        const container = document.createElement('div');
        container.className = 'estado-container';
        container.dataset.recordId = recordId;
        container.dataset.currentEstado = currentEstado;
        
        // ID único para el dropdown
        const dropdownId = `dropdown-${this.entity}-${recordId}`;
        
        container.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-sm btn-${this.getEstadoColor(currentEstado)} dropdown-toggle estado-btn" 
                        type="button" 
                        id="${dropdownId}"
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        title="Haz clic para cambiar estado">
                    ${this.getEstadoIcon(currentEstado)} ${currentEstado}
                </button>
                <div class="dropdown-menu estado-dropdown" aria-labelledby="${dropdownId}">
                    <h6 class="dropdown-header">Cambiar estado a:</h6>
                    <div class="estado-options" data-loading="false">
                        <div class="text-center p-2">
                            <small class="text-muted">Haz clic para cargar opciones...</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Reemplazar el badge original
        badge.parentNode.replaceChild(container, badge);
    }
    
    bindEvents() {
        console.log('🔗 Binding eventos...');
        
        // Event delegation para manejar clicks en opciones
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('estado-option')) {
                console.log('✅ Click en estado-option detectado');
                e.preventDefault();
                e.stopPropagation();
                this.handleEstadoChange(e.target);
            } else if (e.target.closest('.estado-option')) {
                console.log('✅ Click en elemento dentro de estado-option detectado');
                e.preventDefault();
                e.stopPropagation();
                this.handleEstadoChange(e.target.closest('.estado-option'));
            }
        });
        
        // Cargar opciones cuando se abre el dropdown
        $(document).on('show.bs.dropdown', '.estado-container .dropdown', (e) => {
            console.log('📂 Dropdown abierto');
            this.loadEstadoOptions(e.target);
        });
        
        console.log('✅ Eventos vinculados correctamente');
    }
    
    async loadEstadoOptions(dropdownElement) {
        const container = dropdownElement.closest('.estado-container');
        const recordId = container.dataset.recordId;
        const currentEstado = container.dataset.currentEstado;
        const optionsContainer = container.querySelector('.estado-options');
        
        if (optionsContainer.dataset.loading === 'true') {
            console.log(`⚠️ Ya cargando opciones para ${recordId}`);
            return;
        }
        
        optionsContainer.dataset.loading = 'true';
        optionsContainer.innerHTML = '<div class="text-center p-2"><small>⏳ Cargando opciones...</small></div>';
        
        try {
            const url = `index.php?entity=${this.entity}&action=obtener_estados_permitidos&record_id=${recordId}&estado_actual=${encodeURIComponent(currentEstado)}`;
            console.log(`📡 Cargando estados desde: ${url}`);
            
            const response = await fetch(url);
            console.log(`📡 Respuesta HTTP: ${response.status} ${response.statusText}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('📋 Estados recibidos:', result);
            
            if (result.success && result.data && result.data.length > 0) {
                this.renderEstadoOptions(optionsContainer, result.data, recordId, currentEstado);
            } else {
                optionsContainer.innerHTML = '<small class="text-muted px-3 py-2 d-block">No hay transiciones disponibles</small>';
            }
        } catch (error) {
            console.error('❌ Error cargando opciones:', error);
            optionsContainer.innerHTML = `<small class="text-danger px-3 py-2 d-block">Error: ${error.message}</small>`;
        } finally {
            optionsContainer.dataset.loading = 'false';
        }
    }
    
    renderEstadoOptions(container, options, recordId, currentEstado) {
        console.log(`🎨 Renderizando ${options.length} opciones para record ${recordId}`);
        
        let html = '';
        
        options.forEach((option, index) => {
            const optionId = `option-${recordId}-${index}`;
            html += `
                <button class="dropdown-item estado-option" 
                        id="${optionId}"
                        data-record-id="${recordId}"
                        data-estado-actual="${currentEstado}"
                        data-estado-nuevo="${option.estado}"
                        title="${option.descripcion}"
                        type="button">
                    <div class="d-flex align-items-center">
                        <span class="mr-2">${this.getEstadoIcon(option.estado)}</span>
                        <div>
                            <div class="font-weight-bold">${option.estado}</div>
                            <small class="text-muted">${option.descripcion}</small>
                        </div>
                    </div>
                </button>
            `;
        });
        
        if (html) {
            container.innerHTML = html;
            console.log(`✅ ${options.length} opciones renderizadas`);
        } else {
            container.innerHTML = '<small class="text-muted px-3 py-2 d-block">Sin opciones disponibles</small>';
        }
    }
    
    async handleEstadoChange(button) {
        console.log('🔄 handleEstadoChange llamado con:', button);
        
        const recordId = button.dataset.recordId;
        const estadoActual = button.dataset.estadoActual;
        const estadoNuevo = button.dataset.estadoNuevo;
        
        console.log(`📋 Datos extraídos:`, {
            recordId,
            estadoActual,
            estadoNuevo
        });
        
        if (!recordId || !estadoActual || !estadoNuevo) {
            console.error('❌ Faltan datos para el cambio de estado:', {
                recordId,
                estadoActual,
                estadoNuevo
            });
            return;
        }
        
        const confirmMessage = `¿Confirma cambiar el estado de "${estadoActual}" a "${estadoNuevo}"?`;
        console.log(`❓ Confirmación: ${confirmMessage}`);
        
        if (!confirm(confirmMessage)) {
            console.log('❌ Usuario canceló el cambio');
            return;
        }
        
        console.log('✅ Usuario confirmó el cambio, ejecutando...');
        this.showEstadoLoader(recordId);
        
        try {
            console.log(`🔄 Cambiando estado: ${estadoActual} → ${estadoNuevo} (ID: ${recordId})`);
            
            const requestData = {
                entity: this.entity,
                record_id: recordId,
                estado_actual: estadoActual,
                estado_nuevo: estadoNuevo,
                usuario_id: 1
            };
            
            console.log('📤 Enviando datos a API dedicada:', requestData);
            
            // USAR API DEDICADA EN LUGAR DE index.php
            const response = await fetch('api_cambiar_estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(requestData)
            });
            
            console.log(`📡 Respuesta API: ${response.status} ${response.statusText}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('❌ Respuesta no es JSON:', text.substring(0, 500));
                throw new Error('El servidor no devolvió JSON válido');
            }
            
            const result = await response.json();
            console.log('📋 Resultado API:', result);
            
            if (result.success) {
                console.log('✅ Cambio exitoso');
                this.handleEstadoChangeSuccess(recordId, estadoNuevo, result.message);
            } else {
                console.log('❌ Cambio falló:', result.message);
                this.handleEstadoChangeError(recordId, estadoActual, result.message || 'Error desconocido');
            }
        } catch (error) {
            console.error('❌ Error cambiando estado:', error);
            this.handleEstadoChangeError(recordId, estadoActual, `Error: ${error.message}`);
        }
    }
    
    showEstadoLoader(recordId) {
        console.log(`⏳ Mostrando loader para ${recordId}`);
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector('.estado-btn');
        
        button.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Cambiando...';
        button.disabled = true;
    }
    
    handleEstadoChangeSuccess(recordId, nuevoEstado, message) {
        console.log(`✅ Éxito para ${recordId}: ${nuevoEstado}`);
        const container = document.querySelector(`[data-record-id="${recordId}"]`);
        const button = container.querySelector('.estado-btn');
        
        // Actualizar el botón
        button.className = `btn btn-sm btn-${this.getEstadoColor(nuevoEstado)} dropdown-toggle estado-btn`;
        button.innerHTML = `${this.getEstadoIcon(nuevoEstado)} ${nuevoEstado}`;
        button.disabled = false;
        
        // Actualizar dataset
        container.dataset.currentEstado = nuevoEstado;
        
        // Mostrar notificación de éxito
        this.showToast('success', '✅ Estado actualizado', message || `Estado cambiado a ${nuevoEstado}`);
        
        // Cerrar dropdown
        const dropdownButton = container.querySelector('.dropdown-toggle');
        if (dropdownButton) {
            $(dropdownButton).dropdown('hide');
        }
        
        console.log(`🎉 Estado actualizado exitosamente a: ${nuevoEstado}`);
    }
    
    handleEstadoChangeError(recordId, estadoOriginal, errorMessage) {
        console.log(`❌ Error para ${recordId}:`, errorMessage);
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
        // Remover toasts anteriores
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        // Crear toast
        const toastHtml = `
            <div class="toast toast-${type}" role="alert" data-delay="4000" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
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
        
        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml;
        document.body.appendChild(toastElement.firstElementChild);
        
        // Activar toast
        const toast = document.body.lastElementChild;
        $(toast).toast('show');
        
        // Remover después de que se oculte
        $(toast).on('hidden.bs.toast', function() {
            this.remove();
        });
    }
}

// Inicializar automáticamente
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en una página de listado relevante
    const isListPage = document.getElementById('hallazgos-table') || 
                      document.getElementById('incidentes-table') ||
                      window.location.href.includes('action=index');
    
    if (isListPage) {
        console.log('🔄 Inicializando EstadoManager...');
        // Pequeño delay para asegurar que otros scripts se carguen
        setTimeout(() => {
            new EstadoManager();
        }, 500);
    }
});