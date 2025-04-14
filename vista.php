<?php
$ocultarNavbar = true;
include '../Notaria/includes/db.php';
include '../Notaria/includes/header.php';   

// Obtener los turnos que están siendo atendidos (estado 'atendiendo')
$sql = "SELECT t.codigo_turno, s.nombre AS servicio, m.nombre AS materia, mes.nombre AS meson_nombre
        FROM turnos t
        LEFT JOIN servicios s ON t.servicio_id = s.id
        LEFT JOIN materias m ON t.materia_id = m.id
        LEFT JOIN meson_servicio ms ON t.servicio_id = ms.servicio_id
        LEFT JOIN meson mes ON ms.meson_id = mes.id
        WHERE t.estado = 'atendiendo'  -- Estado actualizado a 'atendiendo'
        ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$turnos_en_atencion = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h1 class="text-center">Turnos en Atención</h1>
    
    <div class="turnos-container">
        <?php if (count($turnos_en_atencion) > 0): ?>
            <?php foreach ($turnos_en_atencion as $turno): ?>
                <div class="turno">
                    <h4 class="turno-title">Turno: <?= htmlspecialchars($turno['codigo_turno']) ?></h4>
                    <p><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio']) ?></p>
                    <p><strong>Materia:</strong> <?= htmlspecialchars($turno['materia']) ?></p>
                    <p><strong>Mesón:</strong> <?= htmlspecialchars($turno['meson_nombre']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No hay turnos en atención en este momento.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Recarga la página cada 10 segundos para obtener los nuevos turnos
    setInterval(function() {
        location.reload(); // Recarga la página para obtener los nuevos turnos
    }, 10000); // Cada 10 segundos
</script>

<?php include '../Notaria/includes/footer.php'; ?>