<?php
$ocultarNavbar = true;
include '../Notaria/includes/db.php';
include '../Notaria/includes/header.php';

$ultimo_turno_file = 'ultimo_turno.txt';
$ultimo_turno_anterior = file_exists($ultimo_turno_file) ? trim(file_get_contents($ultimo_turno_file)) : null;

$sql = "SELECT t.codigo_turno, s.nombre AS servicio, m.nombre AS materia, mes.nombre AS meson_nombre
        FROM turnos t
        INNER JOIN (
            SELECT servicio_id, MAX(created_at) AS max_fecha
            FROM turnos
            WHERE estado = 'atendiendo'
            GROUP BY servicio_id
        ) ultimos ON t.servicio_id = ultimos.servicio_id AND t.created_at = ultimos.max_fecha
        LEFT JOIN servicios s ON t.servicio_id = s.id
        LEFT JOIN materias m ON t.materia_id = m.id
        LEFT JOIN meson mes ON t.meson_id = mes.id
        WHERE t.estado = 'atendiendo'
        ORDER BY t.created_at DESC
        LIMIT 8";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$turnos_en_atencion = $stmt->fetchAll();

$codigo_turno_actual = $turnos_en_atencion[0]['codigo_turno'] ?? null;
$meson_actual = $turnos_en_atencion[0]['meson_nombre'] ?? null;

$nuevo_turno = $codigo_turno_actual && $codigo_turno_actual !== $ultimo_turno_anterior;
if ($codigo_turno_actual) {
    file_put_contents($ultimo_turno_file, $codigo_turno_actual);
}
?>

<style>
    body {
        background-color: #f0f2f5;
    }
    .turno-box {
        width: 100%;
        font-weight: bold;
        padding: 2rem;
        border-radius: 2rem;
        border: 3px solid #0d6efd;
        background-color: #ffffff;
        text-align: left;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .turno-box.new-turno {
        animation: fadeInUp 1s ease;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .badge-turno {
        font-size: 2rem;
        padding: 1rem 2rem;
        border-radius: 2rem;
    }

    @media (min-width: 576px) {
        .turno-box { font-size: 1.6rem; }
    }
    @media (min-width: 768px) {
        .turno-box { font-size: 2rem; }
    }
    @media (min-width: 992px) {
        .turno-box { font-size: 2.5rem; }
    }
    @media (min-width: 1200px) {
        .turno-box { font-size: 3rem; }
    }
</style>

<div class="container-fluid px-3 py-4">
    <h1 class="text-center display-3 fw-bold mb-5">Turnos en Atención</h1>

    <?php if (count($turnos_en_atencion) > 0): ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($turnos_en_atencion as $index => $turno): ?>
                <div class="turno-box shadow <?= $index === 0 && $nuevo_turno ? 'new-turno' : '' ?>">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center w-100 text-center text-md-start">
                        <div>
                            <span class="badge bg-primary badge-turno mb-3 mb-md-0 me-md-3">
                                <?= htmlspecialchars($turno['codigo_turno']) ?>
                            </span>
                            <span class="fw-bold"><?= htmlspecialchars($turno['servicio']) ?></span>: 
                            <!--<span><?= htmlspecialchars($turno['servicio']) ?></span>-->
                        </div>
                        <div class="text-muted mt-2 mt-md-0">
                            <i class="fas fa-desktop me-1"></i> Mesón <?= htmlspecialchars($turno['meson_nombre']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-muted fs-3">No hay turnos en atención en este momento.</p>
    <?php endif; ?>
</div>

<?php if ($nuevo_turno): ?>
    <audio autoplay>
        <source src="../Notaria/assets/audio/turno_llamado.mp3" type="audio/mpeg">
    </audio>
    <script>
        const mensaje = "Turno <?= $codigo_turno_actual ?>, diríjase al mesón <?= $meson_actual ?>.";
        const voz = new SpeechSynthesisUtterance(mensaje);
        voz.lang = "es-CL";
        speechSynthesis.speak(voz);
    </script>
<?php endif; ?>

<script>
    // Recarga la página cada 2 segundos
    setInterval(() => location.reload(), 2000);

    // Intenta poner en pantalla completa automáticamente
    document.addEventListener('DOMContentLoaded', () => {
        const pantalla = document.documentElement;
        if (pantalla.requestFullscreen) {
            pantalla.requestFullscreen().catch(() => {});
        } else if (pantalla.webkitRequestFullscreen) {
            pantalla.webkitRequestFullscreen();
        } else if (pantalla.msRequestFullscreen) {
            pantalla.msRequestFullscreen();
        }
    });

    // AQUÍ podrías usar WebSocket en lugar de recargar (futuro upgrade)
    // Ejemplo de idea:
    // const socket = new WebSocket("ws://localhost:3000");
    // socket.onmessage = (event) => {
    //     const data = JSON.parse(event.data);
    //     // actualizar DOM dinámicamente sin recargar
    // };
</script>

<?php include '../Notaria/includes/footer.php'; ?>
