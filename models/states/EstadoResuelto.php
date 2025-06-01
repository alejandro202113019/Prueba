<?php
// models/states/EstadoResuelto.php
require_once 'models/interfaces/EstadoInterface.php';

class EstadoResuelto implements EstadoInterface {
    
    public function getNombre() {
        return 'Resuelto';
    }
    
    public function puedeTransicionarA($nuevoEstado) {
        $permitidos = ['Cerrado', 'En Proceso'];
        return in_array($nuevoEstado, $permitidos);
    }
    
    public function getEstadosPermitidos() {
        return [
            'Cerrado' => 'Cerrar definitivamente este registro',
            'En Proceso' => 'Reabrir para trabajo adicional si es necesario'
        ];
    }
    
    public function esCritico() {
        return false; // Ya está resuelto
    }
    
    public function getColor() {
        return 'success';
    }
    
    public function getIcono() {
        return '✅';
    }
    
    public function getMetadata() {
        return [
            'descripcion' => 'Registro resuelto, esperando confirmación para cierre',
            'prioridad' => 'baja',
            'requiere_accion' => false,
            'tiempo_maximo_recomendado' => '168 horas' // 1 semana
        ];
    }
}
?>