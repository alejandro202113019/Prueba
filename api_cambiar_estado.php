<?php
// api_cambiar_estado.php - Endpoint dedicado para cambio de estados
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla para no afectar JSON

// PRIMERA LÍNEA: Headers JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log de inicio
error_log("🔄 API Cambiar Estado - Inicio");

try {
    // Verificar que es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido. Use POST.'
        ]);
        exit;
    }

    // Cargar configuración y modelos
    require_once 'config.php';
    require_once 'models/HallazgoModel.php';
    require_once 'models/IncidenteModel.php';
    require_once 'models/factories/EstadoFactory.php';

    error_log("📋 Datos POST: " . json_encode($_POST));

    // Extraer parámetros
    $entity = $_POST['entity'] ?? 'hallazgo';
    $recordId = $_POST['record_id'] ?? null;
    $estadoActual = $_POST['estado_actual'] ?? null;
    $estadoNuevo = $_POST['estado_nuevo'] ?? null;
    $usuarioId = $_POST['usuario_id'] ?? 1;

    error_log("📋 Parámetros: Entity=$entity, ID=$recordId, $estadoActual -> $estadoNuevo");

    // Validar parámetros
    if (!$recordId || !$estadoActual || !$estadoNuevo) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Parámetros requeridos: record_id, estado_actual, estado_nuevo'
        ]);
        exit;
    }

    // Inicializar modelo según entidad
    if ($entity === 'incidente') {
        $model = new IncidenteModel($pdo);
        $tabla = 'Incidente';
    } else {
        $model = new HallazgoModel($pdo);
        $tabla = 'Hallazgo';
    }

    // Verificar que el registro existe
    $registro = $model->getById($recordId);
    if (!$registro) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => ucfirst($entity) . ' no encontrado'
        ]);
        exit;
    }

    error_log("✅ Registro encontrado: " . ($registro['titulo'] ?? $registro['descripcion']));

    // Validar transición
    if (!EstadoFactory::validarTransicion($estadoActual, $estadoNuevo)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Transición no válida de '{$estadoActual}' a '{$estadoNuevo}'"
        ]);
        exit;
    }

    error_log("✅ Transición válida");

    // Obtener ID del nuevo estado
    $stmt = $pdo->prepare("SELECT id FROM Estado WHERE nombre = ?");
    $stmt->execute([$estadoNuevo]);
    $estadoRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estadoRow) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Estado '{$estadoNuevo}' no encontrado"
        ]);
        exit;
    }

    $nuevoEstadoId = $estadoRow['id'];
    error_log("✅ Estado ID: $nuevoEstadoId");

    // Ejecutar cambio en BD
    $resultado = $model->cambiarEstado($recordId, $nuevoEstadoId);

    if ($resultado) {
        error_log("✅ Cambio exitoso en BD");

        // Registrar en auditoría (opcional)
        try {
            if (file_exists('models/AuditoriaModel.php')) {
                require_once 'models/AuditoriaModel.php';
                $auditoriaModel = new AuditoriaModel($pdo);
                $auditoriaModel->registrar([
                    'tabla' => $tabla,
                    'registro_id' => $recordId,
                    'accion' => 'cambiar_estado',
                    'valor_anterior' => $estadoActual,
                    'valor_nuevo' => $estadoNuevo,
                    'usuario_id' => $usuarioId
                ]);
            }
        } catch (Exception $e) {
            error_log("⚠️ Error auditoría: " . $e->getMessage());
        }

        // Respuesta exitosa
        $response = [
            'success' => true,
            'message' => "Estado cambiado exitosamente de '{$estadoActual}' a '{$estadoNuevo}'",
            'data' => [
                'record_id' => $recordId,
                'entity' => $entity,
                'estado_anterior' => $estadoActual,
                'estado_nuevo' => $estadoNuevo,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];

        error_log("✅ Respuesta: " . json_encode($response));
        echo json_encode($response);

    } else {
        throw new Exception("Error al actualizar en la base de datos");
    }

} catch (Exception $e) {
    error_log("❌ Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

error_log("🔚 API Cambiar Estado - Fin");
exit;
?>