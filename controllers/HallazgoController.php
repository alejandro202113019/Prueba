<?php
// controllers/HallazgoController.php - Versión completa con cambio de estado
require_once 'models/HallazgoModel.php';
require_once 'models/ProcesoModel.php';
require_once 'models/EstadoModel.php';
require_once 'models/UsuarioModel.php';
require_once 'models/SedeModel.php';
require_once 'models/repositories/SedeRepository.php';
require_once 'models/factories/SedeFactory.php';
require_once 'models/factories/EstadoFactory.php';
require_once 'models/commands/CambiarEstadoCommand.php';
require_once 'models/EventManager.php';

class HallazgoController {
    private $model;
    private $procesoModel;
    private $estadoModel;
    private $usuarioModel;
    private $sedeModel;
    private $sedeRepository;

    public function __construct($pdo) {
        $this->model = new HallazgoModel($pdo);
        $this->procesoModel = new ProcesoModel($pdo);
        $this->estadoModel = new EstadoModel($pdo);
        $this->usuarioModel = new UsuarioModel($pdo);
        $this->sedeModel = new SedeModel($pdo);
        $this->sedeRepository = new SedeRepository($pdo);
    }

    public function index() {
        // Verificar si hay filtro por sede
        $sede_id = $_GET['sede_id'] ?? null;
        
        if ($sede_id) {
            $hallazgos = $this->model->getBySede($sede_id);
            $sedeSeleccionada = $this->sedeModel->getById($sede_id);
        } else {
            $hallazgos = $this->model->getAll();
            $sedeSeleccionada = null;
        }
        
        // Obtener sedes para el filtro
        $sedes = $this->sedeModel->getAll(true);
        
        require 'views/hallazgo/list.php';
    }

    public function show($id) {
        $hallazgo = $this->model->getById($id);
        require 'views/hallazgo/show.php';
    }

    public function create() {
        $procesos = $this->procesoModel->getAll();
        $estados = $this->estadoModel->getAll();
        $usuarios = $this->usuarioModel->getAll();
        $sedes = $this->sedeModel->getAll(true); // Solo sedes activas
        require 'views/hallazgo/create.php';
    }

    public function insert($data) {
        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];
        $proceso_ids = $data['procesos'] ?? [];
        $id_estado = $data['id_estado'];
        $id_usuario = $data['id_usuario'];
        $sede_id = !empty($data['sede_id']) ? $data['sede_id'] : null;

