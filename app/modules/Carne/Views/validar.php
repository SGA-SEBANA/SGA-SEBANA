<div class="carnet-validacion" style="margin: 2rem; text-align: center;">
    <h3>Validación de Carné</h3>

    <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
    <p><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
    <p><strong>Estado del Afiliado:</strong> <?= htmlspecialchars($estado_afiliado) ?></p>
    <p><strong>Estado del Carné:</strong> <?= htmlspecialchars($estado_carnet) ?></p>
    <p><strong>Versión:</strong> <?= htmlspecialchars($version) ?></p>
</div>

<!-- Bootstrap JS -->
<script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

<style>
.carnet-validacion {
    border: 1px solid #ccc;
    padding: 1rem;
    border-radius: 8px;
    background-color: #f9f9f9;
    max-width: 400px;
    margin: auto;
}
.carnet-validacion h3 {
    margin-bottom: 1rem;
}
.carnet-validacion p {
    margin: 0.5rem 0;
}
</style>

