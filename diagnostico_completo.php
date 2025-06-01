<?php
// diagnostico_completo.php - Script para identificar y corregir errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Diagnóstico Completo del Sistema</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
</style>";

// ===== 1. VERIFICACIÓN DE BASE DE DATOS =====
echo "<div class='section'>";
echo "<h2>1. 🗄️ Verificación de Base de Datos</h2>";

try {
    require_once 'config.php';
    echo "<span class='success'>✅ Conexión a BD exitosa</span><br>";
    
    // Verificar tablas requeridas
    $tablasRequeridas = ['Hallazgo', 'Incidente', 'Estado', 'Usuario', 'Sedes', 'Proceso'];
    foreach ($tablasRequeridas as $tabla) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt->rowCount() > 0) {
            echo "<span class='success'>✅ Tabla $tabla existe</span><br>";
        } else {
            echo "<span class='error'>❌ Tabla $tabla NO existe</span><br>";
        }
    }
    
    // Verificar columna sede_id en Hallazgo
    $stmt = $pdo->query("SHOW COLUMNS FROM Hallazgo LIKE 'sede_id'");
    if ($stmt->rowCount() > 0) {
        echo "<span class='success'>✅ Columna sede_id en Hallazgo existe</span><br>";
    } else {
        echo "<span class='error'>❌ Columna sede_id en Hallazgo NO existe - Ejecutar: ALTER TABLE Hallazgo ADD COLUMN sede_id INT NULL;</span><br>";
    }
    
    // Verificar datos de prueba
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Estado");
    $estados = $stmt->fetch()['count'];
    echo "<span class='info'>📊 Estados en BD: $estados</span><br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Usuario");
    $usuarios = $stmt->fetch()['count'];
    echo "<span class='info'>📊 Usuarios en BD: $usuarios</span><br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Sedes");
    $sedes = $stmt->fetch()['count'];
    echo "<span class='info'>📊 Sedes en BD: $sedes</span><br>";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Error de BD: " . $e->getMessage() . "</span><br>";
}
echo "</div>";

// ===== 2. VERIFICACIÓN DE ARCHIVOS =====
echo "<div class='section'>";
echo "<h2>2. 📁 Verificación de Archivos Críticos</h2>";

$archivosRequeridos = [
    'config.php',
    'index.php',
    'controllers/HallazgoController.php',
    'controllers/IncidenteController.php',
    'models/HallazgoModel.php',
    'models/IncidenteModel.php',
    'models/SedeModel.php',
    'models/EstadoModel.php',
    'models/factories/EstadoFactory.php',
    'models/commands/CambiarEstadoCommand.php',
    'models/strategies/IncidenteSearchStrategy.php',
    'assets/js/components/SearchComponent.js',
    'assets/js/components/EstadoManager.js',
    'assets/js/components/SedeManager.js',
    'views/hallazgo/list.php',
    'views/incidente/list.php'
];

foreach ($archivosRequeridos as $archivo) {
    if (file_exists($archivo)) {
        echo "<span class='success'>✅ $archivo existe</span><br>";
    } else {
        echo "<span class='error'>❌ $archivo NO existe</span><br>";
    }
}
echo "</div>";

// ===== 3. VERIFICACIÓN DE CLASES =====
echo "<div class='section'>";
echo "<h2>3. 🔧 Verificación de Clases y Patrones</h2>";

