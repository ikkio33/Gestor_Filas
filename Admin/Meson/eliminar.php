<?php
require '../../Includes/db.php';

// Verificar si se pasó el ID
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Eliminar la relación de servicios con el mesón
    $stmt = $pdo->prepare("DELETE FROM meson_servicio WHERE meson_id = ?");
    $stmt->execute([$id]);

    // Eliminar el mesón
    $stmt = $pdo->prepare("DELETE FROM meson WHERE id = ?");
    $stmt->execute([$id]);

    // Redirigir al listado de mesones
    header("Location: index.php");
    exit;
} else {
    // Si no se pasó el ID, redirigir al listado de mesones
    header("Location: index.php");
    exit;
}
