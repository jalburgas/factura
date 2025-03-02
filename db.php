<?php
//**********************************************************************************************************************************************************************************************
//Juan alburgas 2024-2025
/************************************************
 *                                              *
 *                SisGesa-Edu                   *
 *                                              *
 ************************************************/
//Sistema para la Gestion Administrativa Educativa
//Sistema de Facturacion
//*************************************************************************************************************************************************************************************************

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "factura"; // Asegúrate de que el nombre de la base de datos sea correcto

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

