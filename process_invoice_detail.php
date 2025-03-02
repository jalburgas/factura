<?php
include 'db.php';

$factura_id = $_POST['factura_id'];
$curso = $_POST['curso'];
$monto = $_POST['monto'];

$sql = "INSERT INTO factura_detalles (factura_id, curso, monto) VALUES ('$factura_id', '$curso', '$monto')";

if ($conn->query($sql) === TRUE) {
    echo "Nuevo detalle de factura creado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

<a href="index.php">Volver al Menú Principal</a>
