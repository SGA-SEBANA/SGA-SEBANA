<?php
$title = "Oficinas";
ob_start();
?>

<div class="overview-wrap mb-4">
    <h2 class="title-1">Oficinas</h2>
    <a href="/SGA-SEBANA/public/oficinas/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
        <i class="zmdi zmdi-plus"></i> Nueva oficina
    </a>
</div>

<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        $success = (string) $_GET['success'];
        if ($success === 'created') {
            echo 'Oficina creada correctamente.';
        } elseif ($success === 'updated') {
            echo 'Oficina actualizada correctamente.';
        } elseif ($success === 'toggled') {
            echo 'Estado de la oficina actualizado correctamente.';
        } else {
            echo 'Operacion completada correctamente.';
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php if (!empty($offices)): ?>
    <?php foreach ($offices as $office): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 border shadow-sm">
            <div class="card-header text-white" style="background-color:#001B71;">
                <strong><?= htmlspecialchars($office['nombre'] ?? '') ?></strong>
            </div>
            <div class="card-body">
                <p>
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($office['provincia'] ?? '') ?><?= !empty($office['canton']) ? ', ' . htmlspecialchars($office['canton']) : '' ?><br>
                    <?= htmlspecialchars($office['direccion'] ?? '') ?><br>
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($office['telefono'] ?? '') ?><br>
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($office['correo'] ?? '') ?><br>
                    <i class="fas fa-user"></i> Responsable: <?= htmlspecialchars($office['responsable'] ?? '') ?><br>
                    <i class="fas fa-clock"></i> Horario: <?= htmlspecialchars($office['horario_atencion'] ?? '') ?>
                </p>
            </div>
          <div class="card-footer d-flex gap-2">
            <a href="/SGA-SEBANA/public/oficinas/edit/<?= $office['id'] ?>" 
            class="btn btn-sm btn-warning w-50">Editar</a>

            <a href="/SGA-SEBANA/public/oficinas/toggle/<?= $office['id'] ?>"
            class="btn btn-sm <?= ($office['activo'] ?? 0) == 1 ? 'btn-danger' : 'btn-success' ?> w-50">
            <?= ($office['activo'] ?? 0) == 1 ? 'Desactivar' : 'Activar' ?>
            </a>
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
