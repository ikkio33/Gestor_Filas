<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Notaría</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Credenciales inválidas</p>
    <?php endif; ?>
    <form action="procesar_login.php" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>
