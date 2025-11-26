<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../inc/conexion.php';

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// --- Lógica de Paginación ---
define('REGISTROS_POR_PAGINA', 10); // Puedes ajustar este número
$pagina_actual = (int)($_GET['pagina'] ?? 1);
$offset = ($pagina_actual - 1) * REGISTROS_POR_PAGINA;
$id_usuario = $_SESSION['id_usuario'];

try {
    // 1. Contar el total de movimientos del usuario
    $stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM MovimientosAhorros WHERE usuario_id = ?");
    $stmt_conteo->execute([$id_usuario]);
    $total_registros = $stmt_conteo->fetchColumn();
    $total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

    // 2. Obtener los movimientos de la página actual
    $stmt = $pdo->prepare(
        "SELECT tipo, descripcion, fecha, monto 
         FROM MovimientosAhorros 
         WHERE usuario_id = :usuario_id
         ORDER BY fecha DESC, id DESC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $movimientos = $stmt->fetchAll();

    // 3. Devolver la respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success'       => true,
        'movimientos'   => $movimientos, // Usamos la clave 'movimientos'
        'pagina_actual' => $pagina_actual,
        'total_paginas' => $total_paginas
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>