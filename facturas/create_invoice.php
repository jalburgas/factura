<?php
//**********************************************************************************************************************************************************************************************
//Juan alburgas 2024
/************************************************
 *                                              *
 *                SisGesa-Edu                   *
 *                                              *
 ************************************************/
//Sistema para la Gestion Administrativa Educativa
//Sistema de Facturacion
//*************************************************************************************************************************************************************************************************


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
session_start();
//print_r($_SESSION);
require_once("../db.php");
date_default_timezone_set('America/New_York');
$studentData = null;
$clientData = null;
$cedula = '';
$message = '';
$fechaActual = date('Y-m-d'); // Obtener la fecha actual

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
    $_SESSION['cedula'] = $cedula; // Guardar el valor de la cédula en la sesión 
    $studentData = fetchStudentData($cedula);
    $clientData = fetchClientData($cedula);
}

function fetchStudentData($cedula)
{
    global $conn;
    $cadSQL = "SELECT students.cedula as identificador, students.nombre, students.apellido, students.direccion, students.correo,cursos.id AS curso_id, cursos.nombre AS curso_nombre
               FROM students 
               LEFT JOIN cursos ON students.curso_id = cursos.id 
               WHERE students.cedula = ?";
    $stmt = $conn->prepare($cadSQL);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentData = [];
    $studentData['cursos'] = [];

    while ($row = $result->fetch_assoc()) {
        if (empty($studentData['nombre'])) {
            $studentData['identificador'] = $row['identificador'];
            $studentData['nombre'] = $row['nombre'];
            $studentData['apellido'] = $row['apellido'] ?? ''; // Establecer apellido como vacío si no está definido
            $studentData['direccion'] = $row['direccion'];
            $studentData['correo'] = $row['correo'];
        }
        if (isset($row['curso_id'])) {
            $studentData['cursos'][] = ['id' => $row['curso_id'], 'nombre' => $row['curso_nombre']];
            $_SESSION['curso_id'] = $row['curso_id']; // Guardar el curso en la sesión
        }
    }

    $stmt->close();
    return $studentData;
}

// 22-01-2024 10:31 pm Andres Hurtado - La consulta sql no tenia un left join para consultar los curso en la tabla cursos
function fetchClientData($cedula)
{
    global $conn;
    $cadSQL = "SELECT cj.rif as identificador, cj.nombre, cj.rif, cj.direccion, cj.telefono, cj.email, c.id AS curso_id, c.nombre AS curso_nombre
               FROM clientes_juridicos cj
               LEFT JOIN cursos c ON cj.curso_id = c.id
               WHERE cj.rif = ?;";
    // echo $cadSQL; exit();
    $stmt = $conn->prepare($cadSQL);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    $clientData = [];
    $clientData['cursos'] = [];

    while ($row = $result->fetch_assoc()) {
        if (empty($clientData['nombre'])) {
            $clientData['identificador'] = $row['identificador'];
            $clientData['nombre'] = $row['nombre'];
            $clientData['rif'] = $row['rif'] ?? '';
            $clientData['direccion'] = $row['direccion'];
            $clientData['telefono'] = $row['telefono'];
            $clientData['email'] = $row['email'];
        }
        if (isset($row['curso_id'])) {
            $clientData['cursos'][] = ['id' => $row['curso_id'], 'nombre' => $row['curso_nombre']];
            $_SESSION['curso_id'] = $row['curso_id']; // Guardar el curso en la sesión
        }
    }

    $stmt->close();
    return $clientData;
}
function fetchFacturas()
{
    global $conn;
    $query = "SELECT DISTINCT 
    facturas.id, 
    CASE 
        WHEN clientes_juridicos.rif IS NOT NULL THEN clientes_juridicos.nombre
        ELSE students.nombre
    END AS cliente_nombre, 
    facturas.fecha, 
    cursos.nombre AS curso, 
    facturas.usuario
FROM facturas
LEFT JOIN students ON facturas.cliente_id = students.cedula
LEFT JOIN clientes_juridicos ON facturas.cliente_id = clientes_juridicos.rif
LEFT JOIN cursos ON facturas.curso_id = cursos.id
ORDER BY facturas.id DESC;";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener las cuotas
// function fetchCuotas()
// {
//     global $conn;
//     // Realizar la consulta SQL directamente
//     $query = "SELECT id, curso_id, numero_cuota, monto_cuota, fecha_vencimiento FROM cuotas";
//     $stmt = $conn->prepare($query);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // Inicializar el array de cuotas
//     $cuotas = [];

//     // Recorrer los resultados de la consulta
//     while ($row = $result->fetch_assoc()) {
//         $cuotas[] = $row;
//     }

//     // Cerrar la declaración
//     $stmt->close();
//     return $cuotas;
// }

// // // Obtener las cuotas y pasarlas a JavaScript
// $cuotas = fetchCuotas();
// $cuotas_json = json_encode($cuotas);

$facturas = fetchFacturas();

function obtenerPrecioCuota($numeroCuota, $idCurso)
{
    global $conn; // Utiliza la conexión a la base de datos global

    // Define la consulta SQL para obtener el monto de la cuota basado en el número de cuota y el ID del curso
    $query = "SELECT monto_cuota FROM cuotas WHERE numero_cuota = $numeroCuota AND curso_id = $idCurso";
    echo $query;
    exit(); // Muestra la consulta y termina la ejecución del script (para depuración)

    // Prepara la consulta SQL
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        // Si hay un error al preparar la consulta, devuelve un mensaje de error en formato JSON
        echo json_encode(array("precio" => 0, "error" => "Error preparing statement"));
        return;
    }

    // Asigna los valores de $numeroCuota y $idCurso a los marcadores de posición en la consulta
    $stmt->bind_param("ii", $numeroCuota, $idCurso);

    // Ejecuta la consulta SQL
    if (!$stmt->execute()) {
        // Si hay un error al ejecutar la consulta, devuelve un mensaje de error en formato JSON
        echo json_encode(array("precio" => 0, "error" => "Error executing statement"));
        return;
    }

    // Obtiene el resultado de la consulta
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Si se encontraron resultados, convierte el resultado a un array asociativo
        $row = $result->fetch_assoc();
        // Devuelve el precio de la cuota en formato JSON
        echo json_encode(array("precio" => $row['monto_cuota']));
    } else {
        // Si no se encontraron resultados, devuelve un mensaje indicando que no se encontraron datos
        echo json_encode(array("precio" => 0, "error" => "No data found"));
    }
    // Cierra la declaración preparada
    $stmt->close();
}

