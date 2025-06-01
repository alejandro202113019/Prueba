<?php
// models/repositories/SedeRepository.php
require_once 'models/interfaces/SedeRepositoryInterface.php';

class SedeRepository implements SedeRepositoryInterface {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function findAll($activas = true) {
        $sql = "SELECT * FROM Sedes";
        if ($activas) {
            $sql .= " WHERE activa = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Sedes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByUsuario($usuarioId) {
        // Para este ejemplo, todos los usuarios pueden ver todas las sedes activas
        // En un sistema real, podríamos tener una tabla de permisos usuario-sede
        return $this->findAll(true);
    }
    
    public function findHallazgosBySede($sedeId) {
        $stmt = $this->pdo->prepare("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre, s.nombre as sede_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            LEFT JOIN Sedes s ON h.sede_id = s.id
            WHERE h.sede_id = ?
            ORDER BY h.fecha_creacion DESC
        ");
        $stmt->execute([$sedeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function save($sede) {
        if (isset($sede['id']) && $sede['id']) {
            // Actualizar sede existente
            $stmt = $this->pdo->prepare("
                UPDATE Sedes 
                SET nombre = ?, direccion = ?, ciudad = ?, telefono = ?, activa = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $sede['nombre'],
                $sede['direccion'],
                $sede['ciudad'],
                $sede['telefono'],
                $sede['activa'],
                $sede['id']
            ]);
            return $result ? $sede['id'] : false;
        } else {
            // Crear nueva sede
            $stmt = $this->pdo->prepare("
                INSERT INTO Sedes (nombre, direccion, ciudad, telefono, activa)
                VALUES (?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $sede['nombre'],
                $sede['direccion'],
                $sede['ciudad'],
                $sede['telefono'],
                $sede['activa'] ?? 1
            ]);
            return $result ? $this->pdo->lastInsertId() : false;
        }
    }
    
    /**
     * Obtiene estadísticas de hallazgos por sede
     * @return array Estadísticas de hallazgos por sede
     */
    public function getEstadisticasHallazgos() {
        $stmt = $this->pdo->query("
            SELECT 
                s.id,
                s.nombre as sede_nombre,
                COUNT(h.id) as total_hallazgos,
                SUM(CASE WHEN e.nombre = 'Abierto' THEN 1 ELSE 0 END) as hallazgos_abiertos,
                SUM(CASE WHEN e.nombre = 'Cerrado' THEN 1 ELSE 0 END) as hallazgos_cerrados
            FROM Sedes s
            LEFT JOIN Hallazgo h ON s.id = h.sede_id
            LEFT JOIN Estado e ON h.id_estado = e.id
            WHERE s.activa = 1
            GROUP BY s.id, s.nombre
            ORDER BY total_hallazgos DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>