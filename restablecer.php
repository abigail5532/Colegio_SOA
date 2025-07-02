<?php
include 'Includes/Connection.php';

$rol = $_GET['rol'] ?? '';
$token = $_GET['token'] ?? '';

$sql = "SELECT dni FROM $rol WHERE recovery_token=? AND token_expiry > NOW()";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: recuperar_contrasena.php?mensaje=Token+inválido+o+expirado&tipo=danger");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="Styles/restablecer_clave.css">
</head>

<body>

    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Imagen izquierda -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-image"></div>

            <!-- Formulario derecha -->
            <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center">
                <div class="text-center mb-4">
                    <img src="Imagenes/logo.png" alt="Logo" class="logo-imagen mb-2">
                    <h1 class="titulo-imagen">Cambia tu contraseña</h1>
                </div>
                <div class="form-container w-75">
                    <form action="actualizar_clave.php" method="POST" onsubmit="return validarFormulario()">
                        <input type="hidden" name="rol" value="<?= htmlspecialchars($rol) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <div class="mb-4">
                            <label class="form-label">Nueva Contraseña</label>
                            <div class="d-flex align-items-start flex-column flex-md-row">
                                <div class="w-100">
                                    <input type="password" name="clave" id="clave" class="form-control" required>
                                    <div id="errorClave" class="invalid-feedback"></div>
                                </div>
                                <i class="fa-solid fa-eye toggle-password ms-md-2 mt-2" onclick="togglePassword('clave', this)"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Repetir Contraseña</label>
                            <div class="d-flex align-items-start flex-column flex-md-row">
                                <div class="w-100">
                                    <input type="password" id="confirmar" class="form-control" required>
                                    <div id="errorConfirmar" class="invalid-feedback"></div>
                                </div>
                                <i class="fa-solid fa-eye toggle-password ms-md-2 mt-2" onclick="togglePassword('confirmar', this)"></i>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-custom w-100">Actualizar Contraseña</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const claveInput = document.getElementById('clave');
        const confirmarInput = document.getElementById('confirmar');
        const errorClave = document.getElementById('errorClave');
        const errorConfirmar = document.getElementById('errorConfirmar');
        const iconClave = document.getElementById('iconClave');
        const iconConfirmar = document.getElementById('iconConfirmar');


        const regexClave = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&._/-])[A-Za-z\d@$!%*#?&._/-]{8,}$/;


        claveInput.addEventListener('input', () => {
            const valor = claveInput.value;
            if (!regexClave.test(valor)) {
                claveInput.classList.add('is-invalid');
                claveInput.classList.remove('is-valid');
                errorClave.textContent = 'Mínimo 8 caracteres, incluir letras, números y un caracter especial.';
                iconClave.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
                iconClave.classList.add('invalid');
                iconClave.classList.remove('valid');
            } else {
                claveInput.classList.remove('is-invalid');
                claveInput.classList.add('is-valid');
                errorClave.textContent = '';
                iconClave.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                iconClave.classList.add('valid');
                iconClave.classList.remove('invalid');
            }
        });


        confirmarInput.addEventListener('input', () => {
            if (confirmarInput.value !== claveInput.value) {
                confirmarInput.classList.add('is-invalid');
                confirmarInput.classList.remove('is-valid');
                errorConfirmar.textContent = 'Las contraseñas no coinciden.';
                iconConfirmar.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
                iconConfirmar.classList.add('invalid');
                iconConfirmar.classList.remove('valid');
            } else {
                confirmarInput.classList.remove('is-invalid');
                confirmarInput.classList.add('is-valid');
                errorConfirmar.textContent = '';
                iconConfirmar.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
                iconConfirmar.classList.add('valid');
                iconConfirmar.classList.remove('invalid');
            }
        });

        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }


        function validarFormulario() {
            if (claveInput.value !== confirmarInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.'
                });
                return false;
            }

            if (!regexClave.test(claveInput.value)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña insegura',
                    text: 'Debe tener mínimo 8 caracteres, incluir letras, números y un caracter especial.'
                });
                return false;
            }

            return true;
        }
    </script>
</body>

</html>