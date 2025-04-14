<?php
// includes/db.php

$host = 'localhost';
$dbname = 'notaria_db';
$username = 'root';
$password = ''; // Cambiar si tienes una contraseña para la base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
