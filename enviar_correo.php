<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
include 'Includes/Connection.php';

$email = $_POST['email'];
$roles = ['alumnos', 'docentes', 'administradores'];
$rol_encontrado = null;
$dni = null;

foreach ($roles as $rol) {
    $sql = "SELECT dni FROM $rol WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $rol_encontrado = $rol;
        $dni = $result->fetch_assoc()['dni'];
        break;
    }
}

if (!$rol_encontrado) {
    header("Location: recuperar_contrasena.php?mensaje=Correo+no+encontrado&tipo=warning");
    exit;
}

$token = bin2hex(random_bytes(32));
$expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

$sql2 = "UPDATE $rol_encontrado SET recovery_token=?, token_expiry=? WHERE email=?";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("sss", $token, $expiry, $email);
$stmt2->execute();

$enlace = "http://localhost/Colegio_SOA/restablecer.php?rol=$rol_encontrado&token=$token";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;
    $mail->Username = 'johnnystattoo24@gmail.com';
    $mail->Password = 'eyuuzvfwckynglpf';

    $mail->setFrom('johnnystattoo24@gmail.com', 'Colegio Blas Pascal - Recuperación');
    $mail->addAddress($email);
    $mail->Subject = 'Solicitud de recuperación de contraseña';
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Body = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Recuperación de contraseña</title>
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
                            <img src='https://img.icons8.com/?size=100&id=FbRY9JkBrjiX&format=png&color=000000' alt='Recuperar contraseña' width='100' style='margin-bottom:20px;'>
                        </td>
                    </tr>

                    <!-- Mensaje -->
                    <tr>
                        <td style='padding:0 30px 30px; text-align:center;'>
                            <h2 style='color:#28a745;'>Solicitud de recuperación de contraseña</h2>
                            <p style='font-size:16px; color:#333;'>Hola,</p>
                            <p style='font-size:16px; color:#333;'>
                                Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón de abajo para continuar.
                            </p>

                            <!-- Botón -->
                            <p style='margin:30px 0;'>
                                <a href='$enlace' style='background-color: #71B600; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block;'>
                                    Restablecer Contraseña
                                </a>
                            </p>

                            <p style='font-size:14px; color:#777;'>Este enlace expirará en 1 hora.</p>
                            <hr style='margin:20px 0;'>
                            <p style='font-size:12px; color:#999;'>
                                Si no solicitaste esta recuperación, puedes ignorar este mensaje.
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

    $mail->AltBody = 'Haz clic en el siguiente enlace para restablecer tu contraseña: ' . $enlace . "\n\nEste enlace expirará en 1 hora.";

    $mail->send();
    header("Location: recuperar_contrasena.php?mensaje=Correo+enviado+correctamente&tipo=success");
    exit;
} catch (Exception $e) {
    error_log("Error al enviar correo: " . $mail->ErrorInfo);
    header("Location: recuperar_contrasena.php?mensaje=Error+al+enviar+el+correo&tipo=danger");
    exit;
}
