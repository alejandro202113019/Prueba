<?php
// models/entities/Sede.php
require_once 'models/interfaces/SedeInterface.php';

class Sede implements SedeInterface {
    private $id;
    private $nombre;
    private $direccion;
    private $ciudad;
    private $telefono;
    private $activa;
    private $fechaCreacion;
    
    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
        $this->direccion = $data['direccion'] ?? '';
        $this->ciudad = $data['ciudad'] ?? '';
        $this->telefono = $data['telefono'] ?? '';
        $this->activa = isset($data['activa']) ? (bool)$data['activa'] : true;
        $this->fechaCreacion = $data['fecha_creacion'] ?? null;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getDireccion() {
        return $this->direccion;
    }
    
    public function getCiudad() {
        return $this->ciudad;
    }
    
    public function getTelefono() {
        return $this->telefono;
    }
    
    public function isActiva() {
        return $this->activa;
    }
    
    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
            'activa' => $this->activa,
            'fecha_creacion' => $this->fechaCreacion
        ];
    }
    
    /**
     * Obtiene el nombre completo con ciudad
     * @return string
     */
    public function getNombreCompleto() {
        return $this->nombre . ' (' . $this->ciudad . ')';
    }
    
    /**
     * Verifica si la sede puede ser asignada a hallazgos
     * @return bool
     */
    public function puedeAsignarHallazgos() {
        return $this->activa && !empty($this->nombre);
    }
}
?>