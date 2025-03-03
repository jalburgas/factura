<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

if (isset($_GET['id'])) {
    $cuota_id = $_GET['id'];

    // Obtener la cuota
    $sql = "SELECT * FROM cuotas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cuota_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cuota = $result->fetch_assoc();
    $stmt->close();

    // Verificar si la cuota existe
    if ($cuota) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $curso_id = $_POST['curso_id'];
            $numero_cuota = $_POST['numero_cuota'];
            $monto_cuota = $_POST['monto_cuota'];
            $fecha_vencimiento = $_POST['fecha_vencimiento'];

            // Actualizar la cuota
            $sql = "UPDATE cuotas SET curso_id = ?, numero_cuota = ?, monto_cuota = ?, fecha_vencimiento = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiisi", $curso_id, $numero_cuota, $monto_cuota, $fecha_vencimiento, $cuota_id);

            if ($stmt->execute()) {
                echo "<script>alert('Cuota actualizada exitosamente.'); window.location.href = 'create_installment.php';</script>";
            } else {
                echo "<script>alert('Error al actualizar la cuota: " . $stmt->error . "'); window.location.href = 'edit_installment.php?id=$cuota_id';</script>";
            }

            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        echo "<script>alert('Cuota no encontrada.'); window.location.href = 'create_installment.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID de cuota no proporcionado.'); window.location.href = 'create_installment.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Cuota</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>

        body {

            background-color: #f8f9fa;

        }

        .container {

            margin-top: 50px;

        }

        .btn-primary {

            background-color: #007bff;

            border-color: #007bff;

        }

    </style>

</head>
<body>
    
    <div class="container">

        <h1 class="text-center">Editar Cuota</h1>

        <form method="POST" action="">

            <div class="form-group">

                <label for="curso_id">ID del Curso:</label>

                <input type="text" class="form-control" id="curso_id" name="curso_id" value="<?php echo $cuota['curso_id']; ?>" required>

            </div>


            <div class="form-group">

                <label for="numero_cuota">Número de Cuota:</label>

                <input type="text" class="form-control" id="numero_cuota" name="numero_cuota" value="<?php echo $cuota['numero_cuota']; ?>" required>

            </div>


            <div class="form-group">

                <label for="monto_cuota">Monto de la Cuota:</label>

                <input type="text" class="form-control" id="monto_cuota" name="monto_cuota" value="<?php echo $cuota['monto_cuota']; ?>" required>

            </div>


            <div class="form-group">

                <label for="fecha_vencimiento">Fecha de Vencimiento:</label>

                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="<?php echo $cuota['fecha_vencimiento']; ?>" required>

            </div>


            <button type="submit" class="btn btn-primary">Actualizar Cuota</button>

            <a href="create_installment.php" class="btn btn-secondary">Volver</a>

        </form>
</body>
</html>
