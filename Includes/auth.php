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

    if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== strtolower($rol)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso no autorizado: esta sección es solo para administradores.');
    }
}
