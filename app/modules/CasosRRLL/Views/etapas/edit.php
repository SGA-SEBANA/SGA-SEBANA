<?php
/**
 * Vista de Editar Etapa
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Etapa: <?= htmlspecialchars($etapa['nombre']) ?></h2>
            <a href="/SGA-SEBANA/public/casos-rrll/<?= $etapa['caso_id'] ?>/etapas" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <strong>¡Error!</strong>
                <?php
                    if($_GET['error'] === 'campos_requeridos') echo "Los campos obligatorios no pueden estar vacíos.";
                    elseif($_GET['error'] === 'nombre_duplicado') echo "Ya existe una etapa con ese nombre.";
                    elseif($_GET['error'] === 'db_error') echo "Error al actualizar en la base de datos.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/casos-rrll/etapas/<?= $etapa['id'] ?>/update" method="POST" class="form-horizontal">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <strong><i class="zmdi zmdi-layers"></i> Información de la Etapa</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-4">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="nombre" class="form-control-label font-weight-bold">Nombre de la Etapa <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" 
                                       class="form-control" value="<?= htmlspecialchars($etapa['nombre']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado" class="form-control-label font-weight-bold">Estado</label>
                                <select id="estado" name="estado" class="form-control">
                                    <option value="pendiente" <?= $etapa['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="en_progreso" <?= $etapa['estado'] === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="finalizado" <?= $etapa['estado'] === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                    <option value="bloqueado" <?= $etapa['estado'] === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                                    <option value="cancelado" <?= $etapa['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion" class="form-control-label font-weight-bold">Descripción</label>
                                <textarea id="descripcion" name="descripcion" rows="4" class="form-control"><?= htmlspecialchars($etapa['descripcion'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable_id" class="form-control-label font-weight-bold">Responsable</label>
                                <select id="responsable_id" name="responsable_id" class="form-control">
                                    <option value="">Sin asignar</option>
                                    <?php foreach($usuarios as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $etapa['responsable_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['nombre_completo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">Orden en el Caso</label>
                                <input type="text" class="form-control" value="Etapa #<?= htmlspecialchars($etapa['orden']) ?>" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-control-label font-weight-bold">Fecha de Inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?= $etapa['fecha_inicio'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_estimada_fin" class="form-control-label font-weight-bold">Fecha Estimada de Finalización</label>
                                <input type="date" id="fecha_estimada_fin" name="fecha_estimada_fin" 
                                       value="<?= $etapa['fecha_estimada_fin'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin" class="form-control-label font-weight-bold">Fecha de Finalización (Real)</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" 
                                       value="<?= $etapa['fecha_fin'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duracion_dias" class="form-control-label font-weight-bold">Duración</label>
                                <input type="text" class="form-control" value="<?= $etapa['duracion_dias'] ?? '-' ?> días" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="resultado" class="form-control-label font-weight-bold">Resultado de la Etapa</label>
                                <textarea id="resultado" name="resultado" rows="3" class="form-control"><?= htmlspecialchars($etapa['resultado'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="documentos_generados" class="form-control-label font-weight-bold">Documentos Generados</label>
                                <textarea id="documentos_generados" name="documentos_generados" rows="2" placeholder="Ej: demanda.pdf, contrato.docx..."
                                          class="form-control"><?= htmlspecialchars($etapa['documentos_generados'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones" class="form-control-label font-weight-bold">Observaciones</label>
                                <textarea id="observaciones" name="observaciones" rows="2" class="form-control"><?= htmlspecialchars($etapa['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-right py-3">
                    <a href="/SGA-SEBANA/public/casos-rrll/<?= $etapa['caso_id'] ?>/etapas" class="btn btn-outline-secondary btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        <!-- Información adicional -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="zmdi zmdi-info"></i> Información del Sistema</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($etapa['fecha_creacion'])) ?></small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Última Actualización:</strong> <?= date('d/m/Y H:i', strtotime($etapa['fecha_actualizacion'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
