<?php
session_start();
include '../db.php';
date_default_timezone_set('America/New_York'); 

// Obtener la fecha actual
$today = date('Y-m-d');

// Consultar el monto total y el total de operaciones del día, incluyendo el tipo de operación
$sql = "(SELECT 'Factura' AS tipo, COUNT(*) AS total_operaciones, SUM(fp.cantidad * fp.precio) AS total_monto
        FROM facturas f
        JOIN factura_productos fp ON f.id = fp.factura_id
        WHERE DATE(f.fecha) = ?)
        UNION
        (SELECT 'Nota de Crédito' AS tipo, COUNT(*) AS total_operaciones, SUM(cn.amount) AS total_monto
        FROM credit_notes cn
        WHERE DATE(cn.created_at) = ?)
        UNION
        (SELECT 'Reverso' AS tipo, COUNT(*) AS total_operaciones, SUM(hr.factura_id) AS total_monto
        FROM historico_reverso hr
        WHERE DATE(hr.fecha_reverso) = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $today, $today, $today);
$stmt->execute();
$result = $stmt->get_result();

$report = [];
while ($row = $result->fetch_assoc()) {
    $report[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
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
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Reporte Diario</h2>
            <p><?php echo date('d/m/Y'); ?></p>
        </div>
        <div class="card-body">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Tipo de Operación</th>
                        <th>Total de Operaciones</th>
                        <th>Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report as $row): ?>
                    <tr>
                        <td><?php echo $row['tipo']; ?></td>
                        <td><?php echo $row['total_operaciones']; ?></td>
                        <td><?php echo number_format($row['total_monto'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-center">
            <a href="create_invoice.php" class="btn btn-primary no-print">Volver</a>
            <button onclick="window.print()" class="btn btn-success no-print">Imprimir</button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>