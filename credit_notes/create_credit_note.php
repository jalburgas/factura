<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_id = $_POST['invoice_id'];
    $amount = $_POST['amount'];
    $reason = $_POST['reason'];

    // Obtener el cliente_id de la factura
    $cliente_sql = "SELECT cliente_id FROM facturas WHERE id = ?";
    $cliente_stmt = $conn->prepare($cliente_sql);
    $cliente_stmt->bind_param('i', $invoice_id);
    $cliente_stmt->execute();
    $cliente_result = $cliente_stmt->get_result();
    $cliente_row = $cliente_result->fetch_assoc();
    $cliente_id = $cliente_row['cliente_id'];

    // Verificar si ya existe una nota de crédito con los mismos detalles
    $check_sql = "SELECT id FROM credit_notes WHERE invoice_id = ? AND amount = ? AND reason = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ids', $invoice_id, $amount, $reason);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Error: Ya existe una nota de crédito con los mismos detalles.";
    } else {
        $sql = "INSERT INTO credit_notes (invoice_id, cliente_id, amount, reason, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iids', $invoice_id, $cliente_id, $amount, $reason);

        if ($stmt->execute()) {
            $credit_note_id = $stmt->insert_id;
            header("Location: create_credit_note.php");
            exit();
        } else {
            echo "Error al crear la nota de crédito: " . $conn->error;
        }
    }
    
}

// Consulta para obtener las notas de crédito junto con el cliente_id de las facturas
$sql = "SELECT cn.id, cn.invoice_id, cn.cliente_id, cn.amount, cn.reason, cn.created_at
        FROM credit_notes cn
        JOIN facturas f ON cn.invoice_id = f.id";
$result = $conn->query($sql);
$credit_notes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nota de Crédito</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
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
    <h2>Crear Nota de Crédito</h2>
    <form method="POST" action="create_credit_note.php">
        <div class="form-group">
            <label for="invoice_id">ID de la Factura</label>
            <input type="number" class="form-control" id="invoice_id" name="invoice_id" required>
        </div>
        <div class="form-group">
            <label for="amount">Monto</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="reason">Razón</label>
            <textarea class="form-control" id="reason" name="reason" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear Nota de Crédito</button>
    </form>

    <h2 class="mt-5">Notas de Crédito Creadas</h2>
    <table id="creditNotesTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID de la Factura</th>
                <th>Cliente ID</th>
                <th>Monto</th>
                <th>Razón</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($credit_notes as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['invoice_id']; ?></td>
                    <td><?php echo $row['cliente_id']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td><?php echo $row['reason']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="view_credit_note.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Ver e Imprimir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#creditNotesTable').DataTable({
        "destroy": true, // Asegura que la tabla se inicialice solo una vez
        "paging": true,
        "searching": true,
        "ordering": true
    });
});
</script>
</body>
</html>