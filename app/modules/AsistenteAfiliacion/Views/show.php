<?php
ob_start();
$estado = strtolower(trim((string) ($solicitud['estado'] ?? '')));
?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Detalle de Solicitud #<?= htmlspecialchars((string) ($solicitud['id'] ?? '')) ?></h2>
            <a href="/SGA-SEBANA/public/asistente-afiliacion/solicitudes" class="btn btn-secondary btn-sm">
                <i class="zmdi zmdi-arrow-left me-1"></i>Volver
            </a>
        </div>

        <?php if (($success ?? null) === 'estado'): ?>
            <div class="alert alert-success">Estado actualizado correctamente.</div>
        <?php endif; ?>

        <?php if (($error ?? null) === 'estado'): ?>
            <div class="alert alert-danger">No se pudo actualizar el estado.</div>
        <?php endif; ?>

        <?php if (!empty($flash_success)): ?>
            <div class="alert alert-info"><?= htmlspecialchars((string) $flash_success) ?></div>
        <?php endif; ?>

        <?php if (!empty($flash_error)): ?>
            <div class="alert alert-warning"><?= htmlspecialchars((string) $flash_error) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong>Datos del Solicitante</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Estado:</strong> <?= htmlspecialchars((string) ($solicitud['estado'] ?? '')) ?></p>
                        <p><strong>Cedula:</strong> <?= htmlspecialchars((string) ($solicitud['cedula'] ?? '')) ?></p>
                        <p><strong>Nombre:</strong> <?= htmlspecialchars(trim((string) (($solicitud['nombre'] ?? '') . ' ' . ($solicitud['apellido1'] ?? '') . ' ' . ($solicitud['apellido2'] ?? '')))) ?></p>
                        <p><strong>Correo:</strong> <?= htmlspecialchars((string) ($solicitud['correo'] ?? '')) ?></p>
                        <p><strong>Celular:</strong> <?= htmlspecialchars((string) ($solicitud['celular'] ?? '')) ?></p>
                        <p><strong>Tipo usuario BNCR:</strong> <?= htmlspecialchars((string) ($solicitud['tipo_usuario'] ?? '')) ?></p>

                        <hr>
                        <p><strong>Numero empleado:</strong> <?= htmlspecialchars((string) ($solicitud['numero_empleado'] ?? '')) ?></p>
                        <p><strong>Oficina BNCR:</strong> <?= htmlspecialchars((string) ($solicitud['oficina_bncr'] ?? '')) ?></p>
                        <p><strong>Departamento:</strong> <?= htmlspecialchars((string) ($solicitud['departamento'] ?? '')) ?></p>
                        <p><strong>Puesto:</strong> <?= htmlspecialchars((string) ($solicitud['puesto'] ?? '')) ?></p>
                        <p><strong>Ingreso BNCR:</strong> <?= htmlspecialchars((string) ($solicitud['fecha_ingreso_bncr'] ?? '')) ?></p>
                        <p><strong>Jubilacion:</strong> <?= htmlspecialchars((string) ($solicitud['fecha_jubilacion'] ?? '-')) ?></p>

                        <hr>
                        <p><strong>Acepta deduccion 1%:</strong> <?= ((int) ($solicitud['acepta_deduccion'] ?? 0) === 1) ? 'Si' : 'No' ?></p>
                        <p><strong>Acepta estatuto:</strong> <?= ((int) ($solicitud['acepta_estatuto'] ?? 0) === 1) ? 'Si' : 'No' ?></p>
                        <p><strong>Fecha envio:</strong> <?= htmlspecialchars((string) ($solicitud['fecha_envio'] ?? '-')) ?></p>
                        <p><strong>Observaciones solicitante:</strong><br><?= nl2br(htmlspecialchars((string) ($solicitud['observaciones'] ?? ''))) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong>Documentos</strong>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($solicitud['pdf_generado_path'])): ?>
                            <a class="btn btn-outline-primary btn-sm mb-2 w-100" href="/SGA-SEBANA/public/asistente-afiliacion/documento/<?= urlencode((string) $solicitud['id']) ?>/generado" target="_blank">
                                Ver PDF generado
                            </a>
                            <a class="btn btn-outline-secondary btn-sm mb-2 w-100" href="/SGA-SEBANA/public/asistente-afiliacion/documento/<?= urlencode((string) $solicitud['id']) ?>/generado?download=1">
                                Descargar PDF generado
                            </a>
                        <?php else: ?>
                            <p class="text-muted">No hay PDF generado.</p>
                        <?php endif; ?>

                        <?php if (!empty($solicitud['pdf_firmado_path'])): ?>
                            <a class="btn btn-outline-success btn-sm mb-2 w-100" href="/SGA-SEBANA/public/asistente-afiliacion/documento/<?= urlencode((string) $solicitud['id']) ?>/firmado" target="_blank">
                                Ver PDF firmado
                            </a>
                            <a class="btn btn-outline-success btn-sm w-100" href="/SGA-SEBANA/public/asistente-afiliacion/documento/<?= urlencode((string) $solicitud['id']) ?>/firmado?download=1">
                                Descargar PDF firmado
                            </a>
                        <?php else: ?>
                            <p class="text-danger mb-0">No se ha adjuntado PDF firmado.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <strong>Gestion de estado</strong>
                    </div>
                    <div class="card-body">
                        <form method="post" action="/SGA-SEBANA/public/asistente-afiliacion/solicitudes/<?= urlencode((string) ($solicitud['id'] ?? '')) ?>/estado">
                            <div class="mb-3">
                                <label class="form-label">Nuevo estado</label>
                                <select name="nuevo_estado" class="form-control" required>
                                    <option value="enviada_aprobacion" <?= ($estado === 'enviada_aprobacion') ? 'selected' : '' ?>>enviada_aprobacion</option>
                                    <option value="aprobada" <?= ($estado === 'aprobada') ? 'selected' : '' ?>>aprobada</option>
                                    <option value="rechazada" <?= ($estado === 'rechazada') ? 'selected' : '' ?>>rechazada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Observaciones internas</label>
                                <textarea class="form-control" rows="3" name="observaciones_admin"><?= htmlspecialchars((string) ($solicitud['observaciones_admin'] ?? '')) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Guardar estado</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
