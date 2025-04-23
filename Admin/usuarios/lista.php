<?php
require_once '../../Includes/auth.php';
requiereRol('administrador');
include '../../includes/db.php';
include '../../includes/header.php';

// Eliminar todo el historial de turnos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_todos'])) {
    $stmt = $pdo->prepare("DELETE FROM turnos");
    $stmt->execute();
}

// Eliminar turno específico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_turno_id'])) {
    $stmt = $pdo->prepare("DELETE FROM turnos WHERE id = ?");
    $stmt->execute([$_POST['eliminar_turno_id']]);
}

// Finalizar turno (pasa a 'atendido')
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_turno_id'])) {
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'atendido' WHERE id = ?");
    $stmt->execute([$_POST['finalizar_turno_id']]);
}

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
    $whereClauses[] = 't.meson_id = ?';
    $params[] = $_GET['meson_id'];
}
if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
    $whereClauses[] = 't.created_at BETWEEN ? AND ?';
    $params[] = $_GET['fecha_inicio'];
    $params[] = $_GET['fecha_fin'];
}

$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($paginaActual - 1) * $registrosPorPagina;

$baseSQL = "FROM turnos t
LEFT JOIN servicios s ON t.servicio_id = s.id
LEFT JOIN materias m ON t.materia_id = m.id
LEFT JOIN clientes c ON t.cliente_id = c.id
LEFT JOIN meson me ON t.meson_id = me.id";

$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = " WHERE " . implode(' AND ', $whereClauses);
}

$sqlCount = "SELECT COUNT(*) $baseSQL $whereSQL";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT t.*, s.letra, s.nombre AS servicio, m.nombre AS materia,
        c.rut, t.meson_id, me.nombre AS meson
        $baseSQL $whereSQL 
        ORDER BY t.created_at DESC LIMIT $inicio, $registrosPorPagina";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$turnos = $stmt->fetchAll();

$servicios = $pdo->query("SELECT * FROM servicios")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias")->fetchAll();
$mesones = $pdo->query("SELECT * FROM meson")->fetchAll();
?>

<div class="container my-5">
    <h2 class="mb-4 fw-bold text-primary">Historial de Turnos</h2>

    <!-- FILTROS -->
    <div class="card shadow-sm rounded p-4 mb-4">
        <form method="GET" class="row g-3">
            <!-- Campos de filtro aquí como antes -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="lista.php" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- BOTÓN ELIMINAR TODO EL HISTORIAL -->
    <div class="d-flex justify-content-end mb-4">
        <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar todo el historial de turnos?')" class="me-3">
            <button type="submit" name="eliminar_todos" class="btn btn-danger">Eliminar Todo el Historial</button>
        </form>
    </div>

    <!-- TABLA -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm rounded">
            <thead class="table-light text-center">
                <tr>
                    <th>Turno</th>
                    <th>RUT</th>
                    <th>Servicio</th>
                    <th>Materia</th>
                    <th>Estado</th>
                    <th>Mesón</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center align-middle">
                <?php if (count($turnos) > 0): ?>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td class="fw-bold"><?= $turno['codigo_turno'] ?></td>
                            <td><?= $turno['rut'] ?></td>
                            <td><?= $turno['servicio'] ?></td>
                            <td><?= $turno['materia'] ?></td>
                            <td>
                                <span class="badge bg-<?= match($turno['estado']) {
                                    'en_atencion' => 'warning text-dark',
                                    'atendido' => 'info',
                                    'finalizado' => 'success',
                                    default => 'secondary',
                                } ?>">
                                    <?= ucfirst($turno['estado']) ?>
                                </span>
                            </td>
                            <td><?= $turno['meson'] ?? '-' ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($turno['created_at'])) ?></td>
                            <td>
                                <form method="POST" class="d-flex justify-content-center gap-1">
                                    <input type="hidden" name="eliminar_turno_id" value="<?= $turno['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este turno?')">🗑</button>
                                </form>
                                <?php if ($turno['estado'] !== 'atendido'): ?>
                                    <form method="POST" class="mt-1">
                                        <input type="hidden" name="finalizar_turno_id" value="<?= $turno['id'] ?>">
                                        <button class="btn btn-sm btn-outline-success" onclick="return confirm('¿Finalizar atención?')">✅</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
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
