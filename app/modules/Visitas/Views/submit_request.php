<?php
$title = 'Nueva solicitud de visita';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva solicitud de visita</h2>
            <a href="/SGA-SEBANA/public/visit-requests" class="au-btn au-btn-icon au-btn--blue">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars((string) $error) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <strong>Datos de la solicitud</strong>
            </div>

            <div class="card-body">
                <form method="POST" action="/SGA-SEBANA/public/visit-requests/create">
                    <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>
                    <div class="row">
                        <?php if (!empty($es_jefatura)): ?>
                            <div class="col-md-7 mb-3">
                                <label for="buscarCedula">Buscar afiliado por cedula</label>
                                <input type="text" id="buscarCedula" class="form-control"
                                       placeholder="Digite la cedula del afiliado">
                                <small class="form-text text-muted">Formato unico: escriba solo numeros, sin guiones ni espacios.</small>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="afiliadoSelect">Afiliado</label>
                                <select name="afiliado_id" id="afiliadoSelect" class="form-control" required>
                                    <option value="">-- Seleccione un afiliado --</option>
                                    <?php foreach (($afiliados ?? []) as $afiliado): ?>
                                        <option value="<?= (int) ($afiliado['id'] ?? 0) ?>"
                                                data-cedula="<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>"
                                                data-nombre="<?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>">
                                            <?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>
                                            (<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info mb-0">
                                    La solicitud se registrara con el afiliado logueado:
                                    <strong><?= htmlspecialchars((string) ($afiliadoData['nombre_completo'] ?? '')) ?></strong>
                                    (<?= htmlspecialchars((string) ($afiliadoData['cedula'] ?? '')) ?>)
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-4 mb-3">
                            <label for="oficina_id">Oficina</label>
                            <select name="oficina_id" id="oficina_id" class="form-control" required>
                                <option value="">-- Seleccione una oficina --</option>
                                <?php foreach (($oficinas ?? []) as $oficina): ?>
                                    <option value="<?= (int) ($oficina['id'] ?? 0) ?>">
                                        <?= htmlspecialchars((string) ($oficina['nombre'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="numeroEmpleado">Numero de empleado / cedula</label>
                            <input type="text"
                                   id="numeroEmpleado"
                                   name="numero_empleado"
                                   value="<?= !empty($es_jefatura) ? '' : htmlspecialchars((string) ($afiliadoData['cedula'] ?? '')) ?>"
                                   class="form-control"
                                   <?= empty($es_jefatura) ? 'readonly' : '' ?>
                                   required>
                            <?php if (!empty($es_jefatura)): ?>
                                <small class="form-text text-muted">Formato unico de cedula: solo numeros.</small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nombreEmpleado">Nombre del empleado</label>
                            <input type="text"
                                   id="nombreEmpleado"
                                   name="nombre_empleado"
                                   value="<?= !empty($es_jefatura) ? '' : htmlspecialchars((string) ($afiliadoData['nombre_completo'] ?? '')) ?>"
                                   class="form-control"
                                   <?= empty($es_jefatura) ? 'readonly' : '' ?>
                                   required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="fecha_visita">Fecha de visita</label>
                            <input type="date" name="fecha_visita" id="fecha_visita" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="hora_visita">Hora de visita</label>
                            <input type="time" name="hora_visita" id="hora_visita" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="tipo_visita">Tipo de visita</label>
                            <select name="tipo_visita" id="tipo_visita" class="form-control" required>
                                <option value="gestion">Gestion</option>
                                <option value="acompanamiento">Acompanamiento</option>
                                <option value="entrega_documentos">Entrega de documentos</option>
                                <option value="reunion">Reunion</option>
                                <option value="otra">Otra</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="motivo">Motivo</label>
                            <textarea name="motivo" id="motivo" class="form-control" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="au-btn au-btn-icon au-btn--green">
                        <i class="fa-solid fa-paper-plane"></i> Enviar solicitud
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/SGA-SEBANA/public/assets/js/afiliados.js"></script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
