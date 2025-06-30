<?php
session_start();
require_once "Includes/Connection.php";

if (!isset($_SESSION['idUser_temp']) || !isset($_SESSION['role_temp']) || !isset($_SESSION['tabla_temp'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['idUser_temp'];
$rol_usuario = $_SESSION['role_temp'];
$tabla = $_SESSION['tabla_temp'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_ingresado = implode('', $_POST['otp']); // unir los 6 inputs

    $query = "SELECT codigo_otp, otp_expiracion FROM $tabla WHERE ";
    $query .= ($tabla == "administradores") ? "idadm=?" : (($tabla == "docentes") ? "iddoc=?" : "idalum=?");

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($codigo_otp_bd, $otp_expiracion_bd);
    $stmt->fetch();
    $stmt->close();

    if ($codigo_ingresado == $codigo_otp_bd && date("Y-m-d H:i:s") <= $otp_expiracion_bd) {
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $id_usuario;

        $query2 = "SELECT nombres, apellidos, dni FROM $tabla WHERE ";
        $query2 .= ($tabla == "administradores") ? "idadm=?" : (($tabla == "docentes") ? "iddoc=?" : "idalum=?");
        $stmt2 = $conexion->prepare($query2);
        $stmt2->bind_param("i", $id_usuario);
        $stmt2->execute();
        $stmt2->bind_result($nombres, $apellidos, $dni);
        $stmt2->fetch();
        $stmt2->close();

        $_SESSION['nombres'] = $nombres;
        $_SESSION['apellidos'] = $apellidos;
        $_SESSION['user'] = $dni;
        $_SESSION['role'] = $rol_usuario;

        unset($_SESSION['idUser_temp'], $_SESSION['role_temp'], $_SESSION['tabla_temp']);

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Código verificado',
                text: 'Inicio de sesión exitoso',
                icon: 'success',
                confirmButtonText: 'Continuar',
                customClass: { popup: 'poppins-font' }
            }).then(() => {
                window.location.href = 'Vista" . ucfirst($rol_usuario) . "/index.php';
            });
        });
        </script>";
        exit();
    } else {
        $error = "Código incorrecto o expirado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Verificación OTP</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="Styles/VerificarOTP.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="otp-card">
        <img src="https://img.icons8.com/color/96/000000/verified-account.png" alt="Verificar">
        <h4 class="mb-3">Verificación de Código</h4>
        <p>Ingrese su código de 6 dígitos enviado al correo.</p>

        <?php if (isset($error)) { ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error',
                        text: '<?php echo $error; ?>',
                        icon: 'error',
                        confirmButtonText: 'Ok',
                        customClass: {
                            popup: 'poppins-font'
                        }
                    });
                });
            </script>
        <?php } ?>

        <form method="POST" action="">
            <div class="otp-inputs d-flex justify-content-center mb-4">
                <?php for ($i = 0; $i < 6; $i++) { ?>
                    <input type="text" name="otp[]" maxlength="1" required pattern="\d">
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-verify w-100 py-2 text-white">Verificar</button>
        </form>
        <div class="mt-3 d-flex justify-content-between">
            <a href="reenviar_otp.php" class="btn btn-secondary">Reenviar Código</a>
            <a href="index.php" class="btn btn-outline-danger">Regresar al Login</a>
        </div>

    </div>

    <script>
        // Mover focus automáticamente
        const inputs = document.querySelectorAll('.otp-inputs input');
        inputs.forEach((input, i) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && i < inputs.length - 1) {
                    inputs[i + 1].focus();
                }
            });
        });
    </script>
</body>

</html>