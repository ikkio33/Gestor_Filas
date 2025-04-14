<?php
require '../Includes/db.php';
require '../Includes/header.php';

// Mostrar listado de mesones si no se ha seleccionado uno
if (!isset($_GET['meson_id'])) {
    $mesones = $pdo->query("SELECT * FROM meson WHERE disponible = 1")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container mt-4">
        <h2 class="mb-4">Selecciona el mesón que estás atendiendo</h2>
        <div class="row">
            <?php foreach ($mesones as $meson): ?>
                <div class="col-md-4 mb-3">
                    <a href="dashboard.php?meson_id=<?= $meson['id'] ?>" class="btn btn-primary w-100 p-3">
                        <?= htmlspecialchars($meson['nombre']) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php exit;
}

// ID del mesón actual
$meson_id = intval($_GET['meson_id']);

// Obtener nombre del mesón
$meson = $pdo->prepare("SELECT nombre FROM meson WHERE id = ?");
$meson->execute([$meson_id]);
$meson = $meson->fetch(PDO::FETCH_ASSOC);

// Obtener servicios asociados al mesón
$stmt = $pdo->prepare("SELECT servicio_id FROM meson_servicio WHERE meson_id = ?");
$stmt->execute([$meson_id]);
$servicios_ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'servicio_id');

$siguiente_turno = null;
$turnos_espera = [];

if (!empty($servicios_ids)) {
    $placeholders = implode(',', array_fill(0, count($servicios_ids), '?'));

    // Siguiente turno pendiente para hoy
    $stmt = $pdo->prepare("
        SELECT * FROM turnos
        WHERE estado = 'pendiente'
        AND servicio_id IN ($placeholders)
        AND DATE(created_at) = CURDATE()
        ORDER BY created_at ASC LIMIT 1
    ");
    $stmt->execute($servicios_ids);
    $siguiente_turno = $stmt->fetch(PDO::FETCH_ASSOC);

    // Todos los turnos pendientes para hoy
    $stmt = $pdo->prepare("
        SELECT *, TIMESTAMPDIFF(MINUTE, created_at, NOW()) AS minutos_espera
        FROM turnos
        WHERE estado = 'pendiente'
        AND servicio_id IN ($placeholders)
        AND DATE(created_at) = CURDATE()
        ORDER BY created_at ASC
    ");
    $stmt->execute($servicios_ids);
    $turnos_espera = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Dashboard - <?= htmlspecialchars($meson['nombre']) ?></h2>

    <?php if ($siguiente_turno): ?>
        <form action="llamar_turno.php" method="POST" class="mb-3">
            <input type="hidden" name="meson_id" value="<?= $meson_id ?>">
            <input type="hidden" name="turno_id" value="<?= $siguiente_turno['id'] ?>">
            <button type="submit" class="btn btn-success btn-lg w-100">
                📢 Llamar a siguiente turno (<?= $siguiente_turno['numero_turno'] ?>)
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-info">No hay turnos pendientes para hoy en este mesón.</div>
    <?php endif; ?>

    <h5>Turnos en espera hoy</h5>
    <ul class="list-group">
        <?php foreach ($turnos_espera as $turno): ?>
            <li class="list-group-item d-flex justify-content-between">
                <span><?= $turno['numero_turno'] ?></span>
                <span class="badge bg-secondary">⏱ <?= $turno['minutos_espera'] ?> min</span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include '../Includes/footer.php'; ?>
