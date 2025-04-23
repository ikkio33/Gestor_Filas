<?php
require '../Includes/db.php';
require '../Includes/navbar-dash.php';

// Validación de mesón seleccionado
if (!isset($_GET['meson_id'])) {
    header('Location: seleccionar_meson.php');
    exit;
}

$meson_id = $_GET['meson_id'];

// Obtener el nombre del mesón
$meson = $pdo->prepare("SELECT nombre FROM meson WHERE id = ?");
$meson->execute([$meson_id]);
$meson = $meson->fetch(PDO::FETCH_ASSOC);

// Obtener servicios asociados al mesón
$stmt = $pdo->prepare("SELECT servicio_id FROM meson_servicio WHERE meson_id = ?");
$stmt->execute([$meson_id]);
$servicios_ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'servicio_id');

// Inicializar variables
$turno_actual = null;
$turnos_espera = [];
$turnos_atendidos = [];

if (!empty($servicios_ids)) {
    // Turno actual en atención
    $stmt = $pdo->prepare("
        SELECT * FROM turnos 
        WHERE estado = 'atendiendo' 
        AND meson_id = ? 
        AND DATE(created_at) = CURDATE() 
        ORDER BY updated_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$meson_id]);
    $turno_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    // Turnos pendientes
    $placeholders = implode(',', array_fill(0, count($servicios_ids), '?'));
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

    // Turnos atendidos
    $stmt = $pdo->prepare("
        SELECT codigo_turno, created_at, updated_at, 
        TIMESTAMPDIFF(MINUTE, created_at, updated_at) AS minutos_atencion
        FROM turnos 
        WHERE estado = 'atendido' 
        AND servicio_id IN ($placeholders) 
        AND meson_id = ? 
        AND DATE(created_at) = CURDATE() 
        ORDER BY updated_at DESC
    ");
    $params = array_merge($servicios_ids, [$meson_id]);
    $stmt->execute($params);
    $turnos_atendidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?= htmlspecialchars($meson['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f7;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { max-width: 1200px; margin-top: 30px; }
        .turno-card {
            background: #fff; border-radius: 8px; padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px;
        }
        .turno-button { width: 100%; padding: 10px; font-size: 1rem; margin-top: 10px; }
        .turno-list li {
            padding: 10px; background-color: #f0f4f7; border-radius: 8px;
            margin-bottom: 10px; display: flex; justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">📟 Dashboard - <?= htmlspecialchars($meson['nombre']) ?></h2>

    <!-- Turno en atención -->
    <?php if ($turno_actual): ?>
        <div class="turno-card">
            <h3>🔴 Turno en Atención</h3>
            <p><strong>Turno:</strong> <?= $turno_actual['codigo_turno'] ?></p>
            <p><strong>Desde:</strong> <?= $turno_actual['created_at'] ?></p>
            <form action="terminar_turno.php" method="POST">
                <input type="hidden" name="turno_id" value="<?= $turno_actual['id'] ?>">
                <input type="hidden" name="meson_id" value="<?= $meson_id ?>">
                <button type="submit" class="btn btn-danger turno-button">✅ Finalizar turno</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No hay turno en atención actualmente.</div>
    <?php endif; ?>

    <!-- Llamar siguiente turno -->
    <div class="turno-card">
        <h3>📢 Llamar siguiente turno</h3>
        <?php if (!empty($turnos_espera)): ?>
            <form action="llamar_turno.php" method="POST">
                <input type="hidden" name="turno_id" value="<?= $turnos_espera[0]['id'] ?>">
                <input type="hidden" name="meson_id" value="<?= $meson_id ?>">
                <button type="submit" class="btn btn-primary turno-button">
                    📣 Llamar turno <?= $turnos_espera[0]['codigo_turno'] ?>
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-info">No hay turnos pendientes en este momento.</div>
        <?php endif; ?>
    </div>

    <!-- Turnos pendientes -->
    <div class="turno-card">
        <h3>⏳ Turnos Pendientes</h3>
        <?php if (count($turnos_espera) > 1): ?>
            <ul class="turno-list">
                <?php foreach (array_slice($turnos_espera, 1) as $turno): ?>
                    <li>
                        <div><strong><?= $turno['codigo_turno'] ?></strong> — <?= $turno['minutos_espera'] ?> min</div>
                        <small><?= $turno['created_at'] ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No hay más turnos en espera.</p>
        <?php endif; ?>
    </div>

    <!-- Turnos atendidos -->
    <div class="turno-card">
        <h3>✅ Turnos Atendidos Hoy</h3>
        <?php if (!empty($turnos_atendidos)): ?>
            <ul class="turno-list">
                <?php foreach ($turnos_atendidos as $turno): ?>
                    <li>
                        <div>
                            <strong><?= $turno['codigo_turno'] ?></strong><br>
                            <small>Finalizado: <?= date('H:i', strtotime($turno['updated_at'])) ?></small>
                        </div>
                        <span class="badge bg-success"><?= $turno['minutos_atencion'] ?> min</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">Aún no se han atendido turnos hoy.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Refrescar cada 10 segundos automáticamente
    setInterval(() => location.reload(), 10000);
</script>
</body>
</html>
