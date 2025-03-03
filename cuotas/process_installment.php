<?php
include '../db.php';

if (isset($_POST['curso_id']) && isset($_POST['num_cuotas'])) {
    $curso_id = $_POST['curso_id'];
    $num_cuotas = $_POST['num_cuotas'];

    // Obtener el costo del curso y el monto de la inscripción
    $sql = "SELECT precio, inscrip FROM cursos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $curso_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $curso = $result->fetch_assoc();
    $stmt->close();

    $costo_curso = $curso['precio'];
    $monto_inscripcion = $curso['inscrip']; // Obtener el monto de la inscripción
    $monto_cuota = ($costo_curso - $monto_inscripcion) / $num_cuotas; // Ajustar el monto de las cuotas

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Insertar la cuota inicial con numero_cuota 0 y monto de la inscripción
        $sql = "INSERT INTO cuotas (curso_id, numero_cuota, monto_cuota, fecha_vencimiento) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Cuota inicial (inscripción)
        $numero_cuota_inicial = 0;
        $fecha_vencimiento_inicial = date('Y-m-d'); // Fecha actual para la cuota inicial
        $stmt->bind_param("iids", $curso_id, $numero_cuota_inicial, $monto_inscripcion, $fecha_vencimiento_inicial);
        $stmt->execute();

        // Insertar las cuotas restantes
        for ($i = 1; $i <= $num_cuotas; $i++) {
            $fecha_vencimiento = date('Y-m-d', strtotime("+$i month"));
            $stmt->bind_param("iids", $curso_id, $i, $monto_cuota, $fecha_vencimiento);
            $stmt->execute();
        }

        $stmt->close();

        // Confirmar la transacción
        $conn->commit();

        echo "<script>alert('Cuotas registradas exitosamente.'); window.location.href = 'create_installment.php';</script>";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "<script>alert('Error al registrar las cuotas: " . $e->getMessage() . "'); window.location.href = 'create_installment.php';</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('No se proporcionaron todos los datos necesarios.'); window.location.href = 'create_installment.php';</script>";
}
?>