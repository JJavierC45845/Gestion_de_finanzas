<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/estilo_sesion.css">
</head>

<body class="d-flex align-items-center py-5">
    
    <main class="container-xl">
        <div class="row g-4 g-lg-5 justify-content-center align-items-center">

            <div class="col-lg-5">
                <form id="loginForm" class="login-card p-4 p-md-5">
                    <i class="bi bi-wallet2 login-icon"></i>
                    <h1 class="text-center mb-4 h3">Accede a tus Finanzas</h1>
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" placeholder="Ingrese su usuario" required>
                    </div>

                    <div class="mb-4">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" placeholder="Ingrese su contraseña" required>
                    </div> 
                    
                    <button type="submit" class="btn btn-custom w-100">Ingresar</button>
                    
                    <div id="error" class="alert alert-danger mt-4 d-none mb-0"></div>
                </form>
            </div>

            <div class="col-lg-7 d-none d-lg-flex justify-content-center align-items-center">
                <img src="img/logo.png" class="img-fluid rounded-4" style="max-width: 80%;" alt="Logo de la empresa">
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/inicio_sesion.js"></script>
</body>
</html>