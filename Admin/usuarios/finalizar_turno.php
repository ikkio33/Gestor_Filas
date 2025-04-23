<?php
require_once '../../Includes/auth.php';
requiereRol('administrador');
require_once '../../Includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE turnos SET estado = 'finalizado' WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    header('Location: lista.php');
    exit;
}
