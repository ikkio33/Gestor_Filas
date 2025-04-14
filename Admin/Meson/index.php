<?php
require '../../Includes/db.php';

// Obtener todos los mesones con sus servicios asociados
$stmt = $pdo->query("
    SELECT meson.id, meson.nombre, meson.estado, meson.disponible, GROUP_CONCAT(servicios.nombre) AS servicios
    FROM meson
    LEFT JOIN meson_servicio ON meson_servicio.meson_id = meson.id
    LEFT JOIN servicios ON servicios.id = meson_servicio.servicio_id
    GROUP BY meson.id
");
$mesones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../Includes/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Administrar Mesones</h2>
    <a href="crear.php" class="btn btn-primary mb-3">+ Crear nuevo mesón</a>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Disponible</th>
                    <th>Servicios Asociados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mesones as $meson): ?>
                <tr>
                    <td><?= htmlspecialchars($meson['nombre']) ?></td>
                    <td><?= htmlspecialchars($meson['estado']) ?></td>
                    <td>
                        <span class="badge bg-<?= $meson['disponible'] ? 'success' : 'secondary' ?>">
                            <?= $meson['disponible'] ? 'Sí' : 'No' ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        // Si hay servicios asociados, los mostramos
                        if ($meson['servicios']) {
                            $servicios = explode(',', $meson['servicios']);
                            foreach ($servicios as $servicio) {
                                echo '<span class="badge bg-info text-dark mb-1">' . htmlspecialchars($servicio) . '</span>';
                            }
                        } else {
                            echo '<span class="badge bg-warning text-dark mb-1">Sin servicios</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?= $meson['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
