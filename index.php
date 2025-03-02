<?php
//**********************************************************************************************************************************************************************************************
//Juan Alburgas 2024
/************************************************
 *                                              *
 *                SisGesa-Edu                   *
 *                                              *
 ************************************************/
//Sistema para la Gestion Administrativa Educativa
//Sistema de Facturacion
//*************************************************************************************************************************************************************************************************

//****************************
//*                          *
//*        ¡ADVERTENCIA!     *
//*                          *
//****************************
// El uso de esta herramienta informatica debe seguir la normativa del SENIAT 00071 para la generacion de facturas
// se recomienda leer la normativa a continuacion se señalan algunos puntos resaltantes

/*Capítulo II
De los Medios de Emisión
Artículo 6. Los sujetos regidos por esta Providencia Administrativa, deben emitir las
facturas y las notas de débito y de crédito a través de los siguientes medios: 
3. Sistemas computarizados o automatizados para la emisión de facturas y otros
documentos: herramienta tecnológica informática que permite imprimir la información
correspondiente a las operaciones realizadas por los contribuyentes, sobre los formatos o formas
libres elaborados por las imprentas autorizadas. 

Artículo 10. Cuando los sistemas computarizados o automatizados para la emisión de
facturas y otros documentos, se encuentren inoperantes o averiados, los documentos deberán
emitirse sobre formatos elaborados por imprentas autorizadas, con el número del documento
precedido de la palabra "serie", seguida de caracteres que la identifiquen y diferencien. En estos
casos, los emisores deben mantener permanentemente en el establecimiento los referidos
formatos, a los fines de dar cumplimiento a lo establecido en este artículo. 

Sección III
De las notas de débito y de crédito
Artículo 22. Las notas de débito o de crédito deben emitirse en el caso de ventas de bienes
o prestaciones de servicios que quedaren sin efecto parcial o totalmente u originaren un ajuste,
por cualquier causa, y por las cuales se otorgaron facturas.
El original y las copias de las notas de débito y de crédito, deben contener el enunciado:
"Nota de Débito" o "Nota de Crédito".
Artículo 23. Las notas de débito y de crédito emitidas a través de los medios señalados en
los numerales 1 y 2 del Artículo 6 de esta Providencia Administrativa, deben cumplir con los
requisitos previstos en el Artículo 13 o en el Artículo 15 de esta Providencia Administrativa, según
sea el caso, con excepción de lo establecido en el numeral 1 de los referidos artículos. Igualmente,
deben hacer referencia a la fecha, número y monto de la factura que soportó la operación. 
*/

ini_set('session.cookie_httponly', 1); // Evitar acceso a cookies mediante JavaScript
ini_set('session.use_only_cookies', 1); // Solo permitir el uso de cookies para sesiones
session_start();
include 'db.php';

// Configuración de seguridad de sesión

session_regenerate_id(true); // Regenerar ID de sesión para prevenir fijación de sesión

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generar un token aleatorio
}

// Obtener los datos de la empresa
$sql = "SELECT nombre, rif, logo FROM empresa LIMIT 1";
$result = $conn->query($sql);
$empresa = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .empresa-info { 
            text-align: center; 
            margin-bottom: 20px; 
        } 
        .empresa-logo { 
            max-height: 100px; 
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Gestión de Facturación Educativa</a>
</nav>
<div class="container mt-3 empresa-info"> 
    <?php if ($empresa): ?> 
        <img src="empresa/uploads/<?php echo htmlspecialchars($empresa['logo']); ?>" alt="Logo de la Empresa" class="empresa-logo"> 
        <h2><?php echo htmlspecialchars($empresa['nombre']); ?></h2> 
        <p>RIF: <?php echo htmlspecialchars($empresa['rif']); ?></p> 
    <?php endif; ?> 
</div>
<div class="container mt-5">
    <h2 class="text-center">Iniciar Sesión</h2>
    <div class="card">
        <div class="card-body">
            <form action="process_login.php" method="post">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" class="form-control" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <!-- Campo oculto para el token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>