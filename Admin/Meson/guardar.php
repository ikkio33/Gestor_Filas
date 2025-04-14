<?php
require '../../Includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meson_id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $estado = $_POST['estado'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $servicios = isset($_POST['servicios']) ? $_POST['servicios'] : [];

    // Iniciar una transacción para asegurar que todas las operaciones se hagan correctamente
    $pdo->beginTransaction();

    try {
        // Actualizar el nombre y estado del mesón
        $updateMesonQuery = $pdo->prepare("UPDATE meson SET nombre = ?, estado = ?, disponible = ? WHERE id = ?");
        $updateMesonQuery->execute([$nombre, $estado, $disponible, $meson_id]);

        // Eliminar los servicios actuales asociados al mesón
        $deleteServiciosQuery = $pdo->prepare("DELETE FROM meson_servicio WHERE meson_id = ?");
        $deleteServiciosQuery->execute([$meson_id]);

        // Insertar los nuevos servicios seleccionados
        if (!empty($servicios)) {
            $insertServiciosQuery = $pdo->prepare("INSERT INTO meson_servicio (meson_id, servicio_id) VALUES (?, ?)");
            foreach ($servicios as $servicio_id) {
                $insertServiciosQuery->execute([$meson_id, $servicio_id]);
            }
        }

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir o mostrar mensaje de éxito
        header("Location: index.php?success=1");
        exit;
    } catch (Exception $e) {
        // Si algo sale mal, hacer rollback de la transacción
        $pdo->rollBack();
        // Mostrar el error
        echo "Error: " . $e->getMessage();
    }
}
?>
