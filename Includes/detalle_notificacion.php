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

// Verificar que el usuario es el dueño
if (($notif['id_docente'] != $id_user) && ($notif['id_alumno'] != $id_user)) {
    exit("Acceso no autorizado.");
}

// Marcar como leída
$sql_update = "UPDATE notificaciones SET leido = 1 WHERE id_notificacion = ?";
$stmt_upd = mysqli_prepare($conexion, $sql_update);
mysqli_stmt_bind_param($stmt_upd, "i", $id_notif);
mysqli_stmt_execute($stmt_upd);

// Si la notificación es de tipo reunión_confirmada, buscar datos adicionales
$info_reunion = '';
if ($notif['tipo'] === 'reunion_confirmada') {
    // Buscar la cita más reciente del docente en el mismo día y horas similares
    if (preg_match('/(\d{4}-\d{2}-\d{2}) de (\d{2}:\d{2}:\d{2}) a (\d{2}:\d{2}:\d{2})/', $notif['mensaje'], $match)) {
        $fecha = $match[1];
        $horai = $match[2];
        $horaf = $match[3];

        $sql_cita = "SELECT reunion, nomfamiliar, descripcion FROM cita 
                     WHERE docente = ? AND fecha = ? AND horai = ? AND horaf = ?
                     ORDER BY idcita DESC LIMIT 1";

        $stmt_cita = mysqli_prepare($conexion, $sql_cita);
        mysqli_stmt_bind_param($stmt_cita, "isss", $notif['id_docente'], $fecha, $horai, $horaf);
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
}
$info_nota = '';
if ($notif['tipo'] === 'nota_subida') {
    $mensaje = $notif['mensaje'];

    // Buscar curso y evaluación con expresiones regulares
    if (preg_match('/(del|de)\s\*?(.+?)\*?\s(en|en el curso de)\s\*?(.+?)\*?/', $mensaje, $match)) {
        $evaluacion = htmlspecialchars($match[2]);
        $curso = htmlspecialchars($match[4]);

        $info_nota = "
            <p><strong>Evaluación:</strong> $evaluacion</p>
            <p><strong>Curso:</strong> $curso</p>
            <p><em>Puedes revisar tu boletín académico para ver el detalle de tu calificación.</em></p>
        ";
    } else {
        // Mensaje genérico si no se puede extraer
        $info_nota = "<p><em>Puedes revisar tu boletín académico para ver el detalle de tu calificación.</em></p>";
    }
}


?>

<!-- Contenido HTML del Modal -->
<p><strong>Mensaje:</strong> <?= htmlspecialchars($notif['mensaje']) ?></p>
<p><strong>Fecha de notificación:</strong> <?= date("d/m/Y H:i", strtotime($notif['fecha'])) ?></p>

<?= $info_reunion ?>
<?= $info_nota ?>



