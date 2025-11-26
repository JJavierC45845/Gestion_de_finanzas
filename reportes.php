<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once 'inc/conexion.php';
$id_usuario = $_SESSION['id_usuario'];

// --- 1. VALIDACIÓN Y OBTENCIÓN DE FECHAS ---
$fechaInicio = $_GET['fechaInicio'] ?? date('Y-m-01');
$fechaFin = $_GET['fechaFin'] ?? date('Y-m-t');
$today = date('Y-m-d'); // Fecha actual
$error_msg = ''; // Variable para guardar el mensaje de error

// Validar que la fecha de inicio no sea mayor a la final
if ($fechaInicio > $fechaFin) {
    $error_msg = 'La fecha de inicio no puede ser mayor que la fecha final.';
}
// Validar que la fecha de inicio no sea futura
elseif ($fechaInicio > $today) {
    $error_msg = 'La fecha de inicio no puede ser una fecha futura.';
}
// Validar que la fecha final no sea futura
elseif ($fechaFin > $today) {
    $error_msg = 'La fecha final no puede ser una fecha futura.';
}

// --- 2. INICIALIZAR VARIABLES ---
$totalIngresos = 0;
$totalGastos = 0;
$balanceNeto = 0;
$gastos_por_categoria = [];

// --- 3. CONSULTAS (SOLO SI NO HAY ERROR) ---
if (empty($error_msg)) {
    // Total de Ingresos en el rango
    $stmt_ing = $pdo->prepare("SELECT SUM(monto) FROM Ingresos WHERE usuario_id = ? AND fecha BETWEEN ? AND ?");
    $stmt_ing->execute([$id_usuario, $fechaInicio, $fechaFin]);
    $totalIngresos = $stmt_ing->fetchColumn() ?? 0;

    // Total de Gastos en el rango
    $stmt_gas = $pdo->prepare("SELECT SUM(monto) FROM Gastos WHERE usuario_id = ? AND fecha BETWEEN ? AND ?");
    $stmt_gas->execute([$id_usuario, $fechaInicio, $fechaFin]);
    $totalGastos = $stmt_gas->fetchColumn() ?? 0;

    // Balance Neto
    $balanceNeto = $totalIngresos - $totalGastos;

    // Consulta para el gráfico (Gastos por Categoría)
    $stmt_cat = $pdo->prepare(
        "SELECT c.nombre, SUM(g.monto) as total
         FROM Gastos g
         JOIN Categorias c ON g.categoria_id = c.id
         WHERE g.usuario_id = ? AND g.fecha BETWEEN ? AND ?
         GROUP BY c.nombre
         ORDER BY total DESC"
    );
    $stmt_cat->execute([$id_usuario, $fechaInicio, $fechaFin]);
    $gastos_por_categoria = $stmt_cat->fetchAll();
}

// --- 4. PREPARAR DATOS PARA JAVASCRIPT (CHART.JS) ---
// (Esto se ejecuta siempre, si hay error, simplemente pasará arrays vacíos al JS)
$chartLabels = [];
$chartData = [];
foreach ($gastos_por_categoria as $gasto) {
    $chartLabels[] = $gasto['nombre'];
    $chartData[] = $gasto['total'];
}
$chartLabelsJSON = json_encode($chartLabels);
$chartDataJSON = json_encode($chartData);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas - Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_reportes.css">
</head>
<body>

