<?php
include '../includes/db.php';
$ocultarNavbar = true;
include '../includes/header.php';

if (!isset($_POST['rut'])) {
    header('Location: index.php');
    exit;
}

$rut = $_POST['rut'];

// Obtener servicios y materias asociadas
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY letra ASC, nombre ASC")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias ORDER BY nombre ASC")->fetchAll();

// Agrupar materias por servicio
$materiasPorServicio = [];
foreach ($materias as $materia) {
    $materiasPorServicio[$materia['servicio_id']][] = $materia;
}
?>

<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">Seleccione una Materia</h2>
        <p class="text-muted">RUT: <?= htmlspecialchars($rut) ?></p>
    </div>

    <?php if (count($servicios) <= 6): ?>
        <!-- Vista tradicional con tarjetas si hay pocos servicios -->
        <div class="row">
            <?php foreach ($servicios as $servicio): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <strong><?= htmlspecialchars($servicio['nombre']) ?> (<?= $servicio['letra'] ?>)</strong>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($materiasPorServicio[$servicio['id']])): ?>
                                <?php foreach ($materiasPorServicio[$servicio['id']] as $materia): ?>
                                    <form method="POST" action="confirmar.php" class="mb-2">
                                        <input type="hidden" name="rut" value="<?= htmlspecialchars($rut) ?>">
                                        <input type="hidden" name="materia_id" value="<?= $materia['id'] ?>">
                                        <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <?= htmlspecialchars($materia['nombre']) ?>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <form method="POST" action="confirmar.php" class="text-center">
                                    <input type="hidden" name="rut" value="<?= htmlspecialchars($rut) ?>">
                                    <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        Seleccionar Servicio
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Vista en acordeón si hay muchos servicios -->
        <div class="accordion" id="accordionServicios">
            <?php foreach ($servicios as $index => $servicio): ?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading<?= $index ?>">
                        <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                            <?= htmlspecialchars($servicio['nombre']) ?> (<?= $servicio['letra'] ?>)
                        </button>
                    </h2>
                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
                        aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionServicios">
                        <div class="accordion-body">
                            <?php if (!empty($materiasPorServicio[$servicio['id']])): ?>
                                <?php foreach ($materiasPorServicio[$servicio['id']] as $materia): ?>
                                    <form method="POST" action="confirmar.php" class="mb-2">
                                        <input type="hidden" name="rut" value="<?= htmlspecialchars($rut) ?>">
                                        <input type="hidden" name="materia_id" value="<?= $materia['id'] ?>">
                                        <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <?= htmlspecialchars($materia['nombre']) ?>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <form method="POST" action="confirmar.php" class="text-center">
                                    <input type="hidden" name="rut" value="<?= htmlspecialchars($rut) ?>">
                                    <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        Seleccionar Servicio
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
