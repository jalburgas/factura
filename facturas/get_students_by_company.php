<?php
require_once("../db.php");

if(isset($_GET['empresa_id'])) {
    $empresa_id = filter_var($_GET['empresa_id'], FILTER_VALIDATE_INT);
    
    $query = "SELECT id, cedula, nombre 
              FROM students 
              WHERE empresa_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}