<!DOCTYPE html>
<html>
<head>
    <title>Registrar Detalle de Factura</title>
</head>
<body>

<h2>Registrar Detalle de Factura</h2>
<form action="process_invoice_detail.php" method="post">
    ID de Factura: <input type="number" name="factura_id" required><br>
    Curso: <input type="text" name="curso" required><br>
    Monto: <input type="number" step="0.01" name="monto" required><br>
    <input type="submit" value="Registrar Detalle de Factura">
</form>

<a href="menu.php">Volver al Menú Principal</a>

</body>
</html>
