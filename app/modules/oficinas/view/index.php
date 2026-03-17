<?php
$title = "Oficinas";
ob_start();
?>

<div class="row g-4">
    <?php if (!empty($offices)): ?>
    <?php foreach($offices as $office): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 border shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong><?= htmlspecialchars($office['nombre'] ?? 'Sin nombre') ?></strong>
            </div>
            <div class="card-body">
                <p>
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($office['provincia'] ?? '') ?><?= !empty($office['canton']) ? ', ' . htmlspecialchars($office['canton']) : '' ?><br>
                    <?= htmlspecialchars($office['direccion'] ?? 'No disponible') ?><br>
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($office['telefono'] ?? 'No disponible') ?><br>
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($office['correo'] ?? 'No disponible') ?><br>
                    <i class="fas fa-user"></i> Responsable:
                    <?= htmlspecialchars($office['responsable'] ?? 'Sin asignar') ?><br>
                    <i class="fas fa-clock"></i> Horario:
                    <?= htmlspecialchars($office['horario_atencion'] ?? 'No disponible') ?>
                </p>
            </div>

            <div class="card-footer d-flex justify-content">
                <a href="/SGA-SEBANA/public/oficinas/edit/<?= htmlspecialchars($office['id'] ?? 0) ?>"
                    class="btn btn-sm btn-warning me-2">Editar</a>

                <a href="/SGA-SEBANA/public/oficinas/delete/<?= htmlspecialchars($office['id'] ?? 0) ?>"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar esta oficina?');">Eliminar</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="col-12">
        <div class="alert alert-info text-center">No hay oficinas registradas.</div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>