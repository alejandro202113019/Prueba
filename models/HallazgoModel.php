<?php
// models/HallazgoModel.php - Versión con cambio de estado
require_once 'config.php';

class HallazgoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre, s.nombre as sede_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            LEFT JOIN Sedes s ON h.sede_id = s.id
            ORDER BY h.fecha_creacion DESC
        ");
        $hallazgos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hallazgos as &$hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgos;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre, s.nombre as sede_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            LEFT JOIN Sedes s ON h.sede_id = s.id
            WHERE h.id = ?
        ");
        $stmt->execute([$id]);
        $hallazgo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgo;
    }

    public function insert($titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario, $sede_id = null) {
        $stmt = $this->pdo->prepare("INSERT INTO Hallazgo (titulo, descripcion, id_estado, id_usuario, sede_id) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$titulo, $descripcion, $id_estado, $id_usuario, $sede_id]);

        if ($result) {
            $hallazgo_id = $this->pdo->lastInsertId();
            $this->updateProcesos($hallazgo_id, $proceso_ids);
            return true;
        }
        return false;
    }

    public function update($id, $titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario, $sede_id = null) {
        $stmt = $this->pdo->prepare("UPDATE Hallazgo SET titulo = ?, descripcion = ?, id_estado = ?, id_usuario = ?, sede_id = ? WHERE id = ?");
        $result = $stmt->execute([$titulo, $descripcion, $id_estado, $id_usuario, $sede_id, $id]);

        if ($result) {
            $this->updateProcesos($id, $proceso_ids);
            return true;
        }
        return false;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Hallazgo WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cambia el estado de un hallazgo
     * @param int $id ID del hallazgo
     * @param int $nuevoEstadoId ID del nuevo estado
     * @return bool Éxito de la operación
     */
    public function cambiarEstado($id, $nuevoEstadoId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE Hallazgo SET id_estado = ?, fecha_actualizacion = NOW() WHERE id = ?");
            return $stmt->execute([$nuevoEstadoId, $id]);
        } catch (PDOException $e) {
            error_log("Error cambiando estado en HallazgoModel: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el estado actual de un hallazgo
     * @param int $id ID del hallazgo
     * @return string|null Nombre del estado actual
     */
    public function getEstadoActual($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.nombre 
                FROM Hallazgo h 
                JOIN Estado e ON h.id_estado = e.id 
                WHERE h.id = ?
            ");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['nombre'] : null;
        } catch (PDOException $e) {
            error_log("Error obteniendo estado actual: " . $e->getMessage());
            return null;
        }
    }
	
	private function updateProcesos($hallazgo_id, $proceso_ids) {
        // Eliminar procesos existentes
        $stmt = $this->pdo->prepare("DELETE FROM Hallazgo_Proceso WHERE id_hallazgo = ?");
        $stmt->execute([$hallazgo_id]);

        // Insertar procesos seleccionados
        foreach ($proceso_ids as $proceso_id) {
            $stmt = $this->pdo->prepare("INSERT INTO Hallazgo_Proceso (id_hallazgo, id_proceso) VALUES (?, ?)");
            $stmt->execute([$hallazgo_id, $proceso_id]);
        }
    }

    public function getProcesos($hallazgo_id) {
        $stmt = $this->pdo->prepare("SELECT p.* FROM Proceso p INNER JOIN Hallazgo_Proceso hp ON p.id = hp.id_proceso WHERE hp.id_hallazgo = ?");
        $stmt->execute([$hallazgo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene hallazgos filtrados por sede
     * @param int $sede_id ID de la sede
     * @return array Lista de hallazgos de la sede
     */
    public function getBySede($sede_id) {
        $stmt = $this->pdo->prepare("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre, s.nombre as sede_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            LEFT JOIN Sedes s ON h.sede_id = s.id
            WHERE h.sede_id = ?
            ORDER BY h.fecha_creacion DESC
        ");
        $stmt->execute([$sede_id]);
        $hallazgos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hallazgos as &$hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgos;
    }

    /**
     * Busca hallazgos por múltiples criterios incluyendo sede
     * @param array $filtros Array de filtros
     * @return array Lista de hallazgos filtrados
     */
    public function search($filtros = []) {
        $sql = "
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre, s.nombre as sede_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            LEFT JOIN Sedes s ON h.sede_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['sede_id'])) {
            $sql .= " AND h.sede_id = ?";
            $params[] = $filtros['sede_id'];
        }

        if (!empty($filtros['estado_id'])) {
            $sql .= " AND h.id_estado = ?";
            $params[] = $filtros['estado_id'];
        }

        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND h.id_usuario = ?";
            $params[] = $filtros['usuario_id'];
        }

        if (!empty($filtros['titulo'])) {
            $sql .= " AND h.titulo LIKE ?";
            $params[] = '%' . $filtros['titulo'] . '%';
        }

        $sql .= " ORDER BY h.fecha_creacion DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $hallazgos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hallazgos as &$hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgos;
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
                    COUNT(h.id) as total,
                    ROUND(COUNT(h.id) * 100.0 / (SELECT COUNT(*) FROM Hallazgo), 2) as porcentaje
                FROM Estado e
                LEFT JOIN Hallazgo h ON e.id = h.id_estado
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