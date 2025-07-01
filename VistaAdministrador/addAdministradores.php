<?php
session_start();
$id_user = $_SESSION['idUser'];
include('../Includes/Connection.php');
include_once "Ajax/ajaxAdministradores.php";
include_once "../Includes/Header.php";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Administradores</h6>
        </div>
        <div class="card-body" style="color: black;">
            <form id="formAdministradores" method="post" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="idadm" id="idadm" value="<?php echo $idadm; ?>">
                            <label class="col-form-label">DNI:</label>
                            <input type="text" class="form-control" name="dniadm" id="dniadm" value="<?php echo $dni; ?>" required pattern="[0-9]{8}" maxlength="8" minlength="8">
                            <div class="invalid-feedback">Ingrese un DNI válido de 8 dígitos.</div>
                        </div>
                        <div class="form-group">
                            
                            <label class="col-form-label">Nombres:</label>
                            <input type="text" class="form-control" name="nombresadm" id="nombresadm" value="<?php echo $nombres; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Apellidos:</label>
                            <input type="text" class="form-control" name="apellidosadm" id="apellidosadm" value="<?php echo $apellidos; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Correo electrónico:</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" required>
                            <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                        </div>
                        
                    </div>  
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label class="col-form-label">Teléfono:</label>
                            <input type="text" class="form-control" name="telefonoadm" id="telefonoadm" value="<?php echo $telefono; ?>" required pattern="^9\d{8}$" maxlength="9">
                            <div class="invalid-feedback">Ingrese un número de 9 dígitos valido.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Rol:</label>
                            <select class="form-control" name="roladm" id="roladm" required >
                                <option value="Coordinador" <?php echo ($estado == 'Coordinador') ? 'selected' : ''; ?>>Coordinador</option>
                                <option value="Administrador" <?php echo ($estado == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
    <label class="col-form-label">Contraseña:</label>
    <div class="input-group">
        <input type="password" class="form-control" name="claveadm" id="claveadm" <?php echo empty($idadm) ? 'required' : ''; ?>>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fas fa-eye" id="iconEye"></i>
            </button>
        </div>
        <div class="invalid-feedback">Ingrese una contraseña.</div>
    </div>
    <?php if (!empty($idadm)) : ?>
        <small class="form-text text-muted">
            Si no deseas cambiar la contraseña, deja este campo vacío.
        </small>
    <?php endif; ?>
</div>
                        
                    </div> 
                </div>
                <div class="modal-footer">
                    <a href="tblAdministradores.php" class="btn" style="background-color: red; color: white;">Cancelar</a>
                    <input type="submit" value="Guardar" class="btn" id="btnAccion" style="background-color: #71B600; color: white;">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /.End Page Content -->
 <script>
    // Bootstrap validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('claveadm');
    const icon = document.getElementById('iconEye');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>


<?php
require_once "../Includes/Footer.php";
?>