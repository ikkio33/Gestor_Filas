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

    // Verificar que el turno exista y esté pendiente
    $stmt = $pdo->prepare("SELECT * FROM turnos WHERE id = ? AND estado = 'pendiente'");
    $stmt->execute([$turno_id]);
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        header("Location: dashboard.php?meson_id=$meson_id&error=no_encontrado");
        exit;
    }

    // Actualizar estado del turno a 'atendiendo'
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendiendo', meson_id = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt->execute([$meson_id, $turno_id])) {
        header("Location: dashboard.php?meson_id=$meson_id&error=fallo_update");
        exit;
    }

    header("Location: dashboard.php?meson_id=$meson_id&turno_llamado=$turno_id");
    exit;
}

header("Location: dashboard.php?error=faltan_datos");
?>
