<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (!empty($_SESSION['active'])) {
    switch ($_SESSION['role']) {
        case 'docente':
            header('location: VistaDocente/index.php');
            break;
        case 'administrador':
            header('location: VistaAdministrador/index.php');
            break;
        case 'alumno':
            header('location: VistaAlumno/index.php');
            break;
    }
    exit();
} else {
    if (!empty($_POST)) {
        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'Ingrese las credenciales correctas',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            });
            </script>";
        } else {
            require_once "Includes/Connection.php";
            require 'vendor/autoload.php';

            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = mysqli_real_escape_string($conexion, $_POST['clave']);

            // Consulta para docentes
            $query_docentes = mysqli_query($conexion, "SELECT * FROM docentes WHERE dni = '$user'");
            $resultado_docentes = mysqli_num_rows($query_docentes);

            // Consulta para administradores
            $query_administradores = mysqli_query($conexion, "SELECT * FROM administradores WHERE dni = '$user'");
            $resultado_administradores = mysqli_num_rows($query_administradores);

            // Consulta para alumnos
            $query_alumnos = mysqli_query($conexion, "SELECT * FROM alumnos WHERE dni = '$user'");
            $resultado_alumnos = mysqli_num_rows($query_alumnos);

            $user_found = false;

            if ($resultado_docentes > 0) {
                $dato = mysqli_fetch_array($query_docentes);
                if (password_verify($clave, $dato['clave'])) {
                    $user_found = true;
                    $tabla = "docentes";
                    $id_usuario = $dato['iddoc'];
                    $correo_usuario = $dato['email'];
                    $nombre_usuario = $dato['nombres'];
                    $rol_usuario = 'docente';
                }
            } elseif ($resultado_administradores > 0) {
                $dato = mysqli_fetch_array($query_administradores);
                if (password_verify($clave, $dato['clave'])) {
                    $user_found = true;
                    $tabla = "administradores";
                    $id_usuario = $dato['idadm'];
                    $correo_usuario = $dato['email'];
                    $nombre_usuario = $dato['nombres'];
                    $rol_usuario = 'administrador';
                }
            } elseif ($resultado_alumnos > 0) {
                $dato = mysqli_fetch_array($query_alumnos);
                if (password_verify($clave, $dato['clave'])) {
                    $user_found = true;
                    $tabla = "alumnos";
                    $id_usuario = $dato['idalum'];
                    $correo_usuario = $dato['email'];
                    $nombre_usuario = $dato['nombres'];
                    $rol_usuario = 'alumno';
                }
            }

            if ($user_found) {
                // Generar código OTP
                $codigo_otp = rand(100000, 999999);
                $expira = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                // Guardar OTP en la base de datos
                $queryUpdate = "UPDATE $tabla SET codigo_otp=?, otp_expiracion=? WHERE ";
                $queryUpdate .= ($tabla == "administradores") ? "idadm=?" : (($tabla == "docentes") ? "iddoc=?" : "idalum=?");
                $stmtUpdate = $conexion->prepare($queryUpdate);
                $stmtUpdate->bind_param("ssi", $codigo_otp, $expira, $id_usuario);
                $stmtUpdate->execute();

                // Enviar correo con PHPMailer
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'johnnystattoo24@gmail.com';
                    $mail->Password = 'eyuuzvfwckynglpf';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('johnnystattoo24@gmail.com', 'Colegio Blas Pascal - Verificación');
                    $mail->addAddress($correo_usuario, $nombre_usuario);
                    $mail->Subject = 'Código de verificación';
                    $mail->CharSet = 'UTF-8';
                    $mail->isHTML(true);
                    $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='es'>
                    <head>
                        <meta charset='UTF-8'>
                        <title>Verificación de inicio de sesión</title>
                        <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
                    </head>
                    <body style='margin:0; padding:0; font-family: Poppins, Arial, sans-serif; background-color: #f4f4f4;'>
                        <table width='100%' bgcolor='#f4f4f4' cellpadding='0' cellspacing='0'>
                            <tr>
                                <td>
                                    <table align='center' width='600' cellpadding='0' cellspacing='0' bgcolor='#ffffff' style='margin:40px auto; border-radius:8px; overflow:hidden; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
                                        
                                        <!-- Header -->
                                        <tr>
                                            <td bgcolor='#28a745' style='padding:20px; text-align:center; color:white;'>
                                                <h1 style='margin:0;'>Colegio Blas Pascal</h1>
                                            </td>
                                        </tr>

                                        <!-- Imagen -->
                                        <tr>
                                            <td style='text-align:center; padding:30px;'>
                                                <img src='https://img.icons8.com/?size=100&id=zDcXogDRCtTj&format=png&color=000000' alt='Verificación' width='100' style='margin-bottom:20px;'>
                                            </td>
                                        </tr>

                                        <!-- Mensaje -->
                                        <tr>
                                            <td style='padding:0 30px 30px; text-align:center;'>
                                                <h2 style='color:#28a745;'>Verificación de inicio de sesión</h2>
                                                <p style='font-size:16px; color:#333;'>Hola, <strong>$nombre_usuario</strong></p>
                                                <p style='font-size:16px; color:#333;'>
                                                    Hemos recibido un intento de inicio de sesión en tu cuenta. Para completar el acceso, ingresa el siguiente código de verificación:
                                                </p>

                                                <!-- Código OTP -->
                                                <p style='margin:30px 0;'>
                                                    <span style='display:inline-block; background-color: #f0f0f0; padding: 15px 25px; font-size: 24px; letter-spacing: 5px; border-radius: 8px; font-weight: bold; color: #333;'>
                                                        $codigo_otp
                                                    </span>
                                                </p>

                                                <p style='font-size:14px; color:#777;'>Este código expirará en 5 minutos.</p>
                                                <hr style='margin:20px 0;'>
                                                <p style='font-size:12px; color:#999;'>
                                                    Si no solicitaste esta verificación, puedes ignorar este mensaje.
                                                </p>
                                            </td>
                                        </tr>

                                        <!-- Footer -->
                                        <tr>
                                            <td bgcolor='#f0f0f0' style='padding:20px; text-align:center; font-size:12px; color:#777;'>
                                                © 2025 Colegio Blas Pascal | notificaciones.blaspascal@gmail.com
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";


                    $mail->send();

                    // Guardar en sesión para verificar luego
                    $_SESSION['idUser_temp'] = $id_usuario;
                    $_SESSION['role_temp'] = $rol_usuario;
                    $_SESSION['tabla_temp'] = $tabla;

                    // Mostrar SweetAlert y luego redirigir
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                        icon: 'success',
                        title: 'Código enviado',
                        text: 'Se envió el código de verificación a su correo',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                        if (result.isConfirmed) {
                        window.location = 'verificar_otp.php';
                        }
                        });
                        });
                    </script>";
                    exit();
                } catch (Exception $e) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al enviar el código de verificación: {$mail->ErrorInfo}',
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Credenciales incorrectas',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    });
                </script>";
                session_destroy();
            }

            mysqli_close($conexion);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="Styles/Login.css">
    <link rel="stylesheet" type="text/css" href="Styles/Login2.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <title>Blas Pascal</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-4 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
                <div class="featured-image mb-1">
                    <img src="Styles/Img/Portada.jpg" class="img-fluid" style="border-radius: 1rem 0 0 1rem; height: 630px;">
                </div>
            </div>
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="login-content">
                        <form method="post" action="index.php" id="formLogin">
                            <div class="d-flex justify-content-center align-items-center mb-1 pb-1">
                                <img src="Styles/Img/Logo.jpg" alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem; height: 190px; width: 190px;">
                            </div>
                            <h6 class="fw-normal mb-3 pb-3 text-center" style="letter-spacing: 2px;">Ingresar las credenciales para iniciar sesión</h6>
                            <div class="input-div one">
                                <div class="i">
                                    <i class="fas fa-user"></i>
                                </div>

                                <div class="div">
                                    <h5>Usuario</h5>
                                    <input id="usuario" type="text" class="input" name="usuario">
                                </div>
                            </div>

                            <div class="input-div pass">

                                <div class="i">
                                    <i class="fas fa-lock"></i>
                                </div>

                                <div class="div">
                                    <h5>Contraseña</h5>
                                    <input type="password" id="input" class="input" name="clave">
                                </div>

                            </div>

                            <div class="view">
                                <div class="fas fa-eye verPassword" onclick="vista()" id="verPassword"></div>
                            </div>

                            <br>

                            <br>
                            <input name="btningresar" class="btn" type="submit" value="INICIAR SESIÓN">
                            <a href="recuperar_contrasena.php" class="btn-link" style="color: #007BFF;">Olvidé mi contraseña</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
    <script src="js/main2.js"></script>
    <script src="js/fontawesome.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Loader al enviar formulario
    document.getElementById('formLogin').addEventListener('submit', function(e) {
        // Muestra el loader antes de enviar
        Swal.fire({
            title: 'Procesando...',
            html: '<img src="Imagenes/loading.gif" width="80" alt="Cargando...">',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Obtener los campos de entrada
        var usuarioInput = document.getElementById('usuario');
        var passwordInput = document.getElementById('input');

        // Obtener los mensajes de alerta
        var mensajesAlerta = document.querySelectorAll('.alert');

        // Ocultar los mensajes de alerta al comenzar a escribir en los campos de entrada
        usuarioInput.addEventListener('keyup', function() {
            ocultarMensajesAlerta();
        });

        passwordInput.addEventListener('keyup', function() {
            ocultarMensajesAlerta();
        });

        // Función para ocultar los mensajes de alerta
        function ocultarMensajesAlerta() {
            mensajesAlerta.forEach(function(alerta) {
                alerta.style.display = 'none';
            });
        }
    });
</script>