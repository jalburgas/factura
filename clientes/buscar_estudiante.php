<?php
require_once("../db.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    echo "Cédula recibida: " . $cedula; // Verifica la cédula recibida

    // Consultar datos del estudiante
    $sql_estudiante = "SELECT cedula, nombre, apellido, curso_id FROM students WHERE cedula = ?";
    $stmt_estudiante = $conn->prepare($sql_estudiante);
    $stmt_estudiante->bind_param("s", $cedula);
    $stmt_estudiante->execute();
    $result_estudiante = $stmt_estudiante->get_result();

    if ($result_estudiante->num_rows > 0) {
        $studentData = $result_estudiante->fetch_assoc();
        echo json_encode([
            'success' => true,
            'cedula' => $studentData['cedula'],
            'nombre' => $studentData['nombre'],
            'apellido' => $studentData['apellido'],
            'curso_id' => $studentData['curso_id']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt_estudiante->close();
} else {
    echo json_encode(['success' => false]);
}
?>

