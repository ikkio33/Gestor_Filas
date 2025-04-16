<?php
$titulo = "Ingreso de RUT";
include '../includes/layout_Totem.php';
?>

<style>
    .touch-keyboard {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .touch-keyboard button {
        padding: 1.5rem;
        font-size: 2rem;
        touch-action: manipulation;
        user-select: none;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Ingrese su RUT</h4>
                </div>
                <div class="card-body">
                    <form action="seleccionar.php" method="POST" id="rutForm">
                        <div class="mb-4">
                            <input type="text" name="rut" id="rut" class="form-control form-control-lg text-center" placeholder="Ej: 12345678-K" required readonly>
                        </div>

                        <div class="touch-keyboard">
                            <?php
                            $teclas = array_merge(range(1, 9), ['K', 0, '-']);
                            foreach ($teclas as $valor) {
                                echo "<button type='button' class='btn btn-outline-secondary' onclick=\"agregar('$valor')\">$valor</button>";
                            }
                            ?>
                            <button type="button" class="btn btn-outline-warning" onclick="borrar()">←</button>
                            <button type="submit" class="btn btn-primary">Continuar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function agregar(valor) {
        const input = document.getElementById('rut');
        input.value += valor;
    }

    function borrar() {
        const input = document.getElementById('rut');
        input.value = input.value.slice(0, -1);
    }
</script>

<?php include '../includes/layout-totem-footer.php'; ?>
