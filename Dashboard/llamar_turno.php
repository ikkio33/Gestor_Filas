<?php
require '../Includes/db.php';

if (isset($_POST['turno_id'], $_POST['meson_id'])) {
    $turno_id = intval($_POST['turno_id']);
    $meson_id = intval($_POST['meson_id']);

    // Verificar si ya hay un turno en atención en este mesón
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE meson_id = ? AND estado = 'atendiendo'");
    $stmt->execute([$meson_id]);

    if ($stmt->fetchColumn() > 0) {
        header("Location: dashboard.php?meson_id=$meson_id&error=ocupado");
        exit;
    }

    // Llamar al turno (actualizar estado y mesón)
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendiendo', meson_id = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$meson_id, $turno_id]);

    header("Location: dashboard.php?meson_id=$meson_id");
    exit;
}

// Al final de llamar_turno.php
header("Location: dashboard.php?meson_id=$meson_id&turno_llamado=$turno_id");

?>
