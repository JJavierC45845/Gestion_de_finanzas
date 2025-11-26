<?php
// Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. SECUENCIA DE PROTECCIÓN
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once 'inc/conexion.php';
$id_usuario = $_SESSION['id_usuario'];

// --- 2. OBTENER DATOS PARA LAS TARJETAS ---

// A. Saldo Actual (Total Ingresos - Total Gastos)
$stmt_total_ing = $pdo->prepare("SELECT SUM(monto) FROM Ingresos WHERE usuario_id = ? AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$stmt_total_ing->execute([$id_usuario]);
$totalIngresos = $stmt_total_ing->fetchColumn() ?? 0;

$stmt_total_gas = $pdo->prepare("SELECT SUM(monto) FROM Gastos WHERE usuario_id = ? AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$stmt_total_gas->execute([$id_usuario]);
$totalGastos = $stmt_total_gas->fetchColumn() ?? 0;

$saldoActual = $totalIngresos - $totalGastos;

// B. Ingresos del Mes (Filtra por el mes y año actual)
$stmt_mes_ing = $pdo->prepare("SELECT SUM(monto) FROM Ingresos WHERE usuario_id = ? AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$stmt_mes_ing->execute([$id_usuario]);
$ingresosMes = $stmt_mes_ing->fetchColumn() ?? 0;

// C. Gastos del Mes (Filtra por el mes y año actual)
$stmt_mes_gas = $pdo->prepare("SELECT SUM(monto) FROM Gastos WHERE usuario_id = ? AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$stmt_mes_gas->execute([$id_usuario]);
$gastosMes = $stmt_mes_gas->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_panel.css"> 
</head>
<body>

<div class="wrapper">
    <nav class="sidebar p-3">
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-wallet2 fs-4 me-3"></i>
            <span class="fs-5 fw-bold">Mis Finanzas</span>
        </div>
        <ul class="nav nav-pills flex-column gap-2">
            <li class="nav-item">
                <a href="acceso_permitido.php" class="nav-link active" aria-current="page">
                    <i class="bi bi-pie-chart-fill me-2"></i> Resumen
                </a>
            </li>
            <li class="nav-item">
                <a href="usuarios.php" class="nav-link">
                    <i class="bi bi-people-fill me-2"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a href="ingresos.php" class="nav-link">
                    <i class="bi bi-arrow-down-circle-fill me-2"></i> Ingresos
                </a>
            </li>
            <li class="nav-item">
                <a href="gastos.php" class="nav-link">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i> Gastos
                </a>
            </li>
            <li class="nav-item">
                <a href="categorias.php" class="nav-link">
                    <i class="bi bi-tag-fill me-2"></i> Categorías
                </a>
            </li>
            <li class="nav-item">
                <a href="ahorros.php" class="nav-link">
                    <i class="bi bi-piggy-bank-fill me-2"></i> Ahorros
                </a>
            </li>
            <li class="nav-item">
                <a href="reportes.php" class="nav-link">
                    <i class="bi bi-bar-chart-line-fill me-2"></i> Reportes
                </a>
            </li>
        </ul>
        <hr class="my-4">
        <div class="text-center">
            <button class="btn" style="background-color: var(--color-acento); color: var(--color-fondo-principal);" id="cerrar_sesion" onclick="window.location.href='cerrar_sesion.php'">
                Cerrar Sesión <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>
    </nav>

    <main class="main-content">
        <div class="mb-4">
            <h1>¡Hola, <?= htmlspecialchars($_SESSION['nombre_usuario']) ?>!</h1>
            <p class="lead">Este es tu resumen financiero del mes.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-finance p-3">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Saldo Total Acumulado</h5>
                        <p class="card-text display-6 fw-bold <?= $saldoActual >= 0 ? 'text-success' : 'text-danger' ?>">
                            $<?= number_format($saldoActual, 2) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-finance p-3">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Ingresos (Mes Actual)</h5>
                        <p class="card-text display-6 fw-bold">
                            $<?= number_format($ingresosMes, 2) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-finance p-3">
                    <div class="card-body">
                        <h5 class="card-title text-danger">Gastos (Mes Actual)</h5>
                        <p class="card-text display-6 fw-bold">
                            $<?= number_format($gastosMes, 2) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>