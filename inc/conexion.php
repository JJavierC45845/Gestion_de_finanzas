<?php



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/Mexico_City');

// Configuración para MySQL
$host = "localhost";
$port = "3307";
$dbname = "gestor";
$username = "root";
$password = "123456789";
$charset = "utf8mb4"; // Usar utf8mb4 para compatibilidad completa de caracteres

// DSN (Data Source Name) para MySQL
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // En un entorno de producción, no muestres el error detallado al usuario.
    error_log("Error de conexión: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor, intente más tarde.");
}

// Función para hashear contraseñas (está perfecta)
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}
?>