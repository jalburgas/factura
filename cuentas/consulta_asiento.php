<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
session_start();
require_once("../db.php");
class GestionContable {
public static function getAsientosConFacturas($limit = 100) {
    global $conn;
    $query = "SELECT 
                a.id,
                a.fecha,
                c.nombre AS cuenta,
                a.descripcion,
                a.debe,
                a.haber,
                f.id AS factura_id,
                f.cliente_id,
                f.total_factura
              FROM asientos_contables a
              JOIN cuentas_contables c ON a.cuenta_id = c.numero_cuenta
              LEFT JOIN facturas f ON a.factura_id = f.id
              ORDER BY a.fecha DESC
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function getAsientosRelacionados($factura_id) {
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM asientos_contables WHERE factura_id = ?");
  $stmt->bind_param("i", $factura_id);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Asientos Contables</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
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
    <div class="container">
        <h1 class="mt-5">Consulta de Asientos Contables</h1>
        <p class="lead">Listado de asientos contables con facturas asociadas.</p>
        
<table id="tablaAsientos" class="table table-hover">
    <thead>
        <tr>
            <th>Factura ID</th>
            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Descripción</th>
            <th>Debe</th>
            <th>Haber</th>
            <th>Cliente</th>
            <th>Total Factura</th>
        </tr>
    </thead>
    <tbody>
        
        <?php foreach (GestionContable::getAsientosConFacturas(100) as $asiento): ?>
        <tr>
            <td>
                <?php if ($asiento['factura_id']): ?>
                <a href="ver_factura.php?id=<?= $asiento['factura_id'] ?>">
                    #<?= $asiento['factura_id'] ?>
                </a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td><?= $asiento['fecha'] ?></td>
            <td><?= $asiento['cuenta'] ?></td>
            <td><?= $asiento['descripcion'] ?></td>
            <td class="text-end"><?= number_format($asiento['debe'], 2) ?></td>
            <td class="text-end"><?= number_format($asiento['haber'], 2) ?></td>
            <td><?= $asiento['cliente_id'] ?></td>
            <td class="text-end">
                <?= $asiento['total_factura'] ? number_format($asiento['total_factura'], 2) : '-' ?>
            </td>
        </tr>
        <?php endforeach; ?>
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