<?php
// models/interfaces/SearchStrategyInterface.php
interface SearchStrategyInterface {
    /**
     * Realiza la búsqueda según los criterios proporcionados
     * @param mixed $criteria Criterios de búsqueda
     * @return array|null Resultado de la búsqueda
     */
    public function search($criteria);
    
    /**
     * Valida que los criterios de búsqueda sean correctos
     * @param mixed $criteria Criterios a validar
     * @return bool True si son válidos
     */
    public function validate($criteria);
}
?>