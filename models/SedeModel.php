<?php
// models/SedeModel.php
require_once 'config.php';

class SedeModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($activas = true) {
        $sql = "SELECT * FROM Sedes";
        if ($activas) {
            $sql .= " WHERE activa = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Sedes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($nombre, $direccion, $ciudad, $telefono, $activa = 1) {
        $stmt = $this->pdo->prepare("INSERT INTO Sedes (nombre, direccion, ciudad, telefono, activa) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $direccion, $ciudad, $telefono, $activa]);
    }

    public function update($id, $nombre, $direccion, $ciudad, $telefono, $activa = 1) {
        $stmt = $this->pdo->prepare("UPDATE Sedes SET nombre = ?, direccion = ?, ciudad = ?, telefono = ?, activa = ? WHERE id = ?");
        return $stmt->execute([$nombre, $direccion, $ciudad, $telefono, $activa, $id]);
    }

    public function delete($id) {
        // Verificar si hay hallazgos asociados antes de eliminar
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Hallazgo WHERE sede_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            throw new Exception("No se puede eliminar la sede porque tiene hallazgos asociados");
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM Sedes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getEstadisticas() {
        $stmt = $this->pdo->query("
            SELECT 
                s.id,
                s.nombre,
                s.ciudad,
                COUNT(h.id) as total_hallazgos,
                SUM(CASE WHEN e.nombre = 'Abierto' THEN 1 ELSE 0 END) as hallazgos_abiertos,
                SUM(CASE WHEN e.nombre = 'En Proceso' THEN 1 ELSE 0 END) as hallazgos_en_proceso,
                SUM(CASE WHEN e.nombre = 'Resuelto' THEN 1 ELSE 0 END) as hallazgos_resueltos,
                SUM(CASE WHEN e.nombre = 'Cerrado' THEN 1 ELSE 0 END) as hallazgos_cerrados
            FROM Sedes s
            LEFT JOIN Hallazgo h ON s.id = h.sede_id
            LEFT JOIN Estado e ON h.id_estado = e.id
            WHERE s.activa = 1
            GROUP BY s.id, s.nombre, s.ciudad
            ORDER BY total_hallazgos DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>