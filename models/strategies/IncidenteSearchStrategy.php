<?php
// models/strategies/IncidenteSearchStrategy.php
require_once 'models/interfaces/SearchStrategyInterface.php';

class IncidenteSearchStrategy implements SearchStrategyInterface {
    private $incidenteModel;
    
    public function __construct($incidenteModel) {
        $this->incidenteModel = $incidenteModel;
    }
    
    public function validate($id) {
        // Validar que sea numérico y mayor a 0
        if (!is_numeric($id)) {
            return false;
        }
        
        $id = (int)$id;
        return $id > 0;
    }
    
    public function search($id) {
        if (!$this->validate($id)) {
            throw new InvalidArgumentException("El ID debe ser un número entero mayor a 0");
        }
        
        return $this->incidenteModel->searchById((int)$id);
    }
}
?>