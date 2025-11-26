<?php
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

// --- 2. OBTENER CATEGORÍAS (Para el Modal de Agregar/Editar) ---
$stmt_cat = $pdo->prepare("SELECT id, nombre FROM Categorias WHERE usuario_id = ? AND tipo = 'ingreso'");
$stmt_cat->execute([$id_usuario]);
$categorias = $stmt_cat->fetchAll();

// --- 3. LÓGICA DE PAGINACIÓN (SOLO PÁGINA 1) ---
define('REGISTROS_POR_PAGINA', 10);
$pagina_actual = 1;
$offset = 0;

// Contar el total de registros para el paginador
$stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM Ingresos WHERE usuario_id = ?");
$stmt_conteo->execute([$id_usuario]);
$total_registros = $stmt_conteo->fetchColumn();
$total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

// Obtener solo los registros de la primera página
$stmt_ingresos = $pdo->prepare(
    "SELECT i.id, i.monto, i.descripcion, i.fecha, c.id as categoria_id, c.nombre as categoria_nombre
     FROM Ingresos i
     JOIN Categorias c ON i.categoria_id = c.id
     WHERE i.usuario_id = :usuario_id
     ORDER BY i.fecha DESC, i.id DESC
     LIMIT :limit OFFSET :offset"
);
$stmt_ingresos->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt_ingresos->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_ingresos->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
$stmt_ingresos->execute();
$ingresos = $stmt_ingresos->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas - Ingresos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_ingresos.css">
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
            <li class="nav-item"><a href="ingresos.php" class="nav-link active"><i class="bi bi-arrow-down-circle-fill me-2"></i> Ingresos</a></li>
            <li class="nav-item"><a href="gastos.php" class="nav-link"><i class="bi bi-arrow-up-circle-fill me-2"></i> Gastos</a></li>
            <li class="nav-item"><a href="categorias.php" class="nav-link"><i class="bi bi-tag-fill me-2"></i> Categorías</a></li>
            <li class="nav-item"><a href="ahorros.php" class="nav-link"><i class="bi bi-piggy-bank-fill me-2"></i> Ahorros</a></li>
            <li class="nav-item"><a href="reportes.php" class="nav-link"><i class="bi bi-bar-chart-line-fill me-2"></i> Reportes</a></li>
        </ul>
        <hr class="my-4">
        <div class="text-center">
            <button class="btn btn-principal" id="cerrar_sesion" onclick="window.location.href='cerrar_sesion.php'">
                Cerrar Sesión <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>
    </nav>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Ingresos</h1>
            <button class="btn btn-principal" data-bs-toggle="modal" data-bs-target="#modalIngreso" data-accion="agregar">
                <i class="bi bi-plus-circle-fill me-2"></i>Agregar Ingreso
            </button>
        </div>
        
        <div class="card">
            <table class="tabla-finanzas">
                <thead>
                    <tr>
                        <th>Monto</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-ingresos-body">
                    <?php if (empty($ingresos)): ?>
                        <tr><td colspan="5" class="text-center">No hay ingresos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($ingresos as $ingreso): ?>
                        <tr>
                            <td class="fw-bold text-success">$<?= htmlspecialchars(number_format($ingreso['monto'], 2)) ?></td>
                            <td><?= htmlspecialchars($ingreso['categoria_nombre']) ?></td>
                            <td><?= htmlspecialchars($ingreso['descripcion']) ?></td>
                            <td><?= htmlspecialchars(date("d/m/Y", strtotime($ingreso['fecha']))) ?></td>
                            <td>
                                <button class="btn btn-modificar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalIngreso"
                                        data-id="<?= $ingreso['id'] ?>"
                                        data-monto="<?= $ingreso['monto'] ?>"
                                        data-categoria_id="<?= $ingreso['categoria_id'] ?>"
                                        data-descripcion="<?= htmlspecialchars($ingreso['descripcion']) ?>"
                                        data-fecha="<?= $ingreso['fecha'] ?>"
                                        data-accion="modificar">
                                    Modificar
                                </button>
                                <button class="btn btn-eliminar btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                        data-id="<?= $ingreso['id'] ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <nav class="mt-4" 
                 id="paginador-ingresos" 
                 data-pagina-actual="<?= $pagina_actual ?>" 
                 data-total-paginas="<?= $total_paginas ?>">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" id="pagina-anterior-ingresos">Antes</a>
                    </li>
                    <li class="page-item <?= ($total_paginas <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" id="pagina-siguiente-ingresos">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>
</div>

<div class="modal fade" id="modalIngreso" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="modalIngresoLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="api/crud_ingresos.php" method="POST">
          <div class="modal-body">
                <input type="hidden" name="ingresoId" id="ingresoId">
                <input type="hidden" name="accion" id="accion">
                <div class="row g-3">
                    <div class="col-md-6"><label for="monto" class="form-label">Monto</label><input type="number" step="0.01" class="form-control" id="monto" name="monto" required></div>
                    <div class="col-md-6"><label for="fecha" class="form-label">Fecha</label><input type="date" class="form-control" id="fecha" name="fecha" required></div>
                    <div class="col-12">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="" disabled>Seleccione...</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><label for="descripcion" class="form-label">Descripción (opcional)</label><input type="text" class="form-control" id="descripcion" name="descripcion"></div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-principal">Guardar</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirmar Eliminación</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>¿Estás seguro? Esta acción no se puede deshacer.</p></div>
            <div class="modal-footer">
                <form action="api/crud_ingresos.php" method="POST">
                    <input type="hidden" name="ingresoId" id="eliminarIngresoId">
                    <input type="hidden" name="accion" value="eliminar">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-eliminar">Confirmar Eliminación</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalIngreso = document.getElementById('modalIngreso');
    if (modalIngreso) {
        modalIngreso.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const accion = button.getAttribute('data-accion');
            
            const modalTitle = modalIngreso.querySelector('.modal-title');
            const form = modalIngreso.querySelector('form');
            form.reset(); 

            form.querySelector('#accion').value = accion;

            if (accion === 'modificar') {
                modalTitle.textContent = 'Modificar Ingreso';
                form.querySelector('#ingresoId').value = button.getAttribute('data-id');
                form.querySelector('#monto').value = button.getAttribute('data-monto');
                form.querySelector('#fecha').value = button.getAttribute('data-fecha');
                form.querySelector('#categoria_id').value = button.getAttribute('data-categoria_id');
                form.querySelector('#descripcion').value = button.getAttribute('data-descripcion');
            } else {
                modalTitle.textContent = 'Agregar Nuevo Ingreso';
                form.querySelector('#fecha').valueAsDate = new Date(); 
            }
        });
    }

    // ESTE ES EL SCRIPT QUE LLENA EL MODAL DE ELIMINAR
    const modalEliminar = document.getElementById('modalConfirmarEliminar');
    if (modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const ingresoId = button.getAttribute('data-id');
            const inputEliminar = modalEliminar.querySelector('#eliminarIngresoId');
            if (inputEliminar) {
                inputEliminar.value = ingresoId;
            }
        });
    }
});
</script>
<script src="js/paginador_ingresos.js"></script>
</body>
</html>