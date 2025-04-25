<?php
require_once '../Includes/db.php';
header('Content-Type: application/json');

// Autenticación simple por token
//$headers = getallheaders();
//$token = $headers['Authorization'] ?? '';
//
//if ($token !== 'Bearer TU_TOKEN_SEGURO') {
//    http_response_code(401);
//    echo json_encode(['error' => 'No autorizado']);
//    exit;
//}

// Obtener el último turno generado
$sql = "SELECT 
            t.codigo_turno,
            t.estado,
            c.rut,
            s.nombre AS servicio
        FROM turnos t
        LEFT JOIN clientes c ON t.cliente_id = c.id
        LEFT JOIN servicios s ON t.servicio_id = s.id
        ORDER BY t.created_at DESC
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    echo json_encode([
        'codigo_turno' => $data['codigo_turno'],
        'rut' => $data['rut'],
        'servicio' => $data['servicio'],
        'estado' => $data['estado']
    ]);
} else {
    echo json_encode(['error' => 'No hay turnos registrados']);
}
