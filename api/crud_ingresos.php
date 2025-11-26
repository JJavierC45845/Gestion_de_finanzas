<?php
// Iniciar sesión para obtener el ID del usuario logueado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../inc/conexion.php';

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php?error=no_sesion');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../ingresos.php');
    exit;
}

// Obtener el ID del usuario de la SESIÓN (¡MUY IMPORTANTE PARA LA SEGURIDAD!)
$id_usuario = $_SESSION['id_usuario'];

$accion = $_POST['accion'] ?? '';
$categoria_id = $_POST['categoria_id'] ?? '';
$monto = $_POST['monto'] ?? '';
$descripcion = $_POST['descripcion'] ?? ''; // Corregido el typo
$fecha = $_POST['fecha'] ?? '';
$ingreso_id = $_POST['ingresoId'] ?? '';

switch ($accion){
    case 'agregar':
        if (empty($categoria_id) || empty($monto) || empty($fecha)) {
            header('Location: ../ingresos.php?error=faltan_datos');
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO Ingresos (usuario_id, categoria_id, monto, descripcion, fecha) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $categoria_id, $monto, $descripcion, $fecha]);

        header('Location: ../ingresos.php?status=agregado');
        break;

    case 'modificar':
        if (empty($categoria_id) || empty($monto) || empty($fecha) || empty($ingreso_id)) {
            header('Location: ../ingresos.php?error=faltan_datos');
            exit;
        }

        // Se añade una validación extra para asegurar que el ingreso que se modifica pertenece al usuario logueado
        $stmt = $pdo->prepare("UPDATE Ingresos SET categoria_id = ?, monto = ?, descripcion = ?, fecha = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$categoria_id, $monto, $descripcion, $fecha, $ingreso_id, $id_usuario]);

        header('Location: ../ingresos.php?status=modificado');
        break;

    case 'eliminar':
        if (empty($ingreso_id)) {
            header('Location: ../ingresos.php?error=no_id');
            exit;
        }

        // Se añade una validación extra para asegurar que solo el dueño del ingreso puede borrarlo
        $stmt = $pdo->prepare("DELETE FROM Ingresos WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$ingreso_id, $id_usuario]);

        header('Location: ../ingresos.php?status=eliminado');
        break;
    
    default:
        header('Location: ../ingresos.php');
        break;
}
?>