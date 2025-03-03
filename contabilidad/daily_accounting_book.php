<?php
include '../db.php';
session_start();

function getMonthName($monthNumber) {
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    return $months[$monthNumber];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Contable Diario - Facturas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }
        .dt-button.buttons-excel {
            background-color: #28a745 !important;
            color: white !important;
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
    <h2 class="text-center">Libro Contable Ventas</h2>
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="month">Seleccione el Mes</label>
                    <select class="form-control" name="month" id="month" required>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $monthName = getMonthName($i);
                            echo "<option value='$i'>$monthName</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Seleccione el Año</label>
                    <input type="number" class="form-control" name="year" id="year" value="<?php echo date('Y'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Consultar</button>
            </form>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $month = $_POST['month'];
        $year = $_POST['year'];

        // Consultar las facturas, notas de crédito y notas de débito del mes y año seleccionados y agrupar por día
        $sql = "(SELECT 
    f.id, 
    f.cliente_id,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.nombre
        WHEN s.cedula IS NOT NULL THEN CONCAT(s.nombre, ' ', s.apellido)
        ELSE 'Cliente Desconocido'
    END AS nombre_cliente,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.direccion
        WHEN s.cedula IS NOT NULL THEN s.direccion
        ELSE 'Dirección Desconocida'
    END AS direccion,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.telefono
        WHEN s.cedula IS NOT NULL THEN s.telefono
        ELSE 'Teléfono Desconocido'
    END AS telefono,
    f.fecha, 
    c.nombre AS curso_nombre, 
    fp.producto, 
    fp.cantidad, 
    fp.precio, 
    (fp.cantidad * fp.precio) AS monto, 
    'Factura' AS tipo, 
    NULL AS descripcion, 
    NULL AS factura_afectada
 FROM facturas f
 JOIN factura_productos fp ON f.id = fp.factura_id
 JOIN cursos c ON f.curso_id = c.id
 LEFT JOIN clientes_juridicos cj ON f.cliente_id = cj.rif
 LEFT JOIN students s ON f.cliente_id = s.cedula
 WHERE MONTH(f.fecha) = ? AND YEAR(f.fecha) = ?)

UNION

(SELECT 
    cn.id, 
    f.cliente_id,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.nombre
        WHEN s.cedula IS NOT NULL THEN CONCAT(s.nombre, ' ', s.apellido)
        ELSE 'Cliente Desconocido'
    END AS nombre_cliente,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.direccion
        WHEN s.cedula IS NOT NULL THEN s.direccion
        ELSE 'Dirección Desconocida'
    END AS direccion,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.telefono
        WHEN s.cedula IS NOT NULL THEN s.telefono
        ELSE 'Teléfono Desconocido'
    END AS telefono,
    cn.created_at AS fecha, 
    c.nombre AS curso_nombre, 
    NULL AS producto, 
    NULL AS cantidad, 
    cn.amount AS precio, 
    cn.amount AS monto, 
    'Nota de Crédito' AS tipo, 
    cn.reason AS descripcion, 
    cn.invoice_id AS factura_afectada
 FROM credit_notes cn
 JOIN facturas f ON cn.invoice_id = f.id
 JOIN cursos c ON f.curso_id = c.id
 LEFT JOIN clientes_juridicos cj ON f.cliente_id = cj.rif
 LEFT JOIN students s ON f.cliente_id = s.cedula
 WHERE MONTH(cn.created_at) = ? AND YEAR(cn.created_at) = ?)

UNION

(SELECT 
    dn.id, 
    f.cliente_id,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.nombre
        WHEN s.cedula IS NOT NULL THEN CONCAT(s.nombre, ' ', s.apellido)
        ELSE 'Cliente Desconocido'
    END AS nombre_cliente,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.direccion
        WHEN s.cedula IS NOT NULL THEN s.direccion
        ELSE 'Dirección Desconocida'
    END AS direccion,
    CASE
        WHEN cj.rif IS NOT NULL THEN cj.telefono
        WHEN s.cedula IS NOT NULL THEN s.telefono
        ELSE 'Teléfono Desconocido'
    END AS telefono,
    dn.created_at AS fecha, 
    c.nombre AS curso_nombre, 
    NULL AS producto, 
    NULL AS cantidad, 
    dn.amount AS precio, 
    dn.amount AS monto, 
    'Nota de Débito' AS tipo, 
    dn.reason AS descripcion, 
    dn.invoice_id AS factura_afectada
 FROM debit_notes dn
 JOIN facturas f ON dn.invoice_id = f.id
 JOIN cursos c ON f.curso_id = c.id
 LEFT JOIN clientes_juridicos cj ON f.cliente_id = cj.rif
 LEFT JOIN students s ON f.cliente_id = s.cedula
 WHERE MONTH(dn.created_at) = ? AND YEAR(dn.created_at) = ?)

ORDER BY fecha, id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiii", $month, $year, $month, $year, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<h3 class="mt-5">Facturas, Notas de Crédito y Notas de Débito del Mes y Año Seleccionados</h3>';
        echo '<table id="accountingTable" class="table table-bordered mt-3">';
        echo '<thead><tr><th>Fecha</th><th>Factura</th><th>Cliente ID</th><th>Nombre_Cliente</th><th>Direccion_Cliente</th><th>Telefono</th><th>Curso</th><th>Cuota</th><th>Cantidad</th><th>Precio</th><th>Monto</th><th>Tipo</th><th>Descripción</th><th>Factura Afectada</th></tr></thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            $fecha = date("Y-m-d", strtotime($row['fecha']));
            echo '<tr>';
            echo '<td>' . $fecha . '</td>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['cliente_id'] . '</td>';
            echo '<td>' . $row['nombre_cliente'] . '</td>';
            echo '<td>' . $row['direccion'] . '</td>';
            echo '<td>' . $row['telefono'] . '</td>';
            echo '<td>' . $row['curso_nombre'] . '</td>'; // Cambiado a curso_nombre
            echo '<td>' . $row['producto'] . '</td>';
            echo '<td>' . $row['cantidad'] . '</td>';
            echo '<td>' . number_format($row['precio'], 2) . '</td>';
            echo '<td>' . number_format($row['monto'], 2) . '</td>';
            echo '<td>' . $row['tipo'] . '</td>';
            echo '<td>' . $row['descripcion'] . '</td>';
            echo '<td>' . $row['factura_afectada'] . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        $stmt->close();
        $conn->close();
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script>
$(document).ready(function() {
    $('#accountingTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Copiar'
            },
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn-excel'
            },
            {
                extend: 'csvHtml5',
                text: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF'
            }
        ]
    });
});
</script>
<style>
.btn-excel {
    background-color: #28a745 !important;
    color: white !important;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>