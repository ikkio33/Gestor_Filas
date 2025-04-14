<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" rel="stylesheet" href="/Notaria/public/CSS/styles.css">

    <!-- Incluir Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../public/CSS/public_Styles.css">

</head>
<body>
<!-- navbar.php o donde tengas el código del navbar -->
<?php if (!isset($ocultarNavbar) || !$ocultarNavbar): ?><!--ocultar barra de nav-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Mi Notaría</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="/Notaria/dashboard/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Notaria/Admin/usuarios/index.php">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Notaria/Admin/ventanas/organizador.php">Servicios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Notaria/Admin/usuarios/lista.php">Turnos </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Notaria/Admin/Meson/index.php">Meson</a>
            </li>
            <!-- Agrega más enlaces aquí -->
        </ul>
    </div>
</nav>
<?php endif; ?>

    <!-- Incluir Bootstrap JS y Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

