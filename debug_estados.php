<?php
// debug_estados.php - Versión simplificada
echo "<h2>🔍 Debug de Estados - Simplificado</h2>";

// Test 1: Cargar el archivo único
echo "<h3>1. Test Carga de EstadosManager</h3>";
try {
    require_once 'assets/js/components/EstadoManager.js';
    echo "✅ EstadosManager.php cargado correctamente<br>";
    
    // Test de EstadoFactory
    $estados = EstadoFactory::obtenerTodos();
    echo "✅ EstadoFactory funciona. Estados encontrados: " . count($estados) . "<br>";
    
    foreach ($estados as $nombre => $estado) {
        echo "📋 <strong>$nombre:</strong> " . $estado->getIcono() . " (Color: " . $estado->getColor() . ")<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error cargando EstadosManager: " . $e->getMessage() . "<br>";
}

// Test 2: Transiciones
echo "<h3>2. Test de Transiciones</h3>";
try {
    echo "Abierto → En Proceso: " . (EstadoFactory::validarTransicion('Abierto', 'En Proceso') ? '✅' : '❌') . "<br>";
    echo "Abierto → Cerrado: " . (EstadoFactory::validarTransicion('Abierto', 'Cerrado') ? '✅' : '❌') . " (debe fallar)<br>";
    echo "En Proceso → Resuelto: " . (EstadoFactory::validarTransicion('En Proceso', 'Resuelto') ? '✅' : '❌') . "<br>";
    
    // Estados permitidos
    $permitidos = EstadoFactory::obtenerEstadosPermitidos('Abierto');
    echo "Estados permitidos desde 'Abierto': " . json_encode($permitidos) . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error en transiciones: " . $e->getMessage() . "<br>";
}

// Test 3: Base de datos
echo "<h3>3. Test Base de Datos</h3>";
try {
    require_once 'config.php';
    
    $stmt = $pdo->query("SELECT * FROM Estado");
    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Estados en BD: " . count($estados) . "<br>";
    foreach ($estados as $estado) {
        echo "&nbsp;&nbsp;- " . $estado['id'] . ": " . $estado['nombre'] . "<br>";
    }
    
    // Test HallazgoModel
    require_once 'models/HallazgoModel.php';
    $hallazgoModel = new HallazgoModel($pdo);
    $estadoActual = $hallazgoModel->getEstadoActual(1);
    echo "✅ Estado actual del hallazgo #1: " . ($estadoActual ?: 'No encontrado') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error BD: " . $e->getMessage() . "<br>";
}

// Test 4: Endpoint directo
echo "<h3>4. Test Endpoint Directo</h3>";
try {
    // Simular GET
    $_GET = [
        'entity' => 'hallazgo',
        'action' => 'obtener_estados_permitidos',
        'record_id' => '1',
        'estado_actual' => 'Abierto'
    ];
    
    require_once 'controllers/HallazgoController.php';
    $controller = new HallazgoController($pdo);
    
    echo "📡 Simulando: GET index.php?entity=hallazgo&action=obtener_estados_permitidos&record_id=1&estado_actual=Abierto<br>";
    echo "📋 Respuesta del endpoint:<br>";
    echo "<pre>";
    ob_start();
    $controller->obtenerEstadosPermitidos();
    $response = ob_get_clean();
    echo htmlspecialchars($response);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ Error en endpoint: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Instrucciones:</strong></p>";
echo "<ol>";
echo "<li>Si todo está ✅, <a href='index.php?entity=hallazgo'>ve a probar los hallazgos</a></li>";
echo "<li>Si hay errores ❌, comparte la salida de este debug</li>";
echo "</ol>";
?>