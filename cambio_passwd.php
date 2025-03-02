<?php
session_start();
include 'db.php';

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria

// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
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
                <a class="nav-link" href="menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Cambiar Contraseña</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_change_password.php" method="post">
                <div class="form-group">
                    <label for="current_password">Contraseña Actual</label>
                    <input type="password" class="form-control" name="current_password" id="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña</label>
                    <input type="password" class="form-control" name="new_password" id="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>