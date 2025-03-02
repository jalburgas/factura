<?php
session_start();
include 'db.php';

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
    <title>Sistema de Gestión Administrativa Educativa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .navbar-text {
            color: #28a745 !important; /* Verde destacado */
            font-weight: bold;
        }
        .empresa-info {
            text-align: center;
            margin-top: 20px;
        }
        .empresa-logo {
            max-width: 50px; /* Reducir más el tamaño del logo */
            margin-right: 10px; /* Espacio a la derecha del logo */
        }
        .card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
            width: 220px; /* Aumentar el ancho de las tarjetas */
            height: 120px; /* Reducir más la altura de las tarjetas */
            margin: 0 auto; /* Centrar las tarjetas */
            background-color: #232323; /* Fondo oscuro */
            color: white; /* Texto blanco */
            border-top-right-radius: 30px; /* Bordes redondeados */
            border-bottom-right-radius: 30px; /* Bordes redondeados */
            border-left: 10px solid #83b735; /* Borde izquierdo verde */
            text-transform: uppercase; /* Texto en mayúsculas */
            text-decoration: none; /* Sin subrayado */
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            background-color: #1e1e1e; /* Fondo más oscuro al pasar el cursor */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%; /* Asegurar que el contenido ocupe toda la tarjeta */
            text-align: center; /* Centrar el texto */
        }
        .card-title {
            margin-bottom: 10px; /* Reducir el margen inferior */
            font-size: 1rem; /* Reducir más el tamaño de la fuente */
            color: white; /* Color blanco para las etiquetas <h5> */
        }
        .btn-primary {
            background-color: #83b735;
            border-color: #83b735;
            color: white;
            font-size: 0.75rem; /* Reducir más el tamaño de la fuente del botón */
        }
        .btn-primary:hover {
            background-color: #6fa02b;
            border-color: #6fa02b;
            color: white;
        }
        .menu-title {
            margin-top: 20px; /* Reducir el margen superior */
            margin-bottom: 15px; /* Reducir el margen inferior */
            font-size: 1.25rem; /* Reducir el tamaño de la fuente */
            font-weight: bold;
            color: #003366;
        }
        .outer-box {
            background-color: #83b735;
            padding: 15px; /* Reducir el padding */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 3px solid #83b735; /* Borde verde */
            max-width: 1200px; /* Aumentar el ancho del recuadro */
            margin: 0 auto; /* Centrar el recuadro */
        }
        .inner-box {
            background-color: #ffffff;
            padding: 30px; /* Reducir el padding */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
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
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="register_user.php">Registrar Usuario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="empresa/config_empresa.php">Configuración Empresa</a>
                </li>
            <?php endif; ?>
             <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="cambio_passwd.php">Cambio Password</a>
            </li>
        </ul>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-3 empresa-info">
    <div class="row align-items-center">
        <div class="col-auto">
            <?php echo 'Fecha: ' . date("Y-m-d "); ?>
        </div>
        <?php if ($empresa): ?>
            <div class="col-auto">
                <img src="empresa/uploads/<?php echo $empresa['logo']; ?>" alt="Logo de la Empresa" class="empresa-logo">
            </div>
            <div class="col-auto">
                <h2 class="d-inline"><?php echo $empresa['nombre']; ?></h2>
                <p class="d-inline">RIF: <?php echo $empresa['rif']; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">
    <div class="outer-box">
        <div class="inner-box">
            <div class="menu-title">Gestión de Facturación</div>
            <div class="row mt-4 text-center">
                <?php if (isset($_SESSION['rol']) && ( $_SESSION['rol'] == 'caja')): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='facturas/create_invoice.php';" data-toggle="tooltip" data-placement="top" title="Registrar Factura">
                            <div class="card-body text-center">
                                <h5 class="card-title">Registrar Factura</h5>
                                <a href="facturas/create_invoice.php" class="btn btn-primary mt-auto"><i class="fas fa-file-invoice"></i></a>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='estudiantes/create_student.php';" data-toggle="tooltip" data-placement="top" title="Registrar Estudiante">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Registrar Estudiante</h5>
                                    <a href="estudiantes/create_student.php" class="btn btn-primary mt-auto"><i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='clientes/register_juridical_client_view.php';" data-toggle="tooltip" data-placement="top" title="Registrar Cliente Jurídico">
                            <div class="card-body text-center">
                                <h5 class="card-title">Registrar Cliente Jurídico</h5>
                                <a href="clientes/register_juridical_client.php" class="btn btn-primary mt-auto"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div> 
                      <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='pagos/payments.php';" data-toggle="tooltip" data-placement="top" title="Consulta de Pagos">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Consulta de Pagos</h5>
                                    <a href="pagos/payments.php" class="btn btn-primary mt-auto"><i class="fas fa-search-dollar"></i></a>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-6 col-lg-3 mb-4">
                       <div class="card h-100" onclick="location.href='tasa/tasa.php';" data-toggle="tooltip" data-placement="top" title="Tasa de Cambio">
                        <div class="card-body text-center">
                         <h5 class="card-title">Tasa</h5>
                              <a href="tasa/tasa.php" class="btn btn-primary mt-auto"><i class="fas fa-dollar-sign"></i></a>
                         </div>
                      </div>
                </div>    
                    
                    <?php endif; ?>
                 
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] != 'caja')): ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='facturas/create_invoice.php';" data-toggle="tooltip" data-placement="top" title="Registrar Factura">
                            <div class="card-body text-center">
                                <h5 class="card-title">Registrar Factura</h5>
                                <a href="facturas/create_invoice.php" class="btn btn-primary mt-auto"><i class="fas fa-file-invoice"></i></a>
                            </div>
                        </div>
                    </div> 
                  
                
                        <div class="col-md-6 col-lg-3 mb-4">
                       <div class="card h-100" onclick="location.href='tasa/tasa.php';" data-toggle="tooltip" data-placement="top" title="Tasa de Cambio">
                        <div class="card-body text-center">
                         <h5 class="card-title">Tasa</h5>
                              <a href="tasa/tasa.php" class="btn btn-primary mt-auto"><i class="fas fa-dollar-sign"></i></a>
                         </div>
                      </div>
                </div>
                   
                    
                <?php endif; ?>
            </div>
            <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] != 'caja')): ?>
                <div class="menu-title">Gestión de Cuotas</div>
                <div class="row mt-4 text-center">
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'administrador' || $_SESSION['rol'] == 'supervisor')): ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='cuotas/create_installment.php';" data-toggle="tooltip" data-placement="top" title="Registrar Cuota">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Registrar Cuota</h5>
                                    <a href="cuotas/create_installment.php" class="btn btn-primary mt-auto"><i class="fas fa-coins"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='pagos/payments.php';" data-toggle="tooltip" data-placement="top" title="Consulta de Pagos">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Consulta de Pagos</h5>
                                    <a href="pagos/payments.php" class="btn btn-primary mt-auto"><i class="fas fa-search-dollar"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="menu-title">Gestión Contable</div>
                <div class="row mt-4 text-center">
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'administrador' || $_SESSION['rol'] == 'supervisor')): ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='contabilidad/accounting_book.php';" data-toggle="tooltip" data-placement="top" title="Libro Contable">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Resumen Comtable </h5>
                                    <a href="contabilidad/accounting_book.php" class="btn btn-primary mt-auto"><i class="fas fa-book"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='contabilidad/daily_accounting_book.php';" data-toggle="tooltip" data-placement="top" title="Libro Contable Diario">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Libro de Ventas</h5>
                                    <a href="contabilidad/daily_accounting_book.php" class="btn btn-primary mt-auto"><i class="fas fa-calendar-alt"></i></a>
                                </div>
                            </div>
                        </div>                    

                 <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='credit_notes/create_credit_note.php';" data-toggle="tooltip" data-placement="top" title="Crear Nota de Crédito">
                            <div class="card-body text-center">
                                <h5 class="card-title">Crear Nota de Crédito</h5>
                                <a href="credit_notes/create_credit_note.php" class="btn btn-primary mt-auto"><i class="fas fa-file-alt"></i></a>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='debit_notes/create_debit_note.php';" data-toggle="tooltip" data-placement="top" title="Crear Nota de Debito">
                            <div class="card-body text-center">
                                <h5 class="card-title">Crear Nota de Debito</h5>
                                <a href="credit_notes/create_credit_note.php" class="btn btn-primary mt-auto"><i class="fas fa-file-alt"></i></a>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>
                <div class="menu-title">Gestión Cuentas</div>
                <div class="row mt-4 text-center">
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'administrador' || $_SESSION['rol'] == 'supervisor')): ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='cuentas/modulo_contable.php';" data-toggle="tooltip" data-placement="top" title="Libro Contable">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Registro de Gastos </h5>
                                    <a href="cuentas/modulo_contable.php" class="btn btn-primary mt-auto"><i class="fas fa-book"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4 text-center">                    
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='cuentas/consulta_asiento.php';" data-toggle="tooltip" data-placement="top" title="Libro Contable">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Consulta Asientos </h5>
                                    <a href="cuentas/consulta_asiento.php" class="btn btn-primary mt-auto"><i class="fas fa-book"></i></a>
                                </div>
                            </div>
                        </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="menu-title">Gestión Académica</div>
                <div class="row mt-4 text-center">
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'administrador' || $_SESSION['rol'] == 'supervisor')): ?>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='curso/create_course.php';" data-toggle="tooltip" data-placement="top" title="Registrar Curso">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Registrar Curso</h5>
                                    <a href="curso/create_course.php" class="btn btn-primary mt-auto"><i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100" onclick="location.href='estudiantes/create_student.php';" data-toggle="tooltip" data-placement="top" title="Registrar Estudiante">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Registrar Estudiante</h5>
                                    <a href="estudiantes/create_student.php" class="btn btn-primary mt-auto"><i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100" onclick="location.href='clientes/register_juridical_client.php';" data-toggle="tooltip" data-placement="top" title="Registrar Cliente Jurídico">
                            <div class="card-body text-center">
                                <h5 class="card-title">Registrar Cliente Jurídico</h5>
                                <a href="clientes/register_juridical_client.php" class="btn btn-primary mt-auto"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>