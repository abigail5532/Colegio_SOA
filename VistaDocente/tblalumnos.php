<?php
session_start();
$id_user = $_SESSION['idUser'];
require_once('../Includes/Connection.php');
require_once "../Includes/HeaderDoc.php";
$idasig = $_GET['idasig'];
$idaula = $_GET['idaula'];
$query = mysqli_query($conexion, "SELECT a.idalum, asig.nombre AS asignatura, 
n.nombre AS nivel, g.nombre AS grado, au.seccion, au.yearacad, n.nombre AS nivel,
CONCAT(a.apellidos, ', ', a.nombres) AS nombre_alumno,
CONCAT(g.nombre, ' ', au.seccion) AS grado_seccion
FROM alumnos a
INNER JOIN aulas au ON a.aula = au.idaula
INNER JOIN grados g ON au.grado = g.idgrado
INNER JOIN niveles n ON g.nivel = n.idniv
INNER JOIN asignar_grado_asignatura relgrado ON au.idaula = relgrado.aula
INNER JOIN asignaturas asig ON relgrado.asignatura = asig.idasig
WHERE a.aula = '$idaula' AND asig.idasig = '$idasig' ORDER BY nombre_alumno ASC");

if ($query && mysqli_num_rows($query) > 0) {
    $rowinfo = mysqli_fetch_assoc($query);
    $yearacad = $rowinfo['yearacad'];
    $nivel = $rowinfo['nivel'];
    $asignatura = $rowinfo['asignatura'];
    $grado_seccion = $rowinfo['grado_seccion'];
}

?>

<!-- Begin Page Content -->
<div class="container-fluid">
            <!--DATATABLE-->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="head-table m-0 font-weight-bold">Registro de Alumnos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="color: black;">
                        <table class="table table-bordered" id="tblFree" width="100%" cellspacing="0" style="color: black;">
                            <thead>
                                <tr>
                                    <th>Apellidos y Nombres</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td><?php echo $row['nombre_alumno']; ?></td>
                                <td>
                                    <a href="nota.php?idalum=<?php echo $row['idalum']; ?>&idasig=<?php echo $idasig; ?>"
                                    class="btn" style="background-color: #3357FF; color: white;">
                                        Calificar
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
</div>

<?php 
require_once "../Includes/Footer.php";
?>
