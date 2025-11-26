<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// 1. Proteger la página
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
require_once 'inc/conexion.php';
$id_usuario = $_SESSION['id_usuario'];
define('REGISTROS_POR_PAGINA', 5); // Define cuántos registros mostrar por página

// --- 2. Cargar Página 1 de INGRESOS ---
$pagina_actual_ingresos = 1;
$offset_ingresos = 0;
// Contar el total de categorías de ingresos
$stmt_conteo_ing = $pdo->prepare("SELECT COUNT(*) FROM Categorias WHERE usuario_id = ? AND tipo = 'ingreso'");
$stmt_conteo_ing->execute([$id_usuario]);
$total_registros_ingresos = $stmt_conteo_ing->fetchColumn();
$total_paginas_ingresos = ceil($total_registros_ingresos / REGISTROS_POR_PAGINA);

// Obtener los registros de la primera página de ingresos
$stmt_ingresos = $pdo->prepare("SELECT id, nombre FROM Categorias WHERE usuario_id = :usuario_id AND tipo = 'ingreso' ORDER BY nombre LIMIT :limit OFFSET :offset");
$stmt_ingresos->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt_ingresos->bindParam(':offset', $offset_ingresos, PDO::PARAM_INT);
$stmt_ingresos->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
$stmt_ingresos->execute();
$categorias_ingreso = $stmt_ingresos->fetchAll();

// --- 3. Cargar Página 1 de GASTOS ---
$pagina_actual_gastos = 1;
$offset_gastos = 0;
// Contar el total de categorías de gastos
$stmt_conteo_gas = $pdo->prepare("SELECT COUNT(*) FROM Categorias WHERE usuario_id = ? AND tipo = 'gasto'");
$stmt_conteo_gas->execute([$id_usuario]);
$total_registros_gastos = $stmt_conteo_gas->fetchColumn();
$total_paginas_gastos = ceil($total_registros_gastos / REGISTROS_POR_PAGINA);

// Obtener los registros de la primera página de gastos
$stmt_gastos = $pdo->prepare("SELECT id, nombre FROM Categorias WHERE usuario_id = :usuario_id AND tipo = 'gasto' ORDER BY nombre LIMIT :limit OFFSET :offset");
$stmt_gastos->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt_gastos->bindParam(':offset', $offset_gastos, PDO::PARAM_INT);
$stmt_gastos->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
$stmt_gastos->execute();
$categorias_gasto = $stmt_gastos->fetchAll();

