<?php
// config.php - Configuración para XAMPP Local
$host = 'localhost';
$dbname = 'GestionHallazgos';
$user = 'root';
$pass = '';  // Vacío para XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "¡Conexión exitosa a la base de datos local!"; // Para verificar
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
    exit;
}
?>