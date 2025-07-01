<?php
session_start();
include('../Includes/Connection.php');

if (isset($_GET['idadm'])) {
    $idadm = $_GET['idadm'];
    $sql = "UPDATE administradores SET estado = 'Activo' WHERE idadm = $idadm";
    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Administrador reactivado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al reactivar administrador";
        $_SESSION['tipo_mensaje'] = "error";
    }
}

header("Location: tblAdministradores.php?estado=Activo");
exit();
?>

