<?php
// Incluir auth.php para que las funciones estén disponibles
require_once '../../Includes/auth.php';  // Asegúrate de que la ruta sea correcta

// Verificación de acceso para administradores
requiereRol('administrador');

include '../../includes/header.php';
include '../../includes/db.php';
//require_once 'config.php';

// Crear Servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_servicio'])) {
    $nombre = trim($_POST['nuevo_servicio']);

    // Letras disponibles
    $letrasDisponibles = range('A', 'Z');
    $stmt = $pdo->query("SELECT letra FROM servicios");
    $letrasUsadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Buscar letra libre
    $letraLibre = null;
    foreach ($letrasDisponibles as $letra) {
        if (!in_array($letra, $letrasUsadas)) {
            $letraLibre = $letra;
            break;
        }
    }

    if ($letraLibre) {
        $stmt = $pdo->prepare("INSERT INTO servicios (nombre, letra) VALUES (:nombre, :letra)");
        $stmt->execute([
            'nombre' => $nombre,
            'letra' => $letraLibre
        ]);
        header("Location: organizador.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>No hay letras disponibles. Elimina un servicio para liberar una.</div>";
    }
}

// Crear Materia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_materia'], $_POST['servicio_id'])) {
    $nombre = $_POST['nuevo_materia'];
    $servicio_id = $_POST['servicio_id'];
    $stmt = $pdo->prepare("INSERT INTO materias (nombre, servicio_id) VALUES (?, ?)");
    $stmt->execute([$nombre, $servicio_id]);
}

// Eliminar servicio
if (isset($_GET['eliminar_servicio'])) {
    // Primero, eliminamos las materias asociadas a este servicio
    $stmt = $pdo->prepare("DELETE FROM materias WHERE servicio_id = ?");
    $stmt->execute([$_GET['eliminar_servicio']]);

    // Luego eliminamos el servicio
    $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->execute([$_GET['eliminar_servicio']]);

    header("Location: organizador.php");
    exit;
}

// Eliminar materia
if (isset($_GET['eliminar_materia'])) {
    $stmt = $pdo->prepare("DELETE FROM materias WHERE id = ?");
    $stmt->execute([$_GET['eliminar_materia']]);
}

// Obtener datos
$servicios = $pdo->query("SELECT * FROM servicios")->fetchAll();
$materias = $pdo->query("SELECT * FROM materias")->fetchAll();

// Agrupar materias
$materiasPorServicio = [];
foreach ($materias as $materia) {
    $materiasPorServicio[$materia['servicio_id']][] = $materia;
}
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Organizador de Servicios y Materias</h1>

    <div class="row mb-4">
        <!-- Formulario crear nuevo servicio -->
        <div class="col-12 col-md-6 offset-md-3">
            <form method="POST" class="d-flex gap-2">
                <input type="text" name="nuevo_servicio" class="form-control" placeholder="Nuevo servicio" required>
                <button type="submit" name="crear_servicio" class="btn btn-outline-primary">Agregar Servicio</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php foreach ($servicios as $servicio): ?>
            <div class="col-12 col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <strong><?= htmlspecialchars($servicio['nombre']) ?> (<?= $servicio['letra'] ?>)</strong>
                        <a href="?eliminar_servicio=<?= $servicio['id'] ?>" class="btn btn-outline-light btn-sm" onclick="return confirm('¿Eliminar este servicio?')">Eliminar</a>
                    </div>
                    <div class="card-body">
                        <!-- Form crear materia -->
                        <form method="POST" class="d-flex mb-3 gap-2">
                            <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
                            <input type="text" name="nuevo_materia" class="form-control" placeholder="Nueva materia" required>
                            <button type="submit" class="btn btn-outline-primary">Agregar</button>
                        </form>

                        <ul class="list-group" id="servicio-<?= $servicio['id'] ?>" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <?php if (!empty($materiasPorServicio[$servicio['id']])): ?>
                                <?php foreach ($materiasPorServicio[$servicio['id']] as $materia): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center" draggable="true" ondragstart="drag(event)" id="materia-<?= $materia['id'] ?>">
                                        <?= htmlspecialchars($materia['nombre']) ?>
                                        <a href="?eliminar_materia=<?= $materia['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar esta materia?')">x</a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No hay materias</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }

    function drop(ev) {
        ev.preventDefault();
        const data = ev.dataTransfer.getData("text");
        const draggedElement = document.getElementById(data);
        const targetList = ev.target.closest("ul");

        if (targetList && draggedElement && targetList.id !== draggedElement.parentElement.id) {
            targetList.appendChild(draggedElement);

            const materiaId = data.split("-")[1];
            const nuevoServicioId = targetList.id.split("-")[1];

            fetch('actualizar_materia.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ materia_id: materiaId, servicio_id: nuevoServicioId })
})
.then(response => {
    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
    }
    return response.json();
})
.then(data => {
    if (!data.success) {
        throw new Error('Error en la actualización de la materia');
    }
    // Actualización exitosa, puedes agregar lógica adicional aquí
})
.catch(error => {
    console.error('Error al actualizar la materia:', error);
    alert('Hubo un problema al actualizar la materia. Por favor, inténtalo nuevamente.');
});
        }
    }
</script>       
