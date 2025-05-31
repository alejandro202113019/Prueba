<?php
// index.php
require_once 'config.php';

$entity = $_GET['entity'] ?? 'hallazgo'; // Valor por defecto 'hallazgo'
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

if ($entity === 'incidente') {
    require_once 'controllers/IncidenteController.php';
    $controller = new IncidenteController($pdo);

    if ($action === 'planes_accion' && $id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action_plan']) && $_POST['action_plan'] === 'create') {
                $controller->insertPlanAccion($id, $_POST);
            } elseif (isset($_POST['action_plan']) && $_POST['action_plan'] === 'edit') {
                $id_plan_accion = $_POST['id_plan_accion'];
                $controller->updatePlanAccion($id, $id_plan_accion, $_POST);
            }
        } elseif (isset($_GET['delete_plan']) && $_GET['delete_plan']) {
            $id_plan_accion = $_GET['delete_plan'];
            $controller->deletePlanAccion($id, $id_plan_accion);
        } else {
            $controller->planesAccion($id);
        }
    } else {
        // Acciones existentes para 'incidente'
        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->insert($_POST);
        } elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update($id, $_POST);
        } elseif ($action === 'delete' && $id) {
            $controller->delete($id);
        } elseif ($action === 'show' && $id) {
            $controller->show($id);
        } elseif ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit' && $id) {
            $controller->edit($id);
        } else {
            $controller->index();
        }
    }
} else {
    require_once 'controllers/HallazgoController.php';
    $controller = new HallazgoController($pdo);

    // Acciones existentes para 'hallazgo'
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->insert($_POST);
    } elseif ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->update($id, $_POST);
    } elseif ($action === 'delete' && $id) {
        $controller->delete($id);
    } elseif ($action === 'show' && $id) {
        $controller->show($id);
    } elseif ($action === 'create') {
        $controller->create();
    } elseif ($action === 'edit' && $id) {
        $controller->edit($id);
    } else {
        $controller->index();
    }
}
?>