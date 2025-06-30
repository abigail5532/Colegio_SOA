<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="Styles/recuperar_clave.css">
</head>

<body>

    <?php if (isset($_GET['mensaje'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?= htmlspecialchars($_GET['tipo'] ?? 'info') ?>',
                    title: '<?= htmlspecialchars($_GET['mensaje']) ?>',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    <?php endif; ?>

    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Imagen izquierda -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-image"></div>

            <!-- Formulario derecha -->
            <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center">
                <div class="text-center mb-4">
                    <img src="Imagenes/logo.png" alt="Logo" class="logo-imagen mb-2">
                    <br>
                    <br>
                    <h1 class="titulo-imagen">Recupera tu contraseña</h1>
                </div>
                <div class="form-container w-75">
                    <form id="formRecuperar" action="enviar_correo.php" method="POST">
                        <div class="mb-4">
                            <input type="email" name="email" class="form-control input-line" required placeholder="Correo electrónico">
                            <div class="form-text text-start">Ingrese su correo registrado para enviarle el enlace.</div>
                        </div>
                        <button type="submit" class="btn btn-custom w-100 mb-3">Enviar enlace de recuperación</button>
                    </form>

                    <div class="text-center">
                        <a href="index.php" class="link-custom">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS para animación de input -->
    <script>
        // Loader al enviar formulario
        document.getElementById('formRecuperar').addEventListener('submit', function(e) {
            e.preventDefault(); // evita envío inmediato
            Swal.fire({
                title: 'Enviando correo...',
                html: '<img src="Imagenes/loading.gif" width="80" alt="Cargando...">',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    
                    setTimeout(() => {
                        e.target.submit();
                    }, 500);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.input-line');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.classList.add('focused');
                });
                input.addEventListener('blur', () => {
                    if (input.value === "") {
                        input.classList.remove('focused');
                    }
                });
            });
        });
    </script>

</body>

</html>