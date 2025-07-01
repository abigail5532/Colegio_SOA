<?php
session_start();
$id_user = $_SESSION['idUser'];
require("../Includes/Connection.php");
if (!empty($_GET['idadm'])) {
    $idadm = $_GET['idadm'];
    $query_update = mysqli_query($conexion, "UPDATE administradores SET estado = 'Inactivo' WHERE idadm = $idadm");
    mysqli_close($conexion);
    header("Location: tblAdministradores.php");
}