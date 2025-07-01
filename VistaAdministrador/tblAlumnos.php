<?php
session_start();
$id_user = $_SESSION['idUser'];
include('../Includes/Connection.php');
include_once "Ajax/ajaxAlumnos.php";
include_once "../Includes/Header.php";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!--DATATABLE-->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Alumnos</h6>
        </div>
        <div class="card-body">
            <a href="addAlumnos.php" id="btnNuevo" class="btn" type="button" style="background-color:  #71B600; color: white;"><i class="fa-solid fa-circle-plus"></i> Agregar</a>
            <form method="get" class="form-inline mb-3" style="float: right;">
    <label class="mr-2 font-weight-bold">Filtrar por estado:</label>
    <select name="estado" class="form-control" onchange="this.form.submit()">
        <option value="Todos" <?php echo (!isset($_GET['estado']) || $_GET['estado'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
        <option value="Activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Activo') ? 'selected' : ''; ?>>Activos</option>
        <option value="Inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivos</option>
    </select>
</form>

        </br>
            </br> 
            <div class="table-responsive" style="color: black;">
                <table class="table table-bordered" id="tblBlasPascal" width="100%" cellspacing="0" style="color: black;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Apellidos y Nombres</th>
                            <th>Email</th>
                            <th>Fec. Nacimiento</th>
                            <th>Aula</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php
                        $estadoSeleccionado = isset($_GET['estado']) ? $_GET['estado'] : 'Activo';

                        $condicionEstado = "";
                        if ($estadoSeleccionado == 'Activo' || $estadoSeleccionado == 'Inactivo') {
                            $condicionEstado = "WHERE al.estado = '$estadoSeleccionado'";
                        }

                        $query = mysqli_query($conexion, "SELECT 
                            al.idalum, al.dni, al.nombres, al.apellidos, al.email, al.fecnacimiento, al.estado,
                            au.seccion,
                            n.nombre AS nivel, 
                            g.nombre AS grado
                            FROM alumnos al 
                            INNER JOIN aulas au ON al.aula = au.idaula 
                            INNER JOIN grados g ON au.grado = g.idgrado 
                            INNER JOIN niveles n ON g.nivel = n.idniv
                            $condicionEstado");

                        $result = mysqli_num_rows($query);
                        if ($result > 0) {
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?php echo $data['idalum']; ?></td>
                                    <td><?php echo $data['dni']; ?></td>
                                    <td><?php echo $data['apellidos'] . ', ' . $data['nombres']; ?></td>
                                    <td><?php echo $data['email']; ?></td>
                                    <td><?php echo $data['fecnacimiento']; ?></td>
                                    <td><?php echo $data['nivel'] . ' - ' . $data['grado'] . ' - ' . $data['seccion']; ?></td>
                                    <td><?php echo $data['estado']; ?></td>
                                    <td>
                                        <?php if ($data['estado'] == 'Activo') { ?>
                                            <a href="addAlumnos.php?idalum=<?php echo $data['idalum']; ?>" class="btn" style="background-color: #71B600; color: white;">
                                                <i class='fas fa-edit'></i>
                                            </a>
                                            <form action="deleteAlumnos.php?idalum=<?php echo $data['idalum']; ?>" method="post" class="eliminarconfirmar d-inline">
                                                <button class="btn" style="background-color: red; color: white;" type="submit">
                                                    <i class='fas fa-trash-alt'></i>
                                                </button>
                                            </form>
                                        <?php } else { ?>
                                            <a href='reactivarAlumno.php?idalum=<?php echo $data['idalum']; ?>' class='btn btn-info'>Reactivar</a>

                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- /.End Page Content -->
 <?php if (isset($_SESSION['mensaje'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '<?= $_SESSION['tipo_mensaje'] == "success" ? "Éxito" : "Error" ?>',
            text: '<?= $_SESSION['mensaje'] ?>',
            icon: '<?= $_SESSION['tipo_mensaje'] ?>',
            confirmButtonText: 'OK'
        });
    });
</script>
<?php 
unset($_SESSION['mensaje']);
unset($_SESSION['tipo_mensaje']);
endif; 
?>

<?php
require_once "../Includes/Footer.php";
?>