<?php
// controllers/HallazgoController.php - Versión con cambio de estado
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
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            $recordId = $_POST['record_id'] ?? null;
            $estadoActual = $_POST['estado_actual'] ?? null;
            $estadoNuevo = $_POST['estado_nuevo'] ?? null;
            $usuarioId = $_POST['usuario_id'] ?? 1; // TODO: Obtener usuario actual
            $comentario = $_POST['comentario'] ?? null;
            
            if (!$recordId || !$estadoActual || !$estadoNuevo) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Parámetros requeridos: record_id, estado_actual, estado_nuevo'
                ]);
                return;
            }
            
            // Verificar que el hallazgo existe
            $hallazgo = $this->model->getById($recordId);
            if (!$hallazgo) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Hallazgo no encontrado'
                ]);
                return;
            }
            
            // Crear y ejecutar comando usando Command Pattern
            $command = new CambiarEstadoCommand(
                $recordId,
                'hallazgo',
                $estadoActual,
                $estadoNuevo,
                $usuarioId,
                $this->model,
                $comentario
            );
            
            $resultado = $command->execute();
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => "Estado cambiado exitosamente de '{$estadoActual}' a '{$estadoNuevo}'",
                    'data' => [
                        'record_id' => $recordId,
                        'estado_anterior' => $estadoActual,
                        'estado_nuevo' => $estadoNuevo,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                throw new Exception("Error ejecutando el comando de cambio de estado");
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error cambiando estado: ' . $e->getMessage()
            ]);
            
            error_log("Error en cambio de estado: " . $e->getMessage());
        }
    }
}
?>