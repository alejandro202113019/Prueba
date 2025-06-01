<?php
// models/states/EstadoEnProceso.php
require_once 'models/interfaces/EstadoInterface.php';

class EstadoEnProceso implements EstadoInterface {
    
    public function getNombre() {
        return 'En Proceso';
    }
    
    public function puedeTransicionarA($nuevoEstado) {
        $permitidos = ['Abierto', 'Resuelto', 'Cerrado'];
        return in_array($nuevoEstado, $permitidos);
    }
    
    public function getEstadosPermitidos() {
        return [
            'Abierto' => 'Regresar a estado abierto si se requiere más información',
            'Resuelto' => 'Marcar como resuelto una vez completado',
            'Cerrado' => 'Cerrar sin resolución (casos especiales)'
        ];
    }
    
    public function esCritico() {
        return false; // Ya se está trabajando
    }
    
    public function getColor() {
        return 'warning';
    }
    
    public function getIcono() {
        return '⚠️';
    }
    
    public function getMetadata() {
        return [
            'descripcion' => 'Registro en progreso, siendo trabajado activamente',
            'prioridad' => 'media',
            'requiere_accion' => true,
            'tiempo_maximo_recomendado' => '72 horas'
        ];
    }
}
?>