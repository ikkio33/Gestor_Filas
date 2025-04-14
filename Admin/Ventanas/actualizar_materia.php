<?php
include '../../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['materia_id']) || !isset($data['servicio_id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$materia_id = (int) $data['materia_id'];
$servicio_id = (int) $data['servicio_id'];

try {
    $stmt = $pdo->prepare("UPDATE materias SET servicio_id = ? WHERE id = ?");
    $stmt->execute([$servicio_id, $materia_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
}

