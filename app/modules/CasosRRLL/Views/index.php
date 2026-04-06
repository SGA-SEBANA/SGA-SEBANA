<?php
/**
 * Vista de Listado de Casos RRLL
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="title-1 mb-4">Gestión de Casos RRLL</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> 
                <strong>¡Éxito!</strong>
                <?php
                    if($success === 'creado') echo "Caso registrado correctamente.";
                    elseif($success === 'actualizado') echo "Caso actualizado correctamente.";
                    elseif($success === 'archivado') echo "Caso archivado satisfactoriamente.";
                    elseif($success === 'eliminado') echo "Caso eliminado correctamente.";
                    elseif($success === 'estado_actualizado') echo "El estado del caso ha sido actualizado.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <strong>¡Error!</strong>
                <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros y búsqueda -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Búsqueda y Filtros</strong>
            </div>
            <div class="card-body">
                <form action="/SGA-SEBANA/public/casos-rrll" method="GET" class="form-inline">
                    <div class="row w-100">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="q" class="mb-1">Buscar</label>
                                <input type="text" id="q" name="q" class="form-control form-control-sm" 
                                       placeholder="Expediente, título..." value="<?= htmlspecialchars($filtros['busqueda']) ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estado" class="mb-1">Estado</label>
                                <select name="estado" id="estado" class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="en_progreso" <?= ($filtros['estado'] ?? '') === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="cerrado" <?= ($filtros['estado'] ?? '') === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                                    <option value="archivado" <?= ($filtros['estado'] ?? '') === 'archivado' ? 'selected' : '' ?>>Archivado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="prioridad" class="mb-1">Prioridad</label>
                                <select name="prioridad" id="prioridad" class="form-control form-control-sm">
                                    <option value="">Todas</option>
                                    <option value="baja" <?= ($filtros['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                                    <option value="media" <?= ($filtros['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                                    <option value="alta" <?= ($filtros['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                                    <option value="urgente" <?= ($filtros['prioridad'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="categoria_id" class="mb-1">Categoría</label>
                                <select name="categoria_id" id="categoria_id" class="form-control form-control-sm">
                                    <option value="">Todas</option>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="etapa_nombre" class="mb-1">Etapa (nombre)</label>
                                <input type="text"
                                       id="etapa_nombre"
                                       name="etapa_nombre"
                                       class="form-control form-control-sm"
                                       placeholder="Ej: investigacion"
                                       value="<?= htmlspecialchars((string) ($filtros['etapa_nombre'] ?? '')) ?>">
                            </div>
                        </div>
                        <div class="col-md-12 d-flex align-items-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                <i class="zmdi zmdi-search"></i> Filtrar
                            </button>
                            <a href="/SGA-SEBANA/public/casos-rrll/reporte/pdf?estado=<?= urlencode((string) ($filtros['estado'] ?? '')) ?>&prioridad=<?= urlencode((string) ($filtros['prioridad'] ?? '')) ?>&categoria_id=<?= urlencode((string) ($filtros['categoria_id'] ?? '')) ?>&etapa_nombre=<?= urlencode((string) ($filtros['etapa_nombre'] ?? '')) ?>&etapa_estado=<?= urlencode((string) ($filtros['etapa_estado'] ?? '')) ?>" 
                               class="btn btn-danger btn-sm">
                                <i class="zmdi zmdi-download"></i> Generar PDF
                            </a>
                            <a href="/SGA-SEBANA/public/casos-rrll/reporte/pdf?solo_investigacion=1" class="btn btn-outline-danger btn-sm ms-2">
                                <i class="zmdi zmdi-collection-text"></i> Reporte investigacion
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Botón crear nuevo caso -->
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

        <!-- Tabla de casos -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Expediente</th>
                        <th>Título</th>
                        <th>Afiliado</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Progreso</th>
                        <th>Responsable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($casos)): ?>
                        <?php foreach($casos as $caso): ?>
                            <tr class="tr-shadow">
                                <td>
                                    <strong><?= htmlspecialchars($caso['numero_expediente'])?></strong>
                                </td>
                                <td><?= htmlspecialchars(substr($caso['titulo'], 0, 40)) ?></td>
                                <td><?= htmlspecialchars($caso['afiliado_nombre'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-<?= $caso['estado'] === 'activo' ? 'success' : ($caso['estado'] === 'en_progreso' ? 'info' : ($caso['estado'] === 'archivado' ? 'secondary' : 'warning')) ?>">
                                        <?= ucfirst($caso['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $caso['prioridad'] === 'urgente' ? 'danger' : ($caso['prioridad'] === 'alta' ? 'warning' : ($caso['prioridad'] === 'media' ? 'info' : 'secondary')) ?>">
                                        <?= ucfirst($caso['prioridad']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $caso['progreso'] ?>%" 
                                             aria-valuenow="<?= $caso['progreso'] ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= $caso['progreso'] ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($caso['responsable_nombre'] ?? 'Sin asignar') ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <div class="js-item-menu" style="position: relative; display: inline-block;">
                                            <button class="item" type="button" title="Acciones">
                                                <i class="zmdi zmdi-more-vert"></i>
                                            </button>
                                            <div class="account-dropdown js-dropdown" style="min-width: 200px;">
                                                <div class="account-dropdown__body">
                                                    <div class="account-dropdown__item">
                                                        <a href="/SGA-SEBANA/public/casos-rrll/show/<?= $caso['id'] ?>">
                                                            <i class="zmdi zmdi-eye"></i> Ver Detalles
                                                        </a>
                                                    </div>
                                                    <div class="account-dropdown__item">
                                                        <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas">
                                                            <i class="zmdi zmdi-layers"></i> Etapas (<?= $caso['total_etapas'] ?? 0 ?>)
                                                        </a>
                                                    </div>
                                                    <div class="account-dropdown__item">
                                                        <a href="/SGA-SEBANA/public/casos-rrll/edit/<?= $caso['id'] ?>">
                                                            <i class="zmdi zmdi-edit"></i> Editar
                                                        </a>
                                                    </div>
                                                    <div class="account-dropdown__item">
                                                        <a href="#" data-bs-toggle="modal" 
                                                           data-bs-target="#deleteModal" data-caso-id="<?= $caso['id'] ?>">
                                                            <i class="zmdi zmdi-delete"></i> Eliminar
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
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="zmdi zmdi-info"></i> No hay casos registrados
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

<!-- Modal: Eliminar Caso -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Caso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formDeleteCaso">
                <div class="modal-body">
                    <p class="text-danger"><strong>Esta acción es irreversible.</strong></p>
                    <p>Se eliminarán el caso y todas sus etapas asociadas del sistema.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Eliminar Caso - actualiza el action del form según el caso seleccionado
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const casoId = button.getAttribute('data-caso-id');
        const form = document.getElementById('formDeleteCaso');
        form.setAttribute('action', `/SGA-SEBANA/public/casos-rrll/delete/${casoId}`);
    });
}
</script>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
