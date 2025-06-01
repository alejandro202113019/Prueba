<?php
// models/interfaces/SedeRepositoryInterface.php
interface SedeRepositoryInterface {
    /**
     * Obtiene todas las sedes
     * @param bool $activas Solo sedes activas
     * @return array Lista de sedes
     */
    public function findAll($activas = true);
    
    /**
     * Busca una sede por ID
     * @param int $id ID de la sede
     * @return array|null Sede encontrada o null
     */
    public function find($id);
    
    /**
     * Busca sedes por usuario (según permisos)
     * @param int $usuarioId ID del usuario
     * @return array Lista de sedes disponibles para el usuario
     */
    public function findByUsuario($usuarioId);
    
    /**
     * Obtiene todos los hallazgos de una sede específica
     * @param int $sedeId ID de la sede
     * @return array Lista de hallazgos de la sede
     */
    public function findHallazgosBySede($sedeId);
    
    /**
     * Guarda una sede (crear o actualizar)
     * @param array $sede Datos de la sede
     * @return bool|int ID de la sede guardada o false si falló
     */
    public function save($sede);
}
?>