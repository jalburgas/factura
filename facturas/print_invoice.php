<?php
require_once("../db.php");

if (isset($_GET['id'])) {
    $factura_id = $_GET['id'];

    // Obtener los datos de la factura
    $query = "SELECT facturas.id, 
                     IFNULL(CONCAT(students.nombre, ' ', students.apellido), clientes_juridicos.nombre) AS cliente_nombre,
                     IFNULL(students.cedula, clientes_juridicos.rif) AS identificacion,
                     facturas.fecha, 
                     cursos.nombre AS curso
              FROM facturas
              LEFT JOIN students ON facturas.cliente_id = students.cedula
              LEFT JOIN clientes_juridicos ON facturas.cliente_id = clientes_juridicos.rif
              JOIN cursos ON facturas.curso_id = cursos.id
              WHERE facturas.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $factura_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $factura = $result->fetch_assoc();

    // Obtener los productos de la factura
    $query = "SELECT producto, cantidad, precio FROM factura_productos WHERE factura_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $factura_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Error: ID de factura no proporcionado.");
}
date_default_timezone_set('America/Caracas');
// Formatear la fecha
$fecha_formateada = date('d-m-Y h:i:s A', strtotime($factura['fecha']));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Imprimir Factura</title>
    <style>
        @media print {
            body {
                font-size: 8px;
            }

            .invoice-container {
                width: 50mm;
                /* Ajusta el ancho según las especificaciones de tu impresora fiscal */
                padding: 0mm;
                border: none;
                /* Quita el borde para la impresión */
                box-sizing: border-box;
            }

            .invoice-header,
            .invoice-footer {
                text-align: center;
            }

            .invoice-body {
                margin-top: 10px;
            }

            .product-item {
                margin-bottom: 5px;
            }

            .product-item p {
                margin: 0;
            }

            .btn-print,
            .btn-back {
                display: none;
                /* Oculta los botones durante la impresión */
            }
        }

        .btn-back {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container invoice-container">

        <div class="invoice-body">
            <h2 class="text-center">Factura #<?php echo @$factura['id']; ?></h2>
            <p><strong>Cliente:</strong> <?php echo @$factura['cliente_nombre']; ?></p>
            <p><strong>CI/RIF:</strong> <?php echo @$factura['identificacion']; ?></p>
            <p><strong>Fecha:</strong> <?php echo $fecha_formateada; ?></p>
            <p><strong>Curso:</strong> <?php echo @$factura['curso']; ?></p>
            <h3>Descripcion</h3>
            <div>
                <?php
                $total = 0;
                foreach ($productos as $producto):
                    $subtotal = $producto['cantidad'] * $producto['precio'];
                    $iva = $subtotal * 0.16;
                    $total += $subtotal + $iva;
                ?>
                    <div class="product-item">
                        <p><strong>Cuota:</strong> <?php echo $producto['producto']; ?>
                            <strong>Cantidad:</strong> <?php echo $producto['cantidad']; ?>
                        </p>
                        <p><strong>Precio:</strong> <?php echo number_format($producto['precio'], 2); ?></p>
                        <p><strong>Sub Total:</strong> <?php echo number_format($subtotal, 2); ?>
                            <strong>Iva:</strong> <?php echo number_format($iva, 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="text-right">Total: <?php echo number_format($total, 2); ?></h3>
        </div>

    </div>
    <button onclick="window.print()" class="btn-back">Imprimir</button>
    <a href="create_invoice.php" class="btn-back">Regresar</a>
</body>

</html>