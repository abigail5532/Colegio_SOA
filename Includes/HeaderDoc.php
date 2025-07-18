<?php
if (empty($_SESSION['active'])) {
    header('Location: ../');
}
include "../Includes/Connection.php";
$id_user = $_SESSION['idUser'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>BlasPascal</title>
    <!-- BEGIN CALENDAR -->
    <link rel="stylesheet" type="text/css" href="../Styles/cssCalendar/fullcalendar.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../Styles/cssCalendar/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Styles/cssCalendar/home.css">

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

    <!-- ÍCONOS -->
    <script src="https://kit.fontawesome.com/a20b11dfdd.js" crossorigin="anonymous"></script>
    <!-- Custom styles for this template-->
    <link href="../Styles/modules.css" rel="stylesheet">
    <link href="../Styles/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="../Styles/clockpicker.css" rel="stylesheet">

</head>

<body id="page-top" class="esto">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="menu navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon">
                    <img class="img-profile rounded-circle" src="../Imagenes/logo.png" style="width: 40px; height: 40px;">
                </div>
                <div class="sidebar-brand-text mx-3"><strong>Blas Pascal</strong></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-house"></i>
                    <span>Inicio</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item -->
            <li class="nav-item">
                <a class="nav-link" href="tblAsignaturas.php">
                    <i class="fas fa-fw fa-chalkboard-user"></i>
                    <span>Asignaturas</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item -->
            <li class="nav-item">
                <a class="nav-link" href="Calendario.php">
                    <i class="fas fa-fw fa-calendar-days"></i>
                    <span>Calendario</span></a>
            </li>

            <!-- Nav Item -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-pen-to-square"></i>
                    <span>Reuniones</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="tblHorarios.php">Horarios Disponibles</a>
                        <a class="collapse-item" href="tblCitas.php">Citas</a>
                        <div class="collapse-divider"></div>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" style="color: #71B600;">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter" id="count_notif"></span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown" style="max-height: 300px; overflow-y: auto;">
                                <h6 class="dropdown-header">
                                    Notificaciones
                                </h6>
                                <div id="notificaciones_content">
                                    <p class="dropdown-item text-center small text-gray-500">Cargando...</p>
                                </div>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION["apellidos"]; ?>, <?php echo $_SESSION["nombres"]; ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="../Imagenes/administrador.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../cerrarSesion.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="../js/jsCalendar/bootstrap.min.js"></script>

                <script>
                    function cargarNotificaciones() {
                        $.ajax({
                            url: '../Includes/get_notificaciones.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                var content = '';
                                var count = 0;

                                if (data.length > 0) {
                                    data.forEach(function(n) {
                                        if (n.leido == 0) count++;

                                        content += '<a class="dropdown-item d-flex align-items-center" href="#" onclick="verNotificacion(' + n.id + ')">';
                                        content += '<div class="mr-3">';
                                        content += '<div class="icon-circle bg-primary">';
                                        content += '<i class="fas fa-info text-white"></i>';
                                        content += '</div></div>';
                                        content += '<div>';
                                        content += '<div class="small text-gray-500">' + n.fecha + '</div>';
                                        content += '<span class="' + (n.leido == 0 ? 'font-weight-bold' : '') + '">' + n.mensaje + '</span>';
                                        content += '</div></a>';
                                    });
                                } else {
                                    content = '<p class="dropdown-item text-center small text-gray-500">Sin notificaciones</p>';
                                }

                                $('#notificaciones_content').html(content);
                                $('#count_notif').text(count > 0 ? count : '');
                            },
                            error: function(xhr, status, error) {
                                console.log('Error al cargar notificaciones:', error);
                            }
                        });
                    }

                    function verNotificacion(id) {
                        $('#detalleNotificacion').html('Cargando...');
                        $('#modalNotificacion').modal('show');

                        fetch('../Includes/detalle_notificacion.php?id=' + id)
                            .then(response => response.text())
                            .then(data => {
                                $('#detalleNotificacion').html(data);
                                cargarNotificaciones(); // Actualizar contador
                            })
                            .catch(error => {
                                $('#detalleNotificacion').html('Error al cargar la notificación.');
                                console.error(error);
                            });
                    }

                    $(document).ready(function() {
                        cargarNotificaciones();
                        setInterval(cargarNotificaciones, 20000);
                    });
                </script>

                <!-- Modal para Detalle de Notificación -->
                <div class="modal fade" id="modalNotificacion" tabindex="-1" role="dialog" aria-labelledby="modalNotifLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalNotifLabel">Detalle de Notificación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" id="detalleNotificacion">
                                Cargando notificación...
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- End of Topbar -->