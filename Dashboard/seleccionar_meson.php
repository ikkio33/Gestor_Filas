<?php
require '../Includes/db.php';
require '../Includes/header.php';

// Obtener los mesones disponibles
$stmt = $pdo->query("SELECT * FROM meson WHERE disponible = 1 ORDER BY nombre ASC");
$mesones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Mesón</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            margin-top: 100px;
            max-width: 600px;
        }

        .card {
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card h3 {
            margin-bottom: 20px;
            color: #343a40;
        }

        .form-select {
            font-size: 1.1rem;
            padding: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            font-size: 1.1rem;
            padding: 10px 20px;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card text-center">
        <h3>🧾 Selecciona tu Mesón</h3>
        <form method="GET" action="dashboard.php">
            <div class="mb-3">
                <select class="form-select" name="meson_id" required>
                    <option value="">-- Selecciona un mesón --</option>
                    <?php foreach ($mesones as $meson): ?>
                        <option value="<?= $meson['id'] ?>"><?= htmlspecialchars($meson['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">➡️ Ir al Dashboard</button>
        </form>
    </div>
</div>

</body>
</html>
