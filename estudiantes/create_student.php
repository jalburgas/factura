<?php
session_start();
include '../db.php';

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
date_default_timezone_set('America/Caracas'); // Establecer la zona horaria
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Estudiante</title>
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
                <a class="nav-link" href="../menu.php">Menú Principal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="generate_id_card.php">Generar Carnet</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Registrar Estudiante</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_student.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cedula">Cédula</label>
                    <input type="text" class="form-control" name="cedula" id="cedula" required>
                </div>
                <div class="form-group">
                    <label for="name">Nombre del Estudiante</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="surname">Apellido del Estudiante</label>
                    <input type="text" class="form-control" name="surname" id="surname" required>
                </div>
                <div class="form-group">
                    <label for="dob">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" name="dob" id="dob" required>
                </div>
                <div class="form-group">
                    <label for="curso_id">Curso</label>
                    <select class="form-control" name="curso_id" id="curso_id" required>
                        <?php
                        include '../db.php';
                        $sql = "SELECT id, nombre FROM cursos";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" class="form-control" name="telefono" id="telefono">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" class="form-control" name="direccion" id="direccion">
                </div>
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="email" class="form-control" name="correo" id="correo">
                </div>
                <div class="form-group">
                    <label for="foto">Foto</label>
                    <div>
                        <video id="video" width="320" height="240" autoplay></video>
                        <button type="button" id="capture" class="btn btn-secondary">Capturar Foto</button>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <input type="hidden" name="foto" id="foto">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar Estudiante</button>
            </form>
        </div>
    </div>

    <h2 class="mt-5">Consultar Estudiantes</h2>
    <table id="studentsTable" class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha de Nacimiento</th>
                <th>Curso</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Correo</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include '../db.php';

            $sql = "SELECT students.id, students.cedula, students.nombre, students.apellido, students.fecha_nacimiento, cursos.nombre AS curso, students.telefono, students.direccion, students.correo, students.foto 
                    FROM students 
                    JOIN cursos ON students.curso_id = cursos.id";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['cedula'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['apellido'] . "</td>";
                echo "<td>" . $row['fecha_nacimiento'] . "</td>";
                echo "<td>" . $row['curso'] . "</td>";
                echo "<td>" . $row['telefono'] . "</td>";
                echo "<td>" . $row['direccion'] . "</td>";
                echo "<td>" . $row['correo'] . "</td>";
                echo "<td><img src='uploads/" . $row['foto'] . "' height='50' width='50'></td>";
                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Exportar a Excel',
                className: 'btn btn-success'
            }
        ]
    });

    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('capture');
    const fotoInput = document.getElementById('foto');

    // Solicitar acceso a la cámara
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error('Error al acceder a la cámara: ', err);
        });

    // Capturar la foto
    captureButton.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convertir la imagen a base64 y asignarla al input oculto
        const dataURL = canvas.toDataURL('image/png');
        fotoInput.value = dataURL;
        alert('Foto capturada correctamente');
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
