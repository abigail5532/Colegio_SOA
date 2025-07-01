<?php
session_start();
$id_user = $_SESSION['idUser'];
include('../Includes/Connection.php');
include_once "Ajax/ajaxAlumnos.php";
include_once "../Includes/Header.php";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="head-table m-0 font-weight-bold">Registro de Alumnos</h6>
        </div>
        <div class="card-body" style="color: black;">
            <form id="formAlumnos" method="post"  class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" name="idalum" id="idalum" value="<?php echo $idalum; ?>">
                            <label class="col-form-label">DNI:</label>
                            <input type="text" class="form-control" name="dnialum" id="dnialum" value="<?php echo $dni; ?>" required pattern="[0-9]{8}" maxlength="8" minlength="8">
                            <div class="invalid-feedback">Ingrese un DNI válido de 8 dígitos.</div>
                        </div>
                        <div class="form-group">
                            
                            <label class="col-form-label">Nombres:</label>
                            <input type="text" class="form-control" name="nombresalum" id="nombresalum" value="<?php echo $nombres; ?>"  required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Apellidos:</label>
                            <input type="text" class="form-control" name="apellidosalum" id="apellidosalum" value="<?php echo $apellidos; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label">Género:</label>
                            <select class="form-control" name="generoalum" id="generoalum" required>
                                <option value="F" <?php if($genero == 'F') echo 'selected'; ?>>Femenino</option> 
                                <option value="M" <?php if($genero == 'M') echo 'selected'; ?>>Masculino</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un genero.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Fec. Nacimiento:</label>
                            <input type="date" class="form-control" name="fecnacimientoalum" id="fecnacimientoalum" value="<?php echo $fecnacimiento; ?>" required>
                            <div class="invalid-feedback">Ingrese una fecha.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Dirección:</label>
                            <input type="text" class="form-control" name="direccionalum" id="direccionalum" value="<?php echo $direccion; ?>" required>
                            <div class="invalid-feedback">Ingrese una dirección.</div>
                        </div>
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label">Correo electrónico:</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" required>
                            <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                        </div>
                        <div class="form-group">
    <label class="col-form-label">Aula:</label>
    <select class="form-control" name="aulaalum" id="aulaalum" required>
        <option value="" disabled selected>-- Seleccionar nivel académico --</option>
        <?php
        $query = mysqli_query($conexion, "SELECT au.idaula, au.seccion, n.nombre AS nivel, g.nombre AS grado 
                                          FROM aulas au 
                                          INNER JOIN grados g ON au.grado = g.idgrado 
                                          INNER JOIN niveles n ON g.nivel = n.idniv");
        while ($row = mysqli_fetch_assoc($query)) {
            $selected = ($row['idaula'] == $aula) ? 'selected' : '';
            echo "<option value='" . $row['idaula'] . "' $selected>" . $row['nivel'] . " - " . $row['grado'] . " - " . $row['seccion'] . "</option>";
        }
        ?>
    </select>
    <div class="invalid-feedback">Seleccione un aula.</div>
</div>

                        <div class="form-group">
    <label class="col-form-label">Contraseña:</label>
    <div class="input-group">
        <input type="password" class="form-control" name="clavealum" id="clavealum" <?php echo empty($idalum) ? 'required' : ''; ?>>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fas fa-eye" id="iconEye"></i>
            </button>
        </div>
        <div class="invalid-feedback">Ingrese una contraseña.</div>
    </div>
    <?php if (!empty($idalum)) : ?>
        <small class="form-text text-muted">
            Si no deseas cambiar la contraseña, deja este campo vacío.
        </small>
    <?php endif; ?>
</div>

                    </div> 
                </div>
                <hr>
                <div style="text-align: center; margin-bottom: 10px;">
                    <span style="border-radius: 12px; padding: 7px; color: #71B600; font-weight: 800;">
                        DATOS DE LOS APODERADOS
                    </span>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label">DNI <sub>(Madre)</sub>:</label>
                            <input type="text" class="form-control" name="dnima" id="dnima" value="<?php echo $dnima; ?>" required pattern="[0-9]{8}" maxlength="8" minlength="8">
                            <div class="invalid-feedback">Ingrese un DNI válido de 8 dígitos.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Nombres y Apellidos <sub>(Madre)</sub>:</label>
                            <input type="text" class="form-control" name="nombresma" id="nombresma" value="<?php echo $nombresma; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Teléfono <sub>(Madre)</sub>:</label>
                            <input type="text" class="form-control" name="telefonoma" id="telefonoma" value="<?php echo $telefonoma; ?>" required pattern="^9\d{8}$" maxlength="9">
                            <div class="invalid-feedback">Ingrese un número de 9 dígitos valido.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label">DNI <sub>(Padre)</sub>:</label>
                            <input type="text" class="form-control" name="dnipa" id="dnipa" value="<?php echo $dnipa; ?>" required pattern="[0-9]{8}" maxlength="8" minlength="8">
                            <div class="invalid-feedback">Ingrese un DNI válido de 8 dígitos.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Nombres y Apellidos <sub>(Padre)</sub>:</label>
                            <input type="text" class="form-control" name="nombrespa" id="nombrespa" value="<?php echo $nombrespa; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Teléfono <sub>(Padre)</sub>:</label>
                            <input type="text" class="form-control" name="telefonopa" id="telefonopa" value="<?php echo $telefonopa; ?>" required pattern="^9\d{8}$" maxlength="9">
                            <div class="invalid-feedback">Ingrese un número de 9 dígitos valido.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-form-label">DNI <sub>(Apoderado)</sub>:</label>
                            <input type="text" class="form-control" name="dniapod" id="dniapod" value="<?php echo $dniapod; ?>" required pattern="[0-9]{8}" maxlength="8" minlength="8">
                            <div class="invalid-feedback">Ingrese un DNI válido de 8 dígitos.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Nombres y Apellidos<sub>(Apoderado)</sub>:</label>
                            <input type="text" class="form-control" name="nombresapod" id="nombresapod" value="<?php echo $nombresapod; ?>" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                            <div class="invalid-feedback">Ingrese solo letras.</div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Teléfono <sub>(Apoderado)</sub>:</label>
                            <input type="text" class="form-control" name="telefonoapod" id="telefonoapod" value="<?php echo $telefonoapod; ?>" required pattern="^9\d{8}$" maxlength="9">
                            <div class="invalid-feedback">Ingrese un número de 9 dígitos valido.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="tblAlumnos.php" class="btn" style="background-color: red; color: white;">Cancelar </a>
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
    const passwordField = document.getElementById('clavealum');
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
include_once "../Includes/Footer.php";
?>