        $this->model->insert($titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario, $sede_id);
        header('Location: index.php?entity=hallazgo&action=index');
    }

    public function edit($id) {
        $hallazgo = $this->model->getById($id);
        $procesos = $this->procesoModel->getAll();
        $estados = $this->estadoModel->getAll();
        $usuarios = $this->usuarioModel->getAll();
        $sedes = $this->sedeModel->getAll(true); // Solo sedes activas
        $selectedProcesos = $this->model->getProcesos($hallazgo['id']);
        $selectedProcesoIds = array_column($selectedProcesos, 'id');
        require 'views/hallazgo/edit.php';
    }

    public function update($id, $data) {
        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];
        $proceso_ids = $data['procesos'] ?? [];
        $id_estado = $data['id_estado'];
        $id_usuario = $data['id_usuario'];
        $sede_id = !empty($data['sede_id']) ? $data['sede_id'] : null;

        $this->model->update($id, $titulo, $descripcion, $proceso_ids, $id_estado, $id_usuario, $sede_id);
        header('Location: index.php?entity=hallazgo&action=index');
    }

    public function delete($id) {
        $this->model->delete($id);
        header('Location: index.php?action=index');
    }

    /**
     * API endpoint para obtener hallazgos por sede
     * Método: GET index.php?entity=hallazgo&action=filtrar_por_sede&sede_id=X
     */
    public function filtrarPorSede() {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $sede_id = $_GET['sede_id'] ?? null;
            
            if (!$sede_id) {
                // Si no hay sede_id, devolver todos los hallazgos
                $hallazgos = $this->model->getAll();
            } else {
                // Obtener hallazgos de la sede específica
                $hallazgos = $this->model->getBySede($sede_id);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $hallazgos,
                'message' => count($hallazgos) . ' hallazgos encontrados'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
            
            error_log("Error en filtro por sede: " . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas de sedes
     */
    public function estadisticasSedes() {
        $estadisticas = $this->sedeRepository->getEstadisticasHallazgos();
        require 'views/hallazgo/estadisticas_sedes.php';
    }

    /**
     * API endpoint para obtener estados permitidos desde el estado actual
     * Método: GET index.php?entity=hallazgo&action=obtener_estados_permitidos&record_id=X&estado_actual=Y
     */
    public function obtenerEstadosPermitidos() {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $recordId = $_GET['record_id'] ?? null;
            $estadoActual = $_GET['estado_actual'] ?? null;
            
            if (!$recordId || !$estadoActual) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Parámetros record_id y estado_actual son requeridos'
                ]);
                return;
            }
            
            // Obtener estados permitidos usando State Pattern
            $estadosPermitidos = EstadoFactory::obtenerEstadosPermitidos($estadoActual);
            
            // Formatear para el frontend
            $opciones = [];
            foreach ($estadosPermitidos as $estado => $descripcion) {
                $opciones[] = [
                    'estado' => $estado,
                    'descripcion' => $descripcion
                ];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $opciones,
                'message' => count($opciones) . ' opciones disponibles'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error obteniendo estados permitidos'
            ]);
            
            error_log("Error obteniendo estados permitidos: " . $e->getMessage());
        }
    }

    /**
     * API endpoint para cambiar estado usando Command Pattern
     * Método: POST con entity=hallazgo&action=cambiar_estado
     */
    public function cambiarEstado() {
        // PRIMERA LÍNEA: Asegurar que no hay output antes
        error_log("🔄 INICIO cambiarEstado() - Method: " . $_SERVER['REQUEST_METHOD']);
        
        // IMPORTANTE: Headers primero, antes de cualquier output
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Log para debug
        error_log("✅ Headers JSON enviados");
        
        try {
            // Verificar que es POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("❌ Método no es POST: " . $_SERVER['REQUEST_METHOD']);
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'message' => 'Método no permitido. Use POST.'
                ]);
                exit; // CRÍTICO
            }
            
            error_log("📋 Datos POST recibidos: " . json_encode($_POST));
            
            $recordId = $_POST['record_id'] ?? null;
            $estadoActual = $_POST['estado_actual'] ?? null;
            $estadoNuevo = $_POST['estado_nuevo'] ?? null;
            $usuarioId = $_POST['usuario_id'] ?? 1;
            $comentario = $_POST['comentario'] ?? null;
            
            error_log("📋 Parámetros extraídos: ID=$recordId, $estadoActual -> $estadoNuevo");
            
            // Validar parámetros requeridos
            if (!$recordId || !$estadoActual || !$estadoNuevo) {
                error_log("❌ Faltan parámetros requeridos");
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Parámetros requeridos: record_id, estado_actual, estado_nuevo',
                    'debug' => [
                        'record_id' => $recordId,
                        'estado_actual' => $estadoActual,
                        'estado_nuevo' => $estadoNuevo
                    ]
                ]);
                exit; // CRÍTICO
            }
            
            // Verificar que el hallazgo existe
            error_log("🔍 Buscando hallazgo ID: $recordId");
            $hallazgo = $this->model->getById($recordId);
            if (!$hallazgo) {
                error_log("❌ Hallazgo no encontrado: $recordId");
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Hallazgo no encontrado'
                ]);
                exit; // CRÍTICO
            }
            
            error_log("✅ Hallazgo encontrado: " . $hallazgo['titulo']);
            
            // Validar transición usando State Pattern
            error_log("🔍 Validando transición: $estadoActual -> $estadoNuevo");
            
            // Asegurar que EstadoFactory esté disponible
            if (!class_exists('EstadoFactory')) {
                require_once 'models/factories/EstadoFactory.php';
            }
            
            if (!EstadoFactory::validarTransicion($estadoActual, $estadoNuevo)) {
                error_log("❌ Transición no válida: $estadoActual -> $estadoNuevo");
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Transición no válida de '{$estadoActual}' a '{$estadoNuevo}'"
                ]);
                exit; // CRÍTICO
            }
            
            error_log("✅ Transición válida");
            
            // Obtener ID del nuevo estado
            error_log("🔍 Buscando ID del estado: $estadoNuevo");
            $stmt = $this->model->getPdo()->prepare("SELECT id FROM Estado WHERE nombre = ?");
            $stmt->execute([$estadoNuevo]);
            $estadoRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$estadoRow) {
                error_log("❌ Estado no encontrado en BD: $estadoNuevo");
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Estado '{$estadoNuevo}' no encontrado en la base de datos"
                ]);
                exit; // CRÍTICO
            }
            
            $nuevoEstadoId = $estadoRow['id'];
            error_log("✅ Estado ID encontrado: $nuevoEstadoId");
            
            // Ejecutar cambio en la base de datos
            error_log("🔄 Ejecutando cambio en BD...");
            $resultado = $this->model->cambiarEstado($recordId, $nuevoEstadoId);
            
            if ($resultado) {
                error_log("✅ Cambio exitoso en BD");
                
                // Registrar en auditoría (opcional)
                try {
                    if (file_exists('models/AuditoriaModel.php')) {
                        require_once 'models/AuditoriaModel.php';
                        $auditoriaModel = new AuditoriaModel($this->model->getPdo());
                        $auditoriaModel->registrar([
                            'tabla' => 'Hallazgo',
                            'registro_id' => $recordId,
                            'accion' => 'cambiar_estado',
                            'valor_anterior' => $estadoActual,
                            'valor_nuevo' => $estadoNuevo,
                            'usuario_id' => $usuarioId,
                            'comentario' => $comentario
                        ]);
                        error_log("✅ Auditoría registrada");
                    }
                } catch (Exception $auditError) {
                    error_log("⚠️ Error en auditoría (no crítico): " . $auditError->getMessage());
                }
                
                // Respuesta exitosa
                $response = [
                    'success' => true,
                    'message' => "Estado cambiado exitosamente de '{$estadoActual}' a '{$estadoNuevo}'",
                    'data' => [
                        'record_id' => $recordId,
                        'estado_anterior' => $estadoActual,
                        'estado_nuevo' => $estadoNuevo,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ];
                
                error_log("✅ Enviando respuesta exitosa: " . json_encode($response));
                echo json_encode($response);
                
            } else {
                error_log("❌ Error en cambio de BD");
                throw new Exception("Error al actualizar el estado en la base de datos");
            }
            
        } catch (Exception $e) {
            error_log("❌ Excepción capturada: " . $e->getMessage());
            http_response_code(500);
            $errorResponse = [
                'success' => false,
                'message' => 'Error cambiando estado: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
            echo json_encode($errorResponse);
        }
        
        // CRÍTICO: Terminar aquí para evitar output adicional
        error_log("🔚 FINAL cambiarEstado() - Ejecutando exit");
        exit;
    }
}
?>