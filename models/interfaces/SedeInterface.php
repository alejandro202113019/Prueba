<?php
// models/interfaces/SedeInterface.php
interface SedeInterface {
    /**
     * Obtiene el ID de la sede
     * @return int
     */
    public function getId();
    
    /**
     * Obtiene el nombre de la sede
     * @return string
     */
    public function getNombre();
    
    /**
     * Obtiene la dirección de la sede
     * @return string
     */
    public function getDireccion();
    
    /**
     * Obtiene la ciudad de la sede
     * @return string
     */
    public function getCiudad();
    
    /**
     * Verifica si la sede está activa
     * @return bool
     */
    public function isActiva();
    
    /**
     * Obtiene información completa de la sede
     * @return array
     */
    public function toArray();
}
?>