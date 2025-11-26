<?php
require_once 'inc/conexion.php'; 
// La conexión ya debe iniciar la sesión (session_start())

// 1. SECUENCIA DE PROTECCIÓN
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
// ID del usuario que está viendo la página
$id_usuario_logueado = $_SESSION['id_usuario'];

// 2. LÓGICA DE PAGINACIÓN INICIAL (Solo para página 1)
define('REGISTROS_POR_PAGINA', 10);
$pagina_actual = 1;
$offset = 0;

// 3. Contar todos los usuarios EXCEPTO el logueado
$stmt_conteo = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE id != ?");
$stmt_conteo->execute([$id_usuario_logueado]);
$total_registros = $stmt_conteo->fetchColumn();
$total_paginas = ceil($total_registros / REGISTROS_POR_PAGINA);

// 4. Seleccionar todos los usuarios EXCEPTO el logueado (para la página 1)
$stmt = $pdo->prepare("SELECT id, nombre_usuario, fecha_registro FROM Usuarios WHERE id != :id_usuario_logueado ORDER BY id DESC LIMIT :limit OFFSET :offset");

// ¡ESTA ES LA CORRECCIÓN DEL ERROR 'bindParam'!
$stmt->bindValue(':limit', REGISTROS_POR_PAGINA, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':id_usuario_logueado', $id_usuario_logueado, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

// 5. Manejar Alertas de la URL
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Finanzas - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_usuarios.css">
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
            <li class="nav-item"><a href="usuarios.php" class="nav-link active"><i class="bi bi-people-fill me-2"></i> Usuarios</a></li>
            <li class="nav-item"><a href="ingresos.php" class="nav-link"><i class="bi bi-arrow-down-circle-fill me-2"></i> Ingresos</a></li>
            <li class="nav-item"><a href="gastos.php" class="nav-link"><i class="bi bi-arrow-up-circle-fill me-2"></i> Gastos</a></li>
            <li class="nav-item"><a href="categorias.php" class="nav-link"><i class="bi bi-tag-fill me-2"></i> Categorías</a></li>
            <li class="nav-item"><a href="ahorros.php" class="nav-link"><i class="bi bi-piggy-bank-fill me-2"></i> Ahorros</a></li>
            <li class="nav-item"><a href="reportes.php" class="nav-link"><i class="bi bi-bar-chart-line-fill me-2"></i> Reportes</a></li>
        </ul>
        <hr class="my-4">
        <div class="text-center">
            <button class="btn btn-principal" id="cerrar_sesion" onclick="window.location.href='cerrar_sesion.php'" >Cerrar Sesión <i class="bi bi-box-arrow-right"></i></button>
        </div>
    </nav>

    <main class="main-content">
    
        <?php if ($error === 'no_self_edit'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> No puedes modificar tu propio usuario desde esta pantalla.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'no_self_delete'): ?>
             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> No puedes eliminar tu propio usuario.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestión de Usuarios</h1>
            <button class="btn btn-principal" data-bs-toggle="modal" data-bs-target="#modalUsuario" data-accion="agregar">
                <i class="bi bi-plus-circle-fill me-2"></i>Agregar Nuevo Usuario
            </button>
        </div>
        
        <div class="card">
            <table class="tabla-finanzas">
                <thead>
                    <tr>
                        <th style="display: none;">ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-usuarios-body">
                <?php if (empty($usuarios)): ?>
                        <tr><td colspan="4" class="text-center">No hay otros usuarios registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td style="display: none;"><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                            <td><?= htmlspecialchars(date("d/m/Y", strtotime($usuario['fecha_registro']))) ?></td>
                            <td>
                                <button class="btn btn-modificar btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalUsuario"
                                        data-id="<?= $usuario['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($usuario['nombre_usuario']) ?>"
                                        data-accion="modificar">
                                    Modificar
                                </button>
                                <button class="btn btn-eliminar btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalConfirmarEliminar"
                                        data-id="<?= $usuario['id'] ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <nav class="mt-4" 
                 id="paginador-usuarios" 
                 data-pagina-actual="<?= $pagina_actual ?>" 
                 data-total-paginas="<?= $total_paginas ?>">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" id="pagina-anterior">Antes</a>
                    </li>
                    <li class="page-item <?= ($total_paginas <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="#" id="pagina-siguiente">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUsuarioLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="api/crud_usuarios.php" method="POST">
          <div class="modal-body">
                <input type="hidden" id="usuarioId" name="usuarioId">
                <input type="hidden" id="accion" name="accion">
                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="form-text">Dejar en blanco para no cambiar la contraseña.</div>
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
            <div class="modal-body"><p>¿Estás seguro? Esta acción no se puede deshacer.</p></div>
            <div class="modal-footer">
                <form action="api/crud_usuarios.php" method="POST">
                    <input type="hidden" name="usuarioId" id="eliminarUsuarioId">
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
    const modalUsuario = document.getElementById('modalUsuario');
    if (modalUsuario) {
        modalUsuario.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const accion = button.getAttribute('data-accion');
            
            const modalTitle = modalUsuario.querySelector('.modal-title');
            const form = modalUsuario.querySelector('form'); 
            
            const inputId = form.querySelector('#usuarioId');
            const inputNombre = form.querySelector('#nombreUsuario');
            const inputAccion = form.querySelector('#accion');
            const inputPassword = form.querySelector('#password');

            if (accion === 'modificar') {
                modalTitle.textContent = 'Modificar Usuario';
                inputId.value = button.getAttribute('data-id');
                inputNombre.value = button.getAttribute('data-nombre');
                inputAccion.value = 'modificar';
                inputPassword.placeholder = 'Dejar en blanco para no cambiar';
            } else {
                modalTitle.textContent = 'Agregar Nuevo Usuario';
                form.reset(); 
                inputAccion.value = 'agregar';
                inputPassword.placeholder = '';
            }
        });
    }

    const modalEliminar = document.getElementById('modalConfirmarEliminar');
    if (modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const usuarioId = button.getAttribute('data-id');
            const inputEliminar = modalEliminar.querySelector('#eliminarUsuarioId');
            inputEliminar.value = usuarioId;
        });
    }
});
</script>
<script src="js/paginador_usuarios.js"></script>
</body>
</html>