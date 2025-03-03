<?php
session_start();
include '../db.php';

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria

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
    <title>Libro Contable - Facturas Mensuales</title>
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
    <h2 class="text-center">Libro Contable - Facturas Mensuales</h2>

    <form action="accounting_book.php" method="post" class="form-inline justify-content-center mb-4">
        <div class="form-group">
            <label for="year" class="mr-2">Año:</label>
            <select name="year" id="year" class="form-control" required>
                <?php
                $currentYear = date("Y");
                for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                    echo "<option value=\"$year\">$year</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary ml-2">Filtrar</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $year = $_POST['year'];

        // Consultar las facturas agrupadas por mes y obtener el monto total de entradas, notas de crédito y notas de débito
        $sql = "SELECT 
            COALESCE(f.mes, cn.mes, dn.mes) as mes,
            COALESCE(f.año, cn.año, dn.año) as año,
            IFNULL(f.total_entradas, 0) as total_entradas,
            IFNULL(cn.total_notas_credito, 0) as total_notas_credito,
            IFNULL(dn.total_notas_debito, 0) as total_notas_debito,
            (IFNULL(f.total_entradas, 0) - IFNULL(cn.total_notas_credito, 0) + IFNULL(dn.total_notas_debito, 0)) as total_final
        FROM (
            SELECT 
                MONTH(f.fecha) as mes, 
                YEAR(f.fecha) as año, 
                SUM(f.total_factura) as total_entradas
            FROM facturas f
            WHERE YEAR(f.fecha) = ?
            GROUP BY YEAR(f.fecha), MONTH(f.fecha)
        ) f
        LEFT JOIN (
            SELECT 
                MONTH(cn.created_at) as mes, 
                YEAR(cn.created_at) as año,    
                SUM(cn.amount) as total_notas_credito    
            FROM credit_notes cn
            WHERE YEAR(cn.created_at) = ?
            GROUP BY YEAR(cn.created_at), MONTH(cn.created_at)
        ) cn ON f.mes = cn.mes AND f.año = cn.año
        LEFT JOIN (
            SELECT 
                MONTH(dn.created_at) as mes, 
                YEAR(dn.created_at) as año,     
                SUM(dn.amount) as total_notas_debito   
            FROM debit_notes dn
            WHERE YEAR(dn.created_at) = ?
            GROUP BY YEAR(dn.created_at), MONTH(dn.created_at)
        ) dn ON f.mes = dn.mes AND f.año = dn.año
        ORDER BY año, mes";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $year, $year, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<table id="accountingTable" class="table table-bordered mt-3">';
        echo '<thead><tr><th>Mes</th><th>Año</th><th>Total Entradas</th><th>Total Notas de Crédito</th><th>Total Notas de Débito</th><th>Total Final</th></tr></thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . getMonthName($row['mes']) . '</td>'; // Mostrar el nombre del mes
            echo '<td>' . $row['año'] . '</td>';
            echo '<td>' . number_format($row['total_entradas'], 2) . '</td>';
            echo '<td>' . number_format($row['total_notas_credito'], 2) . '</td>';
            echo '<td>' . number_format($row['total_notas_debito'], 2) . '</td>';
            echo '<td>' . number_format($row['total_final'], 2) . '</td>';
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
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
