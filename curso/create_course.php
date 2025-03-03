<?php
//**********************************************************************************************************************************************************************************************
//Juan Alburgas 2024-2025
/************************************************
 *                                              *
 *                SisGesa-Edu                   *
 *                                              *
 ************************************************/
//Sistema para la Gestión Administrativa Educativa
//Sistema de Facturación
//*************************************************************************************************************************************************************************************************

session_start();
include '../db.php';

// Verificar si hay un mensaje de éxito o error
if (isset($_GET['success'])) { // Corregido: Paréntesis de cierre añadido
    echo '<script>alert("Curso registrado exitosamente.");</script>';
} elseif (isset($_GET['error'])) { // Corregido: Paréntesis de cierre añadido
    echo '<script>alert("Error al registrar el curso.");</script>';
}

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria

// Obtener la fecha actual en el formato Y-m-d (Año-Mes-Día)
$fecha_actual = date("Y-m-d");

// Función para obtener la tasa de cambio según la fecha
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
        echo '<script>alert("Debe cargar la tasa");</script>'; // Muestra mensaje de JavaScript
        return null; // Retorna null después de mostrar el mensaje
    }

    // Cierra la declaración preparada
    $stmt->close();
}

$tasa = obtenerTasaCambio($fecha_actual);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Curso</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tasa = <?php echo json_encode($tasa); ?>;

            if (!tasa) {
                console.error('Error al obtener la tasa de cambio');
                return;
            }

            var precioDolaresInput = document.getElementById("precio_dolares");
            var precioBolivaresInput = document.getElementById("price");
            var precioDolaresInscripcionInput = document.getElementById("precio_dolares_inscripcion");
            var precioBolivaresInscripcionInput = document.getElementById("priceinc");

            // Conversión para el precio del curso
            precioDolaresInput.addEventListener('input', function() {
                var precioDolares = parseFloat(precioDolaresInput.value) || 0; // Asegurar que sea un número
                var precioBolivares = precioDolares * tasa;
                precioBolivaresInput.value = precioBolivares.toFixed(2);
            });

            // Conversión para el precio de inscripción
            precioDolaresInscripcionInput.addEventListener('input', function() {
                var precioDolaresInscripcion = parseFloat(precioDolaresInscripcionInput.value) || 0; // Asegurar que sea un número
                var precioBolivaresInscripcion = precioDolaresInscripcion * tasa;
                precioBolivaresInscripcionInput.value = precioBolivaresInscripcion.toFixed(2);
            });
        });
    </script>
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
                <a class="nav-link" href="../menu.php">Menú Principal</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Registrar Curso</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_course.php" method="post">
                <div class="form-group">
                    <label for="name">Nombre del Curso:</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea class="form-control" name="description" id="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="precio_dolares">Precio en Dólares:</label>
                    <input type="number" step="0.01" class="form-control" name="precio_dolares" id="precio_dolares" required>
                </div>
                <div class="form-group">
                    <label for="price">Precio en Bolívares:</label>
                    <input type="text" class="form-control" name="price" id="price" readonly>
                </div>
                <div class="form-group">
                    <label for="precio_dolares_inscripcion">Precio en Dólares Inscripción:</label>
                    <input type="number" step="0.01" class="form-control" name="precio_dolares_inscripcion" id="precio_dolares_inscripcion" required>
                </div>
                <div class="form-group">
                    <label for="priceinc">Precio en Bolívares Inscripción:</label>
                    <input type="text" class="form-control" name="priceinc" id="priceinc" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Curso</button>
            </form>
        </div>
    </div>

    <h2 class="mt-5">Consultar Cursos</h2>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../db.php';

            $sql = "SELECT id, nombre, descripcion, precio FROM cursos";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['descripcion'] . "</td>";
                echo "<td>" . $row['precio'] . "</td>";
                echo "<td><a href='edit_course.php?id=" . $row['id'] . "' class='btn btn-warning'>Editar</a></td>";
                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>