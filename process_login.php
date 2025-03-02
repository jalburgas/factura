<?php
//**********************************************************************************************************************************************************************************************
//Juan Alburgas 2024-2025
/************************************************
 *                                              *
 *                SisGesa-Edu                   *
 *                                              *
 ************************************************/
//Sistema para la Gestion Administrativa Educativa
//Sistema de Facturacion
//*************************************************************************************************************************************************************************************************
// Configuración de seguridad de sesión
ini_set('session.cookie_httponly', 1); // Evitar acceso a cookies mediante JavaScript
ini_set('session.use_only_cookies', 1); // Solo permitir el uso de cookies para sesiones
session_start();
include 'db.php';


session_regenerate_id(true); // Regenerar ID de sesión para prevenir fijación de sesión

// Inicializar el contador de intentos si no existe
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Establecer un límite de intentos y un tiempo de bloqueo
$max_attempts = 3;
$lockout_time = 300; // 5 minutos en segundos

// Verificar si el usuario está bloqueado
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    die("Demasiados intentos fallidos. Intenta de nuevo más tarde.");
}

// Verificar el token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Error: Token CSRF inválido.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña usando password_verify
        if (password_verify($password, $row['password'])) { 
            // Inicio de sesión exitoso
            $_SESSION['username'] = $username;
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['login_attempts'] = 0; // Restablecer el contador de intentos
            header("Location: menu.php");
            exit();
        } else {
            // Contraseña incorrecta
            $_SESSION['login_attempts']++;
            echo "Contraseña incorrecta.";
        }
    } else {
        // Usuario no encontrado
        $_SESSION['login_attempts']++;
        echo "Usuario no encontrado.";
    }

    // Verificar si se ha alcanzado el límite de intentos
    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $_SESSION['lockout_time'] = time() + $lockout_time; // Bloquear al usuario
        die("Demasiados intentos fallidos. Intenta de nuevo más tarde.");
    }

    $stmt->close();
    $conn->close();
}
?>