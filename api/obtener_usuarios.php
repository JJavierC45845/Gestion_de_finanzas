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

// ID del usuario que está viendo la página
$id_usuario_logueado = $_SESSION['id_usuario'];

// --- Lógica de Paginación ---
define('REGISTROS_POR_PAGINA', 10);
$pagina_actual = (int)($_GET['pagina'] ?? 1);
$offset = ($pagina_actual - 1) * REGISTROS_POR_PAGINA;

// 1. Contar el total de usuarios EXCEPTO el logueado
$stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE id != ?");
$stmt_conteo->execute([$id_usuario_logueado]);
$total_registros = $stmt_conteo->fetchColumn();
$total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

// 2. Obtener los usuarios de la página actual EXCEPTO el logueado
$stmt = $pdo->prepare("SELECT id, nombre_usuario, fecha_registro FROM Usuarios WHERE id != :id_usuario_logueado ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':id_usuario_logueado', $id_usuario_logueado, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

// 3. Devolver la respuesta en JSON
header('Content-Type: application/json');
echo json_encode([
    'success'       => true,
    'usuarios'      => $usuarios,
    'pagina_actual' => $pagina_actual,
    'total_paginas' => $total_paginas
]);
?>