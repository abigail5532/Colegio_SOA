<?php
session_start();
include('../Includes/Connection.php');

header('Content-Type: application/json');

$response = [];

if (!empty($_SESSION['active'])) {
    $role = $_SESSION['role'];
    $id_user = $_SESSION['idUser'];

    if ($role == 'docente') {
        $sql = "SELECT id_notificacion, mensaje, leido, fecha 
                FROM notificaciones 
                WHERE id_docente = ? 
                ORDER BY fecha DESC";
    } elseif ($role == 'alumno') {
        $sql = "SELECT id_notificacion, mensaje, leido, fecha 
                FROM notificaciones 
                WHERE id_alumno = ? 
                ORDER BY fecha DESC";
    }

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = [
            'id' => $row['id_notificacion'],
            'mensaje' => $row['mensaje'],
            'leido' => $row['leido'],
            'fecha' => $row['fecha']
        ];
    }
}

echo json_encode($response);
