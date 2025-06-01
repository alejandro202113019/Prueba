<?php
// models/IncidenteModel.php - Versión con cambio de estado
require_once 'config.php';

class IncidenteModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT i.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM Incidente i
            LEFT JOIN Estado e ON i.id_estado = e.id
            LEFT JOIN Usuario u ON i.id_usuario = u.id
            ORDER BY i.fecha_ocurrencia DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM Incidente i
            LEFT JOIN Estado e ON i.id_estado = e.id
            LEFT JOIN Usuario u ON i.id_usuario = u.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($descripcion, $fecha_ocurrencia, $id_estado, $id_usuario) {
        $stmt = $this->pdo->prepare("INSERT INTO Incidente (descripcion, fecha_ocurrencia, id_estado, id_usuario) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$descripcion, $fecha_ocurrencia, $id_estado, $id_usuario]);
    }

    public function update($id, $descripcion, $fecha_ocurrencia, $id_estado, $id_usuario) {
        $stmt = $this->pdo->prepare("UPDATE Incidente SET descripcion = ?, fecha_ocurrencia = ?, id_estado = ?, id_usuario = ? WHERE id = ?");
        return $stmt->execute([$descripcion, $fecha_ocurrencia, $id_estado, $id_usuario, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Incidente WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Busca un incidente específico por ID con validación de permisos
     * @param int $id ID del incidente
     * @return array|null Incidente encontrado o null
     */
    public function searchById($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM Incidente i
            LEFT JOIN Estado e ON i.id_estado = e.id
            LEFT JOIN Usuario u ON i.id_usuario = u.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cambia el estado de un incidente
     * @param int $id ID del incidente
     * @param int $nuevoEstadoId ID del nuevo estado
     * @return bool Éxito de la operación
     */
    public function cambiarEstado($id, $nuevoEstadoId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE Incidente SET id_estado = ? WHERE id = ?");
            return $stmt->execute([$nuevoEstadoId, $id]);
        } catch (PDOException $e) {
            error_log("Error cambiando estado en IncidenteModel: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el estado actual de un incidente
     * @param int $id ID del incidente
     * @return string|null Nombre del estado actual
     */
    public function getEstadoActual($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.nombre 
                FROM Incidente i 
                JOIN Estado e ON i.id_estado = e.id 
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['nombre'] : null;
        } catch (PDOException $e) {
            error_log("Error obteniendo estado actual: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene estadísticas de estados para dashboard
     * @return array Estadísticas de estados
     */
    public function getEstadisticasEstados() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    e.nombre as estado,
                    e.id as estado_id,
                    COUNT(i.id) as total,
                    ROUND(COUNT(i.id) * 100.0 / (SELECT COUNT(*) FROM Incidente), 2) as porcentaje
                FROM Estado e
                LEFT JOIN Incidente i ON e.id = i.id_estado
                GROUP BY e.id, e.nombre
                ORDER BY total DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas de estados: " . $e->getMessage());
            return [];
        }
    }
}
?>