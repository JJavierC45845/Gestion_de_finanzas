<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../inc/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php?error=no_sesion');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../ahorros.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$accion = $_POST['accion'] ?? '';
$monto = $_POST['monto'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

// Validación básica
if (empty($accion) || empty($monto) || !is_numeric($monto) || $monto <= 0) {
    header('Location: ../ahorros.php?error=datos_invalidos');
    exit;
}

try {
    switch ($accion) {
        case 'depositar':
            $stmt = $pdo->prepare("INSERT INTO MovimientosAhorros (usuario_id, tipo, monto, descripcion, fecha) VALUES (?, 'deposito', ?, ?, CURDATE())");
            $stmt->execute([$id_usuario, $monto, $descripcion]);
            
            header('Location: ../ahorros.php?status=deposito_ok');
            break;

        case 'retirar':
            // --- Verificación de Fondos ---
            $stmt_saldo = $pdo->prepare("SELECT SUM(CASE WHEN tipo = 'deposito' THEN monto ELSE -monto END) as saldo FROM MovimientosAhorros WHERE usuario_id = ?");
            $stmt_saldo->execute([$id_usuario]);
            $saldo_actual = $stmt_saldo->fetchColumn();

            if ($monto > $saldo_actual) {
                // Si intentas retirar más de lo que tienes
                header('Location: ../ahorros.php?error=fondos_insuficientes');
                exit;
            }
            // --- Fin de la Verificación ---

            $stmt = $pdo->prepare("INSERT INTO MovimientosAhorros (usuario_id, tipo, monto, descripcion, fecha) VALUES (?, 'retiro', ?, ?, CURDATE())");
            $stmt->execute([$id_usuario, $monto, $descripcion]);

            header('Location: ../ahorros.php?status=retiro_ok');
            break;
        
        default:
            header('Location: ../ahorros.php');
            break;
    }
} catch (PDOException $e) {
    error_log("Error en CRUD Ahorros: " . $e->getMessage());
    header('Location: ../ahorros.php?error=db_error');
}
?>