<div class="wrapper">
     <nav class="sidebar p-3">
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-wallet2 fs-4 me-3"></i>
            <span class="fs-5 fw-bold">Mis Finanzas</span>
        </div>
        <ul class="nav nav-pills flex-column gap-2">
            <li class="nav-item"><a href="acceso_permitido.php" class="nav-link"><i class="bi bi-pie-chart-fill me-2"></i> Resumen</a></li>
            <li class="nav-item"><a href="usuarios.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Usuarios</a></li>
            <li class="nav-item"><a href="ingresos.php" class="nav-link"><i class="bi bi-arrow-down-circle-fill me-2"></i> Ingresos</a></li>
            <li class="nav-item"><a href="gastos.php" class="nav-link"><i class="bi bi-arrow-up-circle-fill me-2"></i> Gastos</a></li>
            <li class="nav-item"><a href="categorias.php" class="nav-link"><i class="bi bi-tag-fill me-2"></i> Categorías</a></li>
            <li class="nav-item"><a href="ahorros.php" class="nav-link"><i class="bi bi-piggy-bank-fill me-2"></i> Ahorros</a></li>
            <li class="nav-item"><a href="reportes.php" class="nav-link active"><i class="bi bi-bar-chart-line-fill me-2"></i> Reportes</a></li>
        </ul>
        <hr class="my-4">
        <div class="text-center">
            <button class="btn btn-principal" id="cerrar_sesion" onclick="window.location.href='cerrar_sesion.php'">Cerrar Sesión <i class="bi bi-box-arrow-right"></i></button>
        </div>
    </nav>

    <main class="main-content">
        <h1 class="mb-4">Generador de Reportes</h1>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <form id="formReporte" method="GET" action="reportes.php">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="fechaFin" class="form-label">Fecha Final</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= htmlspecialchars($fechaFin) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-principal w-100">Generar Reporte</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($error_msg)): ?>
        <div id="areaResultados">
            <h2 class="h4 mb-3">Resultados para: <?= htmlspecialchars(date("d/m/Y", strtotime($fechaInicio))) ?> - <?= htmlspecialchars(date("d/m/Y", strtotime($fechaFin))) ?></h2>
            
            <div class="row g-4">
                <div class="col-md-4"><div class="card text-center p-3"><h5 class="text-muted">Total Ingresos</h5><p class="h2 fw-bold text-success">$<?= number_format($totalIngresos, 2) ?></p></div></div>
                <div class="col-md-4"><div class="card text-center p-3"><h5 class="text-muted">Total Gastos</h5><p class="h2 fw-bold text-danger">$<?= number_format($totalGastos, 2) ?></p></div></div>
                <div class="col-md-4"><div class="card text-center p-3"><h5 class="text-muted">Balance Neto</h5><p class="h2 fw-bold <?= $balanceNeto >= 0 ? 'text-primary' : 'text-danger' ?>">$<?= number_format($balanceNeto, 2) ?></p></div></div>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-lg-5">
                    <div class="card">
                        <h5 class="mb-3">Gastos por Categoría</h5>
                        <canvas id="graficoGastos"></canvas>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card">
                         <h5 class="mb-3">Detalle de Gastos</h5>
                        <table class="tabla-finanzas">
                            <thead><tr><th>Categoría</th><th>Monto Total</th><th>% del Total</th></tr></thead>
                            <tbody>
                                <?php if (empty($gastos_por_categoria)): ?>
                                    <tr><td colspan="3" class="text-center">No hay gastos en este período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($gastos_por_categoria as $gasto): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($gasto['nombre']) ?></td>
                                            <td>$<?= number_format($gasto['total'], 2) ?></td>
                                            <td><?= $totalGastos > 0 ? number_format(($gasto['total'] / $totalGastos) * 100, 1) : 0 ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; // Fin del if (empty($error_msg)) ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // --- Lógica para el gráfico DINÁMICO ---
    
    // 1. Obtenemos los datos que PHP preparó
    const labels = <?= $chartLabelsJSON ?>;
    const data = <?= $chartDataJSON ?>;

    const ctx = document.getElementById('graficoGastos');

    // Solo intentar crear el gráfico si el canvas existe y hay datos
    if (ctx && data.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels, // Usamos los labels de PHP
                datasets: [{
                    label: 'Gastos por Categoría',
                    data: data, // Usamos los datos de PHP
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
</script>
</body>
</html>