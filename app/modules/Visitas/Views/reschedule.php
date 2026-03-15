<?php
$title = "Junta Directiva";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->

        <div class="overview-wrap mb-4">
            <h2 class="title-1">Solicitudes</h2>
            <div class="d-flex">
            </div>
        </div>

        <form action="/SGA-SEBANA/public/visit-requests/<?= $solicitud['id'] ?>/reschedule" method="post">
            <div class="form-group mb-3">
                <label for="fecha_reprogramada">Nueva Fecha</label>
                <input type="date" id="fecha_reprogramada" name="fecha_reprogramada"
                    value="<?= $solicitud['fecha_reprogramada'] ?? '' ?>" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="hora_reprogramada">Nueva Hora</label>
                <input type="time" id="hora_reprogramada" name="hora_reprogramada"
                    value="<?= $solicitud['hora_reprogramada'] ?? '' ?>" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="motivo_reprogramacion">Motivo de la Reprogramación</label>
                <textarea id="motivo_reprogramacion" name="motivo_reprogramacion" rows="3" class="form-control"
                    required><?= htmlspecialchars($solicitud['motivo_reprogramacion'] ?? '') ?></textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">Reprogramar</button>
                <a href="/SGA-SEBANA/public/visit-requests" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';