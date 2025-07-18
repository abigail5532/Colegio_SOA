<?php
include('../Includes/Connection.php');

$idasig = isset($_POST['idasig']) ? intval($_POST['idasig']) : 0;
$bimestre = isset($_POST['bimestre']) ? intval($_POST['bimestre']) : 0;
$evaluacion = isset($_POST['evaluacion']) ? trim($_POST['evaluacion']) : '';
$fechainicio = isset($_POST['fechainicio']) ? trim($_POST['fechainicio']) : '';
$fechafin = isset($_POST['fechafin']) ? trim($_POST['fechafin']) : '';

if ($idasig && $bimestre && !empty($evaluacion)) {
    $stmt = $conexion->prepare("INSERT INTO evaluaciones (idasig, bimestre, evaluacion, fechainicio, fechafin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $idasig, $bimestre, $evaluacion, $fechainicio, $fechafin);

    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Éxito',
                    text: 'Evaluación registrada correctamente',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'addEvaluacion.php?idasig=$idasig';
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al registrar la evaluación',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        </script>";
    }
    $stmt->close();
} 

mysqli_close($conexion);
?>
