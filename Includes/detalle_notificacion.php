<?php
session_start();
if (empty($_SESSION['active'])) {
    exit("Sesión no válida.");
}

include "../Includes/Connection.php";

if (!isset($_GET['id'])) {
    exit("ID no especificado.");
}

$id_notif = $_GET['id'];
$id_user = $_SESSION['idUser'];

// Obtener la notificación
$sql = "SELECT * FROM notificaciones WHERE id_notificacion = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_notif);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notif = mysqli_fetch_assoc($result);

if (!$notif) {
    exit("No se encontró la notificación.");
}

// Verificar que el usuario es el destinatario
if (
    ($notif['id_docente'] != $id_user) &&
    ($notif['id_alumno'] != $id_user)
) {
    exit("Acceso no autorizado.");
}

// Marcar como leída
$sql_update = "UPDATE notificaciones SET leido = 1 WHERE id_notificacion = ?";
$stmt_upd = mysqli_prepare($conexion, $sql_update);
mysqli_stmt_bind_param($stmt_upd, "i", $id_notif);
mysqli_stmt_execute($stmt_upd);

// Buscar información de la cita si está vinculada
$info_reunion = '';
if ($notif['tipo'] === 'reunion_confirmada' && !empty($notif['id_cita'])) {
    $sql_cita = "SELECT reunion, nomfamiliar, descripcion FROM cita WHERE idcita = ?";
    $stmt_cita = mysqli_prepare($conexion, $sql_cita);
    mysqli_stmt_bind_param($stmt_cita, "i", $notif['id_cita']);
    mysqli_stmt_execute($stmt_cita);
    $res_cita = mysqli_stmt_get_result($stmt_cita);
    $cita = mysqli_fetch_assoc($res_cita);

    if ($cita) {
        $info_reunion = "
            <p><strong>Tipo de Reunión:</strong> " . htmlspecialchars($cita['reunion']) . "</p>
            <p><strong>Apoderado:</strong> " . htmlspecialchars($cita['nomfamiliar']) . "</p>
            <p><strong>Descripción:</strong> " . htmlspecialchars($cita['descripcion']) . "</p>
        ";
    } else {
        $info_reunion = "<p><em>No se encontró una cita asociada a esta notificación.</em></p>";
    }
}
?>

<!-- Contenido HTML del Modal -->
<p><strong>Mensaje:</strong> <?= htmlspecialchars($notif['mensaje']) ?></p>
<p><strong>Fecha de notificación:</strong> <?= date("d/m/Y H:i", strtotime($notif['fecha'])) ?></p>

<?= $info_reunion ?>




