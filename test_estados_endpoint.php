<?php
// test_estados_endpoint.php - Endpoint de prueba para estados
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");

try {
    require_once "config.php";
    require_once "models/factories/EstadoFactory.php";
    
    $recordId = $_GET["record_id"] ?? null;
    $estadoActual = $_GET["estado_actual"] ?? null;
    
    if (!$recordId || !$estadoActual) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Parámetros record_id y estado_actual son requeridos"
        ]);
        exit;
    }
    
    // Obtener estados permitidos
    $estadosPermitidos = EstadoFactory::obtenerEstadosPermitidos($estadoActual);
    
    // Formatear para el frontend
    $opciones = [];
    foreach ($estadosPermitidos as $estado => $descripcion) {
        $opciones[] = [
            "estado" => $estado,
            "descripcion" => $descripcion
        ];
    }
    
    echo json_encode([
        "success" => true,
        "data" => $opciones,
        "message" => count($opciones) . " opciones disponibles"
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>