<?php
// Iniciar sesión y proteger la página
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once 'inc/conexion.php';
$id_usuario = $_SESSION['id_usuario'];

// 1. OBTENER SALDO TOTAL
$stmt_saldo = $pdo->prepare("SELECT SUM(CASE WHEN tipo = 'deposito' THEN monto ELSE -monto END) as saldo FROM MovimientosAhorros WHERE usuario_id = ?");
$stmt_saldo->execute([$id_usuario]);
$saldo_total = $stmt_saldo->fetchColumn() ?? 0; // Usar 0 si no hay movimientos

// 2. OBTENER HISTORIAL DE MOVIMIENTOS (PÁGINA 1)
define('REGISTROS_POR_PAGINA', 10); // Define cuántos registros mostrar por página
$pagina_actual_ahorros = 1; // Cargar siempre la página 1 inicialmente
$offset = 0; // Offset para la página 1

// Contar el total de movimientos para saber cuántas páginas hay
$stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM MovimientosAhorros WHERE usuario_id = ?");
$stmt_conteo->execute([$id_usuario]);
$total_registros_ahorros = $stmt_conteo->fetchColumn();
$total_paginas_ahorros = ceil($total_registros_ahorros / REGISTROS_POR_PAGINA);

// Obtener solo los movimientos de la primera página
$stmt_mov = $pdo->prepare("SELECT id, tipo, descripcion, fecha, monto FROM MovimientosAhorros WHERE usuario_id = :usuario_id ORDER BY fecha DESC, id DESC LIMIT :limit OFFSET :offset");
$stmt_mov->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt_mov->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_mov->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
$stmt_mov->execute();
$movimientos = $stmt_mov->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas - Ahorros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_ahorros.css">
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
            <li class="nav-item"><a href="ahorros.php" class="nav-link active"><i class="bi bi-piggy-bank-fill me-2"></i> Ahorros</a></li>
            <li class="nav-item"><a href="reportes.php" class="nav-link"><i class="bi bi-bar-chart-line-fill me-2"></i> Reportes</a></li>
        </ul>
        <hr class="my-4">
        <div class="text-center">
            <button class="btn btn-principal" id="cerrar_sesion" onclick="window.location.href='cerrar_sesion.php'">Cerrar Sesión <i class="bi bi-box-arrow-right"></i></button>
        </div>
    </nav>
    
    <main class="main-content">
        <h1 class="mb-4">Gestión de Ahorros</h1>

        <div class="card text-center mb-4">
            <div class="card-body">
                <h5 class="card-title text-muted">Saldo Actual en Ahorros</h5>
                <p class="display-4 fw-bold" style="color: var(--color-texto-principal);">$<?= number_format($saldo_total, 2) ?></p>
            </div>
        </div>

        <div class="d-flex gap-3 mb-4">
            <button class="btn btn-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#modalDepositar">
                <i class="bi bi-plus-circle-fill me-2"></i>Depositar
            </button>
            <button class="btn btn-warning w-100 py-3" data-bs-toggle="modal" data-bs-target="#modalRetirar">
                <i class="bi bi-dash-circle-fill me-2"></i>Retirar
            </button>
        </div>

        <div class="card">
            <h2 class="h4 mb-3">Historial de Movimientos</h2>
            <table class="tabla-finanzas">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody id="tabla-ahorros-body">
                    <?php if (empty($movimientos)): ?>
                        <tr><td colspan="4" class="text-center">No hay movimientos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($movimientos as $mov): ?>
                            <tr>
                                <?php if ($mov['tipo'] == 'deposito'): ?>
                                    <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Depósito</span></td>
                                    <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($mov['fecha']))) ?></td>
                                    <td class="fw-bold text-success">+ $<?= number_format($mov['monto'], 2) ?></td>
                                <?php else: ?>
                                    <td><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Retiro</span></td>
                                    <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                    <td><?= htmlspecialchars(date("d/m/Y", strtotime($mov['fecha']))) ?></td>
                                    <td class="fw-bold text-danger">- $<?= number_format($mov['monto'], 2) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <nav class="mt-4" 
                         id="paginador-ahorros" 
                         data-pagina-actual="<?= $pagina_actual_ahorros ?>" 
                         data-total-paginas="<?= $total_paginas_ahorros ?>">
                        <ul class="pagination justify-content-end">
                            <li class="page-item <?= ($pagina_actual_ahorros <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-anterior-ahorros">Antes</a>
                            </li>
                            <li class="page-item <?= ($total_paginas_ahorros <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-siguiente-ahorros">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
        </div>
    </main>
</div>

<div class="modal fade" id="modalDepositar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Depositar a Ahorros</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="api/crud_ahorros.php" method="POST">
          <div class="modal-body">
                <input type="hidden" name="accion" value="depositar">
                <div class="mb-3"><label for="montoDeposito" class="form-label">Monto a Depositar</label><input type="number" step="0.01" class="form-control" id="montoDeposito" name="monto" placeholder="$0.00" required></div>
                <div class="mb-3"><label for="descDeposito" class="form-label">Descripción (opcional)</label><input type="text" class="form-control" id="descDeposito" name="descripcion" placeholder="Ej: Ahorro quincenal"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Confirmar Depósito</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalRetirar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Retirar de Ahorros</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="api/crud_ahorros.php" method="POST">
          <div class="modal-body">
                <input type="hidden" name="accion" value="retirar">
                <div class="mb-3"><label for="montoRetiro" class="form-label">Monto a Retirar</label><input type="number" step="0.01" class="form-control" id="montoRetiro" name="monto" placeholder="$0.00" required></div>
                <div class="mb-3"><label for="descRetiro" class="form-label">Descripción (opcional)</label><input type="text" class="form-control" id="descRetiro" name="descripcion" placeholder="Ej: Pago de servicios"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Confirmar Retiro</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/paginador_ahorros.js"></script>
</body>
</html>