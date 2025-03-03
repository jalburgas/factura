<?php
require_once("../db.php");

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];

    // Mostrar mensaje de advertencia
    echo "<script>
        if (confirm('¿Estás seguro de que deseas eliminar esta empresa?')) {
            window.location.href = 'delete_empresa.php?confirm=true&id=$empresa_id';
        } else {
            window.location.href = 'config_empresa.php';
        }
    </script>";
}

if (isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
    // Eliminar el registro de la empresa
    $sql = "DELETE FROM empresa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $empresa_id);

    if ($stmt->execute()) {
        echo "Empresa eliminada exitosamente.";
    } else {
        echo "Error al eliminar la empresa: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<a href="config_empresa.php">Volver</a>
<a href="index.php">Menú Principal</a>
