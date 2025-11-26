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
define('REGISTROS_POR_PAGINA', 10);
$pagina_actual = (int)($_GET['pagina'] ?? 1);
$offset = ($pagina_actual - 1) * REGISTROS_POR_PAGINA;
$id_usuario = $_SESSION['id_usuario'];

try {
    // 1. Contar el total de gastos del usuario
    // CORRECCIÓN: Apuntar a la tabla 'Gastos'
    $stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM Gastos WHERE usuario_id = ?");
    $stmt_conteo->execute([$id_usuario]);
    $total_registros = $stmt_conteo->fetchColumn();
    $total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

    // 2. Obtener los gastos de la página actual (con JOIN para el nombre de la categoría)
    // CORRECCIÓN: Apuntar a 'Gastos' y usar el alias 'g'
    $stmt = $pdo->prepare(
        "SELECT g.id, g.monto, g.descripcion, g.fecha, c.id as categoria_id, c.nombre as categoria_nombre
         FROM Gastos g 
         JOIN Categorias c ON g.categoria_id = c.id
         WHERE g.usuario_id = :usuario_id
         ORDER BY g.fecha DESC, g.id DESC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $gastos = $stmt->fetchAll();

    // 3. Devolver la respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success'       => true,
        'gastos'        => $gastos, // La clave 'gastos' es la que espera el JS
        'pagina_actual' => $pagina_actual,
        'total_paginas' => $total_paginas
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>