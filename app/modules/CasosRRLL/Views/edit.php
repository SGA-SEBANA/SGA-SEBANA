<?php
/**
 * Vista de Editar Caso RRLL
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Caso: <?= htmlspecialchars($caso['numero_expediente']) ?></h2>
            <a href="/SGA-SEBANA/public/casos-rrll/show/<?= $caso['id'] ?>" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i>
                <strong>¡Error!</strong>
                <?php
                    if($error_msg === 'campos_requeridos') echo "Los campos marcados con (*) son obligatorios.";
                    elseif($error_msg === 'expediente_duplicado') echo "El número de expediente ya existe.";
                    elseif($error_msg === 'db_error') echo "Error al actualizar en la base de datos.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="/SGA-SEBANA/public/casos-rrll/update/<?= $caso['id'] ?>" method="POST" class="form-horizontal">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <strong><i class="zmdi zmdi-case"></i> Información del Caso</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="numero_expediente" class="form-control-label font-weight-bold">Número de Expediente</label>
                                <input type="text" id="numero_expediente" name="numero_expediente" 
                                       class="form-control" value="<?= htmlspecialchars($caso['numero_expediente']) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categoria_id" class="form-control-label font-weight-bold">Categoría <span class="text-danger">*</span></label>
                                <select id="categoria_id" name="categoria_id" class="form-control" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $caso['categoria_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="prioridad" class="form-control-label font-weight-bold">Prioridad</label>
                                <select id="prioridad" name="prioridad" class="form-control">
                                    <option value="baja" <?= $caso['prioridad'] === 'baja' ? 'selected' : '' ?>>Baja</option>
                                    <option value="media" <?= $caso['prioridad'] === 'media' ? 'selected' : '' ?>>Media</option>
                                    <option value="alta" <?= $caso['prioridad'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                                    <option value="urgente" <?= $caso['prioridad'] === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="titulo" class="form-control-label font-weight-bold">Título del Caso <span class="text-danger">*</span></label>
                                <input type="text" id="titulo" name="titulo" 
                                       class="form-control" value="<?= htmlspecialchars($caso['titulo']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado" class="form-control-label font-weight-bold">Estado</label>
                                <select id="estado" name="estado" class="form-control">
                                    <option value="activo" <?= $caso['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="en_progreso" <?= $caso['estado'] === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="cerrado" <?= $caso['estado'] === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                                    <option value="suspendido" <?= $caso['estado'] === 'suspendido' ? 'selected' : '' ?>>Suspendido</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion" class="form-control-label font-weight-bold">Descripción del Caso <span class="text-danger">*</span></label>
                                <textarea id="descripcion" name="descripcion" rows="4" class="form-control" required><?= htmlspecialchars($caso['descripcion']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hechos" class="form-control-label font-weight-bold">Hechos Relevantes</label>
                                <textarea id="hechos" name="hechos" rows="3" class="form-control"><?= htmlspecialchars($caso['hechos'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="afiliado_id" class="form-control-label font-weight-bold">Afiliado Involucrado</label>
                                <select id="afiliado_id" name="afiliado_id" class="form-control">
                                    <option value="">Sin asociar afiliado</option>
                                    <?php foreach($afiliados as $afiliado): ?>
                                        <option value="<?= $afiliado['id'] ?>" <?= $afiliado['id'] == $caso['afiliado_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($afiliado['nombre_completo'] . ' - ' . $afiliado['cedula']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="responsable_actual" class="form-control-label font-weight-bold">Responsable del Caso</label>
                                <select id="responsable_actual" name="responsable_actual" class="form-control">
                                    <option value="">Sin asignar</option>
                                    <?php foreach($usuarios as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $caso['responsable_actual'] ? 'selected' : '' ?>>
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
                                <label for="empresa_involucrada" class="form-control-label font-weight-bold">Empresa Involucrada</label>
                                <input type="text" id="empresa_involucrada" name="empresa_involucrada" 
                                       class="form-control" value="<?= htmlspecialchars($caso['empresa_involucrada'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento_afectado" class="form-control-label font-weight-bold">Departamento Afectado</label>
                                <input type="text" id="departamento_afectado" name="departamento_afectado" 
                                       class="form-control" value="<?= htmlspecialchars($caso['departamento_afectado'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_incidente" class="form-control-label font-weight-bold">Fecha del Incidente</label>
                                <input type="date" id="fecha_incidente" name="fecha_incidente" 
                                       value="<?= $caso['fecha_incidente'] ?? '' ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="resultado_final" class="form-control-label font-weight-bold">Resultado Final</label>
                                <textarea id="resultado_final" name="resultado_final" rows="2" class="form-control"><?= htmlspecialchars($caso['resultado_final'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones" class="form-control-label font-weight-bold">Observaciones</label>
                                <textarea id="observaciones" name="observaciones" rows="2" class="form-control"><?= htmlspecialchars($caso['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-right py-3">
                    <a href="/SGA-SEBANA/public/casos-rrll/show/<?= $caso['id'] ?>" class="btn btn-outline-secondary btn-sm px-4 mr-2">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
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
