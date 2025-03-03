<?php
session_start();
include '../db.php';

// Desactivar la visualización de warnings
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$empresa_sql = "SELECT nombre, logo FROM empresa LIMIT 1";
$empresa_result = $conn->query($empresa_sql);
$empresa = $empresa_result->fetch_assoc();

$student = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $student_sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($student_sql);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Carnet de Estudiante</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .id-card {
            width: 3.37in; /* Ancho estándar de un carnet de identificación */
            height: 2.13in; /* Alto estándar de un carnet de identificación */
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            margin: 0 auto;
            font-size: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background: #fff; /* Fondo blanco */
            background-image: url('path/to/academic_background.png'); /* Imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.9; /* Transparencia */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .id-card .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .id-card .logo, .id-card .student-photo {
            width: 0.8in; /* Reducir el tamaño de las imágenes */
            height: 0.8in; /* Hacer las imágenes cuadradas */
            border: 2px solid #000;
        }
        .id-card h2 {
            margin: 5px 0;
            font-size: 16px; /* Ajustar el tamaño del texto */
            color: #333;
        }
        .id-card h3 {
            margin: 5px 0;
            font-size: 14px; /* Ajustar el tamaño del texto */
            color: #333;
        }
        .id-card p {
            margin: 2px 0;
            color: #555;
        }
        .id-card .footer {
            margin-top: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    
    <div class="card mb-4">
        <div class="card-body">
            <form action="generate_id_card.php" method="get">
                <div class="form-group">
                    <label for="student_id">Seleccione el Estudiante</label>
                    <select class="form-control" name="student_id" id="student_id" required>
                        <?php
                        include '../db.php';
                        $sql = "SELECT id, nombre, apellido FROM students";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . " " . $row['apellido'] . "</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generar Carnet</button>
            </form>
        </div>
    </div>

    <?php if (isset($student)): ?>
    <div class="id-card">
        <div class="header">
            <img src="../empresa/uploads/<?php echo $empresa['logo']; ?>" alt="Logo de la Empresa" class="logo">
            <img src="uploads/<?php echo $student['foto']; ?>" alt="Foto del Estudiante" class="student-photo">
        </div>
        <h2><?php echo $empresa['nombre']; ?></h2>
        <h3><?php echo $student['nombre'] . " " . $student['apellido']; ?> - Estudiante</h3>
        <p><strong>Cédula:</strong> <?php echo $student['cedula']; ?></p>        
        <p><strong>Curso:</strong> <?php echo $student['curso']; ?></p>
        
            <p>Válido hasta: <?php echo date('d/m/Y', strtotime('+1 year')); ?></p>
        
    </div>
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success">Imprimir Carnet</button>
    </div>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>