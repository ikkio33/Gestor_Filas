<?php
// Prueba rápida para verificar el hash almacenado
$hash = '$2y$10$fNabDgFYLVupDAMqJKMnDuC0aR6CQ9OL8hVEkiqNq55k0R3XTfZjK';  // Reemplázalo con un hash real de la base de datos
$password = '1111111    ';  // La contraseña ingresada en el formulario

var_dump($usuarioData['password']);

if (password_verify($password, $hash)) {
    echo 'La contraseña es válida.';
} else {
    echo 'Contraseña inválida.';
}
?>
