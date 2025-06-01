<?php
// models/states/EstadoCerrado.php
require_once 'models/interfaces/EstadoInterface.php';

class EstadoCerrado implements EstadoInterface {
    
    public function getNombre() {
        return 'Cerrado';
    }
    
    public function puedeTransicionarA($nuevoEstado) {
        // Los estados cerrados solo pueden reabrirse en casos especiales
        $permitidos = ['Abierto'];
        return in_array($nuevoEstado, $permitidos);
    }
    
    public function getEstadosPermitidos() {
        return [
            'Abierto' => 'Reabrir registro cerrado (requiere justificación)'
        ];
    }
    
    public function esCritico() {
        return false; // Ya está cerrado
    }
    
    public function getColor() {
        return 'secondary';
    }
    
    public function getIcono() {
        return '🔒';
    }
    
    public function getMetadata() {
        return [
            'descripcion' => 'Registro cerrado definitivamente',
            'prioridad' => 'ninguna',
            'requiere_accion' => false,
            'tiempo_maximo_recomendado' => null,
            'es_final' => true
        ];
    }
}
?>