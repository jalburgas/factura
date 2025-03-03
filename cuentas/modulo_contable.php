<?php
require_once("../db.php");

// Clase para manejo de Gastos y Asientos Contables
class GestionContable {
    // Registrar un gasto con su asiento contable
    public static function registrarGasto($fecha, $descripcion, $cuenta_gasto_id, $cuenta_contrapartida_id, $monto) {
        global $conn;
        
        try {
            $conn->begin_transaction();
            
            // Insertar el gasto
            $stmt = $conn->prepare("INSERT INTO gastos 
                (fecha, descripcion, cuenta_id, monto) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssid", $fecha, $descripcion, $cuenta_gasto_id, $monto);
            $stmt->execute();
            $gasto_id = $conn->insert_id;
            
            // Generar asiento contable (doble partida)
            self::crearAsiento($fecha, $cuenta_gasto_id, $descripcion, $monto, 0, $gasto_id); // Débito
            self::crearAsiento($fecha, $cuenta_contrapartida_id, $descripcion, 0, $monto, $gasto_id); // Crédito
            
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error contable: " . $e->getMessage());
            return false;
        }
    }
    
    private static function crearAsiento($fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO asientos_contables 
            (fecha, cuenta_id, descripcion, debe, haber, gasto_id)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisddi", $fecha, $cuenta_id, $descripcion, $debe, $haber, $gasto_id);
        return $stmt->execute();
    }
    
    // Obtener todas las cuentas de gastos
    public static function getCuentasGastos() {
        global $conn;
        $result = $conn->query("SELECT * FROM cuentas_contables WHERE tipo = 'Gastos'");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Obtener asientos contables
    public static function getAsientos($limit = 100) {
        global $conn;
        $stmt = $conn->prepare("SELECT a.*, c.nombre as cuenta_nombre 
                              FROM asientos_contables a
                              JOIN cuentas_contables c ON a.cuenta_id = c.numero_cuenta
                              ORDER BY a.fecha DESC
                              LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_gasto'])) {
    $resultado = GestionContable::registrarGasto(
        $_POST['fecha'],
        $_POST['descripcion'],
        $_POST['cuenta_gasto'],
        $_POST['cuenta_contrapartida'],
        $_POST['monto']
    );
    
    if ($resultado) {
        $mensaje = "Gasto registrado correctamente con su asiento contable";
    } else {
        $error = "Error al registrar el gasto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Contable de Gastos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
</head>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Gestión Escolar</a>
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
<div class="container mt-4">
    <?php if (isset($mensaje)): ?>
    <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario de Registro de Gastos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Registro de Gastos</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Descripción del Gasto</label>
                            <textarea name="descripcion" class="form-control" rows="2" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label>Monto</label>
                            <input type="number" name="monto" step="0.01" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Cuenta de Gasto</label>
                            <select name="cuenta_gasto" class="form-select" required>
                                <?php foreach (GestionContable::getCuentasGastos() as $cuenta): ?>
                                <option value="<?= $cuenta['numero_cuenta'] ?>">
                                    <?= $cuenta['numero_cuenta'] ?> - <?= $cuenta['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label>Cuenta de Contrapartida</label>
                            <select name="cuenta_contrapartida" class="form-select" required>
                                <option value="20100">20100 - Proveedores</option>
                                <option value="10200">10200 - Bancos</option>
                                <option value="20200">20200 - Cuentas por Pagar</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="registrar_gasto" class="btn btn-primary">
                            Registrar Gasto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Listado de Asientos Contables -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Últimos Asientos Contables</h5>
                </div>
                <div class="card-body">
                    <table id="tablaAsientos" class="table table-hover table-striped" style="width:100%">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Descripción</th>
            <th>Debe</th>
            <th>Haber</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (GestionContable::getAsientos(1000) as $asiento): ?>
        <tr>
            <td><?= $asiento['fecha'] ?></td>
            <td><?= $asiento['cuenta_nombre'] ?></td>
            <td><?= $asiento['descripcion'] ?></td>
            <td class="text-end"><?= number_format($asiento['debe'], 2) ?></td>
            <td class="text-end"><?= number_format($asiento['haber'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Cuentas de Gastos -->
    <div class="mt-4">
        <h4>Plan de Cuentas - Gastos</h4>
        <table class="table table-bordered">
            <thead class="table-warning">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (GestionContable::getCuentasGastos() as $cuenta): ?>
                <tr>
                    <td><?= $cuenta['numero_cuenta'] ?></td>
                    <td><?= $cuenta['nombre'] ?></td>
                    <td><?= $cuenta['descripcion'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaAsientos').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Exportar a Excel',
                className: 'btn btn-success',
                title: 'Asientos Contables',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c[r^="D"], row c[r^="E"]', sheet).attr('s', '2'); // Formato numérico
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
});
</script>
</body>
</html>