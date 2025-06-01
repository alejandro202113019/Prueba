<?php
// models/interfaces/CommandInterface.php
interface CommandInterface {
    /**
     * Ejecuta el comando
     * @return bool Éxito de la ejecución
     */
    public function execute();
    
    /**
     * Deshace el comando (si es posible)
     * @return bool Éxito del rollback
     */
    public function undo();
    
    /**
     * Obtiene información del comando para logging
     * @return array Información del comando
     */
    public function getLogInfo();
}
?>