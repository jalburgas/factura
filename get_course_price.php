<?php
include 'db.php';

$curso_id = $_GET['curso_id'];
$sql = "SELECT price FROM courses WHERE id = $curso_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["price" => $row['price']]);
} else {
    echo json_encode(["price" => 0]);
}

$conn->close();
?>
