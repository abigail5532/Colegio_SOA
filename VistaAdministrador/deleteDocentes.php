<?php
session_start();
$id_user = $_SESSION['idUser'];
require("../Includes/Connection.php");

if (!empty($_GET['iddoc'])) {
    $iddoc = $_GET['iddoc'];
    $query_update = mysqli_query($conexion, "UPDATE docentes SET estado = 'Inactivo' WHERE iddoc = $iddoc");

    mysqli_close($conexion);
    header("Location: tblDocentes.php");
    exit;
}
?>
