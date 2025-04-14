<?php
require '../../Includes/db.php';

// Obtener todos los servicios disponibles
$stmt = $pdo->query("SELECT * FROM servicios");
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../Includes/header.php'; ?> <!-- Tu header con Bootstrap -->

<div class="container mt-4">
    <h2 class="mb-4">Crear Mesón</h2>

    <form action="guardar.php" method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado:</label>
            <input type="text" name="estado" class="form-control" value="disponible" required>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="disponible" value="1" checked>
            <label class="form-check-label">Disponible</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Servicios Asociados:</label><br>
            <?php foreach ($servicios as $servicio): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="servicios[]" value="<?= $servicio['id'] ?>" id="serv_<?= $servicio['id'] ?>">
                    <label class="form-check-label" for="serv_<?= $servicio['id'] ?>">
                        <?= htmlspecialchars($servicio['nombre']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../../Includes/footer.php'; ?> <!-- Si tenés un footer -->
