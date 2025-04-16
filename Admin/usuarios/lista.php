<?php
include '../../includes/db.php';
include '../../includes/header.php';

// Filtros
$whereClauses = [];
$params = [];

if (!empty($_GET['estado'])) {
    $whereClauses[] = 't.estado = ?';
    $params[] = $_GET['estado'];
}
if (!empty($_GET['servicio_id'])) {
    $whereClauses[] = 't.servicio_id = ?';
    $params[] = $_GET['servicio_id'];
}
if (!empty($_GET['materia_id'])) {
    $whereClauses[] = 't.materia_id = ?';
    $params[] = $_GET['materia_id'];
}
if (!empty($_GET['meson_id'])) {
    $whereClauses[] = 't.meson_id = ?'; // 👈 Cambiado de ms a t
    $params[] = $_GET['meson_id'];
}
if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
    $whereClauses[] = 't.created_at BETWEEN ? AND ?';
    $params[] = $_GET['fecha_inicio'];
    $params[] = $_GET['fecha_fin'];
}

// Paginación
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Query base SIN JOIN con meson_servicio
$baseSQL = "FROM turnos t
LEFT JOIN servicios s ON t.servicio_id = s.id
LEFT JOIN materias m ON t.materia_id = m.id
LEFT JOIN clientes c ON t.cliente_id = c.id
LEFT JOIN meson me ON t.meson_id = me.id"; // 👈 Aquí usamos directamente t.meson_id

$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = " WHERE " . implode(' AND ', $whereClauses);
}

// Total de registros
$sqlCount = "SELECT COUNT(*) $baseSQL $whereSQL";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Obtener turnos
$sql = "SELECT t.*, s.letra, s.nombre AS servicio, m.nombre AS materia,
        c.rut, t.meson_id, me.nombre AS meson
        $baseSQL $whereSQL 
        ORDER BY t.created_at DESC LIMIT $inicio, $registrosPorPagina";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$turnos = $stmt->fetchAll();

// Datos para los selects
$servicios = $pdo->query("SELECT * FROM servicios")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias")->fetchAll();
$mesones = $pdo->query("SELECT * FROM meson")->fetchAll();
?>


<div class="container my-5">
    <h2 class="mb-4 fw-bold text-primary">Historial de Turnos</h2>

    <div class="card shadow-sm rounded p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= ($_GET['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_atencion" <?= ($_GET['estado'] ?? '') === 'en_atencion' ? 'selected' : '' ?>>En atención</option>
                    <option value="finalizado" <?= ($_GET['estado'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Servicio</label>
                <select name="servicio_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= $servicio['id'] ?>" <?= ($_GET['servicio_id'] ?? '') == $servicio['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($servicio['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Materia</label>
                <select name="materia_id" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?= $materia['id'] ?>" <?= ($_GET['materia_id'] ?? '') == $materia['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($materia['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Mesón</label>
                <select name="meson_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($mesones as $meson): ?>
                        <option value="<?= $meson['id'] ?>" <?= ($_GET['meson_id'] ?? '') == $meson['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($meson['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="lista.php" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm rounded">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Turno</th>
                    <th>RUT</th>
                    <th>Servicio</th>
                    <th>Materia</th>
                    <th>Estado</th>
                    <th>Mesón</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($turnos) > 0): ?>
                    <?php foreach ($turnos as $turno): ?>
                        <tr class="text-center align-middle">
                            <td class="fw-bold"><?= $turno['codigo_turno'] ?></td>
                            <td><?= $turno['rut'] ?></td>
                            <td><?= $turno['servicio'] ?></td>
                            <td><?= $turno['materia'] ?></td>
                            <td>
                                <span class="badge bg-<?= $turno['estado'] === 'finalizado' ? 'success' : ($turno['estado'] === 'en_atencion' ? 'warning text-dark' : 'secondary') ?>">
                                    <?= ucfirst($turno['estado']) ?>
                                </span>
                            </td>
                            <td><?= $turno['meson'] ?? '-' ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($turno['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $paginaActual ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
