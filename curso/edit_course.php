<?php
session_start();
include '../db.php';

// Obtener el ID del curso de la URL
$course_id = $_GET['id'];

$query = "SELECT nombre, descripcion, precio FROM cursos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

// Obtener la tasa de cambio actual
$fecha_actual = date("Y-m-d");
$tasa = obtenerTasaCambio($fecha_actual);

function obtenerTasaCambio($fecha) {
    global $conn; // Variable global para la conexión a la base de datos

    // Validar formato de fecha
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) {
        return null; // Formato de fecha inválido
    }

    // Consulta SQL para obtener la tasa de cambio basada en la fecha
    $query = "SELECT tasa FROM tasa_cambio WHERE fecha = ?";
    $stmt = $conn->prepare($query);

    // Verifica si la preparación de la consulta fue exitosa
    if (!$stmt) {
        return null; // Error al preparar la consulta
    }

    // Asigna el parámetro fecha a la consulta
    $stmt->bind_param("s", $fecha); // "s" indica que el parámetro es de tipo string (cadena)

    // Ejecuta la consulta y verifica si fue exitosa
    if (!$stmt->execute()) {
        return null; // Error al ejecutar la consulta
    }

    // Obtiene el resultado de la consulta
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Convierte el resultado a un array asociativo
        $datos = $result->fetch_assoc();
        return $datos['tasa']; // Devuelve solo el valor de la tasa de cambio
    } else {
        return null; // No se encontraron datos para la fecha especificada
    }

    // Cierra la declaración preparada
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">SisGesA-Edu</a>
    <div class="collapse navbar-collapse">
    <ul class="navbar-nav mr-auto">
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <span class="navbar-text">Usuario: <?php echo $_SESSION['username']; ?></span>
                </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-auto">
             <li class="nav-item">
             <a class="nav-link" href="create_course.php">Cursos</a>
         </li>
            <li class="nav-item">
                <a class="nav-link" href="../menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>
    <div class="container mt-5">

        <h2 class="text-center">Editar Curso</h2>
        <div class="card">
            <div class="card-body">

                <form action="update_course.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $course_id; ?>">

                    <div class="form-group">
                        <label for="name">Nombre del Curso:</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?php echo $course['nombre']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción:</label>
                        <textarea class="form-control" name="description" id="description" required><?php echo $course['descripcion']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="precio_dolares">Precio en Dólares:</label>
                        <input type="number" step="any" class="form-control" id="precio_dolares" value="<?php echo ($course['precio'] / $tasa); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Precio en Bolívares:</label>
                        <input type="text" class="form-control" name="price" id="price" value="<?php echo $course['precio']; ?>" readonly>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Actualizar Curso</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tasa = <?php echo json_encode($tasa); ?>;
            
            if (!tasa) {
                console.error('Error al obtener la tasa de cambio');
                return;
            }

            var precioDolaresInput = document.getElementById("precio_dolares");
            var precioBolivaresInput = document.getElementById("price");

            precioDolaresInput.addEventListener('input', function() {
                var precioDolares = parseFloat(precioDolaresInput.value) || 0; // Asegurar que sea un número
                var precioBolivares = precioDolares * tasa;
                precioBolivaresInput.value = precioBolivares.toFixed(2);
            });
        });
    </script>
</body>
</html>