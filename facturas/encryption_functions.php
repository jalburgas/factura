<?php
// Clave secreta para la encriptación (debe ser la misma para encriptar y desencriptar)
define('SECRET_KEY', 'tu_clave_secreta_aqui'); // Cambia esto por una clave segura
define('SECRET_IV', '1234567890123456'); // IV de 16 bytes (16 caracteres)

// Función para encriptar
function encrypt($data) {
    $output = openssl_encrypt(
        $data,
        'AES-256-CBC',
        SECRET_KEY,
        0,
        SECRET_IV
    );
    return base64_encode($output); // Codificar en Base64 para que sea seguro en la URL
}

// Función para desencriptar
function decrypt($data) {
    $data = base64_decode($data); // Decodificar desde Base64
    return openssl_decrypt(
        $data,
        'AES-256-CBC',
        SECRET_KEY,
        0,
        SECRET_IV
    );
}
?>