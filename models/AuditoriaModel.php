<?php
// models/AuditoriaModel.php
require_once 'config.php';

class AuditoriaModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->crearTablaAuditoria();
    }
    
    /**
     * Crea la tabla de auditoría si no existe
     */
    private function crearTablaAuditoria() {
        $sql = "
            CREATE TABLE IF NOT EXISTS auditoria (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tabla VARCHAR(50) NOT NULL,
                registro_id INT NOT NULL,
                accion VARCHAR(50) NOT NULL,
                valor_anterior TEXT,
                valor_nuevo TEXT,
                usuario_id INT,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45),
                user_agent TEXT,
                comentario TEXT,
                metadata JSON,
                INDEX idx_tabla_registro (tabla, registro_id),
                INDEX idx_usuario_fecha (usuario_id, timestamp),
                INDEX idx_accion (accion)
            ) ENGINE=InnoDB;
        ";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creando tabla auditoría: " . $e->getMessage());
        }
    }
    
    /**
     * Registra un evento de auditoría
     * @param array $datos Datos del evento
     * @return bool Éxito del registro
     */
    public function registrar($datos) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO auditoria (
                    tabla, registro_id, accion, valor_anterior, valor_nuevo,
                    usuario_id, ip_address, user_agent, comentario, metadata
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $datos['tabla'] ?? '',
                $datos['registro_id'] ?? 0,
                $datos['accion'] ?? '',
                $datos['valor_anterior'] ?? null,
                $datos['valor_nuevo'] ?? null,
                $datos['usuario_id'] ?? null,
                $this->getClientIP(),
                $this->getUserAgent(),
                $datos['comentario'] ?? null,
                isset($datos['metadata']) ? json_encode($datos['metadata']) : null
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando auditoría: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el historial de auditoría para un registro específico
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro
     * @return array Historial de auditoría
     */
    public function obtenerHistorial($tabla, $registroId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, u.nombre as usuario_nombre
                FROM auditoria a
                LEFT JOIN Usuario u ON a.usuario_id = u.id
                WHERE a.tabla = ? AND a.registro_id = ?
                ORDER BY a.timestamp DESC
            ");
            $stmt->execute([$tabla, $registroId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo historial: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene estadísticas de auditoría
     * @param array $filtros Filtros opcionales
     * @return array Estadísticas
     */
    public function obtenerEstadisticas($filtros = []) {
        try {
            $sql = "
                SELECT 
                    DATE(timestamp) as fecha,
                    accion,
                    COUNT(*) as total,
                    COUNT(DISTINCT usuario_id) as usuarios_unicos
                FROM auditoria 
                WHERE 1=1
            ";
            $params = [];
            
            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND DATE(timestamp) >= ?";
                $params[] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND DATE(timestamp) <= ?";
                $params[] = $filtros['fecha_hasta'];
            }
            
            if (!empty($filtros['accion'])) {
                $sql .= " AND accion = ?";
                $params[] = $filtros['accion'];
            }
            
            $sql .= " GROUP BY DATE(timestamp), accion ORDER BY fecha DESC, total DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene la IP del cliente
     * @return string IP del cliente
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Obtiene el User Agent del cliente
     * @return string User Agent
     */
    private function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
    
    /**
     * Limpia registros de auditoría antiguos
     * @param int $dias Días de antigüedad para limpiar
     * @return int Número de registros eliminados
     */
    public function limpiarAntiguos($dias = 90) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM auditoria WHERE timestamp < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$dias]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error limpiando auditoría: " . $e->getMessage());
            return 0;
        }
    }
}
?>