<?php
session_start();
$id_user = $_SESSION['idUser'];
require("../Includes/Connection.php");
if (!empty($_GET['idalum'])) {
    $idalum = $_GET['idalum'];
     $query_update = mysqli_query($conexion, "UPDATE alumnos SET estado = 'Inactivo' WHERE idalum = $idalum");
    mysqli_close($conexion);
    header("Location: tblAlumnos.php");
}