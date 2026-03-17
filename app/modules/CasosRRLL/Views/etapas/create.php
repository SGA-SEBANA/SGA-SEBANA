<?php
/**
 * Vista de Crear Nueva Etapa
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva Etapa: <?= htmlspecialchars($caso['numero_expediente']) ?></h2>
            <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <strong>¡Error!</strong>
                <?php
                    if($_GET['error'] === 'campos_requeridos') echo "Los campos obligatorios no pueden estar vacíos.";
                    elseif($_GET['error'] === 'nombre_duplicado') echo "Ya existe una etapa con ese nombre en este caso.";
                    elseif($_GET['error'] === 'db_error') echo "Error al guardar en la base de datos.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas/store" method="POST" class="form-horizontal">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <strong><i class="zmdi zmdi-layers"></i> Información de la Etapa</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre" class="form-control-label font-weight-bold">Nombre de la Etapa <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej: Presentación de Demanda..." 
                                       class="form-control" required autofocus>
                                <small class="form-text text-muted">Nombre descriptivo de la etapa del proceso</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion" class="form-control-label font-weight-bold">Descripción</label>
                                <textarea id="descripcion" name="descripcion" rows="4" 
                                          placeholder="Describe los objetivos y tareas de esta etapa..."
                                          class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado" class="form-control-label font-weight-bold">Estado Inicial</label>
                                <select id="estado" name="estado" class="form-control">
                                    <option value="pendiente" selected>Pendiente</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="bloqueado">Bloqueado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable_id" class="form-control-label font-weight-bold">Responsable</label>
                                <select id="responsable_id" name="responsable_id" class="form-control">
                                    <option value="">Sin asignar</option>
                                    <?php foreach($usuarios as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= htmlspecialchars($user['nombre_completo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio" class="form-control-label font-weight-bold">Fecha de Inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                                       value="<?= date('Y-m-d') ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_estimada_fin" class="form-control-label font-weight-bold">Fecha Estimada de Finalización</label>
                                <input type="date" id="fecha_estimada_fin" name="fecha_estimada_fin" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones" class="form-control-label font-weight-bold">Observaciones</label>
                                <textarea id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Notas o comentarios sobre lo esperado en esta etapa..."
                                          class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-right py-3">
                    <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas" class="btn btn-outline-secondary btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                    <button type="reset" class="btn btn-outline-warning btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-refresh-alt"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Etapa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
