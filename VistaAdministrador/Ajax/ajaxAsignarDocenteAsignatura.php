<?php
include('../Includes/Connection.php');

if (!isset($_GET['iddoc']) || empty($_GET['iddoc'])) {
    header("Location: tblDocentes.php");
    exit();
}

$iddoc = $_GET['iddoc'];

$query = mysqli_query($conexion, "SELECT * FROM asignar_grado_asignatura");
$docentes = mysqli_query($conexion, "SELECT * FROM docentes WHERE iddoc = $iddoc");
$consulta = mysqli_query($conexion, "SELECT * FROM asignar_docente_asignatura WHERE docente = $iddoc");
$resultDocente = mysqli_num_rows($docentes);

if(empty($resultDocente)){
    header("Location: tblDocentes.php");
}

$datos = array();
while ($asignado = mysqli_fetch_assoc($consulta)) {
    $datos[$asignado['asignatura']] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener asignaturas seleccionadas (si hay)
    $asignaturas = isset($_POST['asignaturas']) ? $_POST['asignaturas'] : [];

    // Obtener asignaciones actuales
    $consulta_actuales = mysqli_query($conexion, "SELECT asignatura FROM asignar_docente_asignatura WHERE docente = $iddoc");
    $asignaturas_actuales = [];
    while ($fila = mysqli_fetch_assoc($consulta_actuales)) {
        $asignaturas_actuales[] = $fila['asignatura'];
    }

    // Eliminar las que ya no están seleccionadas
    $asignaturas_a_eliminar = array_diff($asignaturas_actuales, $asignaturas);
    if (!empty($asignaturas_a_eliminar)) {
        $asignaturas_str = implode(",", $asignaturas_a_eliminar);
        $sql = mysqli_query($conexion, "DELETE FROM asignar_docente_asignatura WHERE docente = $iddoc AND asignatura IN ($asignaturas_str)");
        if (!$sql) {
            echo "Error al eliminar: " . mysqli_error($conexion);
        }
    }

    // Insertar nuevas asignaturas
    $asignaturas_a_insertar = array_diff($asignaturas, $asignaturas_actuales);
    foreach ($asignaturas_a_insertar as $asig) {
        $insert = mysqli_query($conexion, "INSERT INTO asignar_docente_asignatura (docente, asignatura) VALUES ($iddoc, $asig)");
        if (!$insert) {
            echo "Error al insertar: " . mysqli_error($conexion);
        }
    }

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Asignación actualizada',
                text: 'Se guardaron los cambios correctamente.',
                icon: 'success',
                confirmButtonText: 'Ok'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'tblDocentes.php';
                }
            });
        });
    </script>";
}


mysqli_close($conexion);
?>