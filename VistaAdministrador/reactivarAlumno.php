<?php
session_start();
include('../Includes/Connection.php');

if (isset($_GET['idalum'])) {
    $idalum = $_GET['idalum'];

    $sql = "UPDATE alumnos SET estado = 'Activo' WHERE idalum = $idalum";
    if (mysqli_query($conexion, $sql)) {
        $_SESSION['mensaje'] = "Alumno reactivado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error al reactivar alumno";
        $_SESSION['tipo_mensaje'] = "error";
    }
}

header("Location: tblAlumnos.php?estado=Activo");
exit();
?>
