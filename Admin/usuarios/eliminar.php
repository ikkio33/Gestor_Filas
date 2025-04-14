<?php
include '../../includes/db.php';

if (!isset($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit;
}

$id = $_GET['id'];

// Verifica si el usuario existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    echo "Usuario no encontrado.";
    exit;
}

// Elimina el usuario
$delete = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$delete->execute([$id]);

// Redirige de vuelta al listado
header("Location: index.php");
exit;
