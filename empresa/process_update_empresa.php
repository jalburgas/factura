<?php
include '../db.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$rif = $_POST['rif'];
$direccion = $_POST['direccion'];
$logo = $_FILES['logo']['name'];

if ($logo) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["logo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["logo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["logo"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $sql = "UPDATE empresa SET nombre='$nombre', rif='$rif', direccion='$direccion', logo='$logo' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Datos de la empresa actualizados exitosamente.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    $sql = "UPDATE empresa SET nombre='$nombre', rif='$rif', direccion='$direccion' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Datos de la empresa actualizados exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<a href="config_empresa.php">Volver</a>
<a href="index.php">Menú Principal</a>
