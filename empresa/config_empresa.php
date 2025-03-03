<?php
session_start();
include '../db.php';

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria
?>
<!DOCTYPE html>
<html>
<head>
    <title>Configurar Datos de la Empresa</title>
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
                <a class="nav-link" href="../menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Configurar Datos de la Empresa</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_empresa.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre de la Empresa</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label for="rif">RIF</label>
                    <input type="text" class="form-control" name="rif" id="rif" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea class="form-control" name="direccion" id="direccion" required></textarea>
                </div>
                <div class="form-group">
                    <label for="logo">Logo de la Empresa</label>
                    <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

    <h2 class="mt-5">Empresas Registradas</h2>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>RIF</th>
                <th>Dirección</th>
                <th>Logo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../db.php';

            $sql = "SELECT * FROM empresa";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['rif'] . "</td>";
                echo "<td>" . $row['direccion'] . "</td>";
                echo "<td><img src='uploads/" . $row['logo'] . "' alt='" . $row['nombre'] . "' height='50'></td>";
                echo "<td>
                        <a href='update_empresa.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                        <a href='delete_empresa.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
