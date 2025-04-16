<?php
// includes/header.php
session_start(); // Asegúrate de tener esto al inicio
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #1c2c4c !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: #ffc107 !important;
        }

        .nav-item.active .nav-link {
            color: #ffc107 !important;
        }
    </style>
</head>
<body>

<?php if ((!isset($ocultarNavbar) || !$ocultarNavbar) && isset($_SESSION['usuario_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Notaria/login.php">Mi Notaría</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">

                <!-- Siempre visible -->
                <li class="nav-item active">
                    <a class="nav-link" href="/Notaria/dashboard/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>

                <!-- Solo para administradores y superiores -->
                <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) !== 'funcionario'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/Notaria/Admin/usuarios/index.php"><i class="fas fa-users-cog"></i> Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Notaria/Admin/ventanas/organizador.php"><i class="fas fa-layer-group"></i> Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Notaria/Admin/usuarios/lista.php"><i class="fas fa-ticket-alt"></i> Turnos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Notaria/Admin/Meson/index.php"><i class="fas fa-desktop"></i> Mesón</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Siempre visible -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-danger" href="/Notaria/login.php?logout=1"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
