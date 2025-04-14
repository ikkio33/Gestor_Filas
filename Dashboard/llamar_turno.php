<?php
require '../Includes/db.php';

if (isset($_POST['turno_id'], $_POST['meson_id'])) {
    $turno_id = intval($_POST['turno_id']);
    $meson_id = intval($_POST['meson_id']);

    // Actualizar el estado del turno
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendiendo' WHERE id = ?");
    $stmt->execute([$turno_id]);

    // Redirigir de nuevo al dashboard
    header("Location: dashboard.php?meson_id=$meson_id");
    exit;
}
?>
