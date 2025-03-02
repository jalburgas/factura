<?php
include 'db.php';

$sql = "SELECT students.id, students.cedula, students.name, students.surname, students.dob, courses.name AS curso_id, students.telefono, students.direccion, students.correo, students.foto FROM students JOIN courses ON students.curso_id = courses.id";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(["data" => $data]);

$conn->close();
?>

