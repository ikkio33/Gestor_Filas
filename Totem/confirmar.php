<?php
include '../includes/db.php';
include '../includes/header.php';

// Verificación básica
if (!isset($_POST['rut'], $_POST['materia_id'], $_POST['servicio_id'])) {
    echo "<div class='alert alert-danger'>Faltan datos necesarios.</div>";
    exit;
}

$rut = $_POST['rut'];
$materia_id = $_POST['materia_id'];
$servicio_id = $_POST['servicio_id'];

// Buscar o insertar cliente
$stmt = $pdo->prepare("SELECT id FROM clientes WHERE rut = ?");
$stmt->execute([$rut]);
$cliente = $stmt->fetch();

if ($cliente) {
    $cliente_id = $cliente['id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO clientes (rut, created_at, updated_at) VALUES (?, NOW(), NOW())");
    $stmt->execute([$rut]);
    $cliente_id = $pdo->lastInsertId();
}

// Obtener la letra del servicio
$stmt = $pdo->prepare("SELECT letra FROM servicios WHERE id = ?");
$stmt->execute([$servicio_id]);
$servicio = $stmt->fetch();

if (!$servicio) {
    echo "<div class='alert alert-danger'>Servicio no válido.</div>";
    exit;
}

$letra = $servicio['letra'];

// Obtener el último número usado para este servicio
$stmt = $pdo->prepare("SELECT MAX(numero_turno) AS max_num FROM turnos WHERE servicio_id = ?");
$stmt->execute([$servicio_id]);
$result = $stmt->fetch();
$ultimoNumero = $result['max_num'] ?? 0;
$nuevoNumero = $ultimoNumero + 1;

// Insertar turno
$stmt = $pdo->prepare("INSERT INTO turnos (codigo_turno, cliente_id, numero_turno, servicio_id, materia_id, estado, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, 'pendiente', NOW(), NOW())");

$codigoTurno = $letra . str_pad($nuevoNumero, 2, '0', STR_PAD_LEFT);

$stmt->execute([
    $codigoTurno,
    $cliente_id,
    $nuevoNumero,
    $servicio_id,
    $materia_id
]);
?>

<div class="container text-center mt-5">
    <h2 class="text-success">¡Turno Generado!</h2>
    <p class="display-4"><?= $codigoTurno ?></p>
    <p class="mt-3">Por favor, espere a ser llamado en pantalla.</p>
    <a href="index.php" class="btn btn-primary mt-4">Volver al inicio</a>
</div>

<?php include '../includes/footer.php'; ?>
