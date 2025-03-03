<?php
include '../db.php';

if (isset($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];
    $cedula = $_GET['cedula'];

    // Consulta actualizada con cliente_id y tabla/columna correcta
    $sql = "SELECT numero_cuota, monto AS monto_cuota 
            FROM cuotas_pendientes 
            WHERE curso_id = ? AND cliente_id = ? 
            ORDER BY numero_cuota ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $curso_id, $cedula); // Asume que cedula es string (s)
    $stmt->execute();
    $result = $stmt->get_result();

    $cuotas = array();
    while ($row = $result->fetch_assoc()) {
        $cuotas[] = $row;
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($cuotas);
}
?>