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
    header('Location: ../gastos.php');
    exit;
}

// Obtener el ID del usuario de la SESIÓN
$id_usuario = $_SESSION['id_usuario'];

$accion = $_POST['accion'] ?? '';
$categoria_id = $_POST['categoria_id'] ?? '';
$monto = $_POST['monto'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fecha = $_POST['fecha'] ?? '';
// CORRECCIÓN: Se cambió 'ingresoId' por 'gastoId' para que coincida con el formulario
$gasto_id = $_POST['gastoId'] ?? '';

switch ($accion){
    case 'agregar':
        if (empty($categoria_id) || empty($monto) || empty($fecha)) {
            header('Location: ../gastos.php?error=faltan_datos');
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO Gastos (usuario_id, categoria_id, monto, descripcion, fecha) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $categoria_id, $monto, $descripcion, $fecha]);

        header('Location: ../gastos.php?status=agregado');
        break;

    case 'modificar':
        if (empty($categoria_id) || empty($monto) || empty($fecha) || empty($gasto_id)) {
            header('Location: ../gastos.php?error=faltan_datos');
            exit;
        }

        $stmt = $pdo->prepare("UPDATE Gastos SET categoria_id = ?, monto = ?, descripcion = ?, fecha = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$categoria_id, $monto, $descripcion, $fecha, $gasto_id, $id_usuario]);

        header('Location: ../gastos.php?status=modificado');
        break;

    case 'eliminar':
        if (empty($gasto_id)) {
            header('Location: ../gastos.php?error=no_id');
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM Gastos WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$gasto_id, $id_usuario]);

        header('Location: ../gastos.php?status=eliminado');
        break;
    
    default:
        header('Location: ../gastos.php');
        break;
}
?>