<?php
session_start();
$id_user = $_SESSION['idUser'];
include('../Includes/Connection.php');
include_once "Ajax/ajaxDocentes.php";
include_once "../Includes/Header.php";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!--DATATABLE-->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Docentes</h6>
        </div>
        
        <div class="card-body">
            <a href="addDocentes.php" id="btnNuevo" class="btn" type="button" style="background-color:  #71B600; color: white;"><i class="fa-solid fa-circle-plus"></i> Agregar</a>
            
            <form method="get" class="form-inline mb-3" style="float: right;">
    <label class="mr-2 font-weight-bold" >Filtrar por estado:</label>
    <select name="estado" class="form-control" onchange="this.form.submit()">
        <option value="Todos" <?php echo (!isset($_GET['estado']) || $_GET['estado'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
        <option value="Activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Activo') ? 'selected' : ''; ?>>Activos</option>
        <option value="Inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivos</option>
    </select>
</form>

            </br> 
            <div class="table-responsive" style="color: black;">
                <table class="table table-bordered" id="tblBlasPascal" width="100%" cellspacing="0" style="color: black;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Apellidos y Nombres</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $estadoFiltro = isset($_GET['estado']) ? $_GET['estado'] : 'Activo';

                        if ($estadoFiltro == 'Todos') {
                            $query = mysqli_query($conexion, "SELECT * FROM docentes");
                        } else {
                            $query = mysqli_query($conexion, "SELECT * FROM docentes WHERE estado = '$estadoFiltro'");
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?php echo $row['iddoc']; ?></td>
                            <td><?php echo $row['dni']; ?></td>
                            <td><?php echo $row['apellidos'] . ', ' . $row['nombres']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['estado']; ?></td>
                            <td>
                                <?php if ($row['estado'] == 'Activo') { ?>
                                    <a href="addAsignarDocenteAsignatura.php?iddoc=<?php echo $row['iddoc']; ?>" class="btn" style="background-color: #3357FF; color: white;"><i class='fas fa-book'></i></a>
                                    <a href="addDocentes.php?iddoc=<?php echo $row['iddoc']; ?>" class="btn" style="background-color: #71B600; color: white;"><i class='fas fa-edit'></i></a>
                                    <form action="deleteDocentes.php?iddoc=<?php echo $row['iddoc']; ?>" method="post" class="eliminarconfirmar d-inline">
                                        <button class="btn" style="background-color: red; color: white;" type="submit"><i class='fas fa-trash-alt'></i></button>
                                    </form>
                                <?php } else { ?>
                                    <form action="reactivarDocente.php?iddoc=<?php echo $row['iddoc']; ?>" method="post" class="d-inline">
                                        <button class="btn btn-success" type="submit"><i class='fas fa-redo'></i> Reactivar</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
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