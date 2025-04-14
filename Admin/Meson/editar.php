<?php
require '../../Includes/db.php';

$id = intval($_GET['id']); // Obtener el ID del mesón

// Consultar los datos del mesón
$stmt = $pdo->prepare("SELECT * FROM meson WHERE id = ?");
$stmt->execute([$id]);
$meson = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener todos los servicios disponibles
$servicios = $pdo->query("SELECT * FROM servicios")->fetchAll(PDO::FETCH_ASSOC);

// Obtener los servicios ya asociados a este mesón
$asociados = $pdo->query("SELECT servicio_id FROM meson_servicio WHERE meson_id = $id")->fetchAll(PDO::FETCH_ASSOC);
$servicios_actuales = array_column($asociados, 'servicio_id');
?>

<?php include '../../Includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Editar Mesón</h2>

    <form action="guardar.php" method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="id" value="<?= $meson['id'] ?>"> <!-- ID del mesón -->

        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($meson['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado:</label>
            <input type="text" name="estado" class="form-control" value="<?= htmlspecialchars($meson['estado']) ?>" required>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="disponible" value="1" <?= $meson['disponible'] ? 'checked' : '' ?>>
            <label class="form-check-label">Disponible</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Servicios Asociados:</label><br>
            <?php foreach ($servicios as $servicio): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="servicios[]" value="<?= $servicio['id'] ?>" id="serv_<?= $servicio['id'] ?>"
                        <?= in_array($servicio['id'], $servicios_actuales) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="serv_<?= $servicio['id'] ?>">
                        <?= htmlspecialchars($servicio['nombre']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>

    <!-- Botón de eliminar -->
    <form action="eliminar.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este mesón?');">
        <input type="hidden" name="id" value="<?= $meson['id'] ?>">
        <button type="submit" class="btn btn-danger mt-3">Eliminar Mesón</button>
    </form>
</div>

<?php include '../../Includes/footer.php'; ?>
