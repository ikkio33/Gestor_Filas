<?php
session_start();
require '../Notaria/Includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los valores del formulario
    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($usuario) || empty($contrasena)) {
        header('Location: login.php?error=campos_vacios');
        exit();
    }

    // Buscar el usuario en la base de datos por nombre
    $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE nombre = :usuario LIMIT 1");
    $stmt->execute(['usuario' => $usuario]);
    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioData) {
        // Verificar que la contraseña sea correcta
        if (password_verify($contrasena, $usuarioData['password'])) {
            // Login correcto
            $_SESSION['usuario_id'] = $usuarioData['id'];
            $_SESSION['usuario'] = $usuarioData['nombre'];
            $_SESSION['rol'] = $usuarioData['rol'];

            // Redirigir según el rol del usuario
            switch (strtolower($usuarioData['rol'])) {
                case 'administrador':
                    // Redirigir a la página de administración de usuarios
                    header('Location: Admin/usuarios/index.php');
                    break;
                case 'funcionario':
                    // Redirigir al dashboard del funcionario
                    header('Location: Dashboard/dashboard.php');
                    break;
                default:
                    header('Location: login.php?error=rol_invalido');
                    exit();
            }
            exit();
        } else {
            // Contraseña incorrecta
            header('Location: login.php?error=contrasena_incorrecta');
            exit();
        }
    } else {
        // Usuario no encontrado
        header('Location: login.php?error=usuario_no_encontrado');
        exit();
    }
} else {
    // Si no es un POST, redirigir al login
    header('Location: login.php');
    exit();
}
?>
