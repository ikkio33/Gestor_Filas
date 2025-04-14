<?php
session_start();

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function requiereLogin() {
    if (!estaLogueado()) {
        header('Location: ../login.php');
        exit;
    }
}

function requiereRol($rol) {
    requiereLogin();

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rol) {
        echo "Acceso denegado. Se requiere rol: $rol";
        exit;
    }
}
