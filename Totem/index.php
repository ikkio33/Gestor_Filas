<?php
$ocultarNavbar = true;
include '../includes/header.php';
?>

<div class="container text-center mt-5">
    <h2>Ingrese su RUT</h2>
    <form action="seleccionar.php" method="POST" id="rutForm">
        <input type="text" name="rut" id="rut" class="form-control form-control-lg text-center mb-3" placeholder="Ej: 12345678-K" required readonly>
        <div class="d-grid gap-2 d-md-block">
            <?php
            $teclas = array_merge(range(1, 9), ['K', 0, '-']);
            foreach ($teclas as $valor) {
                echo "<button type='button' class='btn btn-secondary m-1' onclick=\"agregar('$valor')\">$valor</button>";
            }
            ?>
            <button type="button" class="btn btn-warning m-1" onclick="borrar()">←</button>
            <button type="submit" class="btn btn-success m-1">Continuar</button>
        </div>
    </form>
</div>

<script>
    function agregar(valor) {
        document.getElementById('rut').value += valor;
    }
    function borrar() {
        let rut = document.getElementById('rut');
        rut.value = rut.value.slice(0, -1);
    }
</script>

<?php include '../includes/footer.php'; ?>
