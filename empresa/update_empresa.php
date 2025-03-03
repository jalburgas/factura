<?php
include '../db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM empresa WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Actualizar Datos de la Empresa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Gestión Escolar</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Actualizar Datos de la Empresa</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_update_empresa.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre de la Empresa</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $row['nombre']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="rif">RIF</label>
                    <input type="text" class="form-control" name="rif" id="rif" value="<?php echo $row['rif']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea class="form-control" name="direccion" id="direccion" required><?php echo $row['direccion']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo">Logo de la Empresa</label>
                    <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
