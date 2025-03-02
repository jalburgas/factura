<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cliente Jurídico</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
        .container {
            max-width: 800px;
        }
        .card {
            margin-top: 20px;
        }
        .card-body {
            padding: 30px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .text-center {
            margin-bottom: 20px;
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
    <h2 class="text-center">Registrar Cliente Jurídico</h2>
    <div class="card">
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
           <form action="register_juridical_client.php" method="post">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label for="rif">RIF</label>
                    <input type="text" class="form-control" name="rif" id="rif" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" id="telefono" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <!-- <h1>Estudiantes</h1>
                <div class="form-group">
                    <input type="text" class="form-control" id="cedulaEstudiante" name="cedulaEstudiante" placeholder="Cédula del Estudiante" required> 
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="nombreEstudiante" name="nombreEstudiante" placeholder="Nombre del Estudiante" required>
                </div> -->
                <div class="form-group">
                    <label for="curso_id">Curso</label>
                    <select class="form-control" name="curso_id" id="curso_id" required>
                        <?php
                        include '../db.php';
                        $sql = "SELECT id, nombre FROM cursos";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono">Cantidad Alumnos</label>
                    <input type="text" class="form-control" name="cant" id="cant" required>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Cliente</button>
            </form>
        </div>
    </div>

    <div >
        <div >
            <h2 class="text-center">Clientes Jurídicos Registrados</h2>
            <table id="clientes_juridicos" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RIF</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes_juridicos as $cliente): ?>
                        <tr>
                            <td><?php echo $cliente['id']; ?></td>
                            <td><?php echo $cliente['nombre']; ?></td>
                            <td><?php echo $cliente['rif']; ?></td>
                            <td><?php echo $cliente['direccion']; ?></td>
                            <td><?php echo $cliente['telefono']; ?></td>
                            <td><?php echo $cliente['email']; ?></td>
                         
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#clientes_juridicos').DataTable();
});
</script>
</body>
</html>
