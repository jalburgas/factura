<?php

session_start();
//print_r( $_SESSION ); 
//print_r($_POST);exit();
require_once("../db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cedula = $_POST['cedula'];
    $control = $_POST['nrocontrol'];
    $fecha = $_POST['fecha'];
    $curso_id = $_POST['curso_id'];
    $productos = $_POST['productos'];
    $cantidades = $_POST['cantidades'];
    $precios = $_POST['precios'];
    $iva_si_no = $_POST['iva_si_no'];
    $usuario = $_SESSION['username'];
    $iva = ($iva_si_no == 1) ? 16 : 0;    
    $banco = $_POST['cuenta_contable'];
  // Verificar que la fecha no esté vacía y sea válida
  if (empty($fecha) || !strtotime($fecha)) {
    die("Error: Fecha no válida.");
}
   // Convertir la fecha al formato correcto
   $fecha = date('Y-m-d H:i:s', strtotime($fecha));
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
    $query = "INSERT INTO facturas (nrocontol, cliente_id, fecha, curso_id, usuario, sub_total, iva, total_factura) VALUES (?,?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssdii", $control, $cedula, $fecha, $curso_id, $usuario, $sub_total, $IVA, $total_factura);
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
    //******************************************************************************************************************************************************* */

   //Insertar los asientos contables
    //******************************************************************************************************************************************************* */    
    $query_asientos = "INSERT INTO asientos_contables (fecha, cuenta_id, descripcion, debe, haber, gasto_id, factura_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_asientos = $conn->prepare($query_asientos);

    // Asiento contable para el sub_total
    $cuenta_id = 40100; // Suponiendo que 1 es la cuenta de ingresos
    $descripcion = 'Ingreso por factura';
    $debe = 0;
    $haber = $sub_total;
    $gasto_id = null;
    $stmt_asientos->bind_param("sisddii", $fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id, $factura_id);
    $stmt_asientos->execute();

     // Asiento contable para el IVA
$cuenta_id = 20500; // Suponiendo que 2 es la cuenta de IVA
$descripcion = 'IVA por factura';
$debe = 0;
$haber = $IVA;
$stmt_asientos->bind_param("sisddii", $fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id, $factura_id);
$stmt_asientos->execute();

$query_asientos = "INSERT INTO asientos_contables (fecha, cuenta_id, descripcion, debe, haber, gasto_id, factura_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_asientos = $conn->prepare($query_asientos);

// Asiento contable para el sub_total ENTRADA A BANCOS
$cuenta_id =  $banco; // Suponiendo que 1 es la cuenta de ingresos
$descripcion = 'Ingreso por factura';
$debe = $sub_total;
$haber = 0;
$gasto_id = null;
$stmt_asientos->bind_param("sisddii", $fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id, $factura_id);
$stmt_asientos->execute();
     // Asiento contable para el IVA ENTRADA BANCOS
     $cuenta_id =  $banco; // Suponiendo que 2 es la cuenta de IVA
     $descripcion = 'IVA por factura';
     $debe = $IVA;
     $haber = 0;
     $stmt_asientos->bind_param("sisddii", $fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id, $factura_id);
     $stmt_asientos->execute();
     
     $query_asientos = "INSERT INTO asientos_contables (fecha, cuenta_id, descripcion, debe, haber, gasto_id, factura_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
     $stmt_asientos = $conn->prepare($query_asientos);






    header("Location: create_invoice.php?message=Factura generada exitosamente.");
    exit();
}
