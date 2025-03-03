<?php
include '../db.php';

$sql = "SELECT id, nombre, precio FROM cursos";
$result = $conn->query($sql);

$cursos = array();
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($cursos);
?>