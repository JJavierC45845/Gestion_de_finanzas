<?php
// Se debe iniciar sesión para acceder a la variable $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../inc/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php?error=no_sesion');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../usuarios.php');
    exit;
}

// ID del usuario que está realizando la acción
$id_usuario_logueado = $_SESSION['id_usuario'];

// Datos del formulario
$accion = $_POST['accion'] ?? '';
$nombre_usuario = $_POST['nombreUsuario'] ?? '';
$password = $_POST['password'] ?? '';
$usuario_id = $_POST['usuarioId'] ?? ''; // ID del usuario a modificar/eliminar

switch ($accion) {
    case 'agregar':
        // (Tu código de 'agregar' es correcto, no necesita cambios)
        if (empty($nombre_usuario) || empty($password)) {
            header('Location: ../usuarios.php?error=faltan_datos');
            exit;
        }
        $password_hash = hashPassword($password);
        $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre_usuario, password_hash) VALUES (?, ?)");
        $stmt->execute([$nombre_usuario, $password_hash]);
        header('Location: ../usuarios.php?status=agregado');
        break;

    case 'modificar':
        // --- NUEVA VALIDACIÓN DE SEGURIDAD ---
        if ($usuario_id == $id_usuario_logueado) {
            header('Location: ../usuarios.php?error=no_self_edit');
            exit;
        }
        // --- FIN DE LA VALIDACIÓN ---

        if (empty($nombre_usuario) || empty($usuario_id)) {
            header('Location: ../usuarios.php?error=faltan_datos');
            exit;
        }

        if (!empty($password)) {
            $password_hash = hashPassword($password);
            $stmt = $pdo->prepare("UPDATE Usuarios SET nombre_usuario = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$nombre_usuario, $password_hash, $usuario_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE Usuarios SET nombre_usuario = ? WHERE id = ?");
            $stmt->execute([$nombre_usuario, $usuario_id]);
        }

        header('Location: ../usuarios.php?status=modificado');
        break;

    case 'eliminar':
        // --- NUEVA VALIDACIÓN DE SEGURIDAD ---
        if ($usuario_id == $id_usuario_logueado) {
            header('Location: ../usuarios.php?error=no_self_delete');
            exit;
        }
        // --- FIN DE LA VALIDACIÓN ---

        if (empty($usuario_id)) {
            header('Location: ../usuarios.php?error=no_id');
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM Usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);

        header('Location: ../usuarios.php?status=eliminado');
        break;
    
    default:
        header('Location: ../usuarios.php');
        break;
}
?>