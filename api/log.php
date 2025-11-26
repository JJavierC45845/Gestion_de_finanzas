<?php
// Siempre inicia la sesión al principio de los scripts que la usan.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// La ruta correcta para incluir la conexión
require_once '../inc/conexion.php';

// Preparamos una respuesta por defecto
$response = ['success' => false, 'message' => 'Datos de POST no recibidos.'];

// Verificamos que se envíen 'usuario' y 'contrasena' desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['contrasena'])) {
    $nombre_usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    if (!$pdo) {
        $response['message'] = 'Error de conexión a la base de datos.';
    } else {
        try {
            // Buscamos al usuario por su nombre de usuario
            $stmt = $pdo->prepare("SELECT id, nombre_usuario, password_hash FROM Usuarios WHERE nombre_usuario = ?");
            $stmt->execute([$nombre_usuario]);
            $usuario = $stmt->fetch();

            // Si el usuario existe y la contraseña es correcta
            if ($usuario && password_verify($contrasena, $usuario['password_hash'])) {
                // Guardamos los datos del usuario en la sesión
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario']; // Corregido el typo
                
                $response['success'] = true;
                $response['message'] = 'Inicio de sesión exitoso.';
            } else {
                // Si no, enviamos un mensaje de error
                $response['message'] = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            error_log('Error en login: ' . $e->getMessage());
            $response['message'] = 'Error en el sistema. Intente más tarde.';
        }
    }
}

// Devolvemos la respuesta en formato JSON para que JavaScript la pueda leer
header('Content-Type: application/json');
echo json_encode($response);
?>