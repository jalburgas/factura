<?php
// Incluir la conexión a la base de datos
require_once("../db.php");

// Datos del formulario
$fecha = $_POST['fecha']; // Asegúrate de validar y sanitizar esta entrada
$tasa = $_POST['tasa'];   // Asegúrate de validar y sanitizar esta entrada

// 1. Verificar si la fecha ya está registrada
$sql_check = "SELECT fecha FROM tasa_cambio WHERE fecha = '$fecha'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Si la fecha ya está registrada, muestra un mensaje con un botón para regresar
    echo "La tasa para la fecha $fecha ya fue registrada.<br><br>";
    echo "<a href='tasa.php'><button style='background-color: #007BFF; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Regresar</button></a>";
} else {
    // Si la fecha no está registrada, inserta el nuevo registro
    $sql_insert = "INSERT INTO tasa_cambio (fecha, tasa) VALUES ('$fecha', '$tasa')";

    if ($conn->query($sql_insert) === TRUE) {
        // Si la inserción fue exitosa, redirige a tasa.php
        header("Location: tasa.php");
        exit();
    } else {
        // Si hubo un error en la inserción, muestra el mensaje de error
        echo "Error al registrar la tasa: " . $conn->error;
    }
}

$conn->close(); // Cierra la conexión a la base de datos
?>