<?php
/**
 * Vista de Edición de Puesto
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Puesto</h2>
            <a href="/SGA-SEBANA/public/puestos" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <form action="/SGA-SEBANA/public/puestos/edit/<?= $puesto['id'] ?>" method="post" class="form-horizontal">

            <!-- Card 1: Datos principales -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment-account"></i> Datos del Puesto</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="afiliado_id" class="form-control-label">Afiliado</label>
                            <select name="afiliado_id" id="afiliado_id" class="form-control" disabled>
                                <?php foreach ($afiliados as $afiliado): ?>
                                    <option value="<?= $afiliado['id'] ?>" <?= $afiliado['id'] == $puesto['afiliado_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($afiliado['nombre_completo']) ?> -
                                        <?= htmlspecialchars($afiliado['cedula']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">El afiliado no se puede cambiar al editar.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-control-label">Nombre del Puesto *</label>
                            <input type="text" id="nombre" name="nombre" class="form-control"
                                value="<?= htmlspecialchars($puesto['nombre']) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="departamento" class="form-control-label">Departamento</label>
                            <input type="text" id="departamento" name="departamento" class="form-control"
                                value="<?= htmlspecialchars($puesto['departamento'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="oficina_id" class="form-control-label">Oficina</label>
                            <select name="oficina_id" id="oficina_id" class="form-control">
                                <option value="">-- Sin Oficina --</option>
                                <?php foreach ($oficinas as $oficina): ?>
                                    <option value="<?= $oficina['id'] ?>" <?= ($puesto['oficina_id'] ?? '') == $oficina['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($oficina['nombre']) ?> (
                                        <?= htmlspecialchars($oficina['codigo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_asignacion" class="form-control-label">Fecha Asignación *</label>
                            <input type="date" id="fecha_asignacion" name="fecha_asignacion" class="form-control"
                                value="<?= htmlspecialchars($puesto['fecha_asignacion']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_contrato" class="form-control-label">Tipo de Contrato</label>
                            <select name="tipo_contrato" id="tipo_contrato" class="form-control">
                                <option value="indefinido" <?= $puesto['tipo_contrato'] === 'indefinido' ? 'selected' : '' ?>>Indefinido</option>
                                <option value="temporal" <?= $puesto['tipo_contrato'] === 'temporal' ? 'selected' : '' ?>
                                    >Temporal</option>
                                <option value="proyecto" <?= $puesto['tipo_contrato'] === 'proyecto' ? 'selected' : '' ?>
                                    >Proyecto</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jornada" class="form-control-label">Jornada</label>
                            <select name="jornada" id="jornada" class="form-control">
                                <option value="completa" <?= $puesto['jornada'] === 'completa' ? 'selected' : '' ?>
                                    >Completa</option>
                                <option value="media" <?= $puesto['jornada'] === 'media' ? 'selected' : '' ?>>Media
                                </option>
                                <option value="por_horas" <?= $puesto['jornada'] === 'por_horas' ? 'selected' : '' ?>>Por
                                    Horas</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estado" class="form-control-label">Estado</label>
                            <select name="estado" id="estado" class="form-control"
                                onchange="toggleRemovalFields(this.value)">
                                <option value="activo" <?= $puesto['estado'] === 'activo' ? 'selected' : '' ?>>Activo
                                </option>
                                <option value="finalizado" <?= $puesto['estado'] === 'finalizado' ? 'selected' : '' ?>
                                    >Finalizado</option>
                                <option value="suspendido" <?= $puesto['estado'] === 'suspendido' ? 'selected' : '' ?>
                                    >Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="salario_base" class="form-control-label">Salario Base (₡)</label>
                            <input type="number" id="salario_base" name="salario_base" class="form-control" step="0.01"
                                min="0" value="<?= htmlspecialchars($puesto['salario_base'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="asignado_por" class="form-control-label">Asignado Por</label>
                            <select name="asignado_por" id="asignado_por" class="form-control">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>" <?= ($puesto['asignado_por'] ?? '') == $usuario['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($usuario['nombre_completo']) ?> (
                                        <?= htmlspecialchars($usuario['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card 2: Remoción (visible when estado != activo) -->
            <div class="card" id="removal-card" style="<?= $puesto['estado'] === 'activo' ? 'display:none;' : '' ?>">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-time-restore"></i> Datos de Remoción</strong>
                </div>
                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_remocion" class="form-control-label">Fecha Remoción</label>
                            <input type="date" id="fecha_remocion" name="fecha_remocion" class="form-control"
                                value="<?= htmlspecialchars($puesto['fecha_remocion'] ?? '') ?>">
                        </div>
                        <div class="col-md-8">
                            <label for="motivo_remocion" class="form-control-label">Motivo de Remoción</label>
                            <textarea name="motivo_remocion" id="motivo_remocion" rows="2" class="form-control"
                                placeholder="Explique el motivo de la remoción..."><?= htmlspecialchars($puesto['motivo_remocion'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Detalles adicionales -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment"></i> Detalles Adicionales</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descripcion" class="form-control-label">Descripción del Puesto</label>
                            <textarea name="descripcion" id="descripcion" rows="3" class="form-control"
                                placeholder="Describa las responsabilidades y funciones del puesto..."><?= htmlspecialchars($puesto['descripcion'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-control-label">Observaciones</label>
                            <input type="text" name="observaciones" id="observaciones" class="form-control"
                                value="<?= htmlspecialchars($puesto['observaciones'] ?? '') ?>"
                                placeholder="Notas adicionales...">
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
                    </button>
                    <a href="/SGA-SEBANA/public/puestos" class="btn btn-secondary btn-sm">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    function toggleRemovalFields(estado) {
        var card = document.getElementById('removal-card');
        if (estado === 'activo') {
            card.style.display = 'none';
        } else {
            card.style.display = '';
        }
    }
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>