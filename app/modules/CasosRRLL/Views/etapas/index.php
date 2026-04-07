<?php
/**
 * Vista de Listado de Etapas del Caso RRLL
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Etapas del Caso: <?= htmlspecialchars($caso['numero_expediente']) ?></h2>
            <div>
                <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas/historial" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-time"></i> Historial
                </a>
                <a href="/SGA-SEBANA/public/casos-rrll/show/<?= $caso['id'] ?>" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i>
                <?php
                    if($success === 'etapa_creada') echo "Etapa registrada correctamente.";
                    elseif($success === 'etapa_actualizada') echo "Etapa actualizada correctamente.";
                    elseif($success === 'etapa_eliminada') echo "Etapa eliminada correctamente.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <strong>Error:</strong> <?= htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tarjeta de Información del Caso -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Título:</strong> <?= htmlspecialchars($caso['titulo']) ?><br>
                        <strong>Expediente:</strong> <?= htmlspecialchars($caso['numero_expediente']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado del Caso:</strong>
                        <span class="badge bg-<?= $caso['estado'] === 'activo' ? 'success' : 'info' ?>">
                            <?= ucfirst($caso['estado']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Progreso -->
        <?php if (!empty($progreso) && $progreso['total'] > 0): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="zmdi zmdi-bar-chart"></i> Progreso General</h6>
                </div>
                <div class="card-body">
                    <div class="progress" style="height: 30px;">
                        <?php $porcentaje = round(($progreso['completadas'] / $progreso['total']) * 100); ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje ?>%">
                            <?= $porcentaje ?>%
                        </div>
                    </div>
                    <div class="row mt-3 text-center">
                        <div class="col-md-3">
                            <strong><?= $progreso['completadas'] ?></strong> Completadas
                        </div>
                        <div class="col-md-3">
                            <strong><?= $progreso['en_progreso'] ?? 0 ?></strong> En Progreso
                        </div>
                        <div class="col-md-3">
                            <strong><?= $progreso['bloqueadas'] ?? 0 ?></strong> Bloqueadas
                        </div>
                        <div class="col-md-3">
                            <strong><?= $progreso['total'] - ($progreso['completadas'] + ($progreso['en_progreso'] ?? 0) + ($progreso['bloqueadas'] ?? 0)) ?></strong> Pendientes
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botón crear etapa -->
        <div class="table-data__tool mb-3">
            <div class="table-data__tool-left">
                <span class="badge bg-info"><?= count($etapas) ?> etapa(s)</span>
            </div>
            <div class="table-data__tool-right">
                <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas/create" 
                   class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Nueva Etapa
                </a>
            </div>
        </div>

        <!-- Tabla de Etapas -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th>Nombre de la Etapa</th>
                        <th style="width: 12%">Estado</th>
                        <th style="width: 12%">Responsable</th>
                        <th style="width: 12%">Fecha Inicio</th>
                        <th style="width: 12%">Fecha Fin (Est.)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($etapas)): ?>
                        <?php foreach($etapas as $index => $etapa): ?>
                            <tr class="tr-shadow">
                                <td><strong><?= $etapa['orden'] ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($etapa['nombre']) ?></strong>
                                    <?php if (!empty($etapa['descripcion'])): ?>
                                        <br><small class="text-muted"><?= substr(htmlspecialchars($etapa['descripcion']), 0, 60) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $etapa['estado'] === 'finalizado' ? 'success' :
                                        ($etapa['estado'] === 'en_progreso' ? 'primary' :
                                        ($etapa['estado'] === 'bloqueado' ? 'danger' : 'secondary'))
                                    ?>">
                                        <?= ucfirst(str_replace('_', ' ', $etapa['estado'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($etapa['responsable_nombre'] ?? 'Sin asignar') ?></td>
                                <td><?= $etapa['fecha_inicio'] ? date('d/m/Y', strtotime($etapa['fecha_inicio'])) : '-' ?></td>
                                <td><?= $etapa['fecha_estimada_fin'] ? date('d/m/Y', strtotime($etapa['fecha_estimada_fin'])) : '-' ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <div class="js-item-menu" style="position: relative; display: inline-block;">
                                            <button class="item" type="button" title="Acciones">
                                                <i class="zmdi zmdi-more-vert"></i>
                                            </button>
                                            <div class="account-dropdown js-dropdown" style="min-width: 200px;">
                                                <div class="account-dropdown__body">
                                                    <div class="account-dropdown__item">
                                                        <a href="/SGA-SEBANA/public/casos-rrll/etapas/<?= $etapa['id'] ?>/edit">
                                                            <i class="zmdi zmdi-edit"></i> Editar
                                                        </a>
                                                    </div>
                                                    <div class="account-dropdown__item">
                                                        <a href="#" data-bs-toggle="modal" 
                                                           data-bs-target="#cambiarEstadoModal" data-etapa-id="<?= $etapa['id'] ?>">
                                                            <i class="zmdi zmdi-refresh"></i> Cambiar Estado
                                                        </a>
                                                    </div>
                                                    <div class="account-dropdown__item">
                                                        <a href="#" data-bs-toggle="modal" 
                                                           data-bs-target="#deleteEtapaModal" data-etapa-id="<?= $etapa['id'] ?>">
                                                            <i class="zmdi zmdi-block"></i> Anular
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="zmdi zmdi-info"></i> No hay etapas registradas. <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas/create">Crear una nueva</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
?>

<!-- Modal: Cambiar Estado de Etapa -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado de Etapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formCambiarEstado">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevo_estado_etapa">Nuevo Estado:</label>
                        <select name="nuevo_estado" id="nuevo_estado_etapa" class="form-control" required>
                            <option value="">Selecciona un estado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="finalizado">Finalizado</option>
                            <option value="bloqueado">Bloqueado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group mt-3" id="fechaRealWrap" style="display:none;">
                        <label for="fecha_real">Fecha real de finalizacion <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_real" name="fecha_real">
                        <small class="text-muted">Requerida cuando la etapa pasa a finalizada.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Eliminar Etapa -->
<div class="modal fade" id="deleteEtapaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Etapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formDeleteEtapa">
                <div class="modal-body">
                    <p>La etapa no se eliminara fisicamente.</p>
                    <p class="text-muted mb-0">Se marcara como <strong>cancelada</strong> para mantener trazabilidad.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular Etapa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Cambiar Estado
const cambiarEstadoModal = document.getElementById('cambiarEstadoModal');
if (cambiarEstadoModal) {
    cambiarEstadoModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const etapaId = button.getAttribute('data-etapa-id');
        const form = document.getElementById('formCambiarEstado');
        form.setAttribute('action', `/SGA-SEBANA/public/casos-rrll/etapas/${etapaId}/cambiar-estado`);
    });
}

const estadoEtapaSelect = document.getElementById('nuevo_estado_etapa');
const fechaRealWrap = document.getElementById('fechaRealWrap');
const fechaRealInput = document.getElementById('fecha_real');
if (estadoEtapaSelect && fechaRealWrap && fechaRealInput) {
    const toggleFechaReal = () => {
        const necesitaFecha = estadoEtapaSelect.value === 'finalizado';
        fechaRealWrap.style.display = necesitaFecha ? 'block' : 'none';
        fechaRealInput.required = necesitaFecha;
        if (!necesitaFecha) {
            fechaRealInput.value = '';
        }
    };
    estadoEtapaSelect.addEventListener('change', toggleFechaReal);
    toggleFechaReal();
}

// Modal Eliminar Etapa
const deleteEtapaModal = document.getElementById('deleteEtapaModal');
if (deleteEtapaModal) {
    deleteEtapaModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const etapaId = button.getAttribute('data-etapa-id');
        const form = document.getElementById('formDeleteEtapa');
        form.setAttribute('action', `/SGA-SEBANA/public/casos-rrll/etapas/${etapaId}/delete`);
    });
}
</script>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
