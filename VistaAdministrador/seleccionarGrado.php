<?php
session_start();
$id_user = $_SESSION['idUser'];
$iddoc = $_GET['iddoc'];
$idnivel = $_GET['idnivel'];

include('../Includes/Connection.php');
include_once "Ajax/ajaxAsignarDocenteAsignatura.php";
include_once "../Includes/Header.php";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Docentes a Aulas</h6>
        </div>
        <div class="card-body" style="color: black;">
            <form method="post">
                <input type="hidden" name="idrelasig" id="idrelasig">
                <div class="form-group">
                    <label class="col-form-label">Seleccionar las asignaturas para el docente</label>
                    <div class="row">
                    <?php
                    include ("../Includes/Connection.php");
                    
$query = mysqli_query($conexion, "
    SELECT rel.idrelgrado, au.idaula, au.seccion,
           n.nombre AS nivel, g.nombre AS grado, a.nombre AS asignatura 
    FROM asignar_grado_asignatura rel
    JOIN asignaturas a ON rel.asignatura = a.idasig 
    JOIN aulas au ON rel.aula = au.idaula
    JOIN grados g ON au.grado = g.idgrado 
    JOIN niveles n ON g.nivel = n.idniv 
    WHERE n.idniv = $idnivel
    AND (
        NOT EXISTS (
            SELECT 1 FROM asignar_docente_asignatura ada
            WHERE ada.asignatura = rel.idrelgrado AND ada.docente != $iddoc
        )
        OR rel.idrelgrado IN (
            SELECT asignatura FROM asignar_docente_asignatura 
            WHERE docente = $iddoc
        )
    )
    ORDER BY n.nombre, g.nombre, au.seccion
");
                    $grupos = [];

                    while ($row = mysqli_fetch_assoc($query)) {
                        $clave = $row['nivel'] . ' - ' . $row['grado'] . ' - ' . $row['seccion'];
                        $grupos[$clave][] = $row;
                    }

                    foreach ($grupos as $grupoNombre => $items) { ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white py-2 px-3">
                                    <strong><?php echo $grupoNombre; ?></strong>
                                </div>
                                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($items as $item) { ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" id="asignatura_<?php echo $item['idrelgrado']; ?>"
                                                   type="checkbox" name="asignaturas[]"
                                                   value="<?php echo $item['idrelgrado']; ?>"
                                                   <?php echo isset($datos[$item['idrelgrado']]) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="asignatura_<?php echo $item['idrelgrado']; ?>">
                                                <?php echo $item['asignatura']; ?>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="seleccionarNivel.php?iddoc=<?php echo $iddoc; ?>" class="btn btn-secondary">← Volver</a>
                    <a href="tblDocentes.php" class="btn btn-danger">Cancelar</a>
                    <button class="btn btn-success" type="submit">Modificar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "../Includes/Footer.php"; ?>
