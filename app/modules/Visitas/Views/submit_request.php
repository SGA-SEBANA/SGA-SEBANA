<?php
$title = "Junta Directiva";
ob_start();
?>

<div class="row">


    <div class="col-md-12">


        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Solicitudes</h2>

            <a href="/SGA-SEBANA/public/visit-requests" class="au-btn au-btn-icon au-btn--blue">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>


        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars((string) $error) ?>
            </div>
        <?php endif; ?>


        <!-- FORMULARIO CREAR SOLICITUD -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>Nueva Solicitud</strong>
            </div>

            <div class="card-body">

                <form method="POST" action="/SGA-SEBANA/public/visit-requests/create">

                    <div class="row">
                        <?php if (!empty($es_jefatura)): ?>
                        <div class="col-md-8 mb-3">
                            <label>Afiliado</label>
                            <select name="afiliado_id" class="form-control" required>
                                <option value="">-- Seleccione un afiliado --</option>
                                <?php foreach(($afiliados ?? []) as $afiliado): ?>
                                <option value="<?= (int) ($afiliado['id'] ?? 0) ?>">
                                    <?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>
                                    (<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-4 mb-3">
                            <label>Oficina</label>
                            <select name="oficina_id" class="form-control" required>
                                <option value="">-- Seleccione una oficina --</option>
                                <?php foreach($oficinas as $oficina): ?>
                                <option value="<?= $oficina['id'] ?>">
                                    <?= htmlspecialchars($oficina['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Número de empleado</label>
                            <input type="text" name="numero_empleado" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nombre del empleado</label>
                            <input type="text" name="nombre_empleado" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Fecha de visita</label>
                            <input type="date" name="fecha_visita" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Hora de visita</label>
                            <input type="time" name="hora_visita" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Motivo</label>
                            <textarea name="motivo" class="form-control"></textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Tipo de visita</label>
                            <select name="tipo_visita" class="form-control">
                                <option value="ordinaria">Ordinaria</option>
                                <option value="extraordinaria">Extraordinaria</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control"></textarea>
                        </div>

                    </div>

                    <button type="submit" class="au-btn au-btn-icon au-btn--green">
                        <i class="fa-solid fa-paper-plane"></i> Enviar Solicitud
                    </button>

                </form>

            </div>
        </div>



    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
