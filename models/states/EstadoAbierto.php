<?php
// models/states/EstadoAbierto.php
require_once 'models/interfaces/EstadoInterface.php';

class EstadoAbierto implements EstadoInterface {
    
    public function getNombre() {
        return 'Abierto';
    }
    
    public function puedeTransicionarA($nuevoEstado) {
        $permitidos = ['En Proceso', 'Resuelto'];
        return in_array($nuevoEstado, $permitidos);
    }
    
    public function getEstadosPermitidos() {
        return [
            'En Proceso' => 'Comenzar a trabajar en este registro',
            'Resuelto' => 'Marcar como resuelto directamente'
        ];
    }
    
    public function esCritico() {
        return true; // Estado abierto requiere atención
    }
    
    public function getColor() {
        return 'danger'; // Bootstrap class
    }
    
    public function getIcono() {
        return '🚨';
    }
    
    public function getMetadata() {
        return [
            'descripcion' => 'Registro recién creado que requiere atención inmediata',
            'prioridad' => 'alta',
            'requiere_accion' => true,
            'tiempo_maximo_recomendado' => '24 horas'
        ];
    }
}
?>