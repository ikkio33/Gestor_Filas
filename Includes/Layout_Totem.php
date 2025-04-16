<?php
// layout-totem.php
if (!isset($titulo)) $titulo = "Tótem Notarial";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 1.2rem;
        }
        .touch-keyboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .touch-keyboard button {
            padding: 1.5rem;
            font-size: 2rem;
            touch-action: manipulation;
        }
        input[type="text"], input[type="number"], input[type="password"] {
            font-size: 2rem !important;
            text-align: center;
            touch-action: manipulation;
        }
        button, .btn {
            touch-action: manipulation;
        }
    </style>
</head>
<body>

<div class="container py-4">
