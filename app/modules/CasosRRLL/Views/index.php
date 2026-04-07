<?php
/**
 * Vista de listado de casos RRLL (DataGrid principal)
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="title-1 mb-4">Gestion de Casos RRLL</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i>
                <?php
                if ($success === 'creado') {
                    echo 'Caso registrado correctamente.';
                } elseif ($success === 'actualizado') {
                    echo 'Caso actualizado correctamente.';
                } elseif ($success === 'archivado') {
                    echo 'Caso archivado correctamente.';
                } elseif ($success === 'estado_actualizado') {
                    echo 'Estado de caso actualizado correctamente.';
                } elseif ($success === 'responsable_actualizado') {
                    echo 'Responsable actualizado correctamente.';
                } else {
                    echo 'Operacion completada correctamente.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <?= htmlspecialchars((string) $error_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Busqueda y Filtros</strong>
            </div>
            <div class="card-body">
                <form action="/SGA-SEBANA/public/casos-rrll" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="q" class="mb-1">Buscar</label>
                            <input type="text" id="q" name="q" class="form-control form-control-sm"
                                placeholder="Expediente, titulo o afiliado"
                                value="<?= htmlspecialchars((string) ($filtros['busqueda'] ?? '')) ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="estado" class="mb-1">Estado</label>
                            <select name="estado" id="estado" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Abierto</option>
                                <option value="en_progreso" <?= ($filtros['estado'] ?? '') === 'en_progreso' ? 'selected' : '' ?>>En tramite</option>
                                <option value="suspendido" <?= ($filtros['estado'] ?? '') === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                                <option value="cerrado" <?= ($filtros['estado'] ?? '') === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                                <option value="archivado" <?= ($filtros['estado'] ?? '') === 'archivado' ? 'selected' : '' ?>>Archivado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="prioridad" class="mb-1">Prioridad</label>
                            <select name="prioridad" id="prioridad" class="form-control form-control-sm">
                                <option value="">Todas</option>
                                <option value="baja" <?= ($filtros['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                                <option value="media" <?= ($filtros['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                                <option value="alta" <?= ($filtros['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                                <option value="urgente" <?= ($filtros['prioridad'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="categoria_id" class="mb-1">Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="form-control form-control-sm">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= (int) $cat['id'] ?>" <?= (string) ($filtros['categoria_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) $cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="responsable_id" class="mb-1">Responsable</label>
                            <select name="responsable_id" id="responsable_id" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= (int) $usuario['id'] ?>" <?= (string) ($filtros['responsable_id'] ?? '') === (string) $usuario['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($usuario['nombre_completo'] ?: $usuario['username'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="etapa_nombre" class="mb-1">Etapa (nombre)</label>
                            <input type="text" id="etapa_nombre" name="etapa_nombre" class="form-control form-control-sm"
                                placeholder="Ej: investigacion"
                                value="<?= htmlspecialchars((string) ($filtros['etapa_nombre'] ?? '')) ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="etapa_estado" class="mb-1">Estado etapa</label>
                            <select name="etapa_estado" id="etapa_estado" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="pendiente" <?= ($filtros['etapa_estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="en_progreso" <?= ($filtros['etapa_estado'] ?? '') === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                                <option value="finalizado" <?= ($filtros['etapa_estado'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                <option value="bloqueado" <?= ($filtros['etapa_estado'] ?? '') === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                                <option value="cancelado" <?= ($filtros['etapa_estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="con_documentos" class="mb-1">Documentos</label>
                            <select name="con_documentos" id="con_documentos" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="si" <?= ($filtros['con_documentos'] ?? '') === 'si' ? 'selected' : '' ?>>Con documentos</option>
                                <option value="no" <?= ($filtros['con_documentos'] ?? '') === 'no' ? 'selected' : '' ?>>Sin documentos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="atraso" class="mb-1">Semaforo atraso</label>
                            <select name="atraso" id="atraso" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="vencido" <?= ($filtros['atraso'] ?? '') === 'vencido' ? 'selected' : '' ?>>Vencidos</option>
                                <option value="al_dia" <?= ($filtros['atraso'] ?? '') === 'al_dia' ? 'selected' : '' ?>>Al dia</option>
                            </select>
                        </div>
                        <div class="col-md-12 d-flex align-items-end gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="zmdi zmdi-search"></i> Filtrar
                            </button>
                            <a href="/SGA-SEBANA/public/casos-rrll" class="btn btn-outline-secondary btn-sm">
                                Limpiar
                            </a>
                            <a href="/SGA-SEBANA/public/casos-rrll/reporte/pdf?estado=<?= urlencode((string) ($filtros['estado'] ?? '')) ?>&prioridad=<?= urlencode((string) ($filtros['prioridad'] ?? '')) ?>&categoria_id=<?= urlencode((string) ($filtros['categoria_id'] ?? '')) ?>&etapa_nombre=<?= urlencode((string) ($filtros['etapa_nombre'] ?? '')) ?>&etapa_estado=<?= urlencode((string) ($filtros['etapa_estado'] ?? '')) ?>"
                                class="btn btn-danger btn-sm">
                                <i class="zmdi zmdi-download"></i> Generar PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-data__tool mb-3">
            <div class="table-data__tool-left">
                <span class="badge bg-info"><?= count($casos) ?> casos</span>
            </div>
            <div class="table-data__tool-right">
                <a href="/SGA-SEBANA/public/casos-rrll/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Nuevo Caso
                </a>
            </div>
        </div>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Expediente</th>
                        <th>Afiliado</th>
                        <th>Estado</th>
                        <th>Etapa Actual</th>
                        <th>Prioridad</th>
                        <th>Responsable</th>
                        <th>Documentos</th>
                        <th>Ultima actuacion</th>
                        <th>Semaforo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($casos)): ?>
                        <?php foreach ($casos as $caso): ?>
                            <?php
                            $estadoLabel = [
                                'activo' => 'Abierto',
                                'en_progreso' => 'En tramite',
                                'suspendido' => 'Suspendido',
                                'cerrado' => 'Cerrado',
                                'archivado' => 'Archivado'
                            ][$caso['estado']] ?? ucfirst((string) $caso['estado']);

                            $estadoClass = [
                                'activo' => 'success',
                                'en_progreso' => 'info',
                                'suspendido' => 'warning',
                                'cerrado' => 'primary',
                                'archivado' => 'secondary'
                            ][$caso['estado']] ?? 'secondary';

                            $prioridadClass = [
                                'baja' => 'secondary',
                                'media' => 'info',
                                'alta' => 'warning',
                                'urgente' => 'danger'
                            ][$caso['prioridad']] ?? 'secondary';

                            $semaforo = (string) ($caso['semaforo_atraso'] ?? 'sin_fecha');
                            $semaforoClass = [
                                'verde' => 'success',
                                'amarillo' => 'warning',
                                'rojo' => 'danger',
                                'sin_fecha' => 'secondary'
                            ][$semaforo] ?? 'secondary';
                            $semaforoLabel = [
                                'verde' => 'Al dia',
                                'amarillo' => 'Riesgo',
                                'rojo' => 'Atrasado',
                                'sin_fecha' => 'Sin fecha'
                            ][$semaforo] ?? 'Sin fecha';
                            ?>
                            <tr class="tr-shadow">
                                <td>
                                    <strong><?= htmlspecialchars((string) $caso['numero_expediente']) ?></strong>
                                    <div class="small text-muted"><?= htmlspecialchars((string) ($caso['titulo'] ?? '')) ?></div>
                                </td>
                                <td><?= htmlspecialchars((string) ($caso['afiliado_nombre'] ?? 'N/A')) ?></td>
                                <td><span class="badge bg-<?= $estadoClass ?>"><?= $estadoLabel ?></span></td>
                                <td>
                                    <?= htmlspecialchars((string) ($caso['etapa_actual_nombre'] ?? 'Sin etapa')) ?>
                                    <?php if (!empty($caso['etapa_actual_estado'])): ?>
                                        <div class="small text-muted"><?= htmlspecialchars((string) $caso['etapa_actual_estado']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-<?= $prioridadClass ?>"><?= htmlspecialchars(ucfirst((string) $caso['prioridad'])) ?></span></td>
                                <td><?= htmlspecialchars((string) ($caso['responsable_nombre'] ?? 'Sin asignar')) ?></td>
                                <td>
                                    <?php $docs = (int) ($caso['total_documentos'] ?? 0); ?>
                                    <span class="badge bg-<?= $docs > 0 ? 'primary' : 'light text-dark' ?>"><?= $docs > 0 ? $docs . ' adjuntos' : 'Sin adjuntos' ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($caso['ultima_actuacion'])): ?>
                                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $caso['ultima_actuacion']))) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin registro</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-<?= $semaforoClass ?>"><?= $semaforoLabel ?></span></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/casos-rrll/show/<?= (int) $caso['id'] ?>" class="item" title="Dashboard">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                        <a href="/SGA-SEBANA/public/casos-rrll/edit/<?= (int) $caso['id'] ?>" class="item" title="Editar datos fijos">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="/SGA-SEBANA/public/casos-rrll/<?= (int) $caso['id'] ?>/etapas" class="item" title="Etapas">
                                            <i class="zmdi zmdi-layers"></i>
                                        </a>
                                        <a href="#" class="item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-caso-id="<?= (int) $caso['id'] ?>" title="Archivar expediente">
                                            <i class="zmdi zmdi-archive"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="zmdi zmdi-info"></i> No hay casos registrados con los filtros aplicados.
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

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archivar Expediente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formDeleteCaso">
                <div class="modal-body">
                    <p>Esta accion no elimina fisicamente el registro.</p>
                    <p>El expediente se movera a estado <strong>archivado</strong>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Archivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const casoId = button.getAttribute('data-caso-id');
        const form = document.getElementById('formDeleteCaso');
        form.setAttribute('action', `/SGA-SEBANA/public/casos-rrll/delete/${casoId}`);
    });
}
</script>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php'; ?>

