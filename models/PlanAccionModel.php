<?php
// models/PlanAccionModel.php
require_once 'config.php';

class PlanAccionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByRegistro($id_registro, $origen_registro) {
        $stmt = $this->pdo->prepare("
            SELECT pa.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM PlanAccion pa
            INNER JOIN Registro_PlanAccion rpa ON pa.id = rpa.id_plan_accion
            LEFT JOIN Estado e ON pa.id_estado = e.id
            LEFT JOIN Usuario u ON pa.id_usuario = u.id
            WHERE rpa.id_registro = ? AND rpa.origen_registro = ?
        ");
        $stmt->execute([$id_registro, $origen_registro]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT pa.*, e.nombre as estado_nombre, u.nombre as usuario_nombre
            FROM PlanAccion pa
            LEFT JOIN Estado e ON pa.id_estado = e.id
            LEFT JOIN Usuario u ON pa.id_usuario = u.id
            WHERE pa.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO PlanAccion (descripcion, id_usuario, fecha_inicio, fecha_fin, id_estado)
            VALUES (?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $data['descripcion'],
            $data['id_usuario'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['id_estado']
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE PlanAccion SET descripcion = ?, id_usuario = ?, fecha_inicio = ?, fecha_fin = ?, id_estado = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['descripcion'],
            $data['id_usuario'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['id_estado'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM PlanAccion WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function linkToRegistro($id_plan_accion, $id_registro, $origen_registro) {
        $stmt = $this->pdo->prepare("
            INSERT INTO Registro_PlanAccion (id_registro, origen_registro, id_plan_accion)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$id_registro, $origen_registro, $id_plan_accion]);
    }

    public function unlinkFromRegistro($id_plan_accion, $id_registro, $origen_registro) {
        $stmt = $this->pdo->prepare("
            DELETE FROM Registro_PlanAccion
            WHERE id_registro = ? AND origen_registro = ? AND id_plan_accion = ?
        ");
        return $stmt->execute([$id_registro, $origen_registro, $id_plan_accion]);
    }
}
