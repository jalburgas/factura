<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];

    // Consultar pagos realizados por el estudiante
    $sql_pagos = "SELECT f.id AS factura_id, f.cliente_id, f.fecha, c.nombre, fp.id AS producto_id, fp.producto, fp.cantidad, fp.precio
                  FROM facturas f
                  JOIN factura_productos fp ON f.id = fp.factura_id
                  INNER JOIN cursos c ON f.curso_id = c.id
                  WHERE f.cliente_id = ?";
    $stmt_pagos = $conn->prepare($sql_pagos);
    $stmt_pagos->bind_param("s", $cedula);
    $stmt_pagos->execute();
    $result_pagos = $stmt_pagos->get_result();

    // Consultar cuotas pendientes del estudiante
    $sql_cuotas_pendientes = "SELECT cp.id, c.nombre, cp.cliente_id, cp.factura_id, cp.estado_pago, cp.monto, cp.fecha_vencimiento, cp.numero_cuota, cp.fecha_pago, cp.cant_alumnos 
                              FROM cuotas_pendientes cp
                              INNER JOIN cursos c ON cp.curso_id = c.id
                              WHERE cp.cliente_id = ?";
    $stmt_cuotas_pendientes = $conn->prepare($sql_cuotas_pendientes);
    $stmt_cuotas_pendientes->bind_param("s", $cedula);
    $stmt_cuotas_pendientes->execute();
    $result_cuotas_pendientes = $stmt_cuotas_pendientes->get_result();
}

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria

// Obtener la fecha actual
$today = date('Y-m-d');

// Consultar las facturas y sus productos
$sql = "SELECT f.id AS factura_id, f.cliente_id, f.fecha, f.curso_id, fp.id AS producto_id, fp.producto, fp.cantidad, fp.precio
        FROM facturas f
        JOIN factura_productos fp ON f.id = fp.factura_id
        WHERE DATE(f.fecha) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de Pagos y Cuotas Pendientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important;
            font-weight: bold;
        }

        .card {
            border: 2px solid #007bff;
            border-radius: 10px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-bottom: 2px solid #007bff;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tbody tr:hover {
            background-color: #ddd;
        }

        .fecha-roja {
            color: red;
            font-weight: bold;
        }

        .fecha-verde {
            color: green;
            font-weight: bold;
        }

        .estado-rojo {
            color: red;
            font-weight: bold;
        }

        .estado-verde {
            color: green;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Gestión Escolar</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <?php if (isset($_SESSION['username'])) : ?>
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
        <h2 class="text-center">Consulta de Pagos y Cuotas Pendientes</h2>
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="cedula">Cédula del Estudiante</label>
                        <input type="text" class="form-control" name="cedula" id="cedula" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                </form>
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../db.php';

            $cedula = $_POST['cedula'];

            // Mostrar pagos realizados
            echo '<h3 class="mt-5">Pagos Realizados</h3>';
            echo '<table class="table table-bordered mt-3">';
            echo '<thead><tr><th>ID Factura</th><th>Cliente ID</th><th>Fecha</th><th>Curso</th><th>Cuota</th><th>Cantidad</th><th>Precio</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result_pagos->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['factura_id'] . '</td>';
                echo '<td>' . $row['cliente_id'] . '</td>';
                echo '<td>' . $row['fecha'] . '</td>';
                echo '<td>' . $row['nombre'] . '</td>';
                echo '<td>' . $row['producto'] . '</td>';
                echo '<td>' . $row['cantidad'] . '</td>';
                echo '<td>' . number_format($row['precio'], 2) . '</td>';
                echo '</tr>';
            }
  
// ... (código anterior)

// Variables para almacenar los totales
$total = 0;
$total_pagado = 0;
$total_no_pagado = 0;

// Mostrar cuotas pendientes

echo '<table class="table table-bordered mt-3">';
echo '<thead"<h3>Cuotas Pendientes</h3></thead>';
echo '<thead><tr><th>ID</th><th>Curso</th><th>Cliente</th><th>Factura</th><th>Fecha Pago</th><th>Estado Pago</th><th>Monto</th><th>Vencimiento</th><th>Cuota</th><th>Cant_Alumnos</th</tr></thead>';
echo '<tbody>';

while ($row = $result_cuotas_pendientes->fetch_assoc()) {
    $fecha_vencimiento = $row['fecha_vencimiento'];
    $clase_fecha = ($fecha_vencimiento <= $today) ? 'fecha-roja' : 'fecha-verde';
    $estado_pago = $row['estado_pago'];
    $clase_estado = ($estado_pago == 0) ? 'estado-rojo' : 'estado-verde';
    $monto = $row['monto'];

    // Acumular totales
    $total += $monto;
    if ($estado_pago == 1) {
        $total_pagado += $monto;
    } else {
        $total_no_pagado += $monto;
    }

    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>';
    echo '<td>' . $row['nombre'] . '</td>';
    echo '<td>' . $row['cliente_id'] . '</td>';
    echo '<td>' . $row['factura_id'] . '</td>';
    echo '<td>' . ($row['fecha_pago'] ? $row['fecha_pago'] : '') . '</td>';
    echo '<td class="' . $clase_estado . '">' . ($estado_pago == 0 ? 'No pagado' : 'Pagado') . '</td>';
    echo '<td>' . number_format($monto) . '</td>';
    echo '<td class="' . $clase_fecha . '">' . $fecha_vencimiento . '</td>';
    echo '<td>' . $row['numero_cuota'] . '</td>';
    echo '<td>' . $row['cant_alumnos'] . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';

// Mostrar los totales
echo '<div class="mt-4">';
echo '<h4>Resumen de Pagos</h4>';
echo '<p><strong>Total:</strong> ' . number_format($total, 2) . '</p>';
echo '<p><strong>Total Pagado:</strong> ' . number_format($total_pagado, 2) . '</p>';
echo '<p><strong>Total No Pagado:</strong> ' . number_format($total_no_pagado, 2) . '</p>';
echo '</div>';

// Cerrar conexiones
$stmt_pagos->close();
$stmt_cuotas_pendientes->close();
$conn->close();
}
?>
       

        <div class="card mt-5">
            <div class="card-header text-center">
                <h2>Pagos del Día</h2>
                <p><?php echo date('d/m/Y'); ?></p>
            </div>
            <div class="card-body">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>ID Factura</th>
                            <th>Cliente ID</th>
                            <th>Fecha</th>
                            <th>Curso ID</th>
                            <th>Cuota</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $row) : ?>
                            <tr>
                                <td><?php echo $row['factura_id']; ?></td>
                                <td><?php echo $row['cliente_id']; ?></td>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><?php echo $row['curso_id']; ?></td>
                                <td><?php echo $row['producto']; ?></td>
                                <td><?php echo $row['cantidad']; ?></td>
                                <td><?php echo number_format($row['precio'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center">
                <button onclick="window.print()" class="btn btn-success no-print">Imprimir</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>