<?php
session_start();
require_once "Includes/Connection.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['idUser_temp']) || !isset($_SESSION['role_temp']) || !isset($_SESSION['tabla_temp'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['idUser_temp'];
$rol_usuario = $_SESSION['role_temp'];
$tabla = $_SESSION['tabla_temp'];

// Obtener correo y nombre del usuario
$query = "SELECT email, nombres FROM $tabla WHERE ";
$query .= ($tabla == "administradores") ? "idadm=?" : (($tabla == "docentes") ? "iddoc=?" : "idalum=?");

$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($correo_usuario, $nombre_usuario);
$stmt->fetch();
$stmt->close();

// Generar nuevo OTP
$codigo_otp = rand(100000, 999999);
$expira = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Guardar OTP en la base de datos
$queryUpdate = "UPDATE $tabla SET codigo_otp=?, otp_expiracion=? WHERE ";
$queryUpdate .= ($tabla == "administradores") ? "idadm=?" : (($tabla == "docentes") ? "iddoc=?" : "idalum=?");
$stmtUpdate = $conexion->prepare($queryUpdate);
$stmtUpdate->bind_param("ssi", $codigo_otp, $expira, $id_usuario);
$stmtUpdate->execute();
$stmtUpdate->close();

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
                        <tr>
                            <td bgcolor='#28a745' style='padding:20px; text-align:center; color:white;'>
                                <h1 style='margin:0;'>Colegio Blas Pascal</h1>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:center; padding:30px;'>
                                <img src='https://img.icons8.com/color/96/000000/verified-account.png' alt='Verificación' width='100' style='margin-bottom:20px;'>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding:0 30px 30px; text-align:center;'>
                                <h2 style='color:#28a745;'>Nuevo código de verificación</h2>
                                <p style='font-size:16px; color:#333;'>Hola, <strong>$nombre_usuario</strong></p>
                                <p style='font-size:16px; color:#333;'>
                                    Tu nuevo código de verificación es:
                                </p>
                                <p style='margin:30px 0;'>
                                    <span style='display:inline-block; background-color: #f0f0f0; padding: 15px 25px; font-size: 24px; letter-spacing: 5px; border-radius: 8px; font-weight: bold; color: #333;'>
                                        $codigo_otp
                                    </span>
                                </p>
                                <p style='font-size:14px; color:#777;'>Este código expirará en 5 minutos.</p>
                                <hr style='margin:20px 0;'>
                                <p style='font-size:12px; color:#999;'>
                                    Si no solicitaste este código, puedes ignorar este mensaje.
                                </p>
                            </td>
                        </tr>
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

    // Redirigir con mensaje de éxito
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Código reenviado',
            text: 'Se envió un nuevo código de verificación a su correo.',
            confirmButtonText: 'Ok'
        }).then(() => {
            window.location = 'verificar_otp.php';
        });
    });
    </script>";
    exit();

} catch (Exception $e) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo reenviar el código. Inténtelo nuevamente.',
            confirmButtonText: 'Ok'
        }).then(() => {
            window.location = 'verificar_otp.php';
        });
    });
    </script>";
}
?>
