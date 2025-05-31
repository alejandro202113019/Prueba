<?php
// models/HallazgoModel.php
require_once 'config.php';

class HallazgoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
        ");
        $hallazgos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hallazgos as &$hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgos;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT h.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM Hallazgo h
            LEFT JOIN Estado e ON h.id_estado = e.id
            LEFT JOIN Usuario u ON h.id_usuario = u.id
            WHERE h.id = ?
        ");
        $stmt->execute([$id]);
        $hallazgo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hallazgo) {
            $hallazgo['procesos'] = $this->getProcesos($hallazgo['id']);
        }
        return $hallazgo;
    }

    public function insert($titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario) {
        $stmt = $this->pdo->prepare("INSERT INTO Hallazgo (titulo, descripcion, id_estado, id_usuario) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$titulo, $descripcion, $id_estado, $id_usuario]);

        if ($result) {
            $hallazgo_id = $this->pdo->lastInsertId();
            $this->updateProcesos($hallazgo_id, $proceso_ids);
            return true;
        }
        return false;
    }

    public function update($id, $titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario) {
        $stmt = $this->pdo->prepare("UPDATE Hallazgo SET titulo = ?, descripcion = ?, id_estado = ?, id_usuario = ? WHERE id = ?");
        $result = $stmt->execute([$titulo, $descripcion, $id_estado, $id_usuario, $id]);

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
}
?>