<?php
session_start();
include 'db.php';

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
          .navbar-text {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }
     </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Gestión Escolar</a>
    <div class="collapse navbar-collapse">
    <ul class="navbar-nav mr-auto">
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <span class="navbar-text">Usuario: <?php echo $_SESSION['username']; ?></span>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="cambio_passwd_admin.php">Cambio Password</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Registrar Usuario</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_register_user.php" method="post">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select class="form-control" name="rol" id="rol" required>
                        <option value="administrador">Administrador del Sistema</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="caja">Caja</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Usuario</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
