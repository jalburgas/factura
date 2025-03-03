<?php
session_start();
require_once("../db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cedula = $_POST['cedula'];
    $fecha = $_POST['fecha'];
    $curso_id = $_POST['curso_id'];
    $productos = $_POST['productos'];
    $cantidades = $_POST['cantidades'];
    $precios = $_POST['precios'];
    $iva_si_no = $_POST['iva_si_no'];
    $usuario = $_SESSION['username'];
    $iva = ($iva_si_no == 1) ? 16 : 0;

    // Verificar que los arrays tengan el mismo tamaño
    if (count($productos) !== count($cantidades) || count($productos) !== count($precios)) {
        die("Error: Los arrays de productos, cantidades y precios deben tener el mismo tamaño.");
    }

   // Verificar si ya existe un registro en cuotas_pendientes con el mismo cliente_id y curso_id
$query_check_curso = "SELECT estado_pago FROM cuotas_pendientes 
WHERE cliente_id = ? AND curso_id = ? AND numero_cuota = ?";
$stmt_check_curso = $conn->prepare($query_check_curso);

// Asegurar que los tipos de datos coincidan con la estructura de la tabla
// Suponiendo que:
// - cliente_id es VARCHAR -> 's'
// - curso_id es INT -> 'i'
// - numero_cuota es INT -> 'i'
$stmt_check_curso->bind_param("sii", $cedula, $curso_id, $numero_cuota); // 1. Nombre de variable corregido
$stmt_check_curso->execute();
$result_check_curso = $stmt_check_curso->get_result();

if ($result_check_curso->num_rows > 0) {
$row_check_curso = $result_check_curso->fetch_assoc();
$estado_pago = $row_check_curso['estado_pago'];

if ($estado_pago == 1) {
die("Error: Ya existe una cuota pagada para este cliente, curso y número de cuota.");
}
// Si estado_pago es 0, continuar con el proceso
}

    // Obtener la última tasa de cambio registrada
    $query_tasa = "SELECT tasa FROM tasa_cambio ORDER BY fecha DESC LIMIT 1";
    $stmt_tasa = $conn->prepare($query_tasa);
    $stmt_tasa->execute();
    $result_tasa = $stmt_tasa->get_result();

    if ($result_tasa->num_rows > 0) {
        $tasa = $result_tasa->fetch_assoc()['tasa'];
    } else {
        die("Error: No se encontró ninguna tasa de cambio registrada.");
    }

    // Calcular sub_total y total_factura
    $sub_total = 0;
    for ($i = 0; $i < count($productos); $i++) {
        $precio_en_bolivares = $precios[$i] * $tasa;
        $sub_total += $cantidades[$i] * $precio_en_bolivares;
    }

    $total_factura = $sub_total + ($sub_total * $iva / 100);
    $IVA = ($sub_total * $iva / 100);

    // Insertar la factura
    $query = "INSERT INTO facturas (cliente_id, fecha, curso_id, usuario, sub_total, iva, total_factura) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdii", $cedula, $fecha, $curso_id, $usuario, $sub_total, $IVA, $total_factura);
    $stmt->execute();
    $factura_id = $stmt->insert_id;

    // Insertar los productos de la factura
    $query = "INSERT INTO factura_productos (factura_id, producto, cantidad, precio) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    for ($i = 0; $i < count($productos); $i++) {
        $producto = $productos[$i];
        $cantidad = $cantidades[$i];
        $precio_en_bolivares = $precios[$i] * $tasa;
        $stmt->bind_param("isid", $factura_id, $producto, $cantidad, $precio_en_bolivares);
        $stmt->execute();

        // Actualizar cuotas_pendientes cuando el producto coincida con numero_cuota
        $query_update_cuotas = "UPDATE cuotas_pendientes 
                                SET factura_id = ?, estado_pago = ?, fecha_pago = ? 
                                WHERE cliente_id = ? AND numero_cuota = ?";
        $stmt_update_cuotas = $conn->prepare($query_update_cuotas);
        $estado_pago = 1; // Pagado
        $stmt_update_cuotas->bind_param("iissi", $factura_id, $estado_pago, $fecha, $cedula, $producto);
        $stmt_update_cuotas->execute();
    }

    header("Location: create_invoice.php?message=Factura generada exitosamente.");
    exit();
}
?>