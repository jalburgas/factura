<?php
session_start();
include '../db.php';

if (!isset($_GET['id'])) {
    die("ID de la nota de crédito no proporcionado.");
}

$credit_note_id = $_GET['id'];

// Consulta SQL para obtener los datos de la nota de crédito, la factura y el cliente
$sql = "
    SELECT 
        cn.id AS credit_note_id,
        cn.invoice_id,
        cn.amount,
        cn.reason,
        cn.created_at,
        f.cliente_id,
        COALESCE(s.cedula, cj.rif) AS identificador,
        COALESCE(s.nombre, cj.nombre) AS nombre,
        COALESCE(s.apellido, '') AS apellido,
        COALESCE(s.direccion, cj.direccion) AS direccion,
        COALESCE(s.telefono, cj.telefono) AS telefono
    FROM 
        credit_notes cn
    INNER JOIN 
        facturas f ON cn.invoice_id = f.id
    LEFT JOIN 
        students s ON f.cliente_id = s.cedula
    LEFT JOIN 
        clientes_juridicos cj ON f.cliente_id = cj.rif
    WHERE 
        cn.id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param('i', $credit_note_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Nota de crédito no encontrada.");
}

$credit_note = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Nota de Crédito</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-text-green {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }

        /* Ocultar el botón al imprimir */
        @media print {
            .no-print {
                display: none; /* Oculta el botón */
            }
            .invoice-container {
                width: 80mm; /* Ajusta el ancho según las especificaciones de tu impresora fiscal */
                padding: 5mm;
                border: none; /* Quita el borde para la impresión */
                box-sizing: border-box;
            }
            .invoice-header, .invoice-footer {
                text-align: center;
            }
            .invoice-body {
                margin-top: 50px;
            }
            .product-item {
                margin-bottom: 5px;
            }
            .product-item p {
                margin: 0;
            }
            .btn-print, .btn-back {
                display: none; /* Oculta los botones durante la impresión */
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Sistema de Gestión</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <span class="navbar-text navbar-text-green">Usuario: <?php echo $_SESSION['username']; ?></span>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="../menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container invoice-container">
    <div class="invoice-body">
        <div class="product-item">
            <h5>Nota de Crédito #<?php echo $credit_note['credit_note_id']; ?></h5>
            <p><strong>ID de la Factura:</strong> <?php echo $credit_note['invoice_id']; ?></p>
            <p><strong>Monto:</strong> <?php echo $credit_note['amount']; ?></p>
            <p><strong>Razón:</strong> <?php echo $credit_note['reason']; ?></p>
            <p><strong>Fecha de Creación:</strong> <?php echo $credit_note['created_at']; ?></p>

            <!-- Datos del Cliente -->
            <h5 class="mt-4">Datos del Cliente</h5>
            <p><strong>Nombre:</strong> <?php echo $credit_note['nombre'] . ' ' . $credit_note['apellido']; ?></p>
            <p><strong>Cédula/RIF:</strong> <?php echo $credit_note['identificador']; ?></p>
            <p><strong>Dirección:</strong> <?php echo $credit_note['direccion']; ?></p>
            <p><strong>Teléfono:</strong> <?php echo $credit_note['telefono']; ?></p>

            <!-- Botón de impresión -->
            <button onclick="window.print()" class="btn btn-primary no-print">Imprimir Nota de Crédito</button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>