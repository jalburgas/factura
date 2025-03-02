<?php
session_start();
include 'db.php';

// Verificar el token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: Token CSRF inválido.");
    }

    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que la nueva contraseña y la confirmación coincidan
    if ($new_password !== $confirm_password) {
        die("Error: Las contraseñas no coinciden.");
    }

    // Hash de la nueva contraseña
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Actualizar la contraseña en la base de datos
    $update_sql = "UPDATE usuarios SET password = ? WHERE username = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    if ($update_stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $update_stmt->bind_param("ss", $hashedPassword, $username); // Vincular parámetros

    // Ejecutar la consulta
    if ($update_stmt->execute()) {
        echo "Contraseña cambiada exitosamente para el usuario: " . htmlspecialchars($username);
    } else {
        echo "Error al cambiar la contraseña: " . $update_stmt->error;
    }

    // Cerrar la declaración y la conexión
    $update_stmt->close();
    $conn->close();
} else {
    die("Método de solicitud no válido.");
}
?>