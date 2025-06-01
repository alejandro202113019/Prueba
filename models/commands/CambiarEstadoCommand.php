<?php
// models/commands/CambiarEstadoCommand.php
require_once 'models/interfaces/CommandInterface.php';
require_once 'models/factories/EstadoFactory.php';
require_once 'models/EventManager.php';
require_once 'models/AuditoriaModel.php';

class CambiarEstadoCommand implements CommandInterface {
    private $registroId;
    private $tipoRegistro; // 'hallazgo' o 'incidente'
    private $estadoAnterior;
    private $estadoNuevo;
    private $usuarioId;
    private $model;
    private $comentario;
    private $timestamp;
    private $ejecutado;
    
    public function __construct($registroId, $tipoRegistro, $estadoAnterior, $estadoNuevo, $usuarioId, $model, $comentario = null) {
        $this->registroId = $registroId;
        $this->tipoRegistro = $tipoRegistro;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
        $this->usuarioId = $usuarioId;
        $this->model = $model;
        $this->comentario = $comentario;
        $this->timestamp = date('Y-m-d H:i:s');
        $this->ejecutado = false;
    }
    
    public function execute() {
        try {
            // 1. Validar transición usando State Pattern
            if (!EstadoFactory::validarTransicion($this->estadoAnterior, $this->estadoNuevo)) {
                throw new Exception("Transición no válida de '{$this->estadoAnterior}' a '{$this->estadoNuevo}'");
            }
            
            // 2. Obtener ID del nuevo estado
            $nuevoEstadoId = $this->obtenerEstadoId($this->estadoNuevo);
            if (!$nuevoEstadoId) {
                throw new Exception("Estado '{$this->estadoNuevo}' no encontrado en la base de datos");
            }
            
            // 3. Ejecutar cambio en la base de datos
            $resultado = $this->model->cambiarEstado($this->registroId, $nuevoEstadoId);
            if (!$resultado) {
                throw new Exception("Error al actualizar el estado en la base de datos");
            }
            
            // 4. Registrar en auditoría
            $this->registrarAuditoria();
            
            // 5. Notificar evento usando Event Manager
            EventManager::notify('estado_cambiado', [
                'registro_id' => $this->registroId,
                'tipo' => $this->tipoRegistro,
                'estado_anterior' => $this->estadoAnterior,
                'estado_nuevo' => $this->estadoNuevo,
                'usuario_id' => $this->usuarioId,
                'timestamp' => $this->timestamp,
                'comentario' => $this->comentario
            ]);
            
            $this->ejecutado = true;
            return true;
            
        } catch (Exception $e) {
            error_log("Error en CambiarEstadoCommand: " . $e->getMessage());
            return false;
        }
    }
    
    public function undo() {
        if (!$this->ejecutado) {
            return false;
        }
        
        try {
            // Revertir al estado anterior
            $estadoAnteriorId = $this->obtenerEstadoId($this->estadoAnterior);
            $resultado = $this->model->cambiarEstado($this->registroId, $estadoAnteriorId);
            
            if ($resultado) {
                // Registrar la reversión en auditoría
                $this->registrarAuditoria(true);
                
                // Notificar evento de reversión
                EventManager::notify('estado_revertido', [
                    'registro_id' => $this->registroId,
                    'tipo' => $this->tipoRegistro,
                    'estado_revertido_de' => $this->estadoNuevo,
                    'estado_revertido_a' => $this->estadoAnterior,
                    'usuario_id' => $this->usuarioId,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                
                $this->ejecutado = false;
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error en undo CambiarEstadoCommand: " . $e->getMessage());
            return false;
        }
    }
    
    public function getLogInfo() {
        return [
            'comando' => 'CambiarEstado',
            'registro_id' => $this->registroId,
            'tipo_registro' => $this->tipoRegistro,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->estadoNuevo,
            'usuario_id' => $this->usuarioId,
            'timestamp' => $this->timestamp,
            'comentario' => $this->comentario,
            'ejecutado' => $this->ejecutado
        ];
    }
    
    private function obtenerEstadoId($nombreEstado) {
        try {
            $pdo = $this->model->getPdo(); // Asumiendo que el model tiene método getPdo()
            $stmt = $pdo->prepare("SELECT id FROM Estado WHERE nombre = ?");
            $stmt->execute([$nombreEstado]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id'] : null;
        } catch (Exception $e) {
            error_log("Error obteniendo ID de estado: " . $e->getMessage());
            return null;
        }
    }
    
    private function registrarAuditoria($esReversion = false) {
        try {
            $auditoriaModel = new AuditoriaModel($this->model->getPdo());
            $auditoriaModel->registrar([
                'tabla' => ucfirst($this->tipoRegistro),
                'registro_id' => $this->registroId,
                'accion' => $esReversion ? 'revertir_estado' : 'cambiar_estado',
                'valor_anterior' => $this->estadoAnterior,
                'valor_nuevo' => $this->estadoNuevo,
                'usuario_id' => $this->usuarioId,
                'comentario' => $this->comentario,
                'metadata' => json_encode($this->getLogInfo())
            ]);
        } catch (Exception $e) {
            error_log("Error registrando auditoría: " . $e->getMessage());
        }
    }
}
?>