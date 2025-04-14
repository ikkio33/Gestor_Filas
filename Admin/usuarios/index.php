<?php
// admin/usuarios/index.php

include '../../includes/header.php';
include '../../includes/db.php';

// Obtener todos los usuarios
$query = "SELECT * FROM usuarios";
$stmt = $pdo->query($query);
$usuarios = $stmt->fetchAll();

?>

<!-- Titulo -->
<div class="container mt-5">
    <h1 class="text-center mb-4">Usuarios Registrados</h1>

    <!-- Botón para agregar un nuevo usuario -->
    <div class="mb-4 text-right">
        <a href="crear.php" class="btn btn-success">Agregar Nuevo Usuario</a>
    </div>

    <!-- Tabla de usuarios -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
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
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include '../../includes/footer.php';
?>
