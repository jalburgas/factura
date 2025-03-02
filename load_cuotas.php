<?php
include 'db.php';

$sql = "SELECT cuotas.id, courses.name AS curso_id, cuotas.numero_cuota, cuotas.monto_cuota, cuotas.fecha_vencimiento FROM cuotas JOIN courses ON cuotas.curso_id = courses.id";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>
