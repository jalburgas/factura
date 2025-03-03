<?php
session_start();
include '../db.php';

// Obtener los datos del formulario
$course_id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price']; // Assume this is already converted to Bolívares

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Actualizar los detalles del curso
    $query = "UPDATE cursos SET nombre = ?, descripcion = ?, precio = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $name, $description, $price, $course_id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Obtener la cantidad de cuotas para el curso
    $query = "SELECT COUNT(*) AS cantidad_cuotas FROM cuotas WHERE curso_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cantidad_cuotas = $row['cantidad_cuotas'];

    // Calcular el nuevo monto de cada cuota
    $monto_cuota = $price / $cantidad_cuotas;

    // Actualizar los montos de las cuotas
    $query = "UPDATE cuotas SET monto_cuota = ? WHERE curso_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("di", $monto_cuota, $course_id);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Confirmar la transacción
    $conn->commit();
    echo "Curso y cuotas actualizados exitosamente.";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?>

<!-- Redirigir de vuelta a la página de visualización -->
<meta http-equiv="refresh" content="2;url=../edit_course.php">
