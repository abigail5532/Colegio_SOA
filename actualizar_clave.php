<?php
include 'Includes/Connection.php';

$rol = $_POST['rol'];
$token = $_POST['token'];
$clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);

$sql = "SELECT dni FROM $rol WHERE recovery_token=? AND token_expiry > NOW()";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: recuperar_contrasena.php?mensaje=Token+inválido+o+expirado&tipo=danger");
    exit;
}

$sql2 = "UPDATE $rol SET clave=?, recovery_token=NULL, token_expiry=NULL WHERE recovery_token=?";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("ss", $clave, $token);
$stmt2->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contraseña actualizada</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f9f5;
        }
        .card-success {
            border: none;
            border-radius: 15px;
            background: #ffffff;
            padding: 40px 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }
        .card-success:hover {
            transform: scale(1.03);
        }
        .icon-success {
            color: #28a745;
            font-size: 4rem;
        }
        .btn-custom {
            background-color: #28a745;
            color: #fff;
            font-weight: 500;
        }
        .btn-custom:hover {
            background-color: #218838;
            color: #fff;
        }
        .logo {
            max-width: 120px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <img src="Imagenes/logo.png" alt="Logo Colegio" class="logo">
        </div>
        <div class="card card-success mx-auto text-center" style="max-width: 500px;">
            <i class="bi bi-check-circle-fill icon-success mb-3"></i>
            <h3 class="text-success">¡Contraseña actualizada!</h3>
            <p class="mb-4">Tu contraseña se ha cambiado correctamente.<br>Puedes iniciar sesión con tu nueva clave.</p>
            <a href="index.php" class="btn btn-custom">Volver al inicio de sesión</a>
        </div>
    </div>

    <script>
        // Mostrar SweetAlert de éxito automático al cargar
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: 'Tu contraseña ha sido cambiada con éxito.',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
