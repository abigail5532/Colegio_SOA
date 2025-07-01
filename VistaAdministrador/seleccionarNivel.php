<?php
session_start();
include('../Includes/Connection.php');
include_once "../Includes/Header.php";
$iddoc = $_GET['iddoc'];
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="m-0 font-weight-bold">1. Selecciona un Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $niveles = mysqli_query($conexion, "SELECT * FROM niveles");
                while ($nivel = mysqli_fetch_assoc($niveles)) { ?>
                    <div class="col-md-4 mb-4">
                        <a href="seleccionarGrado.php?iddoc=<?php echo $iddoc; ?>&idnivel=<?php echo $nivel['idniv']; ?>" style="text-decoration: none;">
                            <div class="card shadow text-center h-100" style="cursor: pointer;">
                            <img class="img-fluid" src="../Imagenes/tutoria.jpg" alt="Card image cap"/>
                    <p style="text-align: center; color: black; margin-top: 17px; margin-bottom: 0%; font-weight: 800;">
                        
                    </p>    
                            <div class="card-body">
                                    <h5 class="card-title"><?php echo $nivel['nombre']; ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                    <a href="tblDocentes.php?iddoc=<?php echo $iddoc; ?>" class="btn btn-secondary">← Volver</a>
                </div>
        </div>
    </div>
</div>

<?php include_once "../Includes/Footer.php"; ?>
