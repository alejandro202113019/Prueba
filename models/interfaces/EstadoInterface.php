<?php
// models/interfaces/EstadoInterface.php
interface EstadoInterface {
    /**
     * Obtiene el nombre del estado
     * @return string
     */
    public function getNombre();
    
    /**
     * Verifica si puede transicionar a otro estado
     * @param string $nuevoEstado Nombre del nuevo estado
     * @return bool
     */
    public function puedeTransicionarA($nuevoEstado);
    
    /**
     * Obtiene los estados permitidos para transición
     * @return array Lista de estados permitidos
     */
    public function getEstadosPermitidos();
    
    /**
     * Verifica si es un estado crítico
     * @return bool
     */
    public function esCritico();
    
    /**
     * Obtiene el color asociado al estado (para UI)
     * @return string Clase CSS de Bootstrap
     */
    public function getColor();
    
    /**
     * Obtiene el icono asociado al estado
     * @return string Emoji o icono
     */
    public function getIcono();
    
    /**
     * Obtiene información adicional del estado
     * @return array
     */
    public function getMetadata();
}
?>