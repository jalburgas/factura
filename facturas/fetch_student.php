<?php
session_start();
include '../db.php';
//print_r($_GET);
if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    $query = "SELECT estudiantes.nombre, estudiantes.direccion, cursos.id AS curso_id, cursos.nombre AS curso_nombre 
              FROM estudiantes 
              LEFT JOIN cursos_estudiantes ON estudiantes.id = cursos_estudiantes.estudiante_id 
              LEFT JOIN cursos ON cursos_estudiantes.curso_id = cursos.id 
              WHERE estudiantes.cedula = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentData = [];
    $studentData['cursos'] = [];

    while ($row = $result->fetch_assoc()) {
        if (empty($studentData['nombre'])) {
            $studentData['nombre'] = $row['nombre'];
            $studentData['direccion'] = $row['direccion'];
        }
        $studentData['cursos'][] = ['id' => $row['curso_id'], 'nombre' => $row['curso_nombre']];
    }

    if (!empty($studentData['nombre'])) {
        echo json_encode($studentData);
    } else {
        echo json_encode(null);
    }
}
?>

