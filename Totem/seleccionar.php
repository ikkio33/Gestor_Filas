<?php
include '../includes/db.php';
$ocultarNavbar = true;include '../includes/header.php';

if (!isset($_POST['rut'])) {
    header('Location: index.php');
    exit;
}

$rut = $_POST['rut'];

// Obtener servicios y materias asociadas
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias")->fetchAll();

// Agrupar materias por servicio
$materiasPorServicio = [];
foreach ($materias as $materia) {
    $materiasPorServicio[$materia['servicio_id']][] = $materia;
}
?>

<div class="container mt-5 text-center">
    <h2>Seleccione una Materia</h2>
    <p class="text-muted">RUT: <?= htmlspecialchars($rut) ?></p>

    <div class="row">
        <?php foreach ($servicios as $servicio): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <strong><?= htmlspecialchars($servicio['nombre']) ?> (<?= $servicio['letra'] ?>)</strong>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($materiasPorServicio[$servicio['id']])): ?>
                            <?php foreach ($materiasPorServicio[$servicio['id']] as $materia): ?>
                                <form method="POST" action="confirmar.php" class="d-grid mb-2">
                                    <input type="hidden" name="rut" value="<?= htmlspecialchars($rut) ?>">
                                    <input type="hidden" name="materia_id" value="<?= $materia['id'] ?>">
                                    <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <?= htmlspecialchars($materia['nombre']) ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Sin materias</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