function generarComboCuentasBancarias()
{
    global $conn; // Usamos la conexión de db.php

    // Consulta filtrada por rango de cuentas bancarias
    $sql = "SELECT 
                numero_cuenta, 
                nombre 
            FROM cuentas_contables
            WHERE numero_cuenta BETWEEN 110102 AND 110120
            ORDER BY numero_cuenta";

    $result = $conn->query($sql);

    // Iniciamos el combo selector
    echo '<select name="cuenta_contable" id="cuenta_contable" class="form-control" required>';
    echo '<option value="">Seleccione una cuenta...</option>';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $numero_cuenta = htmlspecialchars($row['numero_cuenta']);
            $nombre = htmlspecialchars($row['nombre']);

            // Formatear texto para mostrar (todas están en el rango)
            $texto_mostrar = "Banco_cuenta_contable - $numero_cuenta - $nombre";

            echo "<option value='$numero_cuenta'>$texto_mostrar</option>";
        }
    } else {
        echo '<option value="">No hay cuentas bancarias disponibles</option>';
    }

    echo '</select>';
}
// Verifica si los parámetros `numeroCuota` e `idCurso` están presentes en la solicitud GET
if (isset($_GET['numeroCuota']) && isset($_GET['idCurso'])) {
    $numeroCuota = intval($_GET['numeroCuota']); // Convierte `numeroCuota` a un entero
    $idCurso = intval($_GET['idCurso']); // Convierte `idCurso` a un entero
    obtenerPrecioCuota($numeroCuota, $idCurso); // Llama a la función `obtenerPrecioCuota` con los parámetros proporcionados
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

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Factura</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important;
            font-weight: bold;
        }

        .inputverde {
            background-color: #28a745 !important;
            color: white !important;
            border-radius: 10px;
            /* Ajusta el valor a tu preferencia */
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Gestión de Facturación Educativa</a>
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
        <h2 class="text-center">Factura</h2>
        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

            <div class="form-group">
                <label for="cedula">Cédula del Estudiante o RIF del Cliente Jurídico:</label>
                <input type="text" name="cedula" id="cedula" class="form-control" required value="<?php echo isset($cedula) ? $cedula : ''; ?>">
            </div>

        </form>

        <form action="process_invoice.php" method="post">
            <input type="hidden" name="cedula" value="<?php echo $cedula; ?>">
            <!-- 24-01-25 S.L modufucacion de validacion  -->
            <?php if (is_array($studentData) && count($studentData) > 1) { ?>
                <div class="form-group">
                    <label for="fecha">Numero Control:</label>
                    <input type="text" id="nrocontrol" name="nrocontrol" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label for="nombre1">Nombre:</label>
                    <input type="text" id="nombre1" class="form-control" value="<?php echo isset($studentData['nombre']) ? $studentData['nombre'] : ''; ?> <?php echo isset($studentData['apellido']) ? $studentData['apellido'] : ''; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="direccion1">Dirección:</label>
                    <input type="text" id="direccion1" class="form-control" value="<?php echo isset($studentData['direccion']) ? $studentData['direccion'] : ''; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" class="form-control" value="<?php echo isset($studentData['correo']) ? $studentData['correo'] : ''; ?>" disabled>
                </div>
            <?php } elseif (is_array($clientData) && count($clientData) > 1) { ?>
                <div class="form-group">
                    <label for="fecha">Numero Control:</label>
                    <input type="text" id="nrocontrol" name="nrocontrol" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label for="nombre2">Nombre:</label>
                    <input type="text" id="nombre2" class="form-control" value="<?php echo $clientData['nombre']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="direccion2">Dirección:</label>
                    <input type="text" id="direccion2" class="form-control" value="<?php echo $clientData['direccion']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="rif">RIF:</label>
                    <input type="text" id="rif" class="form-control" value="<?php echo $clientData['rif']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" class="form-control" value="<?php echo $clientData['telefono']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" class="form-control" value="<?php echo $clientData['email']; ?>" disabled>
                </div>
            <?php } ?>
            <div class="form-group">
                <label for="fecha">Fecha y hora:</label>
                <input type="datetime" id="fecha" name="fecha" class="form-control" value="<?php echo date('d-m-Y H:i:s'); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="curso">Curso:</label>
                <?php if (!empty($studentData['cursos'])): ?>
                    <?php foreach ($studentData['cursos'] as $curso): ?>
                        <input type="text" id="curso" name="curso" class="form-control" value="<?php echo $curso['nombre']; ?>" readonly>
                        <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($clientData['cursos'])): ?>
                    <?php foreach ($clientData['cursos'] as $curso): ?>
                        <input type="text" id="curso" name="curso" class="form-control" value="<?php echo $curso['nombre']; ?>" readonly>
                        <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="checkbox">IVA 16%:</label>
                <input type="checkbox" id="checkbox" name="iva_si_no" value="1" checked>
            </div>
            <div class="form-group">
                <label>Cuenta Contable Bancaria:</label>
                <?php generarComboCuentasBancarias(); ?>
            </div>

            <div class="form-group">
                <label for="totalPagado">Total Sin IVA:</label>
                <input type="text" id="totalPagado" class="inputverde" readonly>
                <label for="totaliva"> IVA:</label>
                <input type="text" id="totaliva" class="inputverde" readonly>
                <label for="total"> Total a Pagar:</label>
                <input type="text" id="total" class="inputverde" readonly>
            </div>

            <div class="form-group">
                <div id="productos">
                    <!-- Campos dinámicos para productos, precios y cantidades -->
                </div>
                <button type="button" class="btn btn-success" onclick="agregarProducto()">Agregar Cuota a Pagar</button>
            </div>
            <button type="submit" class="btn btn-primary">Generar Factura</button>
        </form>

        <form>
            <h2 class="text-center mt-5">Facturas Generadas</h2>
            <table id="facturasTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudiante</th>
                        <th>Fecha</th>
                        <th>Curso</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $factura): ?>
                        <tr>
                            <td><?php echo $factura['id']; ?></td>
                            <td><?php echo $factura['cliente_nombre']; ?></td>
                            <td><?php echo $factura['fecha']; ?></td>
                            <td><?php echo $factura['curso']; ?></td>
                            <td><?php echo $factura['usuario']; ?></td>
                            <td>
                                <a href="print_invoice.php?id=<?php echo $factura['id']; ?>" class="btn btn-primary">Imprimir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <a href="daily_report.php" class="btn btn-info">Ver Reporte Diario</a>
            </div>
    </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var dataCurso = [];
        let countAgregarCuota = 0;
        let totalPagado = 0;

        $(document).ready(function() {
            $('#facturasTable').DataTable({
                "order": [] // Deja el ordenamiento a la consulta SQL
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cursoInput = document.querySelector('input[name="curso_id"]');
            const cedulaInput = document.querySelector('input[name="cedula"]');
            if (cursoInput) {
                const cursoId = cursoInput.value;
                const cedulaId = cedulaInput.value;
                fetchCuotas(cursoId, cedulaId);
            } else {
                console.error('Input element with name "curso_id" not found');
            }
        });

        function fetchCuotas(cursoId, cedula) {
            console.log("Fetching cuotas with:", { cursoId, cedula }); // 👈 Verifica parámetros

            fetch(`fetch_cuotas.php?curso_id=${cursoId}&cedula=${cedula}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json()
                })
                .then(data => {
                    console.log("Datos recibidos del SQL:", data); // 👈 Muestra los datos en consola
                    dataCurso = data;
                })
                .catch(error => {
                    console.error('Error al obtener el precio de la cuota:', error);
                    document.querySelector('#precioCuota').value = 0;
                });
        }

        function agregarProducto() {
            countAgregarCuota += 1;
            const productosDiv = document.getElementById('productos');
            const nuevoProducto = document.createElement('div');
            nuevoProducto.className = 'form-group';

            let options = '';

            dataCurso.forEach(cuota => {
                if (cuota.numero_cuota === 0) {
                    options += `<option value="${cuota.numero_cuota}">Inscripción</option>`;
                } else {
                    options += `<option value="${cuota.numero_cuota}">Cuota ${cuota.numero_cuota}</option>`;
                }
            });

            if (countAgregarCuota <= dataCurso.length) {
                nuevoProducto.innerHTML = `
                <label>Seleccione Cuota:</label>
                <select name="productos[]" class="form-control" required>
                    ${options}
                </select>
                <label>Cantidad:</label>
                <input type="number" name="cantidades[]" class="form-control" value="1"  required>
                <label>Precio Dolares:</label>
                <input type="number" id="precioCuota" name="precios[]" class="form-control" step="0.01" readonly required placeholder="Cargando...">
                <label>Precio Bolivares:</label>
                <input type="number" id="precioConTasa" name="preciosConTasa[]" class="form-control" step="0.01" readonly required placeholder="Cargando...">
                `;
            } else {
                alert('No puedes agregar mas cuotas')
            }

            const inputCantidad = nuevoProducto.querySelector('input[name="cantidades[]"]');
            const inputPrecio = nuevoProducto.querySelector('input[name="precios[]"]');
            const inputPrecioConTasa = nuevoProducto.querySelector('input[name="preciosConTasa[]"]');
            const selectCuota = nuevoProducto.querySelector('select[name="productos[]"]');
            if (dataCurso) {
                getSelectCuotaAndPrice(+selectCuota.value);
            };

            function getSelectCuotaAndPrice(selectedCuota) {
                const cuotaEncontrada = dataCurso.find(cuota => cuota.numero_cuota === selectedCuota);

                if (cuotaEncontrada) {
                    inputPrecio.value = cuotaEncontrada.monto_cuota * inputCantidad.value;
                    const montoEnBs = cuotaEncontrada.monto_cuota * <?php echo $tasa; ?>;
                    inputPrecioConTasa.value = montoEnBs.toFixed(2);
                    totalPagado += montoEnBs; // Suma el monto convertido a Bs
                    totaliva = totalPagado * 0.16;
                    total = totalPagado + totaliva;
                    document.getElementById('totalPagado').value = totalPagado.toFixed(2);
                    document.getElementById('totaliva').value = totaliva.toFixed(2);
                    document.getElementById('total').value = total.toFixed(2);
                } else {
                    alert('No se encontró la cuota seleccionada');
                    inputPrecio.value = 0
                }
            }

            selectCuota.addEventListener('change', function() {
                const selectCuota = nuevoProducto.querySelector('select[name="productos[]"]');
                getSelectCuotaAndPrice(+selectCuota.value);
            });

            productosDiv.appendChild(nuevoProducto);
        }
    </script>
</body>

</html>