// Verificar EstadoFactory
try {
    if (class_exists('EstadoFactory')) {
        echo "<span class='success'>✅ EstadoFactory disponible</span><br>";
        
        // Test de estados
        $estados = ['Abierto', 'En Proceso', 'Resuelto', 'Cerrado'];
        foreach ($estados as $estado) {
            try {
                $estadoObj = EstadoFactory::crear($estado);
                echo "<span class='success'>✅ Estado '$estado' funciona correctamente</span><br>";
            } catch (Exception $e) {
                echo "<span class='error'>❌ Error en estado '$estado': " . $e->getMessage() . "</span><br>";
            }
        }
    } else {
        echo "<span class='error'>❌ EstadoFactory NO disponible</span><br>";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error cargando EstadoFactory: " . $e->getMessage() . "</span><br>";
}

// Verificar otros componentes críticos
$clases = [
    'HallazgoModel',
    'IncidenteModel', 
    'SedeModel',
    'CambiarEstadoCommand',
    'IncidenteSearchStrategy'
];

foreach ($clases as $clase) {
    if (class_exists($clase)) {
        echo "<span class='success'>✅ Clase $clase disponible</span><br>";
    } else {
        echo "<span class='warning'>⚠️ Clase $clase NO disponible (puede requerir require_once)</span><br>";
    }
}
echo "</div>";

// ===== 4. TEST DE ENDPOINTS =====
echo "<div class='section'>";
echo "<h2>4. 🌐 Test de Endpoints Críticos</h2>";

// Test básico de URLs principales
$endpoints = [
    'index.php?entity=hallazgo&action=index' => 'Listado de Hallazgos',
    'index.php?entity=incidente&action=index' => 'Listado de Incidentes',
    'index.php?entity=hallazgo&action=filtrar_por_sede' => 'Filtro por Sede',
    'index.php?entity=incidente&action=buscar_por_id&id=1' => 'Búsqueda por ID'
];

foreach ($endpoints as $url => $descripcion) {
    echo "<span class='info'>🔗 $descripcion: $url</span><br>";
}
echo "</div>";

// ===== 5. VERIFICACIÓN JAVASCRIPT =====
echo "<div class='section'>";
echo "<h2>5. 🎯 Verificación de JavaScript</h2>";

// Verificar que los archivos JS existen y tienen contenido
$jsFiles = [
    'assets/js/components/SearchComponent.js',
    'assets/js/components/EstadoManager.js', 
    'assets/js/components/SedeManager.js'
];

foreach ($jsFiles as $jsFile) {
    if (file_exists($jsFile)) {
        $content = file_get_contents($jsFile);
        $lines = count(explode("\n", $content));
        echo "<span class='success'>✅ $jsFile existe ($lines líneas)</span><br>";
        
        // Verificar funciones críticas
        if (strpos($content, 'class ') !== false) {
            echo "<span class='success'>  ✅ Contiene definición de clase</span><br>";
        }
        if (strpos($content, 'addEventListener') !== false) {
            echo "<span class='success'>  ✅ Contiene event listeners</span><br>";
        }
        if (strpos($content, 'fetch(') !== false) {
            echo "<span class='success'>  ✅ Contiene llamadas AJAX</span><br>";
        }
    } else {
        echo "<span class='error'>❌ $jsFile NO existe</span><br>";
    }
}
echo "</div>";

// ===== 6. PRUEBAS FUNCIONALES =====
echo "<div class='section'>";
echo "<h2>6. ⚡ Pruebas Funcionales Rápidas</h2>";

try {
    // Test H-4063: Búsqueda por ID
    echo "<h3>H-4063: Búsqueda de Incidentes</h3>";
    require_once 'models/IncidenteModel.php';
    $incidenteModel = new IncidenteModel($pdo);
    $incidente = $incidenteModel->searchById(1);
    if ($incidente) {
        echo "<span class='success'>✅ Búsqueda por ID funciona</span><br>";
    } else {
        echo "<span class='warning'>⚠️ No se encontró incidente ID=1 (normal si no hay datos)</span><br>";
    }
    
    // Test H-5995: Estados
    echo "<h3>H-5995: Cambio de Estados</h3>";
    require_once 'models/factories/EstadoFactory.php';
    $transicionValida = EstadoFactory::validarTransicion('Abierto', 'En Proceso');
    if ($transicionValida) {
        echo "<span class='success'>✅ Validación de transiciones funciona</span><br>";
    } else {
        echo "<span class='error'>❌ Error en validación de transiciones</span><br>";
    }
    
    // Test H-6568: Sedes
    echo "<h3>H-6568: Gestión de Sedes</h3>";
    require_once 'models/SedeModel.php';
    $sedeModel = new SedeModel($pdo);
    $sedes = $sedeModel->getAll();
    echo "<span class='info'>📊 Sedes encontradas: " . count($sedes) . "</span><br>";
    if (count($sedes) > 0) {
        echo "<span class='success'>✅ Modelo de Sedes funciona</span><br>";
    } else {
        echo "<span class='warning'>⚠️ No hay sedes configuradas</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Error en pruebas funcionales: " . $e->getMessage() . "</span><br>";
}
echo "</div>";

// ===== 7. RECOMENDACIONES =====
echo "<div class='section'>";
echo "<h2>7. 💡 Recomendaciones y Correcciones</h2>";

echo "<h3>🚀 Pasos para Solucionar Errores Comunes:</h3>";
echo "<ol>";
echo "<li><strong>Si falta la tabla Sedes:</strong><br>";
echo "   Ejecutar: <code>mysql -u root -p GestionHallazgos < sedes.sql</code></li>";

echo "<li><strong>Si los JavaScript no funcionan:</strong><br>";
echo "   Verificar que jQuery y Bootstrap estén cargados en las vistas</li>";

echo "<li><strong>Si los endpoints retornan HTML en lugar de JSON:</strong><br>";
echo "   Verificar que no haya salida antes de header('Content-Type: application/json')</li>";

echo "<li><strong>Si los estados no cambian:</strong><br>";
echo "   Verificar en consola del navegador si hay errores de JavaScript</li>";

echo "<li><strong>Si la búsqueda no funciona:</strong><br>";
echo "   Verificar que la URL sea correcta y que el método buscarPorId() esté implementado</li>";
echo "</ol>";

echo "<h3>🔧 Comandos de Reparación Rápida:</h3>";
echo "<pre>";
echo "-- Para crear la tabla Sedes si no existe:\n";
echo "CREATE TABLE Sedes (\n";
echo "    id INT AUTO_INCREMENT PRIMARY KEY,\n";
echo "    nombre VARCHAR(100) NOT NULL,\n";
echo "    direccion VARCHAR(200),\n";
echo "    ciudad VARCHAR(100),\n";
echo "    telefono VARCHAR(20),\n";
echo "    activa BOOLEAN DEFAULT TRUE,\n";
echo "    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n";
echo ");\n\n";

echo "-- Para agregar columna sede_id a Hallazgo:\n";
echo "ALTER TABLE Hallazgo ADD COLUMN sede_id INT NULL;\n";
echo "ALTER TABLE Hallazgo ADD CONSTRAINT fk_hallazgo_sede FOREIGN KEY (sede_id) REFERENCES Sedes(id);\n";
echo "</pre>";
echo "</div>";

// ===== 8. LOG DE ERRORES =====
echo "<div class='section'>";
echo "<h2>8. 📋 Log de Errores PHP</h2>";

$errorLog = ini_get('error_log');
if (file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    $recentErrors = array_slice(explode("\n", $errors), -10);
    echo "<pre>" . implode("\n", $recentErrors) . "</pre>";
} else {
    echo "<span class='info'>No se encontró log de errores o está vacío</span><br>";
}
echo "</div>";

echo "<hr>";
echo "<h2>🏁 Resumen del Diagnóstico</h2>";
echo "<p>Este diagnóstico te ayuda a identificar qué está fallando en tu implementación.</p>";
echo "<p><strong>Para continuar:</strong> Comparte la salida de este script y describe exactamente qué errores ves en el navegador.</p>";
echo "<p><strong>Errores comunes a buscar:</strong></p>";
echo "<ul>";
echo "<li>Errores 404 en llamadas AJAX</li>";
echo "<li>Errores de JavaScript en la consola del navegador</li>";
echo "<li>Errores PHP en el log de errores</li>";
echo "<li>Respuestas HTML cuando se esperan JSON</li>";
echo "</ul>";
?>