<?php
session_start();
$id_user = $_SESSION['idUser'];
include('../Includes/Connection.php');
include_once "Ajax/ajaxAdministradores.php";
include_once "../Includes/Header.php";

// Determinar el estado a mostrar
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'Activo';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!--DATATABLE-->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Administradores</h6>
        </div>
        <div class="card-body">
            <a href="addAdministradores.php" id="btnNuevo" class="btn btn-light" type="button" style="background-color:  #71B600; color: white;"><i class="fa-solid fa-circle-plus"></i> Agregar</a>
            <!-- Filtro por estado -->
            <form method="get" class="form-inline mb-3" style="float: right;">
    <label class="mr-2 font-weight-bold" >Filtrar por estado:</label>
    <select name="estado" class="form-control" onchange="this.form.submit()">
        <option value="Todos" <?php echo (!isset($_GET['estado']) || $_GET['estado'] == 'Todos') ? 'selected' : ''; ?>>Todos</option>
        <option value="Activo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Activo') ? 'selected' : ''; ?>>Activos</option>
        <option value="Inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivos</option>
    </select>
</form>
</br></br>

            <div class="table-responsive" style="color: black;">
                <table class="table table-bordered" id="tblBlasPascal" width="100%" cellspacing="0" style="color: black;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Apellidos y Nombres</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($estado == 'Todos') {
                            $query = mysqli_query($conexion, "SELECT * FROM administradores");
                        } else {
                            $query = mysqli_query($conexion, "SELECT * FROM administradores WHERE estado = '$estado'");
                        }

                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?php echo $data['idadm']; ?></td>
                            <td><?php echo $data['dni']; ?></td>
                            <td><?php echo $data['apellidos'] . ', ' . $data['nombres']; ?></td>
                            <td><?php echo $data['email']; ?></td>
                            <td><?php echo $data['telefono']; ?></td>
                            <td><?php echo $data['rol']; ?></td>
                            <td><?php echo $data['estado']; ?></td>
                            <td>
                                <?php if ($data['estado'] == 'Activo') { ?>
                                    <a href="addAdministradores.php?idadm=<?php echo $data['idadm']; ?>" class="btn" style="background-color: #71B600; color: white;"><i class='fas fa-edit'></i></a>
                                    <form action="deleteAdministradores.php?idadm=<?php echo $data['idadm']; ?>" method="post" class="eliminarconfirmar d-inline">
                                        <button class="btn" style="background-color: red; color: white;" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                    </form>
                                <?php } else { ?>
                                    <form action="reactivarAdministradores.php?idadm=<?php echo $data['idadm']; ?>" method="post" class="d-inline">
                                        <button class="btn btn-success" type="submit"><i class='fas fa-redo'></i> Reactivar</button>
                                    </form>
                                    <?php } ?>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
</div>
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
