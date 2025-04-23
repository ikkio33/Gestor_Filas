<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atencion de Mesones</title>

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

<!-- Includes/navbar-dash.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="../Dashboard/seleccionar_meson.php">🖥️ Meson</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContenido">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php?meson_id=<?= $_GET['meson_id'] ?? '' ?>">📊 Panel</a>
                </li>
            </ul>
            <span class="navbar-text text-light me-3">
                Mesón: <strong><?= htmlspecialchars($meson['nombre'] ?? '') ?></strong>
            </span>
            <a href="../login.php" class="btn btn-outline-light btn-sm">Salir</a> 
        </div>
    </div>
</nav>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
