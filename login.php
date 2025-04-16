<?php 
session_start();

// Cierre de sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Mensaje si la sesión ya está activa
$mensajeBienvenida = null;
if (isset($_SESSION['usuario'])) {
    $mensajeBienvenida = "¡Hola de nuevo, {$_SESSION['usuario']}! Ya tienes una sesión activa.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Notaría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-title {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Iniciar Sesión</h2>

        <?php if ($mensajeBienvenida): ?>
            <div class="alert alert-success text-center">
                <?= $mensajeBienvenida ?>
            </div>
            <div class="text-center mt-3">
                <a href="dashboard/dashboard.php" class="btn btn-primary w-100 mb-2">Ir al Dashboard</a>
                <a href="login.php?logout=1" class="btn btn-danger w-100">Cerrar sesión</a>
            </div>
        <?php else: ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                        switch ($_GET['error']) {
                            case 'campos_vacios':
                                echo "Por favor, complete todos los campos.";
                                break;
                            case 'usuario_no_encontrado':
                                echo "El usuario no fue encontrado.";
                                break;
                            case 'contrasena_incorrecta':
                                echo "La contraseña es incorrecta.";
                                break;
                            case 'rol_invalido':
                                echo "Rol de usuario no válido.";
                                break;
                            default:
                                echo "Hubo un error. Inténtelo nuevamente.";
                                break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form action="procesar_login.php" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