// --- 4. Manejar Alertas ---
$error = $_GET['error'] ?? '';
$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas - Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_categorias.css">
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
            <li class="nav-item"><a href="categorias.php" class="nav-link active"><i class="bi bi-tag-fill me-2"></i> Categorías</a></li>
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

        <?php if ($error === 'categoria_en_uso'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> No se puede eliminar la categoría porque ya está siendo usada.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'categoria_duplicada'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> Ya existe una categoría con ese nombre y tipo.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($status === 'agregado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Categoría agregada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($status === 'modificado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Categoría modificada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($status === 'eliminado'): ?>
             <div class="alert alert-success alert-dismissible fade show" role="alert">
                Categoría eliminada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-6">
                <h1 class="mb-4 h2">Categorías de Ingresos</h1>
                <div class="card">
                    <form action="api/crud_categorias.php" method="POST">
                        <label for="catingreso" class="form-label">Nueva categoría de ingreso</label>
                        <div class="input-group">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="tipo" value="ingreso">
                            <input type="text" class="form-control" id="catingreso" name="nombre" placeholder="Ej: Salario, Ventas" required>
                            <button type="submit" class="btn btn-principal">Agregar</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <table class="tabla-finanzas">
                        <thead><tr><th>Categoría</th><th>Acciones</th></tr></thead>
                        <tbody id="tabla-categorias-ingreso-body">
                            <?php if (empty($categorias_ingreso)): ?>
                                <tr><td colspan="2" class="text-center">No hay categorías de ingreso.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categorias_ingreso as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['nombre']) ?></td>
                                    <td>
                                        <button class="btn btn-modificar btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalModificarCategoria"
                                                data-id="<?= $cat['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($cat['nombre']) ?>"
                                                data-tipo="ingreso">
                                            Modificar
                                        </button>
                                        <button class="btn btn-eliminar btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                                data-id="<?= $cat['id'] ?>"
                                                data-tipo="ingreso">
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
                         data-pagina-actual="<?= $pagina_actual_ingresos ?>" 
                         data-total-paginas="<?= $total_paginas_ingresos ?>">
                        <ul class="pagination justify-content-end">
                            <li class="page-item <?= ($pagina_actual_ingresos <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-anterior-ingresos">Antes</a>
                            </li>
                            <li class="page-item <?= ($total_paginas_ingresos <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-siguiente-ingresos">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="col-lg-6">
                <h1 class="mb-4 h2">Categorías de Gastos</h1>
                <div class="card">
                    <form action="api/crud_categorias.php" method="POST">
                        <label for="catgasto" class="form-label">Nueva categoría de gasto</label>
                        <div class="input-group">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="tipo" value="gasto">
                            <input type="text" class="form-control" id="catgasto" name="nombre" placeholder="Ej: Alimentación, Transporte" required>
                            <button type="submit" class="btn btn-principal">Agregar</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <table class="tabla-finanzas">
                        <thead><tr><th>Categoría</th><th>Acciones</th></tr></thead>
                        <tbody id="tabla-categorias-gasto-body">
                            <?php if (empty($categorias_gasto)): ?>
                                <tr><td colspan="2" class="text-center">No hay categorías de gasto.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categorias_gasto as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['nombre']) ?></td>
                                    <td>
                                        <button class="btn btn-modificar btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalModificarCategoria"
                                                data-id="<?= $cat['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($cat['nombre']) ?>"
                                                data-tipo="gasto">
                                            Modificar
                                        </button>
                                        <button class="btn btn-eliminar btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar"
                                                data-id="<?= $cat['id'] ?>"
                                                data-tipo="gasto">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <nav class="mt-4" 
                         id="paginador-gastos" 
                         data-pagina-actual="<?= $pagina_actual_gastos ?>" 
                         data-total-paginas="<?= $total_paginas_gastos ?>">
                        <ul class="pagination justify-content-end">
                            <li class="page-item <?= ($pagina_actual_gastos <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-anterior-gastos">Antes</a>
                            </li>
                            <li class="page-item <?= ($total_paginas_gastos <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="#" id="pagina-siguiente-gastos">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="modalModificarCategoria" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Modificar Categoría</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="api/crud_categorias.php" method="POST">
          <div class="modal-body">
                <input type="hidden" name="accion" value="modificar">
                <input type="hidden" name="categoria_id" id="mod_categoria_id">
                <input type="hidden" name="tipo" id="mod_tipo">
                <div class="mb-3">
                    <label for="mod_nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-control" id="mod_nombre" name="nombre" required>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-principal">Guardar Cambios</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirmar Eliminación</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>¿Estás seguro? Esta acción no se puede deshacer. Si la categoría está en uso, no se eliminará.</p></div>
            <div class="modal-footer">
                <form action="api/crud_categorias.php" method="POST">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="categoria_id" id="del_categoria_id">
                    <input type="hidden" name="tipo" id="del_tipo">
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
    const modalModificar = document.getElementById('modalModificarCategoria');
    if(modalModificar) {
        modalModificar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const tipo = button.getAttribute('data-tipo');
            
            modalModificar.querySelector('#mod_categoria_id').value = id;
            modalModificar.querySelector('#mod_nombre').value = nombre;
            modalModificar.querySelector('#mod_tipo').value = tipo;
        });
    }

    const modalEliminar = document.getElementById('modalConfirmarEliminar');
    if(modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const tipo = button.getAttribute('data-tipo');
            
            modalEliminar.querySelector('#del_categoria_id').value = id;
            modalEliminar.querySelector('#del_tipo').value = tipo;
        });
    }
});
</script>
<script src="js/paginador_categorias.js"></script>
</body>
</html>