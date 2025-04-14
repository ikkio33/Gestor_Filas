<?php
include '../../includes/db.php';
include '../../includes/header.php';

// Inicialización de filtros
$whereClauses = [];
$params = [];

if (!empty($_GET['estado'])) {
    $whereClauses[] = 'estado = :estado';
    $params[':estado'] = $_GET['estado'];
}

if (!empty($_GET['servicio_id'])) {
    $whereClauses[] = 'servicio_id = :servicio_id';
    $params[':servicio_id'] = $_GET['servicio_id'];
}

if (!empty($_GET['materia_id'])) {
    $whereClauses[] = 'materia_id = :materia_id';
    $params[':materia_id'] = $_GET['materia_id'];
}

if (!empty($_GET['meson_id'])) {
    $whereClauses[] = 'meson_id = :meson_id';
    $params[':meson_id'] = $_GET['meson_id'];
}

if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
    $whereClauses[] = 'created_at BETWEEN :fecha_inicio AND :fecha_fin';
    $params[':fecha_inicio'] = $_GET['fecha_inicio'];
    $params[':fecha_fin'] = $_GET['fecha_fin'];
}

// Construcción de la consulta SQL
$sql = "SELECT t.*, 
                s.letra, s.nombre AS servicio, 
                m.nombre AS materia, 
                c.rut, 
                ms.meson_id, mes.nombre AS meson_nombre
        FROM turnos t
        LEFT JOIN servicios s ON t.servicio_id = s.id
        LEFT JOIN materias m ON t.materia_id = m.id
        LEFT JOIN clientes c ON t.cliente_id = c.id
        LEFT JOIN meson_servicio ms ON t.servicio_id = ms.servicio_id
        LEFT JOIN meson mes ON ms.meson_id = mes.id";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(' AND ', $whereClauses);
}

$sql .= " ORDER BY t.created_at DESC";

// Ejecutar la consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$turnos = $stmt->fetchAll();

// Obtener servicios, materias y mesones para los filtros
$servicios = $pdo->query("SELECT * FROM servicios")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias")->fetchAll();
$mesones = $pdo->query("SELECT * FROM meson")->fetchAll();
?>

<div class="container mt-4">
    <h1>Historial de Turnos</h1>

    <!-- Filtros -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha inicio">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_fin" class="form-control" placeholder="Fecha fin">
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-control">
                    <option value="">Seleccionar estado</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_atencion">En atención</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="servicio_id" class="form-control">
                    <option value="">Seleccionar servicio</option>
                    <?php foreach ($servicios as $servicio): ?>
                        <option value="<?= $servicio['id'] ?>"><?= $servicio['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <select name="materia_id" class="form-control">
                    <option value="">Seleccionar materia</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?= $materia['id'] ?>"><?= $materia['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="meson_id" class="form-control">
                    <option value="">Seleccionar mesón</option>
                    <?php foreach ($mesones as $meson): ?>
                        <option value="<?= $meson['id'] ?>"><?= $meson['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de turnos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código de Turno</th>
                <th>RUT</th>
                <th>Materia</th>
                <th>Servicio</th>
                <th>Mesón</th>
                <th>Estado</th>
                <th>Tiempo de Espera</th>
                <th>Fecha de Creación</th>
                <th>Fecha de Actualización</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($turnos as $turno): ?>
                <tr>
                    <td><?= htmlspecialchars($turno['codigo_turno']) ?></td>
                    <td><?= htmlspecialchars($turno['rut']) ?></td>
                    <td><?= htmlspecialchars($turno['materia']) ?></td>
                    <td><?= htmlspecialchars($turno['servicio']) ?></td>
                    <td><?= htmlspecialchars($turno['meson_nombre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($turno['estado']) ?></td>
                    <td>
                        <?php
                        $createdAt = new DateTime($turno['created_at']);
                        $now = new DateTime();
                        $interval = $createdAt->diff($now);
                        echo $interval->format('%h horas %i minutos');
                        ?>
                    </td>
                    <td><?= htmlspecialchars($turno['created_at']) ?></td>
                    <td><?= htmlspecialchars($turno['updated_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../Includes/footer.php'; ?>
