<?php
session_start();


?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Tasa de Cambio</title>
    <!-- Incluir CSS de Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .navbar-text {
            color: #28a745 !important;
            /* Verde destacado */
            font-weight: bold;
        }
    
    </style>
</head>
<body>
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
        <h2>Registrar Tasa de Cambio</h2>
        <form action="procesar_tasa.php" method="POST">
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="tasa">Tasa de Cambio:</label>
                <input type="text" class="form-control" id="tasa" name="tasa" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>

        

        <h2 class="mt-5">Tasas de Cambio Registradas</h2>
        <table id="tasasTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tasa de Cambio</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Conectar a la base de datos
                require_once("../db.php");

                // Verificar la conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener las tasas de cambio en orden descendente
                $query = "SELECT fecha, tasa FROM tasa_cambio ORDER BY fecha DESC";
                $result = mysqli_query($conn, $query);

                // Verificar si la consulta devuelve resultados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['fecha'] . "</td>";
                        echo "<td>" . $row['tasa'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No hay datos disponibles</td></tr>";
                }

                // Cerrar la conexión
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <!-- Incluir JS de jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Incluir JS de Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Incluir JS de DataTables -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <!-- Inicializar DataTables -->
    <script>
        $(document).ready(function() {
            $('#tasasTable').DataTable({
                "ordering": false // Desactivar la funcionalidad de ordenamiento
            });
        });
    </script>
</body>
</html>
