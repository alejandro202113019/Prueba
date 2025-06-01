<?php
// models/EventManager.php
class EventManager {
    private static $listeners = [];
    private static $eventLog = [];
    
    /**
     * Registra un listener para un evento
     * @param string $eventName Nombre del evento
     * @param callable $callback Función a ejecutar
     */
    public static function listen($eventName, $callback) {
        if (!isset(self::$listeners[$eventName])) {
            self::$listeners[$eventName] = [];
        }
        self::$listeners[$eventName][] = $callback;
    }
    
    /**
     * Notifica un evento a todos sus listeners
     * @param string $eventName Nombre del evento
     * @param array $data Datos del evento
     */
    public static function notify($eventName, $data = []) {
        // Registrar el evento en el log
        self::$eventLog[] = [
            'event' => $eventName,
            'data' => $data,
            'timestamp' => microtime(true),
            'date' => date('Y-m-d H:i:s')
        ];
        
        // Ejecutar listeners registrados
        if (isset(self::$listeners[$eventName])) {
            foreach (self::$listeners[$eventName] as $callback) {
                try {
                    call_user_func($callback, $data);
                } catch (Exception $e) {
                    error_log("Error en listener de evento '{$eventName}': " . $e->getMessage());
                }
            }
        }
        
        // Ejecutar listeners por defecto del sistema
        self::executeDefaultListeners($eventName, $data);
    }
    
    /**
     * Obtiene el log de eventos
     * @param string $eventName Filtrar por evento específico (opcional)
     * @return array Log de eventos
     */
    public static function getEventLog($eventName = null) {
        if ($eventName) {
            return array_filter(self::$eventLog, function($event) use ($eventName) {
                return $event['event'] === $eventName;
            });
        }
        return self::$eventLog;
    }
    
    /**
     * Limpia el log de eventos
     */
    public static function clearEventLog() {
        self::$eventLog = [];
    }
    
    /**
     * Ejecuta listeners por defecto del sistema
     * @param string $eventName Nombre del evento
     * @param array $data Datos del evento
     */
    private static function executeDefaultListeners($eventName, $data) {
        switch ($eventName) {
            case 'estado_cambiado':
                self::onEstadoCambiado($data);
                break;
            case 'estado_revertido':
                self::onEstadoRevertido($data);
                break;
            case 'usuario_notificado':
                self::onUsuarioNotificado($data);
                break;
        }
    }
    
    /**
     * Listener por defecto para cambio de estado
     * @param array $data Datos del evento
     */
    private static function onEstadoCambiado($data) {
        // Log personalizado para cambios de estado
        error_log(sprintf(
            "Estado cambiado: %s #%d de '%s' a '%s' por usuario #%d",
            $data['tipo'],
            $data['registro_id'],
            $data['estado_anterior'],
            $data['estado_nuevo'],
            $data['usuario_id']
        ));
        
        // Disparar notificaciones si el estado es crítico
        $estadoInfo = \EstadoFactory::obtenerInfoEstado($data['estado_nuevo']);
        if ($estadoInfo && $estadoInfo['es_critico']) {
            self::notify('usuario_notificado', [
                'tipo' => 'estado_critico',
                'registro_id' => $data['registro_id'],
                'tipo_registro' => $data['tipo'],
                'estado' => $data['estado_nuevo'],
                'usuario_id' => $data['usuario_id']
            ]);
        }
    }
    
    /**
     * Listener por defecto para reversión de estado
     * @param array $data Datos del evento
     */
    private static function onEstadoRevertido($data) {
        error_log(sprintf(
            "Estado revertido: %s #%d de '%s' a '%s' por usuario #%d",
            $data['tipo'],
            $data['registro_id'],
            $data['estado_revertido_de'],
            $data['estado_revertido_a'],
            $data['usuario_id']
        ));
    }
    
    /**
     * Listener por defecto para notificaciones de usuario
     * @param array $data Datos del evento
     */
    private static function onUsuarioNotificado($data) {
        // Aquí se podría enviar emails, push notifications, etc.
        error_log(sprintf(
            "Notificación enviada: %s para usuario #%d",
            $data['tipo'],
            $data['usuario_id']
        ));
    }
    
    /**
     * Registra listeners por defecto del sistema
     */
    public static function registerDefaultListeners() {
        // Listener para logging avanzado
        self::listen('estado_cambiado', function($data) {
            // Aquí se podrían agregar integraciones externas
            // como Slack, Teams, email, etc.
        });
        
        // Listener para métricas
        self::listen('estado_cambiado', function($data) {
            // Aquí se podrían enviar métricas a sistemas como Google Analytics
            // o sistemas de monitoreo internos
        });
    }
}

// Registrar listeners por defecto cuando se carga la clase
EventManager::registerDefaultListeners();
?>