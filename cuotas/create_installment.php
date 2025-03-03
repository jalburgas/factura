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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cuotas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">SisGesA-Edu</a>
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
    <h2 class="text-center">Registrar Cuotas</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_installment.php" method="post">
                <div class="form-group">
                    <label for="curso_id">Curso</label>
                    <select class="form-control" name="curso_id" id="curso_id" required>
                        <!-- Opciones cargadas dinámicamente -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="num_cuotas">Número de Cuotas</label>
                    <input type="number" class="form-control" name="num_cuotas" id="num_cuotas" required>
                </div>
                <div id="installments-container">
                    <!-- Los campos de cuotas se generarán aquí dinámicamente -->
                </div>
                <button type="submit" class="btn btn-primary">Registrar Cuotas</button>
            </form>
        </div>
    </div>

    <h2 class="mt-5">Consultar Cuotas</h2>
    <table id="cuotasTable" class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Curso</th>
                <th>Número de Cuota</th>
                <th>Monto de Cuota</th>
                <th>Fecha de Vencimiento</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../db.php';
            $sql = "SELECT cuotas.id, cursos.nombre AS curso, cuotas.numero_cuota, cuotas.monto_cuota, cuotas.fecha_vencimiento
                    FROM cuotas 
                    JOIN cursos ON cuotas.curso_id = cursos.id";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['curso'] . "</td>";
                echo "<td>" . $row['numero_cuota'] . "</td>";
                echo "<td>" . $row['monto_cuota'] . "</td>";
                echo "<td>" . $row['fecha_vencimiento'] . "</td>";
                 echo "<td><a href='edit_installment.php?id=" . $row['id'] . "' class='btn btn-warning'>Editar</a></td>";
                echo "</tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTables
    $('#cuotasTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Exportar a Excel',
                className: 'btn btn-success'
            }
        ]
    });

    // Cargar cursos dinámicamente
    $.ajax({
        url: 'fetch_cursos.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var cursoSelect = $('#curso_id');
            cursoSelect.empty();
            $.each(data, function(index, curso) {
                cursoSelect.append('<option value="' + curso.id + '" data-costo="' + curso.costo + '">' + curso.nombre + '</option>');
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al cargar los cursos: ' + textStatus, errorThrown);
        }
    });
});
</script>
</body>
</html>