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
    header('Location: ../categorias.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$accion = $_POST['accion'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$tipo = $_POST['tipo'] ?? ''; // 'ingreso' o 'gasto'
$categoria_id = $_POST['categoria_id'] ?? '';

switch ($accion) {
    case 'agregar':
        if (empty($nombre) || empty($tipo)) {
            header('Location: ../categorias.php?error=faltan_datos');
            exit;
        }

        // --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
        try {
            $stmt = $pdo->prepare("INSERT INTO Categorias (usuario_id, nombre, tipo) VALUES (?, ?, ?)");
            // Esta es la línea 35 que fallaba
            $stmt->execute([$id_usuario, $nombre, $tipo]);
            
            // Si todo sale bien, redirige con éxito
            header('Location: ../categorias.php?status=agregado');

        } catch (PDOException $e) {
            // Verificamos si el error es el de "llave duplicada"
            if ($e->getCode() == '23000') {
                // Si es duplicado, redirigimos con un error amigable
                header('Location: ../categorias.php?error=categoria_duplicada');
            } else {
                // Si es cualquier otro error de BD, redirigimos con un error genérico
                error_log($e->getMessage()); // Opcional: guardar el error real para ti
                header('Location: ../categorias.php?error=db_error');
            }
        }
        // --- FIN DE LA CORRECCIÓN ---
        break;

    case 'modificar':
        if (empty($nombre) || empty($categoria_id) || empty($tipo)) {
            header('Location: ../categorias.php?error=faltan_datos');
            exit;
        }

        // También se puede agregar un try...catch aquí por si renombran
        // a un nombre que ya existe.
        try {
            $stmt = $pdo->prepare("UPDATE Categorias SET nombre = ? WHERE id = ? AND usuario_id = ? AND tipo = ?");
            $stmt->execute([$nombre, $categoria_id, $id_usuario, $tipo]);
            header('Location: ../categorias.php?status=modificado');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                header('Location: ../categorias.php?error=categoria_duplicada');
            } else {
                header('Location: ../categorias.php?error=db_error');
            }
        }
        break;

    case 'eliminar':
        // (Tu código de eliminar es correcto)
        if (empty($categoria_id) || empty($tipo)) {
            header('Location: ../categorias.php?error=no_id');
            exit;
        }
        
        $tabla_a_checar = ($tipo == 'ingreso') ? 'Ingresos' : 'Gastos';
        
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM $tabla_a_checar WHERE categoria_id = ? AND usuario_id = ?");
        $stmt_check->execute([$categoria_id, $id_usuario]);
        $conteo = $stmt_check->fetchColumn();

        if ($conteo > 0) {
            header('Location: ../categorias.php?error=categoria_en_uso');
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM Categorias WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$categoria_id, $id_usuario]);

        header('Location: ../categorias.php?status=eliminado');
        break;
    
    default:
        header('Location: ../categorias.php');
        break;
}
?>