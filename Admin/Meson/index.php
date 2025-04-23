<?php
// Incluir auth.php para que las funciones estén disponibles
require_once '../../Includes/auth.php';  // Asegúrate de que la ruta sea correcta

// Verificación de acceso para administradores
requiereRol('administrador');

require '../../Includes/db.php';
include '../../Includes/header.php';


// Parámetros de filtro
$disponibleFiltro = isset($_GET['disponible']) ? $_GET['disponible'] : '';
$servicioFiltro = isset($_GET['servicio']) ? $_GET['servicio'] : '';

// Paginación
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$registrosPorPagina = 10;
$inicio = ($pagina - 1) * $registrosPorPagina;

// Filtros dinámicos
$where = [];
$params = [];

if ($disponibleFiltro !== '') {
    $where[] = 'meson.disponible = :disponible';
    $params[':disponible'] = $disponibleFiltro;
}

if ($servicioFiltro !== '') {
    $where[] = 'servicios.nombre LIKE :servicio';
    $params[':servicio'] = "%$servicioFiltro%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Consulta principal con filtros y paginación
$stmt = $pdo->prepare("
    SELECT meson.id, meson.nombre, meson.disponible, 
           GROUP_CONCAT(servicios.nombre SEPARATOR ', ') AS servicios
    FROM meson
    LEFT JOIN meson_servicio ON meson_servicio.meson_id = meson.id
    LEFT JOIN servicios ON servicios.id = meson_servicio.servicio_id
    $whereSQL
    GROUP BY meson.id
    LIMIT :inicio, :limite
");

$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':limite', $registrosPorPagina, PDO::PARAM_INT);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$mesones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total para paginación
$countStmt = $pdo->prepare("
    SELECT COUNT(DISTINCT meson.id) as total
    FROM meson
    LEFT JOIN meson_servicio ON meson_servicio.meson_id = meson.id
    LEFT JOIN servicios ON servicios.id = meson_servicio.servicio_id
    $whereSQL
");
foreach ($params as $key => $val) {
    $countStmt->bindValue($key, $val);
}
$countStmt->execute();
$totalMesones = $countStmt->fetchColumn();
$totalPaginas = ceil($totalMesones / $registrosPorPagina);
?>

<div class="container mt-4">
    <h2 class="mb-4">Administrar Mesones</h2>
    <a href="crear.php" class="btn btn-primary mb-3">+ Crear nuevo mesón</a>

    <!-- Filtros -->
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <select name="disponible" class="form-select">
                <option value="">-- Disponibilidad --</option>
                <option value="1" <?= $disponibleFiltro === '1' ? 'selected' : '' ?>>Disponible</option>
                <option value="0" <?= $disponibleFiltro === '0' ? 'selected' : '' ?>>No disponible</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="servicio" class="form-control" placeholder="Buscar servicio..." value="<?= htmlspecialchars($servicioFiltro) ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100" type="submit">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="lista.php" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Disponible</th>
                    <th>Servicios Asociados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mesones as $meson): ?>
                    <tr>
                        <td><?= htmlspecialchars($meson['nombre']) ?></td>
                        <td>
                            <span class="badge bg-<?= $meson['disponible'] ? 'success' : 'danger' ?>">
                                <?= $meson['disponible'] ? 'Sí' : 'No' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($meson['servicios'])): ?>
                                <?php foreach (explode(',', $meson['servicios']) as $servicio): ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars(trim($servicio)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Sin servicios</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $meson['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Navegación de páginas -->
    <?php if ($totalPaginas > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>&disponible=<?= urlencode($disponibleFiltro) ?>&servicio=<?= urlencode($servicioFiltro) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include '../../Includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>