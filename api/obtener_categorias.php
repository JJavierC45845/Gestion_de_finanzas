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
define('REGISTROS_POR_PAGINA', 5); // Puedes ajustar este número
$pagina_actual = (int)($_GET['pagina'] ?? 1);
$offset = ($pagina_actual - 1) * REGISTROS_POR_PAGINA;
$id_usuario = $_SESSION['id_usuario'];

// --- Obtener el TIPO de categoría a buscar ---
$tipo = $_GET['tipo'] ?? '';
if ($tipo !== 'ingreso' && $tipo !== 'gasto') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Tipo de categoría no válido.']);
    exit;
}

try {
    // 1. Contar el total de categorías para ESE TIPO
    $stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM Categorias WHERE usuario_id = ? AND tipo = ?");
    $stmt_conteo->execute([$id_usuario, $tipo]);
    $total_registros = $stmt_conteo->fetchColumn();
    $total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

    // 2. Obtener las categorías de la página actual para ESE TIPO
    $stmt = $pdo->prepare(
        "SELECT id, nombre
         FROM Categorias
         WHERE usuario_id = :usuario_id AND tipo = :tipo
         ORDER BY nombre
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->execute();
    $categorias = $stmt->fetchAll();

    // 3. Devolver la respuesta en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success'       => true,
        'categorias'    => $categorias,
        'pagina_actual' => $pagina_actual,
        'total_paginas' => $total_paginas
    ]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>