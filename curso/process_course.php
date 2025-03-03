<?php
include '../db.php';

// Obtener los datos del formulario
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['precio_dolares'];
$priceinc = $_POST['precio_dolares_inscripcion'];

// Validar que el monto no sea cero
if ($price == 0) {
    echo '<script>alert("El monto es cero. Cargue la tasa."); window.history.back();</script>';
    exit(); // Detener la ejecución del script
}

// Insertar el curso en la tabla cursos
$sql = "INSERT INTO cursos (nombre, descripcion, precio, inscrip) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssdd", $name, $description, $price, $priceinc);

    if ($stmt->execute()) {
        // Redirigir a create_course.php con un mensaje de éxito
        header("Location: create_course.php?success=1");
        exit();
    } else {
        // Redirigir a create_course.php con un mensaje de error
        header("Location: create_course.php?error=1");
        exit();
    }

    $stmt->close();
} else {
    // Redirigir a create_course.php con un mensaje de error
    header("Location: create_course.php?error=1");
    exit();
}

$conn->close();
?>