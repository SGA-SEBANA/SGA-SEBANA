<?php
/**
 * Vista de Creación de Puesto
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Asignar Nuevo Puesto</h2>
            <a href="/SGA-SEBANA/public/puestos" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <form action="/SGA-SEBANA/public/puestos/create" method="post" class="form-horizontal">

            <!-- Card 1: Datos principales -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment-account"></i> Datos del Puesto</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="afiliado_id" class="form-control-label">Afiliado *</label>
                            <select name="afiliado_id" id="afiliado_id" class="form-control" required>
                                <option value="">-- Seleccionar Afiliado --</option>
                                <?php foreach ($afiliados as $afiliado): ?>
                                    <option value="<?= $afiliado['id'] ?>">
                                        <?= htmlspecialchars($afiliado['nombre_completo']) ?> -
                                        <?= htmlspecialchars($afiliado['cedula']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-control-label">Nombre del Puesto *</label>
                            <input type="text" id="nombre" name="nombre" class="form-control"
                                placeholder="Ej: Analista de Crédito" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="departamento" class="form-control-label">Departamento</label>
                            <input type="text" id="departamento" name="departamento" class="form-control"
                                placeholder="Ej: Operaciones">
                        </div>
                        <div class="col-md-6">
                            <label for="oficina_id" class="form-control-label">Oficina</label>
                            <select name="oficina_id" id="oficina_id" class="form-control">
                                <option value="">-- Sin Oficina --</option>
                                <?php foreach ($oficinas as $oficina): ?>
                                    <option value="<?= $oficina['id'] ?>">
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
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_contrato" class="form-control-label">Tipo de Contrato</label>
                            <select name="tipo_contrato" id="tipo_contrato" class="form-control">
                                <option value="indefinido">Indefinido</option>
                                <option value="temporal">Temporal</option>
                                <option value="proyecto">Proyecto</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jornada" class="form-control-label">Jornada</label>
                            <select name="jornada" id="jornada" class="form-control">
                                <option value="completa">Completa</option>
                                <option value="media">Media</option>
                                <option value="por_horas">Por Horas</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estado" class="form-control-label">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="suspendido">Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="salario_base" class="form-control-label">Salario Base (₡)</label>
                            <input type="number" id="salario_base" name="salario_base" class="form-control" step="0.01"
                                min="0" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label for="asignado_por" class="form-control-label">Asignado Por</label>
                            <select name="asignado_por" id="asignado_por" class="form-control">
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>">
                                        <?= htmlspecialchars($usuario['nombre_completo']) ?> (
                                        <?= htmlspecialchars($usuario['username']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card 2: Detalles adicionales -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment"></i> Detalles Adicionales</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="descripcion" class="form-control-label">Descripción del Puesto</label>
                            <textarea name="descripcion" id="descripcion" rows="3" class="form-control"
                                placeholder="Describa las responsabilidades y funciones del puesto..."></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-control-label">Observaciones</label>
                            <input type="text" name="observaciones" id="observaciones" class="form-control"
                                placeholder="Notas adicionales...">
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Asignar Puesto
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm">
                        <i class="zmdi zmdi-refresh-alt"></i> Limpiar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>