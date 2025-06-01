<?php
// models/factories/SedeFactory.php
require_once 'models/interfaces/SedeInterface.php';
require_once 'models/entities/Sede.php';

class SedeFactory {
    /**
     * Crea una instancia de Sede desde un array de datos
     * @param array $data Datos de la sede
     * @return SedeInterface
     */
    public static function create($data) {
        return new Sede($data);
    }
    
    /**
     * Crea una instancia de Sede desde la base de datos por ID
     * @param int $id ID de la sede
     * @param PDO $pdo Conexión a la base de datos
     * @return SedeInterface|null
     */
    public static function createFromId($id, $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM Sedes WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return self::create($data);
        }
        
        return null;
    }
    
    /**
     * Crea múltiples instancias de Sede desde un array de datos
     * @param array $dataArray Array de arrays con datos de sedes
     * @return array Array de SedeInterface
     */
    public static function createMultiple($dataArray) {
        $sedes = [];
        foreach ($dataArray as $data) {
            $sedes[] = self::create($data);
        }
        return $sedes;
    }
    
    /**
     * Crea una sede vacía para formularios
     * @return SedeInterface
     */
    public static function createEmpty() {
        return self::create([
            'id' => null,
            'nombre' => '',
            'direccion' => '',
            'ciudad' => '',
            'telefono' => '',
            'activa' => true
        ]);
    }
    
    /**
     * Valida los datos antes de crear una sede
     * @param array $data Datos a validar
     * @return array Array con 'valid' => bool y 'errors' => array
     */
    public static function validate($data) {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre de la sede es requerido';
        }
        
        if (empty($data['ciudad'])) {
            $errors[] = 'La ciudad es requerida';
        }
        
        if (!empty($data['telefono']) && !preg_match('/^[\d\-\+\(\)\s]+$/', $data['telefono'])) {
            $errors[] = 'El formato del teléfono no es válido';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
?>