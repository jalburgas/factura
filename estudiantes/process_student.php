<?php
include '../db.php';

$cedula = $_POST['cedula'];
$nombre = $_POST['name'];
$apellido = $_POST['surname'];
$dob = $_POST['dob'];
$curso_id = $_POST['curso_id'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$correo = $_POST['correo'];
$foto_base64 = $_POST['foto'];

// Verificar y crear la carpeta uploads si no existe
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Decodificar la imagen base64
list($type, $foto_base64) = explode(';', $foto_base64);
list(, $foto_base64) = explode(',', $foto_base64);
$foto_data = base64_decode($foto_base64);

// Generar un nombre único para la imagen
$foto_nombre = uniqid() . '.png';

// Guardar la imagen en la carpeta 'uploads'
file_put_contents('uploads/' . $foto_nombre, $foto_data);

// Insertar los datos del estudiante en la base de datos
$sql = "INSERT INTO students (cedula, nombre, apellido, fecha_nacimiento, curso_id, telefono, direccion, correo, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $cedula, $nombre, $apellido, $dob, $curso_id, $telefono, $direccion, $correo, $foto_nombre);

if ($stmt->execute()) {
    echo "Estudiante registrado exitosamente";

    // Obtener el ID del estudiante recién insertado
    //$cliente_id = $stmt->insert_id;

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
                                       (curso_id, cliente_id, factura_id, estado_pago, monto, fecha_vencimiento, numero_cuota) 
                                       VALUES (?, ?, NULL, 0, ?, ?, ?)";
        $stmt_insert_cuota = $conn->prepare($sql_insert_cuota_pendiente);
        $stmt_insert_cuota->bind_param("isdsi", $cuota['curso_id'], $cedula, $cuota['monto_cuota'], $cuota['fecha_vencimiento'], $cuota['numero_cuota']);
        $stmt_insert_cuota->execute();
        $stmt_insert_cuota->close();
    }

    $stmt_cuotas->close();
} else {
    echo "Error al registrar el estudiante: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: create_student.php");
exit();
?>

<a href="create_student.php">Volver</a>
<a href="index.php">Menú Principal</a>