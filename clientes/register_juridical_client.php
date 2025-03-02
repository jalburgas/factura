<?php
// filepath: /c:/xampp/htdocs/factura/clientes/register_juridical_client.php

session_start();
require_once("../db.php");
// print_r($_POST); // Descomentar para ver los datos enviados por el formulario

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Inicializar variables
$message = '';
//print_r($_POST); // Descomentar para ver los datos enviados por el formulario
// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $rif = $_POST['rif'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
   // $cedulaEstudiante = $_POST['cedulaEstudiante'] ?? '';
    $cant = $_POST['cant'] ?? '';
    $curso_id = $_POST['curso_id'] ?? '';

    // Insertar el cliente jurídico en la base de datos con los datos del estudiante
    $query_cliente = "INSERT INTO clientes_juridicos (nombre, rif, direccion, telefono, email,curso_id,cant_alumnos) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt_cliente = $conn->prepare($query_cliente)) {
        $stmt_cliente->bind_param("ssssssi", $nombre, $rif, $direccion, $telefono, $email,$curso_id,$cant);
        if ($stmt_cliente->execute()) {
            $message = "Cliente jurídico y estudiante registrados exitosamente.";

    // Consultar las cuotas asociadas al curso del estudiante
    $sql_cuotas = "SELECT id, curso_id, numero_cuota, monto_cuota, fecha_vencimiento 
                   FROM facturas.cuotas 
                   WHERE curso_id = ?";
    $stmt_cuotas = $conn->prepare($sql_cuotas);
    $stmt_cuotas->bind_param("i", $curso_id);
    $stmt_cuotas->execute();
    $result_cuotas = $stmt_cuotas->get_result();

   // print_r( $result_cuotas);
    // Insertar las cuotas pendientes para el estudiante
    while ($cuota = $result_cuotas->fetch_assoc()) {
        $sql_insert_cuota_pendiente = "INSERT INTO facturas.cuotas_pendientes 
                                       (curso_id, cliente_id, factura_id, estado_pago, monto, fecha_vencimiento, numero_cuota, cant_alumnos) 
                                       VALUES (?, ?, NULL, 0, ?, ?, ?, ?)";
                                       $monto = $cant * $cuota['monto_cuota'];      
        $stmt_insert_cuota = $conn->prepare($sql_insert_cuota_pendiente);
        $stmt_insert_cuota->bind_param("isssii", $cuota['curso_id'], $rif ,  $monto, $cuota['fecha_vencimiento'], $cuota['numero_cuota'], $cant);
        $stmt_insert_cuota->execute();
        $stmt_insert_cuota->close();
    }

   
        } else {
            $message = "Error al registrar el cliente jurídico: " . $stmt_cliente->error;
        }
        $stmt_cliente->close();
    } else {
        $message = "Error al preparar la consulta del cliente: " . $conn->error;
    }
}

// Obtener los datos de los clientes jurídicos
$query = "SELECT * FROM clientes_juridicos";
$result = $conn->query($query);
$clientes_juridicos = [];
while ($row = $result->fetch_assoc()) {
    $clientes_juridicos[] = $row;
}

// Incluir el archivo de la vista
include("register_juridical_client_view.php");
?>
