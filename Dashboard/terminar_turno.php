<?php
require '../Includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turno_id = $_POST['turno_id'];
    $meson_id = $_POST['meson_id'];

    // Marcar el turno como atendido
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendido', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$turno_id]);

    // Liberar el mesón (opcional si tienes columna disponible o estado)
    $stmt = $pdo->prepare("UPDATE meson SET estado = 'libre' WHERE id = ?");
    $stmt->execute([$meson_id]);

    header("Location: dashboard.php?meson_id=" . $meson_id);
    exit;
}

