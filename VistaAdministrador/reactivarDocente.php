<?php
session_start();
include('../Includes/Connection.php');

if (isset($_GET['iddoc'])) {
    $iddoc = $_GET['iddoc'];
    $sql = "UPDATE docentes SET estado = 'Activo' WHERE iddoc = $iddoc";
    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Docente reactivado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al reactivar docente";
        $_SESSION['tipo_mensaje'] = "error";
    }
}

header("Location: tblDocentes.php?estado=Activo");
exit();
?>
