<?php
require '../Includes/db.php';

if (isset($_POST['turno_id'], $_POST['meson_id'])) {
    $turno_id = intval($_POST['turno_id']);
    $meson_id = intval($_POST['meson_id']);

    // Verificar si ya hay un turno en atención
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE meson_id = ? AND estado = 'atendiendo'");
    $stmt->execute([$meson_id]);

    if ($stmt->fetchColumn() > 0) {
        header("Location: dashboard.php?meson_id=$meson_id&error=ocupado");
        exit;
    }

    // Volver a llamar al mismo turno
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendiendo', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$turno_id]);

    header("Location: dashboard.php?meson_id=$meson_id");
    exit;
}
