<?php
// fix_estado_dropdown.php - Corrección específica para el problema de estados

echo "<h1>🔧 Corrección para Dropdown de Estados</h1>";

// ===== PASO 1: Verificar que el endpoint responda =====
echo "<h2>1. Testing del Endpoint de Estados</h2>";

try {
    require_once 'config.php';
    require_once 'models/factories/EstadoFactory.php';
    
    // Simular la llamada que hace JavaScript
    $_GET = [
        'entity' => 'hallazgo',
        'action' => 'obtener_estados_permitidos',
        'record_id' => '1',
        'estado_actual' => 'Abierto'
    ];
    
    echo "<p>🧪 Simulando: <code>obtener_estados_permitidos</code></p>";
    
    $estadosPermitidos = EstadoFactory::obtenerEstadosPermitidos('Abierto');
    
    if (!empty($estadosPermitidos)) {
        echo "<p>✅ EstadoFactory funciona correctamente</p>";
        echo "<p>📋 Estados permitidos:</p><ul>";
        foreach ($estadosPermitidos as $estado => $descripcion) {
            echo "<li><strong>$estado:</strong> $descripcion</li>";
        }
        echo "</ul>";
        
        // Formatear como lo haría el controller
        $opciones = [];
        foreach ($estadosPermitidos as $estado => $descripcion) {
            $opciones[] = [
                'estado' => $estado,
                'descripcion' => $descripcion
            ];
        }
        
        $response = [
            'success' => true,
            'data' => $opciones,
            'message' => count($opciones) . ' opciones disponibles'
        ];
        
        echo "<p>📡 Respuesta JSON que debería devolver:</p>";
        echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
        
    } else {
        echo "<p>❌ Error: EstadoFactory no devuelve estados</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// ===== PASO 2: Verificar el Controller =====
echo "<h2>2. Testing del Controller</h2>";

try {
    // Test directo del método
    require_once 'controllers/HallazgoController.php';
    
    echo "<p>🧪 Testing HallazgoController...</p>";
    
    // Simular la llamada
    ob_start();
    
    $controller = new HallazgoController($pdo);
    
    // Capturar cualquier salida antes del JSON
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "<p>❌ PROBLEMA ENCONTRADO: Hay salida antes del JSON</p>";
        echo "<p>📋 Salida capturada:</p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        echo "<p>💡 Esto causará que JavaScript no pueda parsear el JSON</p>";
    } else {
        echo "<p>✅ No hay salida prematura en el controller</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en controller: " . $e->getMessage() . "</p>";
}

// ===== PASO 3: Crear endpoint de prueba =====
echo "<h2>3. Creando Endpoint de Prueba</h2>";

$testEndpointContent = '<?php
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
?>';

if (file_put_contents('test_estados_endpoint.php', $testEndpointContent)) {
    echo "<p>✅ Creado: <code>test_estados_endpoint.php</code></p>";
    echo "<p>🧪 Para probar, visita:</p>";
    echo "<p><a href='test_estados_endpoint.php?record_id=1&estado_actual=Abierto' target='_blank'>";
    echo "test_estados_endpoint.php?record_id=1&estado_actual=Abierto</a></p>";
    echo "<p>Debería devolver JSON con las opciones de estado</p>";
} else {
    echo "<p>❌ Error creando endpoint de prueba</p>";
}

// ===== PASO 4: Verificar requires =====
echo "<h2>4. Verificando Requires</h2>";

$requiredFiles = [
    'models/interfaces/EstadoInterface.php',
    'models/states/EstadoAbierto.php',
    'models/states/EstadoEnProceso.php',
    'models/states/EstadoResuelto.php',
    'models/states/EstadoCerrado.php',
    'models/factories/EstadoFactory.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file existe</p>";
    } else {
        echo "<p>❌ $file NO existe</p>";
    }
}

// ===== PASO 5: Instrucciones =====
echo "<h2>5. 🚀 Instrucciones para Solucionar</h2>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
echo "<h3>Para identificar el problema exacto:</h3>";
echo "<ol>";
echo "<li><strong>Abre la consola del navegador</strong> (F12 → Console)</li>";
echo "<li><strong>Haz clic en un badge de estado</strong> en la lista de hallazgos</li>";
echo "<li><strong>Mira qué error aparece en rojo</strong></li>";
echo "<li><strong>Ve a la pestaña Network</strong> y busca la llamada que falla</li>";
echo "</ol>";

echo "<h3>Probables causas:</h3>";
echo "<ul>";
echo "<li>❌ <strong>Error 404:</strong> La URL no existe o index.php no maneja la acción</li>";
echo "<li>❌ <strong>Error 500:</strong> Error PHP en el servidor</li>";
echo "<li>❌ <strong>HTML en lugar de JSON:</strong> Algo imprime antes del header</li>";
echo "<li>❌ <strong>Requires faltantes:</strong> EstadoFactory no se carga</li>";
echo "</ul>";

echo "<h3>Soluciones por probar:</h3>";
echo "<ol>";
echo "<li><strong>Probar el endpoint de prueba</strong> que creamos arriba</li>";
echo "<li><strong>Si funciona:</strong> El problema está en index.php</li>";
echo "<li><strong>Si no funciona:</strong> El problema está en EstadoFactory</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>🎯 Siguiente paso:</strong> Ejecuta este script y luego prueba el endpoint que se creó</p>";
?>