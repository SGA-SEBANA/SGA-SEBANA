<div class="alert alert-danger" style="margin: 1rem; text-align: center;">
    <h4>Error</h4>
    <p><?= htmlspecialchars($mensaje ?? 'Ha ocurrido un error inesperado.') ?></p>
</div>

<!-- Bootstrap JS -->
<script src="/SGA-SEBANA/public/assets/vendor/bootstrap-5.3.8.bundle.min.js"></script>

<style>
.alert {
    padding: 1rem;
    border-radius: 5px;
}
</style>