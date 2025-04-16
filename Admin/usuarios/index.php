<?php
// admin/usuarios/index.php

include '../../includes/header.php';
include '../../includes/db.php';

// Obtener todos los usuarios
$query = "SELECT * FROM usuarios";
$stmt = $pdo->query($query);
$usuarios = $stmt->fetchAll();

// Comprobar si se ha solicitado verificar una contraseña
if (isset($_GET['verificar_id'])) {
    $usuario_id = $_GET['verificar_id'];
    $query = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $usuario_id]);
    $usuarioVerificado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuarioVerificado) {
        // Mostrar un formulario para ingresar la contraseña a verificar
        echo "<div class='alert alert-info mt-3'>Verificación de contraseña para: {$usuarioVerificado['nombre']}</div>";
        echo "<form method='POST' action=''>";
        echo "<div class='form-group'>";
        echo "<label for='password'>Ingrese la contraseña para verificar:</label>";
        echo "<input type='password' name='password' class='form-control' required>";
        echo "<input type='hidden' name='usuario_id' value='{$usuarioVerificado['id']}'>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-primary'>Verificar Contraseña</button>";
        echo "</form>";
    }
}

// Verificación de la contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id']) && isset($_POST['password'])) {
    $usuario_id = $_POST['usuario_id'];
    $password = $_POST['password'];

    // Obtener el usuario
    $query = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificar si la contraseña ingresada coincide con el hash
        if (password_verify($password, $usuario['password'])) {
            echo "<div class='alert alert-success'>La contraseña es correcta para el usuario {$usuario['nombre']}.</div>";
        } else {
            echo "<div class='alert alert-danger'>Contraseña incorrecta para el usuario {$usuario['nombre']}.</div>";
        }
    }
}
?>

<!-- Título -->
<div class="container mt-5">
    <h1 class="text-center mb-4">Usuarios Registrados</h1>

    <!-- Botón para agregar un nuevo usuario -->
    <div class="mb-4 text-end">
        <a href="crear.php" class="btn btn-primary">Nuevo Usuario</a>
    </div>

    <!-- Tabla de usuarios -->
    <table class="table table-hover table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= ucfirst($usuario['rol']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar.php?id=<?= $usuario['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                        <!-- Botón para verificar la contraseña -->
                        <a href="?verificar_id=<?= $usuario['id'] ?>" class="btn btn-info btn-sm">Verificar Contraseña</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include '../../includes/footer.php';
?>
