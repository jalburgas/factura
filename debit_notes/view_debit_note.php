<?php
session_start();
include '../db.php';

if (!isset($_GET['id'])) {
    die("ID de la nota de débito no proporcionado.");
}

$debit_note_id = $_GET['id'];

$sql = "SELECT id, invoice_id, amount, reason, created_at FROM debit_notes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $debit_note_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Nota de débito no encontrada.");
}

$debit_note = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Nota de Débito</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-text-green {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
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
<div class="container mt-5">
    <h2>Nota de Débito</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nota de Débito #<?php echo $debit_note['id']; ?></h5>
            <p class="card-text"><strong>ID de la Factura:</strong> <?php echo $debit_note['invoice_id']; ?></p>
            <p class="card-text"><strong>Monto:</strong> <?php echo $debit_note['amount']; ?></p>
            <p class="card-text"><strong>Razón:</strong> <?php echo $debit_note['reason']; ?></p>
            <p class="card-text"><strong>Fecha de Creación:</strong> <?php echo $debit_note['created_at']; ?></p>
            <button onclick="window.print()" class="btn btn-primary">Imprimir Nota de Débito</button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>