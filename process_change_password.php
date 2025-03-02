<?php
session_start();
include 'db.php';

// Verificar el token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: Token CSRF inválido.");
    }

    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que la nueva contraseña y la confirmación coincidan
    if ($new_password !== $confirm_password) {
        die("Error: Las contraseñas no coinciden.");
    }

    // Obtener el hash de la contraseña actual del usuario
    $sql = "SELECT password FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña actual
        if (password_verify($current_password, $row['password'])) {
            // Hash de la nueva contraseña
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $update_sql = "UPDATE usuarios SET password = ? WHERE username = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashedPassword, $username);

            if ($update_stmt->execute()) {
                echo "Contraseña cambiada exitosamente.";
            } else {
                echo "Error al cambiar la contraseña: " . $update_stmt->error;
            }

            $update_stmt->close();
        } else {
            die("Error: La contraseña actual es incorrecta.");
        }
    } else {
        die("Error: Usuario no encontrado.");
    }

    $stmt->close();
    $conn->close();
}
?>