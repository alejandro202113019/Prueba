<?php
// models/factories/EstadoFactory.php
require_once 'models/states/EstadoAbierto.php';
require_once 'models/states/EstadoEnProceso.php';
require_once 'models/states/EstadoResuelto.php';
require_once 'models/states/EstadoCerrado.php';

class EstadoFactory {
    
    /**
     * Crea una instancia de estado según el nombre
     * @param string $nombreEstado Nombre del estado
     * @return EstadoInterface
     * @throws InvalidArgumentException Si el estado no existe
     */
    public static function crear($nombreEstado) {
        switch ($nombreEstado) {
            case 'Abierto':
                return new EstadoAbierto();
            case 'En Proceso':
                return new EstadoEnProceso();
            case 'Resuelto':
                return new EstadoResuelto();
            case 'Cerrado':
                return new EstadoCerrado();
            default:
                throw new InvalidArgumentException("Estado no válido: {$nombreEstado}");
        }
    }
    
    /**
     * Obtiene todos los estados disponibles
     * @return array Array de EstadoInterface
     */
    public static function obtenerTodos() {
        return [
            'Abierto' => new EstadoAbierto(),
            'En Proceso' => new EstadoEnProceso(),
            'Resuelto' => new EstadoResuelto(),
            'Cerrado' => new EstadoCerrado()
        ];
    }
    
    /**
     * Valida si una transición es permitida
     * @param string $estadoActual Estado actual
     * @param string $estadoNuevo Estado objetivo
     * @return bool
     */
    public static function validarTransicion($estadoActual, $estadoNuevo) {
        try {
            $estado = self::crear($estadoActual);
            return $estado->puedeTransicionarA($estadoNuevo);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
    
    /**
     * Obtiene los estados permitidos desde un estado actual
     * @param string $estadoActual Estado actual
     * @return array Estados permitidos con descripción
     */
    public static function obtenerEstadosPermitidos($estadoActual) {
        try {
            $estado = self::crear($estadoActual);
            return $estado->getEstadosPermitidos();
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
    
    /**
     * Obtiene información completa de un estado
     * @param string $nombreEstado Nombre del estado
     * @return array Información del estado
     */
    public static function obtenerInfoEstado($nombreEstado) {
        try {
            $estado = self::crear($nombreEstado);
            return [
                'nombre' => $estado->getNombre(),
                'color' => $estado->getColor(),
                'icono' => $estado->getIcono(),
                'es_critico' => $estado->esCritico(),
                'metadata' => $estado->getMetadata(),
                'estados_permitidos' => $estado->getEstadosPermitidos()
            ];
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}
?>