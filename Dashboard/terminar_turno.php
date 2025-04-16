<?php
require '../Includes/db.php';

if (isset($_POST['turno_id'], $_POST['meson_id'])) {
    $turno_id = intval($_POST['turno_id']);
    $meson_id = intval($_POST['meson_id']);

    // Terminar la atención
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendido', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$turno_id]);

    header("Location: dashboard.php?meson_id=$meson_id");
    exit;
}
?>
