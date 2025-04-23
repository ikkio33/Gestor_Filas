<?php
require '../../Includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];

    $pdo->beginTransaction();

    try {
        // Verificamos si se pasó un ID (para editar) o no (para crear)
        if (!empty($_POST['id'])) {
            // 🛠️ ACTUALIZAR
            $meson_id = intval($_POST['id']);

            $updateMesonQuery = $pdo->prepare("UPDATE meson SET nombre = ?, estado = ?, disponible = ? WHERE id = ?");
            $updateMesonQuery->execute([$nombre, $estado, $disponible, $meson_id]);

            $deleteServiciosQuery = $pdo->prepare("DELETE FROM meson_servicio WHERE meson_id = ?");
            $deleteServiciosQuery->execute([$meson_id]);
        } else {
            // 🆕 CREAR
            $insertMesonQuery = $pdo->prepare("INSERT INTO meson (nombre, estado, disponible) VALUES (?, ?, ?)");
            $insertMesonQuery->execute([$nombre, $estado, $disponible]);
            $meson_id = $pdo->lastInsertId(); // Recuperar ID recién creado
        }

        // Asociar servicios seleccionados (si hay)
        if (!empty($servicios)) {
            $insertServiciosQuery = $pdo->prepare("INSERT INTO meson_servicio (meson_id, servicio_id) VALUES (?, ?)");
            foreach ($servicios as $servicio_id) {
                $insertServiciosQuery->execute([$meson_id, $servicio_id]);
            }
        }

        $pdo->commit();
        header("Location: index.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
