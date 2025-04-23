<?php
require_once __DIR__ . '/../../Config/config.php';
session_start();

// Verificar que el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    // Redirigir o mostrar error
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso no autorizado: esta sección es solo para administradores.');
}


if (!defined('ACCESO_PERMITIDO')) {
    // Opcional: redirigir o mostrar error
    header('Location: //'); // O muestra un error 403
    exit('Acceso no autorizado');
}

// Admin/usuarios/crear.php
include '../../includes/header.php';
include '../../includes/db.php';



// Manejo del formulario
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = trim($_POST['rol']);
    $password = trim($_POST['password']);

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($email)) $errores[] = "El email es obligatorio.";
    if (empty($rol)) $errores[] = "El rol es obligatorio.";
    if (empty($password)) $errores[] = "La contraseña es obligatoria.";
    elseif (strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, rol, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $rol, $hash]);
        header("Location: index.php");
        exit;
    }
}
?>

<div class="container mt-4">
    <h2>Agregar Nuevo Usuario</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="crear.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select name="rol" id="rol" class="form-select" required>
                <option value="">Seleccione un rol</option>
                <option value="admin">Administrador</option>
                <option value="empleado">Funcionario</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required minlength="6">
        </div>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
